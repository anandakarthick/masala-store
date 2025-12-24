@extends('layouts.admin')

@section('title', 'Orders')
@section('page_title', 'Orders')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">All Orders</h2>
            <p class="text-sm text-gray-500">Manage customer orders</p>
        </div>
        <a href="{{ route('admin.orders.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Create Order
        </a>
    </div>

    <!-- Filters -->
    <div class="p-4 border-b bg-gray-50">
        <form action="" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Search order/customer..." value="{{ request('search') }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Status</option>
                @foreach(['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Payment</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-gray-300 rounded-lg px-4 py-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-gray-300 rounded-lg px-4 py-2">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg">Filter</button>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">Reset</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-orange-600 hover:underline font-medium">
                                {{ $order->order_number }}
                            </a>
                            <p class="text-xs text-gray-500">{{ ucfirst($order->order_type) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $order->customer_name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->total_items }}</td>
                        <td class="px-6 py-4 font-medium">₹{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $sourceIcons = [
                                    'web' => '<i class="fas fa-globe text-blue-500" title="Web"></i>',
                                    'android' => '<i class="fab fa-android text-green-500" title="Android"></i>',
                                    'ios' => '<i class="fab fa-apple text-gray-700" title="iOS"></i>',
                                ];
                            @endphp
                            <span class="inline-flex items-center gap-1">
                                {!! $sourceIcons[$order->order_source ?? 'web'] ?? $sourceIcons['web'] !!}
                                <span class="text-xs text-gray-500">{{ ucfirst($order->order_source ?? 'web') }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
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
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-shopping-bag text-4xl mb-2"></i>
                            <p>No orders found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $orders->links() }}
    </div>
</div>
@endsection
