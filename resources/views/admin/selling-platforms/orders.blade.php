@extends('layouts.admin')

@section('title', $platform->name . ' Orders')
@section('page_title', $platform->name . ' Orders')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.selling-platforms.show', $platform) }}" class="text-green-600 hover:text-green-700">
        <i class="fas fa-arrow-left mr-2"></i>Back to {{ $platform->name }}
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Orders List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Platform Orders</h2>
            </div>
            
            @if($orders->isEmpty())
                <div class="p-8 text-center">
                    <i class="fas fa-shopping-bag text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500">No orders from {{ $platform->name }} yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Settlement</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800">{{ $order->platform_order_id }}</p>
                                    @if($order->platform_order_status)
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                            {{ $order->platform_order_status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm">{{ $order->customer_name ?? 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium">Rs. {{ number_format($order->platform_order_amount, 2) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-red-600">Rs. {{ number_format($order->commission_amount, 2) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-green-600 font-medium">Rs. {{ number_format($order->settlement_amount, 2) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-500">
                                        {{ $order->platform_order_date ? $order->platform_order_date->format('d M Y') : $order->created_at->format('d M Y') }}
                                    </p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Add Order Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow sticky top-24">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Add Platform Order</h2>
                <p class="text-sm text-gray-500">Manually record orders from {{ $platform->name }}</p>
            </div>
            
            <form action="{{ route('admin.selling-platforms.store-order', $platform) }}" method="POST" class="p-4 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Platform Order ID *</label>
                    <input type="text" name="platform_order_id" required
                           placeholder="e.g., AMZ-123456789"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Amount (Rs.) *</label>
                    <input type="number" name="platform_order_amount" step="0.01" min="0" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Commission ({{ $platform->commission_percentage }}%) will be calculated automatically</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                    <input type="text" name="customer_name"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address</label>
                    <textarea name="shipping_address" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                    <select name="platform_order_status"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Date</label>
                    <input type="date" name="platform_order_date" value="{{ date('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-medium">
                    <i class="fas fa-plus mr-1"></i> Add Order
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
