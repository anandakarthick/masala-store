<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        if (!$this->phonepe || !$this->phonepe->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'PhonePe is not configured or disabled.',
            ], 400);
        }

        if (!$this->client) {
            return response()->json([
                'success' => false,
                'message' => 'PhonePe credentials are not configured correctly.',
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
            $merchantOrderId = 'ORD' . $order->id . 'T' . time();

            // Build payment request using SDK
            $payRequest = StandardCheckoutPayRequestBuilder::builder()
                ->merchantOrderId($merchantOrderId)
                ->amount($amountInPaise)
                ->redirectUrl(config('app.url') . '/api/v1/phonepe/callback?order_id=' . $order->id . '&merchant_order_id=' . $merchantOrderId)
                ->build();

            Log::info('Creating PhonePe order via API', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'merchant_order_id' => $merchantOrderId,
                'amount' => $amountToPay,
            ]);

            // Call PhonePe pay API
            $payResponse = $this->client->pay($payRequest);

            if ($payResponse->getState() === 'PENDING') {
                // Store transaction ID
                $order->update([
                    'transaction_id' => $merchantOrderId,
                ]);

                Log::info('PhonePe order created via API', [
                    'order_id' => $order->id,
                    'merchant_order_id' => $merchantOrderId,
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'redirect_url' => $payResponse->getRedirectUrl(),
                        'merchant_order_id' => $merchantOrderId,
                        'amount' => $amountToPay,
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment initiation failed. State: ' . $payResponse->getState(),
                ], 500);
            }

        } catch (PhonePeException $e) {
            Log::error('PhonePe SDK exception', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
            ], 500);
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

        if (!$this->client) {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'UNKNOWN',
                    'payment_status' => $order->payment_status,
                ],
            ]);
        }

        try {
            $statusResponse = $this->client->getOrderStatus($order->transaction_id, true);
            $state = $statusResponse->getState();

            // Update order if payment completed
            if ($state === 'COMPLETED' && $order->payment_status !== 'paid') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
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
                ],
            ]);

        } catch (PhonePeException $e) {
            Log::error('PhonePe status check exception', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'UNKNOWN',
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
