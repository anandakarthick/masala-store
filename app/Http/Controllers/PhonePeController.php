<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderEmails;
use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder;
use PhonePe\Env;
use PhonePe\common\exceptions\PhonePeException;

class PhonePeController extends Controller
{
    private ?StandardCheckoutClient $client = null;
    private ?PaymentMethod $phonepe = null;

    public function __construct()
    {
        $this->phonepe = PaymentMethod::where('code', 'phonepe')->first();

        if ($this->phonepe) {
            $clientId = $this->phonepe->getSetting('client_id', '');
            $clientSecret = $this->phonepe->getSetting('client_secret', '');
            $clientVersion = (int) $this->phonepe->getSetting('client_version', 1);
            $environment = $this->phonepe->getSetting('environment', 'sandbox');

            if ($clientId && $clientSecret) {
                try {
                    $env = $environment === 'production' ? Env::PRODUCTION : Env::UAT;
                    $this->client = StandardCheckoutClient::getInstance(
                        $clientId,
                        $clientVersion,
                        $clientSecret,
                        $env
                    );
                } catch (\Exception $e) {
                    Log::error('PhonePe SDK initialization failed', ['error' => $e->getMessage()]);
                }
            }
        }
    }

    /**
     * Create PhonePe order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if (!$this->phonepe || !$this->phonepe->is_active) {
            Log::error('PhonePe not configured or disabled');
            return response()->json([
                'success' => false,
                'message' => 'PhonePe is not configured or disabled.',
            ], 400);
        }

        if (!$this->client) {
            Log::error('PhonePe client not initialized - check credentials');
            return response()->json([
                'success' => false,
                'message' => 'PhonePe credentials are not configured correctly. Please check Client ID and Client Secret.',
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
            $merchantOrderId = 'ORD' . $order->id . 'T' . time();

            // Build payment request using SDK
            $payRequest = StandardCheckoutPayRequestBuilder::builder()
                ->merchantOrderId($merchantOrderId)
                ->amount($amountInPaise)
                ->redirectUrl(route('phonepe.callback') . '?order_id=' . $order->id . '&merchant_order_id=' . $merchantOrderId)
                ->build();

            Log::info('Creating PhonePe order', [
                'order_id' => $order->id,
                'merchant_order_id' => $merchantOrderId,
                'amount_inr' => $amountToPay,
                'amount_paise' => $amountInPaise,
            ]);

            // Call PhonePe pay API
            $payResponse = $this->client->pay($payRequest);

            Log::info('PhonePe pay response', [
                'state' => $payResponse->getState(),
                'order_id' => $payResponse->getOrderId(),
            ]);

            if ($payResponse->getState() === 'PENDING') {
                // Store PhonePe order ID
                $order->update([
                    'transaction_id' => $merchantOrderId,
                ]);

                Log::info('PhonePe order created successfully', [
                    'order_id' => $order->id,
                    'merchant_order_id' => $merchantOrderId,
                    'phonepe_order_id' => $payResponse->getOrderId(),
                    'redirect_url' => $payResponse->getRedirectUrl(),
                ]);

                return response()->json([
                    'success' => true,
                    'redirect_url' => $payResponse->getRedirectUrl(),
                    'merchant_order_id' => $merchantOrderId,
                ]);
            } else {
                Log::error('PhonePe order creation failed', [
                    'state' => $payResponse->getState(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment initiation failed. State: ' . $payResponse->getState(),
                ], 500);
            }

        } catch (PhonePeException $e) {
            Log::error('PhonePe SDK exception', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
            ], 500);

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
        $merchantOrderId = $request->query('merchant_order_id');
        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('home')->with('error', 'Order not found.');
        }

        // Check payment status
        $status = $this->checkPaymentStatus($order, $merchantOrderId);

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
    public function checkPaymentStatus(Order $order, ?string $merchantOrderId = null): string
    {
        $merchantOrderId = $merchantOrderId ?? $order->transaction_id;

        if (!$merchantOrderId) {
            return 'FAILED';
        }

        if (!$this->client) {
            Log::error('PhonePe client not available for status check');
            return 'PENDING';
        }

        try {
            $statusResponse = $this->client->getOrderStatus($merchantOrderId, true);

            Log::info('PhonePe status check', [
                'order_id' => $order->id,
                'merchant_order_id' => $merchantOrderId,
                'state' => $statusResponse->getState(),
            ]);

            $state = $statusResponse->getState();

            if ($state === 'COMPLETED') {
                // Payment successful - update order
                if ($order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
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

        } catch (PhonePeException $e) {
            Log::error('PhonePe status check SDK exception', [
                'message' => $e->getMessage(),
            ]);
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

        $data = json_decode($payload, true);

        if (!$data) {
            Log::warning('Invalid PhonePe webhook payload');
            return response()->json(['status' => 'invalid payload'], 400);
        }

        // Extract merchant order ID from different possible locations
        $merchantOrderId = $data['merchantOrderId']
            ?? $data['data']['merchantOrderId']
            ?? null;

        if (!$merchantOrderId) {
            Log::warning('No merchantOrderId in webhook', ['data' => $data]);
            return response()->json(['status' => 'ok']);
        }

        // Find order by transaction ID (merchant order ID)
        $order = Order::where('transaction_id', $merchantOrderId)->first();

        if (!$order) {
            Log::warning('Order not found for webhook', ['merchant_order_id' => $merchantOrderId]);
            return response()->json(['status' => 'ok']);
        }

        $state = $data['state'] ?? $data['data']['state'] ?? '';

        Log::info('PhonePe webhook processing', [
            'order_id' => $order->id,
            'state' => $state,
        ]);

        if ($state === 'COMPLETED') {
            if ($order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                ]);

                Log::info('Payment captured via webhook', [
                    'order_id' => $order->id,
                ]);

                // Send order emails
                SendOrderEmails::dispatch($order->fresh()->load('items.product'));
            }
        } elseif ($state === 'FAILED') {
            if ($order->payment_status !== 'failed') {
                $order->update(['payment_status' => 'failed']);

                Log::warning('Payment failed via webhook', [
                    'order_id' => $order->id,
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
        $result = [
            'payment_method_exists' => (bool) $this->phonepe,
            'is_active' => $this->phonepe ? $this->phonepe->is_active : false,
            'client_id' => null,
            'client_id_set' => false,
            'client_secret_set' => false,
            'client_version' => null,
            'environment' => null,
            'sdk_initialized' => (bool) $this->client,
            'error_details' => null,
        ];

        if ($this->phonepe) {
            $clientId = $this->phonepe->getSetting('client_id');
            $clientSecret = $this->phonepe->getSetting('client_secret');
            $clientVersion = $this->phonepe->getSetting('client_version', 1);

            $result['client_id'] = $clientId ? (substr($clientId, 0, 10) . '...') : 'NOT SET';
            $result['client_id_set'] = !empty($clientId);
            $result['client_secret_set'] = !empty($clientSecret);
            $result['client_version'] = $clientVersion;
            $result['environment'] = $this->phonepe->getSetting('environment', 'sandbox');

            if ($this->client) {
                $result['api_test'] = 'SDK_INITIALIZED';
                $result['error_details'] = 'SDK is ready. Try making a test payment to verify.';
            } elseif ($clientId && $clientSecret) {
                $result['api_test'] = 'SDK_INIT_FAILED';
                $result['error_details'] = 'Credentials are set but SDK failed to initialize. Check Client ID and Client Secret.';
            } else {
                $result['api_test'] = 'CREDENTIALS_MISSING';
                $missingFields = [];
                if (!$clientId) $missingFields[] = 'Client ID';
                if (!$clientSecret) $missingFields[] = 'Client Secret';
                $result['error_details'] = 'Missing: ' . implode(', ', $missingFields) . '. Configure in Admin > Payment Methods > PhonePe';
            }
        } else {
            $result['api_test'] = 'PAYMENT_METHOD_NOT_FOUND';
            $result['error_details'] = 'PhonePe payment method not found in database. Run migrations.';
        }

        return response()->json($result, 200, [], JSON_PRETTY_PRINT);
    }
}
