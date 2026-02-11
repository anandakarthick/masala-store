<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderEmails;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PhonePeController extends Controller
{
    private string $baseUrl;
    private string $merchantId;
    private string $saltKey;
    private string $saltIndex;

    public function __construct()
    {
        $phonepe = PaymentMethod::where('code', 'phonepe')->first();

        if ($phonepe) {
            $this->merchantId = $phonepe->getSetting('merchant_id', '');
            $this->saltKey = $phonepe->getSetting('salt_key', '');
            $this->saltIndex = $phonepe->getSetting('salt_index', '1');

            $environment = $phonepe->getSetting('environment', 'production');
            $this->baseUrl = $environment === 'sandbox'
                ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
                : 'https://api.phonepe.com/apis/hermes';
        }
    }

    /**
     * Generate X-VERIFY checksum for PhonePe
     */
    private function generateChecksum(string $base64Payload, string $endpoint): string
    {
        $string = $base64Payload . $endpoint . $this->saltKey;
        $sha256 = hash('sha256', $string);
        return $sha256 . '###' . $this->saltIndex;
    }

    /**
     * Create PhonePe payment order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $user = $request->user();
        $order = Order::where('order_number', $request->order_number)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Order is already paid.',
            ], 400);
        }

        // Get PhonePe settings
        $phonepe = PaymentMethod::where('code', 'phonepe')->first();

        if (!$phonepe || !$phonepe->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'PhonePe is not configured or disabled.',
            ], 400);
        }

        if (empty($this->merchantId) || empty($this->saltKey)) {
            return response()->json([
                'success' => false,
                'message' => 'PhonePe credentials are not configured.',
            ], 400);
        }

        // Calculate amount to pay
        $amountToPay = $order->total_amount - ($order->wallet_amount_used ?? 0);

        if ($amountToPay < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Order amount must be at least Rs. 1 for online payment.',
            ], 400);
        }

        try {
            $amountInPaise = (int) round($amountToPay * 100);
            $merchantTransactionId = 'MT' . $order->id . 'T' . time();
            $merchantUserId = 'MU' . $user->id;

            // Build payload
            $payload = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $merchantTransactionId,
                'merchantUserId' => $merchantUserId,
                'amount' => $amountInPaise,
                'redirectUrl' => config('app.url') . '/api/v1/phonepe/callback?order_id=' . $order->id,
                'redirectMode' => 'REDIRECT',
                'callbackUrl' => route('phonepe.webhook'),
                'paymentInstrument' => [
                    'type' => 'PAY_PAGE',
                ],
            ];

            // Add mobile number if available
            if ($order->customer_phone) {
                $mobileNumber = preg_replace('/[^0-9]/', '', $order->customer_phone);
                if (strlen($mobileNumber) === 10) {
                    $payload['mobileNumber'] = $mobileNumber;
                } elseif (strlen($mobileNumber) > 10) {
                    $payload['mobileNumber'] = substr($mobileNumber, -10);
                }
            }

            $base64Payload = base64_encode(json_encode($payload));
            $endpoint = '/pg/v1/pay';
            $checksum = $this->generateChecksum($base64Payload, $endpoint);

            Log::info('Creating PhonePe order via API', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'merchant_transaction_id' => $merchantTransactionId,
                'amount' => $amountToPay,
            ]);

            $ch = curl_init($this->baseUrl . $endpoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['request' => $base64Payload]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-VERIFY: ' . $checksum,
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            if (file_exists(base_path('cacert.pem'))) {
                curl_setopt($ch, CURLOPT_CAINFO, base_path('cacert.pem'));
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('PhonePe API CURL error', ['error' => $curlError]);
                return response()->json([
                    'success' => false,
                    'message' => 'Network error. Please try again.',
                ], 500);
            }

            $phonePeResponse = json_decode($response, true);

            if (!$phonePeResponse || !isset($phonePeResponse['success']) || !$phonePeResponse['success']) {
                Log::error('PhonePe order creation failed', [
                    'response' => $phonePeResponse,
                    'http_code' => $httpCode,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $phonePeResponse['message'] ?? 'Failed to create payment order.',
                ], 500);
            }

            // Get redirect URL
            $redirectUrl = $phonePeResponse['data']['instrumentResponse']['redirectInfo']['url'] ?? null;

            if (!$redirectUrl) {
                Log::error('PhonePe: No redirect URL in response');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get payment URL.',
                ], 500);
            }

            // Store transaction ID
            $order->update([
                'transaction_id' => $merchantTransactionId,
            ]);

            Log::info('PhonePe order created via API', [
                'order_id' => $order->id,
                'merchant_transaction_id' => $merchantTransactionId,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'redirect_url' => $redirectUrl,
                    'merchant_transaction_id' => $merchantTransactionId,
                    'amount' => $amountToPay,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('PhonePe API exception', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Check payment status
     */
    public function checkStatus(Request $request, $orderNumber)
    {
        $user = $request->user();
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (!$order->transaction_id) {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'NOT_INITIATED',
                    'payment_status' => $order->payment_status,
                ],
            ]);
        }

        try {
            $endpoint = '/pg/v1/status/' . $this->merchantId . '/' . $order->transaction_id;
            $checksum = $this->generateChecksum('', $endpoint);

            $url = $this->baseUrl . $endpoint;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-VERIFY: ' . $checksum,
                'X-MERCHANT-ID: ' . $this->merchantId,
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            if (file_exists(base_path('cacert.pem'))) {
                curl_setopt($ch, CURLOPT_CAINFO, base_path('cacert.pem'));
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $data = json_decode($response, true);

            if ($data && isset($data['success']) && $data['success']) {
                $code = $data['code'] ?? '';

                // Update order if payment completed
                if ($code === 'PAYMENT_SUCCESS' && $order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                        'transaction_id' => $data['data']['transactionId'] ?? $order->transaction_id,
                    ]);

                    SendOrderEmails::dispatch($order->fresh()->load('items.product'));
                } elseif (in_array($code, ['PAYMENT_ERROR', 'PAYMENT_DECLINED', 'PAYMENT_CANCELLED']) && $order->payment_status !== 'failed') {
                    $order->update(['payment_status' => 'failed']);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => $code,
                        'payment_status' => $order->fresh()->payment_status,
                        'transaction_id' => $data['data']['transactionId'] ?? null,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'PENDING',
                    'payment_status' => $order->payment_status,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('PhonePe status check exception', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'UNKNOWN',
                    'payment_status' => $order->payment_status,
                ],
            ]);
        }
    }
}
