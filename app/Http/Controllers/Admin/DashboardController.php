<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's stats
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Monthly stats
        $monthlyOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $monthlyRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Pending orders
        $pendingOrders = Order::pending()->count();

        // Low stock products
        $lowStockProducts = Product::active()->lowStock()->count();

        // Total customers
        $totalCustomers = User::whereHas('role', fn($q) => $q->where('slug', 'customer'))->count();

        // Total products
        $totalProducts = Product::count();

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Order status distribution
        $orderStatusDistribution = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Top selling products
        $topProducts = Product::withCount(['orderItems as total_sold' => function ($query) {
            $query->select(DB::raw('SUM(quantity)'));
        }])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // Sales chart data (last 7 days)
        $salesChartData = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Category-wise revenue
        $categoryRevenue = Category::withSum(['products as revenue' => function ($query) {
            $query->join('order_items', 'products.id', '=', 'order_items.product_id')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.payment_status', 'paid');
        }], 'order_items.total_price')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        // Expiring products
        $expiringProducts = Product::expiringSoon(30)->count();

        return view('admin.dashboard', compact(
            'todayOrders',
            'todayRevenue',
            'monthlyOrders',
            'monthlyRevenue',
            'pendingOrders',
            'lowStockProducts',
            'totalCustomers',
            'totalProducts',
            'recentOrders',
            'orderStatusDistribution',
            'topProducts',
            'salesChartData',
            'categoryRevenue',
            'expiringProducts'
        ));
    }
}
