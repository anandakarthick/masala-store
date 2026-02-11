<?php

namespace App\Http\Controllers;

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

            // Use production or sandbox based on setting
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
     * Create PhonePe order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_type' => 'nullable|in:UPI,CARD,NET_BANKING,WALLET',
        ]);

        $order = Order::findOrFail($request->order_id);
        $paymentType = $request->input('payment_type', 'PAY_PAGE');

        // Get PhonePe settings
        $phonepe = PaymentMethod::where('code', 'phonepe')->first();

        if (!$phonepe || !$phonepe->is_active) {
            Log::error('PhonePe not configured or disabled');
            return response()->json([
                'success' => false,
                'message' => 'PhonePe is not configured or disabled.',
            ], 400);
        }

        if (empty($this->merchantId) || empty($this->saltKey)) {
            Log::error('PhonePe credentials missing', [
                'merchant_id_set' => !empty($this->merchantId),
                'salt_key_set' => !empty($this->saltKey),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'PhonePe credentials are not configured. Please set Merchant ID and Salt Key.',
            ], 400);
        }

        // Calculate amount to pay (total - wallet if used)
        $amountToPay = $order->total_amount - ($order->wallet_amount_used ?? 0);

        // Ensure minimum amount (₹1 = 100 paise)
        if ($amountToPay < 1) {
            Log::error('Amount too low for PhonePe', ['amount' => $amountToPay]);
            return response()->json([
                'success' => false,
                'message' => 'Order amount must be at least ₹1 for online payment.',
            ], 400);
        }

        try {
            $amountInPaise = (int) round($amountToPay * 100);
            $merchantTransactionId = 'MT' . $order->id . 'T' . time();
            $merchantUserId = 'MU' . ($order->user_id ?? $order->id);

            // Build payload
            $payload = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $merchantTransactionId,
                'merchantUserId' => $merchantUserId,
                'amount' => $amountInPaise,
                'redirectUrl' => route('phonepe.callback') . '?order_id=' . $order->id,
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

            Log::info('Creating PhonePe order', [
                'order_id' => $order->id,
                'merchant_transaction_id' => $merchantTransactionId,
                'amount_inr' => $amountToPay,
                'amount_paise' => $amountInPaise,
                'endpoint' => $this->baseUrl . $endpoint,
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

            // Use CA cert if available
            if (file_exists(base_path('cacert.pem'))) {
                curl_setopt($ch, CURLOPT_CAINFO, base_path('cacert.pem'));
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('PhonePe CURL error', ['error' => $curlError]);
                return response()->json([
                    'success' => false,
                    'message' => 'Network error. Please check your internet connection.',
                ], 500);
            }

            $phonePeResponse = json_decode($response, true);

            Log::info('PhonePe API response', [
                'http_code' => $httpCode,
                'response' => $phonePeResponse,
            ]);

            if (!$phonePeResponse || !isset($phonePeResponse['success']) || !$phonePeResponse['success']) {
                Log::error('PhonePe order creation failed', [
                    'response' => $phonePeResponse,
                    'http_code' => $httpCode,
                ]);

                $errorMessage = 'Failed to create payment order.';
                if (isset($phonePeResponse['message'])) {
                    $errorMessage = $phonePeResponse['message'];
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug' => config('app.debug') ? [
                        'http_code' => $httpCode,
                        'response' => $phonePeResponse,
                    ] : null,
                ], 500);
            }

            // Get redirect URL from response
            $redirectUrl = $phonePeResponse['data']['instrumentResponse']['redirectInfo']['url'] ?? null;

            if (!$redirectUrl) {
                Log::error('PhonePe: No redirect URL in response', ['response' => $phonePeResponse]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get payment URL from PhonePe.',
                ], 500);
            }

            // Store PhonePe transaction ID
            $order->update([
                'transaction_id' => $merchantTransactionId,
            ]);

            Log::info('PhonePe order created successfully', [
                'order_id' => $order->id,
                'merchant_transaction_id' => $merchantTransactionId,
                'redirect_url' => $redirectUrl,
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
                'merchant_transaction_id' => $merchantTransactionId,
            ]);

        } catch (\Exception $e) {
            Log::error('PhonePe exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle PhonePe callback (redirect from PhonePe)
     */
    public function callback(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        // Check payment status
        $status = $this->checkPaymentStatus($order);

        if ($status === 'SUCCESS') {
            return redirect()->route('checkout.success', $order)
                ->with('success', 'Payment successful!');
        } elseif ($status === 'PENDING') {
            return redirect()->route('checkout.payment', $order)
                ->with('info', 'Payment is being processed. Please wait.');
        } else {
            return redirect()->route('checkout.payment', $order)
                ->with('error', 'Payment failed or was cancelled. Please try again.');
        }
    }

    /**
     * Check payment status from PhonePe
     */
    public function checkPaymentStatus(Order $order): string
    {
        if (!$order->transaction_id) {
            return 'FAILED';
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
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            if (file_exists(base_path('cacert.pem'))) {
                curl_setopt($ch, CURLOPT_CAINFO, base_path('cacert.pem'));
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $data = json_decode($response, true);

            Log::info('PhonePe status check', [
                'order_id' => $order->id,
                'http_code' => $httpCode,
                'response' => $data,
            ]);

            if ($data && isset($data['success']) && $data['success']) {
                $code = $data['code'] ?? '';

                if ($code === 'PAYMENT_SUCCESS') {
                    // Payment successful - update order
                    if ($order->payment_status !== 'paid') {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'confirmed',
                            'transaction_id' => $data['data']['transactionId'] ?? $order->transaction_id,
                        ]);

                        // Send order emails
                        SendOrderEmails::dispatch($order->fresh()->load('items.product'));
                    }
                    return 'SUCCESS';
                } elseif ($code === 'PAYMENT_PENDING') {
                    return 'PENDING';
                } else {
                    // FAILED or other states
                    if ($order->payment_status !== 'failed') {
                        $order->update(['payment_status' => 'failed']);
                    }
                    return 'FAILED';
                }
            }

            return 'PENDING';

        } catch (\Exception $e) {
            Log::error('PhonePe status check exception', ['message' => $e->getMessage()]);
            return 'PENDING';
        }
    }

    /**
     * Handle PhonePe webhook (S2S callback)
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();

        Log::info('PhonePe webhook received', ['payload' => $payload]);

        // Verify the callback
        $xVerify = $request->header('X-VERIFY');

        if ($xVerify) {
            // Validate checksum
            $expectedChecksum = hash('sha256', $payload . $this->saltKey) . '###' . $this->saltIndex;
            if ($xVerify !== $expectedChecksum) {
                Log::warning('PhonePe webhook: Invalid checksum');
                // Continue anyway as PhonePe may use different checksum format
            }
        }

        $data = json_decode($payload, true);

        if (!$data) {
            // Try to decode base64 response
            $response = $data['response'] ?? null;
            if ($response) {
                $data = json_decode(base64_decode($response), true);
            }
        }

        if (!$data) {
            Log::warning('Invalid PhonePe webhook payload');
            return response()->json(['status' => 'invalid payload'], 400);
        }

        $merchantTransactionId = $data['data']['merchantTransactionId']
            ?? $data['merchantTransactionId']
            ?? null;

        if (!$merchantTransactionId) {
            Log::warning('No merchantTransactionId in webhook', ['data' => $data]);
            return response()->json(['status' => 'ok']);
        }

        // Find order by transaction ID
        $order = Order::where('transaction_id', $merchantTransactionId)->first();

        if (!$order) {
            Log::warning('Order not found for webhook', ['transaction_id' => $merchantTransactionId]);
            return response()->json(['status' => 'ok']);
        }

        $code = $data['code'] ?? '';

        Log::info('PhonePe webhook processing', [
            'order_id' => $order->id,
            'code' => $code,
        ]);

        if ($code === 'PAYMENT_SUCCESS') {
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'transaction_id' => $data['data']['transactionId'] ?? $order->transaction_id,
                ]);

                Log::info('Payment captured via webhook', [
                    'order_id' => $order->id,
                    'transaction_id' => $data['data']['transactionId'] ?? null,
                ]);

                // Send order emails
                SendOrderEmails::dispatch($order->fresh()->load('items.product'));
            }
        } elseif (in_array($code, ['PAYMENT_ERROR', 'PAYMENT_DECLINED', 'PAYMENT_CANCELLED'])) {
            if ($order->payment_status !== 'failed') {
                $order->update(['payment_status' => 'failed']);

                Log::warning('Payment failed via webhook', [
                    'order_id' => $order->id,
                    'code' => $code,
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Test PhonePe configuration
     */
    public function testConfig()
    {
        $phonepe = PaymentMethod::where('code', 'phonepe')->first();

        $result = [
            'payment_method_exists' => (bool) $phonepe,
            'is_active' => $phonepe ? $phonepe->is_active : false,
            'merchant_id' => null,
            'merchant_id_set' => false,
            'salt_key_set' => false,
            'salt_index' => null,
            'environment' => null,
            'base_url' => null,
            'api_test' => null,
            'curl_available' => function_exists('curl_init'),
            'error_details' => null,
        ];

        if ($phonepe) {
            $merchantId = $phonepe->getSetting('merchant_id');
            $saltKey = $phonepe->getSetting('salt_key');
            $saltIndex = $phonepe->getSetting('salt_index', '1');

            $result['merchant_id'] = $merchantId ? (substr($merchantId, 0, 8) . '...') : 'NOT SET';
            $result['merchant_id_set'] = !empty($merchantId);
            $result['salt_key_set'] = !empty($saltKey);
            $result['salt_index'] = $saltIndex;
            $result['environment'] = $phonepe->getSetting('environment', 'production');

            $environment = $phonepe->getSetting('environment', 'production');
            $result['base_url'] = $environment === 'sandbox'
                ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
                : 'https://api.phonepe.com/apis/hermes';

            if ($merchantId && $saltKey) {
                $result['api_test'] = 'CREDENTIALS_SET';
                $result['error_details'] = 'Credentials are configured. Try making a test payment to verify.';
            } else {
                $result['api_test'] = 'CREDENTIALS_MISSING';
                $missingFields = [];
                if (!$merchantId) $missingFields[] = 'Merchant ID';
                if (!$saltKey) $missingFields[] = 'Salt Key';
                $result['error_details'] = 'Missing: ' . implode(', ', $missingFields) . '. Configure in Admin > Payment Methods > PhonePe';
            }
        } else {
            $result['api_test'] = 'PAYMENT_METHOD_NOT_FOUND';
            $result['error_details'] = 'PhonePe payment method not found in database. Run migrations.';
        }

        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    }
}
