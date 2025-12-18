@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Today's Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Today's Orders</p>
                <p class="text-2xl font-bold text-gray-800">{{ $todayOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            <span class="text-green-600">₹{{ number_format($todayRevenue, 2) }}</span> revenue
        </p>
    </div>

    <!-- Monthly Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Monthly Orders</p>
                <p class="text-2xl font-bold text-gray-800">{{ $monthlyOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-green-600 text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-2">
            <span class="text-green-600">₹{{ number_format($monthlyRevenue, 2) }}</span> revenue
        </p>
    </div>

    <!-- Pending Orders -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Pending Orders</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
        </div>
        <a href="{{ route('admin.orders.index') }}?status=pending" class="text-sm text-orange-600 hover:underline mt-2 inline-block">
            View all →
        </a>
    </div>

    <!-- Low Stock -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Low Stock Products</p>
                <p class="text-2xl font-bold text-gray-800">{{ $lowStockProducts }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
        </div>
        <a href="{{ route('admin.reports.stock') }}" class="text-sm text-orange-600 hover:underline mt-2 inline-block">
            View all →
        </a>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-md p-6 text-white">
        <p class="text-orange-100">Total Products</p>
        <p class="text-3xl font-bold">{{ $totalProducts }}</p>
    </div>
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
        <p class="text-blue-100">Total Customers</p>
        <p class="text-3xl font-bold">{{ $totalCustomers }}</p>
    </div>
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
        <p class="text-green-100">Expiring Soon</p>
        <p class="text-3xl font-bold">{{ $expiringProducts }}</p>
    </div>
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
        <p class="text-purple-100">Total Orders</p>
        <p class="text-3xl font-bold">{{ \App\Models\Order::count() }}</p>
    </div>
</div>

<!-- Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-orange-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-orange-600 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $order->customer_name }}</td>
                            <td class="px-6 py-4 text-sm font-medium">₹{{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-600
                                    @elseif($order->status === 'delivered') bg-green-100 text-green-600
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-600
                                    @else bg-blue-100 text-blue-600
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No orders yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold">Top Selling Products</h3>
            <a href="{{ route('admin.reports.products') }}" class="text-sm text-orange-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($topProducts as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-gray-800 hover:text-orange-600">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->total_sold ?? 0 }}</td>
                            <td class="px-6 py-4">
                                @if($product->stock_quantity <= 0)
                                    <span class="text-red-600 text-sm">Out of Stock</span>
                                @elseif($product->isLowStock())
                                    <span class="text-yellow-600 text-sm">{{ $product->stock_quantity }} (Low)</span>
                                @else
                                    <span class="text-gray-600 text-sm">{{ $product->stock_quantity }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No products yet</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
