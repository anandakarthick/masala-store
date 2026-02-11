<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderEmails;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

            // Use production or sandbox based on setting
            $environment = $phonepe->getSetting('environment', 'production');
            $this->baseUrl = $environment === 'sandbox'
                ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
                : 'https://api.phonepe.com/apis/hermes';
        }
    }

    /**
     * Get payment instrument configuration based on payment type
     */
    private function getPaymentInstrument(string $paymentType): array
    {
        return match ($paymentType) {
            'UPI' => [
                'type' => 'UPI_INTENT',
            ],
            'CARD' => [
                'type' => 'PAY_PAGE',
                'targetApp' => 'CARD',
            ],
            'NET_BANKING' => [
                'type' => 'PAY_PAGE',
                'targetApp' => 'NET_BANKING',
            ],
            'WALLET' => [
                'type' => 'PAY_PAGE',
                'targetApp' => 'WALLET',
            ],
            default => [
                'type' => 'PAY_PAGE',
            ],
        };
    }

    /**
     * Get access token from PhonePe
     */
    private function getAccessToken(): ?string
    {
        try {
            // Check if credentials are set
            if (empty($this->clientId) || empty($this->clientSecret)) {
                Log::error('PhonePe OAuth: Missing credentials', [
                    'client_id_set' => !empty($this->clientId),
                    'client_secret_set' => !empty($this->clientSecret),
                ]);
                return null;
            }

            $url = $this->baseUrl . '/v1/oauth/token';

            Log::info('PhonePe OAuth: Attempting to get token', [
                'url' => $url,
                'client_id' => substr($this->clientId, 0, 10) . '...',
            ]);

            $ch = curl_init($url);
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

            // SSL settings for Windows compatibility
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            // If SSL verification fails, try with bundled CA certificates
            $caPath = ini_get('curl.cainfo');
            if (empty($caPath) && file_exists(base_path('cacert.pem'))) {
                curl_setopt($ch, CURLOPT_CAINFO, base_path('cacert.pem'));
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $curlErrno = curl_errno($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('PhonePe OAuth CURL error', [
                    'error' => $curlError,
                    'errno' => $curlErrno,
                    'url' => $url,
                ]);
                return null;
            }

            $data = json_decode($response, true);

            if ($httpCode === 200 && isset($data['access_token'])) {
                Log::info('PhonePe OAuth: Token obtained successfully');
                return $data['access_token'];
            }

            Log::error('PhonePe OAuth failed', [
                'http_code' => $httpCode,
                'response' => $data,
                'url' => $url,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PhonePe OAuth exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
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
        $paymentType = $request->input('payment_type', 'UPI');

        // Get PhonePe settings
        $phonepe = PaymentMethod::where('code', 'phonepe')->first();

        if (!$phonepe || !$phonepe->is_active) {
            Log::error('PhonePe not configured or disabled');
            return response()->json([
                'success' => false,
                'message' => 'PhonePe is not configured or disabled.',
            ], 400);
        }

        if (!$this->clientId || !$this->clientSecret || !$this->merchantId) {
            Log::error('PhonePe credentials missing');
            return response()->json([
                'success' => false,
                'message' => 'PhonePe credentials are not configured.',
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
            // Get access token
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                Log::error('PhonePe: Failed to get access token for order', [
                    'order_id' => $order->id,
                    'environment' => PaymentMethod::where('code', 'phonepe')->first()?->getSetting('environment'),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authenticate with PhonePe. Please check your PhonePe credentials in Admin Panel or try again later.',
                    'debug' => config('app.debug') ? [
                        'hint' => 'Visit /phonepe/test-config to diagnose the issue',
                    ] : null,
                ], 500);
            }

            $amountInPaise = (int) round($amountToPay * 100);
            $merchantTransactionId = 'MT' . $order->id . '_' . time();

            // Determine payment instrument based on selected type
            $paymentInstrument = $this->getPaymentInstrument($paymentType);

            $payloadData = [
                'merchantId' => $this->merchantId,
                'merchantTransactionId' => $merchantTransactionId,
                'amount' => $amountInPaise,
                'merchantOrderId' => $order->order_number,
                'message' => 'Payment for Order #' . $order->order_number,
                'mobileNumber' => preg_replace('/[^0-9]/', '', $order->customer_phone),
                'paymentInstrument' => $paymentInstrument,
                'redirectUrl' => route('phonepe.callback') . '?order_id=' . $order->id,
                'redirectMode' => 'REDIRECT',
                'callbackUrl' => route('phonepe.webhook'),
            ];

            Log::info('Creating PhonePe order', [
                'order_id' => $order->id,
                'merchant_transaction_id' => $merchantTransactionId,
                'amount_inr' => $amountToPay,
                'amount_paise' => $amountInPaise,
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
                Log::error('PhonePe CURL error', ['error' => $curlError]);
                return response()->json([
                    'success' => false,
                    'message' => 'Network error. Please check your internet connection.',
                ], 500);
            }

            $phonePeResponse = json_decode($response, true);

            if ($httpCode !== 200 || !isset($phonePeResponse['redirectUrl'])) {
                Log::error('PhonePe order creation failed', [
                    'response' => $phonePeResponse,
                    'http_code' => $httpCode,
                ]);

                $errorMessage = 'Failed to create payment order.';
                if (isset($phonePeResponse['message'])) {
                    $errorMessage = 'Payment error: ' . $phonePeResponse['message'];
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'debug' => config('app.debug') ? [
                        'http_code' => $httpCode,
                        'error' => $phonePeResponse,
                    ] : null,
                ], 500);
            }

            // Store PhonePe transaction ID
            $order->update([
                'transaction_id' => $merchantTransactionId,
            ]);

            Log::info('PhonePe order created successfully', [
                'order_id' => $order->id,
                'merchant_transaction_id' => $merchantTransactionId,
            ]);

            return response()->json([
                'success' => true,
                'redirect_url' => $phonePeResponse['redirectUrl'],
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
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                Log::error('Failed to get access token for status check');
                return 'PENDING';
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

            Log::info('PhonePe status check', [
                'order_id' => $order->id,
                'response' => $data,
            ]);

            if ($httpCode === 200 && isset($data['state'])) {
                $state = $data['state'];

                if ($state === 'COMPLETED') {
                    // Payment successful - update order
                    if ($order->payment_status !== 'paid') {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'confirmed',
                            'transaction_id' => $data['transactionId'] ?? $order->transaction_id,
                        ]);

                        // Send order emails
                        SendOrderEmails::dispatch($order->fresh()->load('items.product'));
                    }
                    return 'SUCCESS';
                } elseif ($state === 'PENDING') {
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
     * Handle PhonePe webhook
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();

        Log::info('PhonePe webhook received', ['payload' => $payload]);

        $data = json_decode($payload, true);

        if (!$data || !isset($data['response'])) {
            Log::warning('Invalid PhonePe webhook payload');
            return response()->json(['status' => 'invalid payload'], 400);
        }

        $responseData = $data['response'];
        $merchantTransactionId = $responseData['merchantTransactionId'] ?? null;

        if (!$merchantTransactionId) {
            Log::warning('No merchantTransactionId in webhook');
            return response()->json(['status' => 'ok']);
        }

        // Find order by transaction ID
        $order = Order::where('transaction_id', $merchantTransactionId)->first();

        if (!$order) {
            Log::warning('Order not found for webhook', ['transaction_id' => $merchantTransactionId]);
            return response()->json(['status' => 'ok']);
        }

        $state = $responseData['state'] ?? null;

        Log::info('PhonePe webhook processing', [
            'order_id' => $order->id,
            'state' => $state,
        ]);

        if ($state === 'COMPLETED') {
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'transaction_id' => $responseData['transactionId'] ?? $order->transaction_id,
                ]);

                Log::info('Payment captured via webhook', [
                    'order_id' => $order->id,
                    'transaction_id' => $responseData['transactionId'] ?? null,
                ]);

                // Send order emails
                SendOrderEmails::dispatch($order->fresh()->load('items.product'));
            }
        } elseif ($state === 'FAILED') {
            if ($order->payment_status !== 'failed') {
                $order->update(['payment_status' => 'failed']);

                Log::warning('Payment failed via webhook', [
                    'order_id' => $order->id,
                    'error' => $responseData['responseCode'] ?? 'Unknown',
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
            'client_id' => null,
            'client_id_set' => false,
            'client_secret_set' => false,
            'merchant_id_set' => false,
            'environment' => null,
            'base_url' => null,
            'api_test' => null,
            'curl_available' => function_exists('curl_init'),
            'ssl_version' => null,
            'error_details' => null,
        ];

        // Check cURL SSL info
        if (function_exists('curl_version')) {
            $curlVersion = curl_version();
            $result['ssl_version'] = $curlVersion['ssl_version'] ?? 'unknown';
        }

        if ($phonepe) {
            $clientId = $phonepe->getSetting('client_id');
            $clientSecret = $phonepe->getSetting('client_secret');
            $merchantId = $phonepe->getSetting('merchant_id');

            $result['client_id'] = $clientId ? (substr($clientId, 0, 10) . '...') : 'NOT SET';
            $result['client_id_set'] = !empty($clientId);
            $result['client_secret_set'] = !empty($clientSecret);
            $result['merchant_id_set'] = !empty($merchantId);
            $result['environment'] = $phonepe->getSetting('environment', 'production');

            // Test API connection
            if ($clientId && $clientSecret) {
                $this->clientId = $clientId;
                $this->clientSecret = $clientSecret;
                $this->merchantId = $merchantId;

                $environment = $phonepe->getSetting('environment', 'production');
                $this->baseUrl = $environment === 'sandbox'
                    ? 'https://api-preprod.phonepe.com/apis/pg-sandbox'
                    : 'https://api.phonepe.com/apis/hermes';

                $result['base_url'] = $this->baseUrl;

                // Try to get access token with detailed error capture
                $accessToken = $this->getAccessToken();

                if ($accessToken) {
                    $result['api_test'] = 'SUCCESS';
                    $result['token_preview'] = substr($accessToken, 0, 20) . '...';
                } else {
                    $result['api_test'] = 'AUTH_FAILED';
                    $result['error_details'] = 'Check Laravel logs for details. Common issues: wrong credentials, wrong environment, SSL certificate issues.';
                }
            } else {
                $result['api_test'] = 'CREDENTIALS_MISSING';
                $result['error_details'] = 'Please configure Client ID, Client Secret, and Merchant ID in Admin > Payment Methods > PhonePe';
            }
        } else {
            $result['api_test'] = 'PAYMENT_METHOD_NOT_FOUND';
            $result['error_details'] = 'PhonePe payment method not found in database. Run migrations.';
        }

        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    }
}
