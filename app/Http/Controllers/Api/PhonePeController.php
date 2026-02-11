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
    private string $clientId;
    private string $clientSecret;
    private string $merchantId;

    public function __construct()
    {
        $phonepe = PaymentMethod::where('code', 'phonepe')->first();

        if ($phonepe) {
            $this->clientId = $phonepe->getSetting('client_id', '');
            $this->clientSecret = $phonepe->getSetting('client_secret', '');
            $this->merchantId = $phonepe->getSetting('merchant_id', '');

            $environment = $phonepe->getSetting('environment', 'production');
            $this->baseUrl = $environment === 'sandbox'
                ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
                : 'https://api.phonepe.com/apis/hermes';
        }
    }

    /**
     * Get access token from PhonePe
     */
    private function getAccessToken(): ?string
    {
        try {
            $ch = curl_init($this->baseUrl . '/v1/oauth/token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'client_id' => $this->clientId,
                'client_version' => '1',
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('PhonePe OAuth CURL error', ['error' => $curlError]);
                return null;
            }

            $data = json_decode($response, true);

            if ($httpCode === 200 && isset($data['access_token'])) {
                return $data['access_token'];
            }

            Log::error('PhonePe OAuth failed', [
                'http_code' => $httpCode,
                'response' => $data,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PhonePe OAuth exception', ['message' => $e->getMessage()]);
            return null;
        }
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

        if (!$this->clientId || !$this->clientSecret || !$this->merchantId) {
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
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authenticate with PhonePe. Please try again.',
                ], 500);
            }

            $amountInPaise = (int) round($amountToPay * 100);
            $merchantTransactionId = 'MT' . $order->id . '_' . time();

            $payloadData = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $merchantTransactionId,
                'amount' => $amountInPaise,
                'merchantOrderId' => $order->order_number,
                'message' => 'Payment for Order #' . $order->order_number,
                'mobileNumber' => preg_replace('/[^0-9]/', '', $order->customer_phone),
                'paymentInstrument' => [
                    'type' => 'PAY_PAGE',
                ],
                'redirectUrl' => config('app.url') . '/api/v1/phonepe/callback?order_id=' . $order->id,
                'redirectMode' => 'REDIRECT',
                'callbackUrl' => route('phonepe.webhook'),
            ];

            Log::info('Creating PhonePe order via API', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'merchant_transaction_id' => $merchantTransactionId,
                'amount' => $amountToPay,
            ]);

            $ch = curl_init($this->baseUrl . '/v1/pay');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['request' => $payloadData]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: O-Bearer ' . $accessToken,
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

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

            if ($httpCode !== 200 || !isset($phonePeResponse['redirectUrl'])) {
                Log::error('PhonePe order creation failed', [
                    'response' => $phonePeResponse,
                    'http_code' => $httpCode,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $phonePeResponse['message'] ?? 'Failed to create payment order.',
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
                    'redirect_url' => $phonePeResponse['redirectUrl'],
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
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 'UNKNOWN',
                        'payment_status' => $order->payment_status,
                    ],
                ]);
            }

            $url = $this->baseUrl . '/v1/status/' . $this->merchantId . '/' . $order->transaction_id;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: O-Bearer ' . $accessToken,
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $data = json_decode($response, true);

            if ($httpCode === 200 && isset($data['state'])) {
                $state = $data['state'];

                // Update order if payment completed
                if ($state === 'COMPLETED' && $order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                        'transaction_id' => $data['transactionId'] ?? $order->transaction_id,
                    ]);

                    SendOrderEmails::dispatch($order->fresh()->load('items.product'));
                } elseif ($state === 'FAILED' && $order->payment_status !== 'failed') {
                    $order->update(['payment_status' => 'failed']);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => $state,
                        'payment_status' => $order->fresh()->payment_status,
                        'transaction_id' => $data['transactionId'] ?? null,
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
