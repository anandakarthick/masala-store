<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderCancellationNotification;
use App\Jobs\SendOrderEmails;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\UserAddress;
use App\Services\FirstTimeCustomerService;
use App\Services\InvoiceService;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get cart helper (same logic as CartController)
     */
    private function getCart(Request $request): Cart
    {
        if ($request->user()) {
            // First check if there's a guest cart to merge
            $sessionId = $request->header('X-Session-Id');
            if ($sessionId) {
                $guestCart = Cart::where('session_id', $sessionId)
                    ->whereNull('user_id')
                    ->with('items')
                    ->first();
                
                if ($guestCart && $guestCart->items->count() > 0) {
                    // Get or create user cart
                    $userCart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
                    
                    // Merge items
                    foreach ($guestCart->items as $item) {
                        $existingItem = $userCart->items()
                            ->where('product_id', $item->product_id)
                            ->where('variant_id', $item->variant_id)
                            ->first();
                        
                        if ($existingItem) {
                            $existingItem->increment('quantity', $item->quantity);
                        } else {
                            $userCart->items()->create([
                                'product_id' => $item->product_id,
                                'variant_id' => $item->variant_id,
                                'quantity' => $item->quantity,
                            ]);
                        }
                    }
                    
                    // Delete guest cart
                    $guestCart->items()->delete();
                    $guestCart->delete();
                    
                    // Refresh user cart to get updated items
                    return $userCart->fresh();
                }
            }
            
            return Cart::firstOrCreate(['user_id' => $request->user()->id]);
        }

        $sessionId = $request->header('X-Session-Id', session()->getId());
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    /**
     * Get checkout data
     */
    public function checkoutData(Request $request)
    {
        $cart = $this->getCart($request);
        $cart->load('items.product', 'items.variant');

        if ($cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ], 400);
        }

        $user = $request->user();
        $subtotal = $cart->subtotal;
        $shippingCharge = $subtotal >= Setting::freeShippingAmount() ? 0 : Setting::defaultShippingCharge();

        // Check first-time customer discount
        $firstTimeDiscount = FirstTimeCustomerService::isEligible($user->id, $subtotal);

        // Get payment methods with full details
        $paymentMethods = PaymentMethod::active()
            ->get()
            ->filter(fn($pm) => $pm->isAvailableForAmount($subtotal + $shippingCharge))
            ->map(function($pm) {
                $data = [
                    'id' => $pm->id,
                    'name' => $pm->name,
                    'code' => $pm->code,
                    'display_name' => $pm->display_name ?? $pm->name,
                    'description' => $pm->description,
                    'icon' => $pm->icon,
                    'instructions' => $pm->instructions,
                    'is_online' => $pm->is_online,
                    'extra_charge' => (float) $pm->extra_charge,
                    'extra_charge_type' => $pm->extra_charge_type,
                ];

                // Add UPI details if payment method is UPI
                if ($pm->code === 'upi') {
                    $data['upi_id'] = $pm->getSetting('upi_id');
                    $data['upi_name'] = $pm->getSetting('upi_name');
                    $data['merchant_code'] = $pm->getSetting('merchant_code', '0000');
                    $data['transaction_url'] = $pm->getSetting('transaction_url');
                    $qrCode = $pm->getSetting('qr_code');
                    $data['qr_code_url'] = $qrCode ? asset('storage/' . $qrCode) : null;
                }

                // Add bank details if payment method is bank_transfer
                if ($pm->code === 'bank_transfer') {
                    $data['bank_details'] = [
                        'account_name' => $pm->getSetting('account_name'),
                        'account_number' => $pm->getSetting('account_number'),
                        'bank_name' => $pm->getSetting('bank_name'),
                        'ifsc_code' => $pm->getSetting('ifsc_code'),
                        'branch' => $pm->getSetting('branch'),
                    ];
                }

                return $data;
            })
            ->values();

        // Get saved addresses
        $addresses = UserAddress::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $defaultAddress = $addresses->where('is_default', true)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'cart' => [
                    'subtotal' => (float) $subtotal,
                    'gst_amount' => (float) $cart->gst_amount,
                    'item_count' => $cart->total_items,
                    'total_quantity' => $cart->total_quantity,
                ],
                'shipping_charge' => (float) $shippingCharge,
                'free_shipping_amount' => (float) Setting::freeShippingAmount(),
                'first_time_discount' => $firstTimeDiscount,
                'wallet_balance' => (float) $user->wallet_balance,
                'payment_methods' => $paymentMethods,
                'addresses' => $addresses,
                'default_address' => $defaultAddress,
                'saved_address' => $defaultAddress ? [
                    'name' => $defaultAddress->full_name,
                    'phone' => $defaultAddress->phone,
                    'email' => $user->email,
                    'address' => $defaultAddress->address_line_1 . ($defaultAddress->address_line_2 ? ', ' . $defaultAddress->address_line_2 : ''),
                    'city' => $defaultAddress->city,
                    'state' => $defaultAddress->state,
                    'pincode' => $defaultAddress->pincode,
                    'landmark' => $defaultAddress->landmark,
                ] : [
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'address' => $user->address,
                    'city' => $user->city,
                    'state' => $user->state,
                    'pincode' => $user->pincode,
                ],
            ]
        ]);
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code.',
            ], 400);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon has expired or is no longer valid.',
            ], 400);
        }

        $cart = $this->getCart($request);

        if ($cart->subtotal < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order amount of ₹{$coupon->min_order_amount} required.",
            ], 400);
        }

        $discountAmount = $coupon->calculateDiscount($cart->subtotal);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'data' => [
                'coupon' => [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => (float) $coupon->value,
                    'discount_amount' => (float) $discountAmount,
                ],
            ]
        ]);
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_pincode' => 'required|string|max:10',
            'payment_method' => 'required|string',
            'customer_notes' => 'nullable|string',
            'coupon_code' => 'nullable|string',
            'use_wallet' => 'nullable|boolean',
            'wallet_amount' => 'nullable|numeric|min:0',
            'address_id' => 'nullable|integer', // Optional: use saved address
        ]);

        $user = $request->user();
        $cart = $this->getCart($request);
        $cart->load('items.product', 'items.variant');

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty.',
            ], 400);
        }

        // Validate payment method
        $paymentMethod = PaymentMethod::where('code', $validated['payment_method'])->active()->first();
        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method.',
            ], 400);
        }

        // Check stock
        foreach ($cart->items as $item) {
            $stockQty = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
            if ($item->quantity > $stockQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Not enough stock for {$item->item_name}.",
                ], 400);
            }
        }

        // Calculate totals
        $subtotal = $cart->subtotal;
        $gstAmount = $cart->gst_amount;
        $shippingCharge = $subtotal >= Setting::freeShippingAmount() ? 0 : Setting::defaultShippingCharge();
        $discountAmount = 0;
        $firstTimeDiscountAmount = 0;
        $coupon = null;

        // Apply coupon
        if (!empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))->first();
            if ($coupon && $coupon->isValid() && $subtotal >= $coupon->min_order_amount) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            }
        }

        // First-time discount
        $firstTimeEligibility = FirstTimeCustomerService::isEligible($user->id, $subtotal);
        if ($firstTimeEligibility['eligible']) {
            $firstTimeDiscountAmount = $firstTimeEligibility['discount_amount'];
        }

        $totalDiscount = $discountAmount + $firstTimeDiscountAmount;
        $amountBeforeWallet = $subtotal + $gstAmount + $shippingCharge - $totalDiscount;

        // Handle wallet
        $walletAmountUsed = 0;
        if ($request->boolean('use_wallet') && $user->wallet_balance > 0) {
            $requestedAmount = (float) ($validated['wallet_amount'] ?? 0);
            $maxUsable = min($user->wallet_balance, $amountBeforeWallet);
            $walletAmountUsed = min($requestedAmount, $maxUsable);
        }

        $amountAfterWallet = $amountBeforeWallet - $walletAmountUsed;
        $paymentCharge = $paymentMethod->calculateExtraCharge($amountAfterWallet);
        $totalAmount = $amountBeforeWallet + $paymentCharge;

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_pincode' => $validated['shipping_pincode'],
                'billing_address' => $validated['shipping_address'],
                'billing_city' => $validated['shipping_city'],
                'billing_state' => $validated['shipping_state'],
                'billing_pincode' => $validated['shipping_pincode'],
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscount,
                'first_time_discount_applied' => $firstTimeDiscountAmount,
                'wallet_amount_used' => $walletAmountUsed,
                'gst_amount' => $gstAmount,
                'shipping_charge' => $shippingCharge,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod->code,
                'payment_status' => 'pending',
                'status' => 'pending',
                'customer_notes' => $validated['customer_notes'] ?? null,
                'order_source' => $user->device_type ?? 'android',
            ]);

            // Deduct wallet
            if ($walletAmountUsed > 0) {
                $user->deductFromWallet(
                    $walletAmountUsed,
                    'order',
                    "Payment for Order #{$order->order_number}",
                    $order->id
                );
            }

            // Create order items
            foreach ($cart->items as $item) {
                $product = $item->product;
                $variant = $item->variant;
                $unitPrice = $item->unit_price;
                $itemGst = $product->calculateGst($unitPrice * $item->quantity);

                $order->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'product_name' => $product->name,
                    'product_sku' => $variant ? $variant->sku : $product->sku,
                    'variant_name' => $variant?->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'gst_amount' => $itemGst,
                    'total_price' => ($unitPrice * $item->quantity) + $itemGst,
                ]);

                // Reduce stock
                if ($variant) {
                    $variant->decrement('stock_quantity', $item->quantity);
                } else {
                    StockMovement::recordMovement(
                        $product,
                        'out',
                        $item->quantity,
                        "Order #{$order->order_number}",
                        'Stock reduced for order'
                    );
                }
            }

            // Increment coupon usage
            if (!empty($coupon)) {
                $coupon->incrementUsage();
            }

            // Clear cart
            $cart->clear();

            DB::commit();

            // Update payment status for full wallet payment
            if ($walletAmountUsed >= $totalAmount) {
                $order->update(['payment_status' => 'paid']);
            }

            // Send order confirmation email and push notification for ALL orders
            // This includes COD, UPI, Bank Transfer, and all other payment methods
            SendOrderEmails::dispatch($order->fresh()->load('items.product'));

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'data' => [
                    'order' => $this->formatOrder($order->load('items')),
                    'requires_payment' => !$paymentMethod->isCod() && $walletAmountUsed < $totalAmount,
                    'amount_to_pay' => $amountAfterWallet + $paymentCharge,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order placement error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * Get user orders
     */
    public function myOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items.product.images')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders->getCollection()->map(fn($o) => $this->formatOrder($o)),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * Get single order
     */
    public function show(Request $request, $orderNumber)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->with('items.product.images', 'items.variant')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatOrder($order, true),
        ]);
    }

    /**
     * Track order (public)
     */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'phone' => 'required|string',
        ]);

        $order = Order::where('order_number', $validated['order_number'])
            ->where('customer_phone', $validated['phone'])
            ->with('items.product.images')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found. Please check your order number and phone.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatOrder($order, true),
        ]);
    }

    /**
     * Cancel order (customer)
     */
    public function cancelOrder(Request $request, $orderNumber)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $order = Order::where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (!$order->canBeCancelledByCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled. Only pending orders can be cancelled.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Restore stock
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    ProductVariant::find($item->variant_id)->increment('stock_quantity', $item->quantity);
                } else {
                    StockMovement::recordMovement(
                        $item->product,
                        'in',
                        $item->quantity,
                        "Order #{$order->order_number} cancelled by customer",
                        'Stock restored due to order cancellation'
                    );
                }
            }

            // Refund wallet amount if used
            $walletRefunded = 0;
            if ($order->wallet_amount_used > 0) {
                $order->user->addToWallet(
                    $order->wallet_amount_used,
                    'refund',
                    "Refund for cancelled Order #{$order->order_number}",
                    $order->id
                );
                $walletRefunded = $order->wallet_amount_used;
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => 'customer',
                'cancellation_reason' => $validated['reason'] ?? null,
            ]);

            DB::commit();

            // Send cancellation notification
            SendOrderCancellationNotification::dispatch(
                $order->fresh(),
                'customer',
                $validated['reason'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.',
                'data' => [
                    'order_number' => $order->order_number,
                    'wallet_refunded' => (float) $walletRefunded,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order cancellation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order. Please try again.',
            ], 500);
        }
    }

    /**
     * Confirm payment for UPI/Bank Transfer orders
     * Called by user after completing payment in external app
     */
    public function confirmPayment(Request $request, $orderNumber)
    {
        $validated = $request->validate([
            'transaction_id' => 'nullable|string|max:100',
            'payment_app' => 'nullable|string|max:50', // gpay, phonepe, paytm, etc.
        ]);

        $order = Order::where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Check if payment is already confirmed
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => true,
                'message' => 'Payment already confirmed.',
                'data' => [
                    'order_number' => $order->order_number,
                    'payment_status' => 'paid',
                ],
            ]);
        }

        // Only allow confirmation for pending UPI/Bank Transfer/PhonePe orders
        if (!in_array($order->payment_method, ['upi', 'bank_transfer', 'phonepe'])) {
            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation not required for this payment method.',
            ], 400);
        }

        // Check if order is still in a valid state for payment confirmation
        if ($order->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot confirm payment for a cancelled order.',
            ], 400);
        }

        try {
            // Update payment status
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $validated['transaction_id'] ?? null,
                'payment_confirmed_at' => now(),
                'payment_confirmed_via' => $validated['payment_app'] ?? 'app',
            ]);

            \Log::info('Payment confirmed by user', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $request->user()->id,
                'transaction_id' => $validated['transaction_id'] ?? null,
                'payment_app' => $validated['payment_app'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully! Thank you for your order.',
                'data' => [
                    'order_number' => $order->order_number,
                    'payment_status' => 'paid',
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Payment confirmation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Download invoice
     */
    public function downloadInvoice(Request $request, $orderNumber, InvoiceService $invoiceService)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->with('items.product')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return $invoiceService->downloadInvoice($order);
    }

    /**
     * Get invoice URL (for mobile app to open in browser)
     */
    public function getInvoiceUrl(Request $request, $orderNumber)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Generate a temporary token for invoice download (valid for 1 hour)
        $token = hash('sha256', $order->id . $order->order_number . now()->timestamp . config('app.key'));
        $expires = now()->addHour()->timestamp;
        
        // Store token temporarily in cache
        Cache::put('invoice_token_' . $token, [
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
        ], 3600);

        // Generate signed URL that doesn't require auth
        $url = url("/api/v1/orders/{$orderNumber}/invoice-download?token={$token}&expires={$expires}");

        return response()->json([
            'success' => true,
            'data' => [
                'invoice_url' => $url,
            ],
        ]);
    }

    /**
     * Public invoice download with token (for mobile browser)
     */
    public function downloadInvoicePublic(Request $request, $orderNumber, InvoiceService $invoiceService)
    {
        $token = $request->query('token');
        $expires = $request->query('expires');

        \Log::info('Invoice download request', [
            'order_number' => $orderNumber,
            'token' => $token ? substr($token, 0, 20) . '...' : null,
            'expires' => $expires,
        ]);

        if (!$token || !$expires) {
            \Log::error('Invoice download: Missing token or expires');
            return response()->json([
                'success' => false,
                'message' => 'Invalid request.',
            ], 400);
        }

        // Check if token has expired
        if (now()->timestamp > (int) $expires) {
            \Log::error('Invoice download: Token expired', ['expires' => $expires, 'now' => now()->timestamp]);
            return response()->json([
                'success' => false,
                'message' => 'Link has expired. Please try again.',
            ], 400);
        }

        // Verify token from cache
        $tokenData = Cache::get('invoice_token_' . $token);
        if (!$tokenData) {
            \Log::error('Invoice download: Token not found in cache');
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.',
            ], 400);
        }

        \Log::info('Invoice download: Token validated', $tokenData);

        $order = Order::where('order_number', $orderNumber)
            ->where('id', $tokenData['order_id'])
            ->with('items.product')
            ->first();

        if (!$order) {
            \Log::error('Invoice download: Order not found', ['order_number' => $orderNumber, 'order_id' => $tokenData['order_id']]);
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        \Log::info('Invoice download: Generating PDF for order ' . $order->order_number);

        try {
            // Delete token after successful validation (allow multiple downloads for 5 minutes)
            // We keep the token for a bit to allow retries
            Cache::put('invoice_token_' . $token, $tokenData, 300); // Extend for 5 more minutes
            
            return $invoiceService->downloadInvoice($order);
        } catch (\Exception $e) {
            \Log::error('Invoice download: PDF generation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice. Please try again.',
            ], 500);
        }
    }

    /**
     * Format order for response
     */
    private function formatOrder(Order $order, bool $detailed = false): array
    {
        $data = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'status_color' => $order->status_color,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'subtotal' => (float) $order->subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'shipping_charge' => (float) $order->shipping_charge,
            'total_amount' => (float) $order->total_amount,
            'total_items' => $order->total_items,
            'created_at' => $order->created_at->toISOString(),
            'created_at_formatted' => $order->created_at->format('d M Y, h:i A'),
        ];

        if ($detailed) {
            $data['customer_name'] = $order->customer_name;
            $data['customer_email'] = $order->customer_email;
            $data['customer_phone'] = $order->customer_phone;
            $data['shipping_address'] = $order->full_shipping_address;
            $data['customer_notes'] = $order->customer_notes;
            $data['tracking_number'] = $order->tracking_number;
            $data['delivery_partner'] = $order->delivery_partner;
            $data['expected_delivery_date'] = $order->expected_delivery_date?->format('d M Y');
            $data['delivered_at'] = $order->delivered_at?->format('d M Y');
            $data['wallet_amount_used'] = (float) $order->wallet_amount_used;
            $data['gst_amount'] = (float) $order->gst_amount;
            $data['can_cancel'] = $order->canBeCancelledByCustomer();
            $data['delivery_notes'] = $order->delivery_notes;
            $data['delivery_attachments'] = $order->getDeliveryAttachmentUrls();
            $data['cancelled_at'] = $order->cancelled_at?->format('d M Y, h:i A');
            $data['cancellation_reason'] = $order->cancellation_reason;

            $data['items'] = $order->items->map(fn($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'product_sku' => $item->product_sku,
                'unit_price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'total_price' => (float) $item->total_price,
                'image' => $item->product?->primary_image_url,
            ]);

            // Include payment method details for pending payments (UPI/Bank Transfer/PhonePe)
            if ($order->payment_status === 'pending' &&
                $order->status !== 'cancelled' &&
                in_array($order->payment_method, ['upi', 'bank_transfer', 'phonepe'])) {
                
                $paymentMethod = PaymentMethod::where('code', $order->payment_method)->active()->first();
                
                if ($paymentMethod) {
                    $paymentDetails = [
                        'code' => $paymentMethod->code,
                        'name' => $paymentMethod->name,
                        'display_name' => $paymentMethod->display_name ?? $paymentMethod->name,
                        'instructions' => $paymentMethod->instructions,
                    ];

                    // Add UPI details
                    if ($paymentMethod->code === 'upi') {
                        $paymentDetails['upi_id'] = $paymentMethod->getSetting('upi_id');
                        $paymentDetails['upi_name'] = $paymentMethod->getSetting('upi_name');
                        $paymentDetails['merchant_code'] = $paymentMethod->getSetting('merchant_code', '0000');
                        $paymentDetails['transaction_url'] = $paymentMethod->getSetting('transaction_url');
                        $qrCode = $paymentMethod->getSetting('qr_code');
                        $paymentDetails['qr_code_url'] = $qrCode ? asset('storage/' . $qrCode) : null;
                    }

                    // Add bank transfer details
                    if ($paymentMethod->code === 'bank_transfer') {
                        $paymentDetails['bank_details'] = [
                            'account_name' => $paymentMethod->getSetting('account_name'),
                            'account_number' => $paymentMethod->getSetting('account_number'),
                            'bank_name' => $paymentMethod->getSetting('bank_name'),
                            'ifsc_code' => $paymentMethod->getSetting('ifsc_code'),
                            'branch' => $paymentMethod->getSetting('branch'),
                        ];
                    }

                    // Add PhonePe details
                    if ($paymentMethod->code === 'phonepe') {
                        $paymentDetails['requires_redirect'] = true;
                        $paymentDetails['payment_url'] = route('phonepe.create-order');
                    }

                    $data['payment_method_details'] = $paymentDetails;
                }
            }
        }

        return $data;
    }
}
