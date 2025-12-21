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
        // Validate - at least one field is required
        $request->validate([
            'order_number' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        $orderNumber = $request->input('order_number');
        $phone = $request->input('phone');

        // Check if at least one field is provided
        if (empty($orderNumber) && empty($phone)) {
            return back()->with('error', 'Please enter either Order Number or Mobile Number.');
        }

        // If order number is provided - search by order number
        if (!empty($orderNumber)) {
            $order = Order::where('order_number', trim($orderNumber))->first();

            if (!$order) {
                return back()->with('error', 'Order not found. Please check your order number.');
            }

            $order->load('items.product');

            return view('frontend.tracking.result', [
                'orders' => collect([$order]),
                'searchType' => 'order_number',
                'searchValue' => $orderNumber,
            ]);
        }

        // If phone number is provided - search by phone number
        if (!empty($phone)) {
            // Clean phone number - remove spaces, dashes, etc.
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            
            // Search with multiple phone formats
            $orders = Order::where(function($query) use ($cleanPhone, $phone) {
                $query->where('customer_phone', $phone)
                      ->orWhere('customer_phone', $cleanPhone)
                      ->orWhere('customer_phone', 'LIKE', '%' . $cleanPhone);
                      
                // Also try with last 10 digits if longer
                if (strlen($cleanPhone) > 10) {
                    $last10 = substr($cleanPhone, -10);
                    $query->orWhere('customer_phone', 'LIKE', '%' . $last10);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();

            if ($orders->isEmpty()) {
                return back()->with('error', 'No orders found for this mobile number.');
            }

            $orders->load('items.product');

            return view('frontend.tracking.result', [
                'orders' => $orders,
                'searchType' => 'phone',
                'searchValue' => $phone,
            ]);
        }

        return back()->with('error', 'Please enter either Order Number or Mobile Number.');
    }

    public function show(Order $order)
    {
        // Only allow viewing if user owns the order or is a guest order
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        return view('frontend.tracking.result', [
            'orders' => collect([$order]),
            'searchType' => 'direct',
            'searchValue' => $order->order_number,
        ]);
    }
}
