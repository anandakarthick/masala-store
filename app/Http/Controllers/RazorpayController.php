<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RazorpayController extends Controller
{
    /**
     * Create Razorpay order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Get Razorpay settings
        $razorpay = PaymentMethod::where('code', 'razorpay')->first();
        
        if (!$razorpay || !$razorpay->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay is not configured or disabled.',
            ], 400);
        }

        $keyId = $razorpay->getSetting('key_id');
        $keySecret = $razorpay->getSetting('key_secret');

        if (!$keyId || !$keySecret) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay credentials are not configured.',
            ], 400);
        }

        try {
            // Create Razorpay order using API
            $orderData = [
                'receipt' => $order->order_number,
                'amount' => (int) ($order->total_amount * 100), // Amount in paise
                'currency' => 'INR',
                'notes' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ];

            $ch = curl_init('https://api.razorpay.com/v1/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $keyId . ':' . $keySecret);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $razorpayOrder = json_decode($response, true);

            if ($httpCode !== 200 || !isset($razorpayOrder['id'])) {
                Log::error('Razorpay order creation failed', [
                    'response' => $razorpayOrder,
                    'http_code' => $httpCode,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment order. Please try again.',
                ], 500);
            }

            // Store Razorpay order ID
            $order->update([
                'transaction_id' => $razorpayOrder['id'],
            ]);

            return response()->json([
                'success' => true,
                'razorpay_order_id' => $razorpayOrder['id'],
                'razorpay_key_id' => $keyId,
                'amount' => $order->total_amount,
                'currency' => 'INR',
                'name' => \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products'),
                'description' => 'Order #' . $order->order_number,
                'prefill' => [
                    'name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'contact' => $order->customer_phone,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Payment initialization failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify Razorpay payment
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Get Razorpay settings
        $razorpay = PaymentMethod::where('code', 'razorpay')->first();
        $keySecret = $razorpay->getSetting('key_secret');

        // Verify signature
        $generatedSignature = hash_hmac(
            'sha256',
            $request->razorpay_order_id . '|' . $request->razorpay_payment_id,
            $keySecret
        );

        if ($generatedSignature !== $request->razorpay_signature) {
            Log::warning('Razorpay signature verification failed', [
                'order_id' => $order->id,
                'razorpay_order_id' => $request->razorpay_order_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Please contact support.',
            ], 400);
        }

        // Payment verified - update order
        $order->update([
            'payment_status' => 'paid',
            'transaction_id' => $request->razorpay_payment_id,
            'status' => 'confirmed', // Auto-confirm paid orders
        ]);

        Log::info('Razorpay payment verified', [
            'order_id' => $order->id,
            'payment_id' => $request->razorpay_payment_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful!',
            'redirect_url' => route('checkout.success', $order),
        ]);
    }

    /**
     * Handle Razorpay webhook
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        // Get webhook secret
        $razorpay = PaymentMethod::where('code', 'razorpay')->first();
        $webhookSecret = $razorpay->getSetting('webhook_secret');

        if ($webhookSecret) {
            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            
            if ($signature !== $expectedSignature) {
                Log::warning('Razorpay webhook signature mismatch');
                return response()->json(['status' => 'invalid signature'], 400);
            }
        }

        $event = json_decode($payload, true);
        
        Log::info('Razorpay webhook received', ['event' => $event['event'] ?? 'unknown']);

        if (isset($event['event'])) {
            switch ($event['event']) {
                case 'payment.captured':
                    $this->handlePaymentCaptured($event['payload']['payment']['entity']);
                    break;
                    
                case 'payment.failed':
                    $this->handlePaymentFailed($event['payload']['payment']['entity']);
                    break;
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle captured payment from webhook
     */
    protected function handlePaymentCaptured($payment)
    {
        $razorpayOrderId = $payment['order_id'];
        
        $order = Order::where('transaction_id', $razorpayOrderId)->first();
        
        if ($order && $order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $payment['id'],
                'status' => 'confirmed',
            ]);
            
            Log::info('Payment captured via webhook', [
                'order_id' => $order->id,
                'payment_id' => $payment['id'],
            ]);
        }
    }

    /**
     * Handle failed payment from webhook
     */
    protected function handlePaymentFailed($payment)
    {
        $razorpayOrderId = $payment['order_id'];
        
        $order = Order::where('transaction_id', $razorpayOrderId)->first();
        
        if ($order) {
            $order->update([
                'payment_status' => 'failed',
            ]);
            
            Log::warning('Payment failed via webhook', [
                'order_id' => $order->id,
                'error' => $payment['error_description'] ?? 'Unknown error',
            ]);
        }
    }
}
