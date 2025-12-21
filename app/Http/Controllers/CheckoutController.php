<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderEmails;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderCustomCombo;
use App\Models\PaymentMethod;
use App\Models\Setting;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::getCart();
        $cart->load('items.product', 'items.variant', 'customCombos.items.product', 'customCombos.items.variant', 'customCombos.comboSetting');

        // Check if cart has items OR custom combos
        if ($cart->items->isEmpty() && $cart->customCombos->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();
        $shippingCharge = $cart->subtotal >= Setting::freeShippingAmount() 
            ? 0 
            : Setting::defaultShippingCharge();

        // Get active payment methods available for this order amount
        $paymentMethods = PaymentMethod::availableForAmount($cart->subtotal + $shippingCharge)->get();

        return view('frontend.checkout.index', compact('cart', 'user', 'shippingCharge', 'paymentMethods'));
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        if (!$coupon->isValid()) {
            return back()->with('error', 'This coupon has expired or is no longer valid.');
        }

        $cart = Cart::getCart();

        if ($cart->subtotal < $coupon->min_order_amount) {
            return back()->with('error', "Minimum order amount of ₹{$coupon->min_order_amount} required.");
        }

        session(['coupon' => $coupon]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'required|string',
            'shipping_pincode' => 'required|string|max:10',
            'billing_same' => 'boolean',
            'billing_address' => 'nullable|required_if:billing_same,false|string',
            'billing_city' => 'nullable|required_if:billing_same,false|string',
            'billing_state' => 'nullable|required_if:billing_same,false|string',
            'billing_pincode' => 'nullable|required_if:billing_same,false|string|max:10',
            'order_type' => 'nullable|in:retail,bulk,return_gift',
            'payment_method' => 'required|string',
            'customer_notes' => 'nullable|string',
        ]);

        // Validate payment method
        $paymentMethod = PaymentMethod::where('code', $validated['payment_method'])->active()->first();
        if (!$paymentMethod) {
            return back()->with('error', 'Invalid payment method selected.');
        }

        $cart = Cart::getCart();
        $cart->load('items.product', 'items.variant', 'customCombos.items.product', 'customCombos.items.variant', 'customCombos.comboSetting');

        // Check if cart has items OR custom combos
        if ($cart->items->isEmpty() && $cart->customCombos->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Check stock availability for regular items
        foreach ($cart->items as $item) {
            $stockQty = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
            if ($item->quantity > $stockQty) {
                return back()->with('error', "Not enough stock for {$item->item_name}.");
            }
        }

        // Check stock availability for combo items
        foreach ($cart->customCombos as $combo) {
            foreach ($combo->items as $item) {
                $stockQty = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
                $totalNeeded = $item->quantity * $combo->quantity;
                if ($totalNeeded > $stockQty) {
                    return back()->with('error', "Not enough stock for {$item->item_name} in combo.");
                }
            }
        }

        // Calculate totals
        $subtotal = $cart->subtotal;
        $gstAmount = $cart->gst_amount;
        $shippingCharge = $subtotal >= Setting::freeShippingAmount() ? 0 : Setting::defaultShippingCharge();
        $discountAmount = 0;

        // Apply coupon
        $coupon = session('coupon');
        if ($coupon) {
            $discountAmount = $coupon->calculateDiscount($subtotal);
        }

        // Calculate payment method extra charge
        $paymentCharge = $paymentMethod->calculateExtraCharge($subtotal + $gstAmount + $shippingCharge - $discountAmount);

        $totalAmount = $subtotal + $gstAmount + $shippingCharge - $discountAmount + $paymentCharge;

        // Billing address
        $billingSame = $request->boolean('billing_same', true);

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_pincode' => $validated['shipping_pincode'],
                'billing_address' => $billingSame ? $validated['shipping_address'] : $validated['billing_address'],
                'billing_city' => $billingSame ? $validated['shipping_city'] : $validated['billing_city'],
                'billing_state' => $billingSame ? $validated['shipping_state'] : $validated['billing_state'],
                'billing_pincode' => $billingSame ? $validated['shipping_pincode'] : $validated['billing_pincode'],
                'order_type' => $validated['order_type'] ?? 'retail',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'gst_amount' => $gstAmount,
                'shipping_charge' => $shippingCharge,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod->code,
                'payment_status' => 'pending',
                'status' => 'pending',
                'customer_notes' => $validated['customer_notes'],
            ]);

            // Create order items from regular cart items and update stock
            foreach ($cart->items as $item) {
                $product = $item->product;
                $variant = $item->variant;
                $unitPrice = $item->unit_price;
                $itemGst = $product->calculateGst($unitPrice * $item->quantity);

                $order->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $variant ? $variant->id : null,
                    'product_name' => $product->name,
                    'product_sku' => $variant ? $variant->sku : $product->sku,
                    'variant_name' => $variant ? $variant->name : null,
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

            // Create order items from custom combos
            foreach ($cart->customCombos as $combo) {
                // Create combo snapshot
                $itemsSnapshot = $combo->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'product_name' => $item->product->name,
                        'variant_name' => $item->variant ? $item->variant->name : null,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->total,
                    ];
                })->toArray();

                // Save combo to order
                OrderCustomCombo::create([
                    'order_id' => $order->id,
                    'combo_setting_id' => $combo->combo_setting_id,
                    'combo_name' => $combo->comboSetting->name ?? $combo->combo_name,
                    'quantity' => $combo->quantity,
                    'original_price' => $combo->calculated_price,
                    'discount_amount' => $combo->discount_amount,
                    'final_price' => $combo->final_price,
                    'items_snapshot' => $itemsSnapshot,
                ]);

                // Create individual order items for each product in combo (for stock tracking)
                foreach ($combo->items as $item) {
                    $product = $item->product;
                    $variant = $item->variant;
                    $totalQty = $item->quantity * $combo->quantity;
                    $itemGst = $product->calculateGst($item->unit_price * $totalQty);

                    $order->items()->create([
                        'product_id' => $product->id,
                        'variant_id' => $variant ? $variant->id : null,
                        'product_name' => $product->name . ' (Combo: ' . $combo->comboSetting->name . ')',
                        'product_sku' => $variant ? $variant->sku : $product->sku,
                        'variant_name' => $variant ? $variant->name : null,
                        'unit_price' => $item->unit_price,
                        'quantity' => $totalQty,
                        'gst_amount' => $itemGst,
                        'total_price' => ($item->unit_price * $totalQty) + $itemGst,
                    ]);

                    // Reduce stock
                    if ($variant) {
                        $variant->decrement('stock_quantity', $totalQty);
                    } else {
                        StockMovement::recordMovement(
                            $product,
                            'out',
                            $totalQty,
                            "Order #{$order->order_number} (Combo)",
                            'Stock reduced for combo order'
                        );
                    }
                }
            }

            // Increment coupon usage
            if ($coupon) {
                $coupon->incrementUsage();
                session()->forget('coupon');
            }

            // Clear cart (including combos)
            $cart->clear();

            DB::commit();

            // Send order emails in background (for COD orders immediately, for online after payment)
            if ($paymentMethod->isCod()) {
                SendOrderEmails::dispatch($order->fresh()->load('items.product'));
                return redirect()->route('checkout.success', $order)
                    ->with('success', 'Order placed successfully!');
            }

            // For online payment methods (Razorpay, UPI, Bank Transfer), redirect to payment page
            return redirect()->route('checkout.payment', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function payment(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order);
        }

        $paymentMethod = PaymentMethod::where('code', $order->payment_method)->first();

        return view('frontend.checkout.payment', compact('order', 'paymentMethod'));
    }

    public function success(Order $order)
    {
        // Send order emails if not already sent (for online payments)
        if ($order->payment_status === 'paid') {
            // Check if emails were already sent (you might want to add a flag for this)
        }

        $order->load('items.product');
        return view('frontend.checkout.success', compact('order'));
    }
}
