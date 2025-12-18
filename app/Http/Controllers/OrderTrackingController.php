<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('frontend.tracking.index');
    }

    public function track(Request $request)
    {
        $validated = $request->validate([
            'order_number' => 'required|string',
            'phone' => 'required|string',
        ]);

        $order = Order::where('order_number', $validated['order_number'])
            ->where('customer_phone', $validated['phone'])
            ->first();

        if (!$order) {
            return back()->with('error', 'Order not found. Please check your order number and phone number.');
        }

        $order->load('items.product');

        return view('frontend.tracking.result', compact('order'));
    }

    public function show(Order $order)
    {
        // Only allow viewing if user owns the order or is a guest order
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('frontend.tracking.result', compact('order'));
    }
}
