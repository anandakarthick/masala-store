<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Check for new orders (called via AJAX polling)
     */
    public function checkNewOrders(Request $request)
    {
        $lastCheckedId = $request->get('last_order_id', 0);
        $lastCheckedTime = $request->get('last_checked_at');
        
        $query = Order::where('id', '>', $lastCheckedId);
        
        if ($lastCheckedTime) {
            $query->orWhere('created_at', '>', $lastCheckedTime);
        }
        
        $newOrders = $query->latest()->get();
        
        $ordersData = $newOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'total_amount' => $order->total_amount,
                'formatted_total' => '₹' . number_format($order->total_amount, 2),
                'items_count' => $order->items_count ?? $order->items()->count(),
                'created_at' => $order->created_at->diffForHumans(),
                'url' => route('admin.orders.show', $order),
            ];
        });
        
        return response()->json([
            'success' => true,
            'new_orders' => $ordersData,
            'count' => $newOrders->count(),
            'latest_order_id' => $newOrders->first()?->id ?? $lastCheckedId,
            'checked_at' => now()->toIso8601String(),
        ]);
    }
    
    /**
     * Get pending orders count for badge
     */
    public function pendingCount()
    {
        $count = Order::where('status', 'pending')->count();
        
        return response()->json([
            'count' => $count,
        ]);
    }
    
    /**
     * Mark notification as seen
     */
    public function markAsSeen(Request $request)
    {
        // Store the last seen order ID in session or database
        $request->session()->put('last_seen_order_id', $request->order_id);
        
        return response()->json(['success' => true]);
    }
}
