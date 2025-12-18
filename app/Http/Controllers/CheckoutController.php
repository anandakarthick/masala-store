<?php

namespace App\Http\Controllers;

use App\Jobs\SendOrderEmails;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Setting;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::getCart();
        $cart->load('items.product', 'items.variant');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();
        $shippingCharge = $cart->subtotal >= Setting::freeShippingAmount() 
            ? 0 
            : Setting::defaultShippingCharge();

        return view('frontend.checkout.index', compact('cart', 'user', 'shippingCharge'));
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
            'payment_method' => 'required|in:cod,upi',
            'customer_notes' => 'nullable|string',
        ]);

        $cart = Cart::getCart();
        $cart->load('items.product', 'items.variant');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Check stock availability
        foreach ($cart->items as $item) {
            $stockQty = $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity;
            if ($item->quantity > $stockQty) {
                return back()->with('error', "Not enough stock for {$item->item_name}.");
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

        $totalAmount = $subtotal + $gstAmount + $shippingCharge - $discountAmount;

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
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
                'status' => 'pending',
                'customer_notes' => $validated['customer_notes'],
            ]);

            // Create order items and update stock
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

            // Increment coupon usage
            if ($coupon) {
                $coupon->incrementUsage();
                session()->forget('coupon');
            }

            // Clear cart
            $cart->clear();

            DB::commit();

            // Send order emails in background
            SendOrderEmails::dispatch($order->fresh()->load('items.product'));

            // Redirect based on payment method
            if ($validated['payment_method'] === 'cod') {
                return redirect()->route('checkout.success', $order)
                    ->with('success', 'Order placed successfully!');
            }

            // For UPI/Online payment, redirect to payment page
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

        return view('frontend.checkout.payment', compact('order'));
    }

    public function success(Order $order)
    {
        $order->load('items.product');
        return view('frontend.checkout.success', compact('order'));
    }
}
