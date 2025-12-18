<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->get();

        $summary = [
            'total_orders' => $orders->count(),
            'total_revenue' => $orders->sum('total_amount'),
            'total_gst' => $orders->sum('gst_amount'),
            'average_order_value' => $orders->count() > 0 ? $orders->avg('total_amount') : 0,
        ];

        $dailySales = Order::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.sales', compact('summary', 'dailySales', 'startDate', 'endDate'));
    }

    public function products(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $products = Product::withCount(['orderItems as total_sold' => function ($query) use ($startDate, $endDate) {
            $query->select(DB::raw('COALESCE(SUM(quantity), 0)'))
                ->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                        ->where('payment_status', 'paid');
                });
        }])
            ->withSum(['orderItems as total_revenue' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                        ->where('payment_status', 'paid');
                });
            }], 'total_price')
            ->orderByDesc('total_sold')
            ->paginate(20);

        return view('admin.reports.products', compact('products', 'startDate', 'endDate'));
    }

    public function categories(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $categories = Category::withCount(['products'])
            ->with(['products' => function ($query) use ($startDate, $endDate) {
                $query->withSum(['orderItems as revenue' => function ($q) use ($startDate, $endDate) {
                    $q->whereHas('order', function ($oq) use ($startDate, $endDate) {
                        $oq->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                            ->where('payment_status', 'paid');
                    });
                }], 'total_price');
            }])
            ->get()
            ->map(function ($category) {
                $category->total_revenue = $category->products->sum('revenue') ?? 0;
                return $category;
            })
            ->sortByDesc('total_revenue');

        return view('admin.reports.categories', compact('categories', 'startDate', 'endDate'));
    }

    public function stock()
    {
        $lowStockProducts = Product::lowStock()->with('category')->get();
        $outOfStockProducts = Product::where('stock_quantity', 0)->with('category')->get();
        $expiringProducts = Product::expiringSoon(30)->with('category')->get();

        $stockSummary = [
            'total_products' => Product::count(),
            'in_stock' => Product::where('stock_quantity', '>', 0)->count(),
            'low_stock' => $lowStockProducts->count(),
            'out_of_stock' => $outOfStockProducts->count(),
            'expiring_soon' => $expiringProducts->count(),
        ];

        return view('admin.reports.stock', compact('lowStockProducts', 'outOfStockProducts', 'expiringProducts', 'stockSummary'));
    }

    public function customers(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $topCustomers = Order::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->select(
                'customer_name',
                'customer_phone',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_spent')
            )
            ->groupBy('customer_name', 'customer_phone')
            ->orderByDesc('total_spent')
            ->take(20)
            ->get();

        return view('admin.reports.customers', compact('topCustomers', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Generate CSV based on type
        $filename = "{$type}_report_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');

            if ($type === 'sales') {
                fputcsv($file, ['Date', 'Order Number', 'Customer', 'Amount', 'Status']);

                Order::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
                    ->orderBy('created_at')
                    ->chunk(100, function ($orders) use ($file) {
                        foreach ($orders as $order) {
                            fputcsv($file, [
                                $order->created_at->format('Y-m-d'),
                                $order->order_number,
                                $order->customer_name,
                                $order->total_amount,
                                $order->status,
                            ]);
                        }
                    });
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
