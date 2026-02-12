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
                ->message('Payment for Order #' . $order->order_number)
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
     * Handle PhonePe callback (redirect from PhonePe after payment)
     * This is called when payment is completed/failed on PhonePe
     */
    public function callback(Request $request)
    {
        $orderId = $request->query('order_id');
        $merchantOrderId = $request->query('merchant_order_id');

        Log::info('PhonePe API callback received', [
            'order_id' => $orderId,
            'merchant_order_id' => $merchantOrderId,
        ]);

        $order = Order::find($orderId);

        if (!$order) {
            return $this->returnToApp('error', null, 'Order not found');
        }

        // Check payment status with PhonePe
        $status = 'pending';
        $message = 'Payment is being processed';

        if ($this->client && $merchantOrderId) {
            try {
                $statusResponse = $this->client->getOrderStatus($merchantOrderId, true);
                $state = $statusResponse->getState();

                Log::info('PhonePe callback status check', [
                    'order_id' => $orderId,
                    'state' => $state,
                ]);

                if ($state === 'COMPLETED') {
                    if ($order->payment_status !== 'paid') {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'confirmed',
                        ]);
                        SendOrderEmails::dispatch($order->fresh()->load('items.product'));
                    }
                    $status = 'success';
                    $message = 'Payment successful';
                } elseif ($state === 'FAILED') {
                    if ($order->payment_status !== 'failed') {
                        $order->update(['payment_status' => 'failed']);
                    }
                    $status = 'failed';
                    $message = 'Payment failed';
                } else {
                    $status = 'pending';
                    $message = 'Payment is being processed';
                }
            } catch (\Exception $e) {
                Log::error('PhonePe callback status check failed', ['error' => $e->getMessage()]);
            }
        }

        return $this->returnToApp($status, $order->order_number, $message);
    }

    /**
     * Return to mobile app with deep link
     */
    private function returnToApp(string $status, ?string $orderNumber, string $message)
    {
        // Deep link scheme for the mobile app
        $deepLink = 'svproducts://payment/' . $status;
        if ($orderNumber) {
            $deepLink .= '?order_number=' . $orderNumber;
        }

        // Return HTML page that redirects to the app
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment ' . ucfirst($status) . '</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            color: #333;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        h1 { margin: 0 0 10px; font-size: 24px; }
        p { margin: 0 0 20px; color: #666; }
        .btn {
            display: inline-block;
            background: #5f259f;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 10px;
        }
        .btn:hover { background: #4a1d7a; }
        .order-number {
            background: #f5f5f5;
            padding: 10px 20px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 16px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">' . ($status === 'success' ? '✅' : ($status === 'failed' ? '❌' : '⏳')) . '</div>
        <h1>' . ($status === 'success' ? 'Payment Successful!' : ($status === 'failed' ? 'Payment Failed' : 'Processing Payment')) . '</h1>
        <p>' . htmlspecialchars($message) . '</p>
        ' . ($orderNumber ? '<div class="order-number">Order #' . htmlspecialchars($orderNumber) . '</div>' : '') . '
        <a href="' . $deepLink . '" class="btn">Open App</a>
        <p style="margin-top: 20px; font-size: 12px; color: #999;">
            If the app doesn\'t open automatically, tap the button above.
        </p>
    </div>
    <script>
        // Try to open the app automatically
        setTimeout(function() {
            window.location.href = "' . $deepLink . '";
        }, 1000);
    </script>
</body>
</html>';

        return response($html)->header('Content-Type', 'text/html');
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
