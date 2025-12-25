<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderEmails;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\StockMovement;
use App\Models\UserAddress;
use App\Services\FirstTimeCustomerService;
use Illuminate\Http\Request;
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

            // For COD or full wallet payment, send emails and push notifications
            if ($paymentMethod->isCod() || $walletAmountUsed >= $totalAmount) {
                if ($walletAmountUsed >= $totalAmount) {
                    $order->update(['payment_status' => 'paid']);
                }
                // This job now handles both email and push notification
                SendOrderEmails::dispatch($order->fresh()->load('items.product'));
            }

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
        }

        return $data;
    }
}
