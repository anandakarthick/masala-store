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
        
        // Get new unseen orders
        $newOrders = Order::where('id', '>', $lastCheckedId)
            ->where('is_seen_by_admin', false)
            ->latest()
            ->get();
        
        $ordersData = $newOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'total_amount' => $order->total_amount,
                'formatted_total' => 'Rs. ' . number_format($order->total_amount, 2),
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
     * Get unseen orders count for badge (new orders not yet viewed)
     */
    public function unseenCount()
    {
        $count = Order::where('is_seen_by_admin', false)->count();
        
        return response()->json([
            'count' => $count,
        ]);
    }
    
    /**
     * Get pending orders count for badge
     */
    public function pendingCount()
    {
        $pendingCount = Order::where('status', 'pending')->count();
        $unseenCount = Order::where('is_seen_by_admin', false)->count();
        
        return response()->json([
            'pending_count' => $pendingCount,
            'unseen_count' => $unseenCount,
        ]);
    }
    
    /**
     * Mark single order as seen
     */
    public function markAsSeen(Request $request, Order $order)
    {
        $order->markAsSeen();
        
        return response()->json([
            'success' => true,
            'unseen_count' => Order::where('is_seen_by_admin', false)->count(),
        ]);
    }
    
    /**
     * Mark all orders as seen
     */
    public function markAllAsSeen()
    {
        Order::where('is_seen_by_admin', false)->update([
            'is_seen_by_admin' => true,
            'seen_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'unseen_count' => 0,
        ]);
    }
}
