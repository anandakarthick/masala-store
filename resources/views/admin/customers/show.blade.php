@extends('layouts.admin')
@section('title', 'Customer Details')
@section('page_title', 'Customer: ' . $customer->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $customer->name }}</h2>
                    <p class="text-gray-500">{{ $customer->email }}</p>
                </div>
                <a href="{{ route('admin.customers.edit', $customer) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Contact Details</h4>
                    <div class="space-y-1 text-sm">
                        <p><i class="fas fa-phone text-gray-400 mr-2"></i>{{ $customer->phone ?? 'N/A' }}</p>
                        <p><i class="fas fa-envelope text-gray-400 mr-2"></i>{{ $customer->email }}</p>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Address</h4>
                    <p class="text-sm text-gray-600">{{ $customer->full_address ?: 'No address provided' }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Recent Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($customer->orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-orange-600 hover:underline">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 font-medium">₹{{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($order->status === 'delivered') bg-green-100 text-green-600
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-600
                                        @else bg-blue-100 text-blue-600
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No orders yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Stats -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Orders</span>
                    <span class="text-xl font-bold">{{ $stats['total_orders'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Spent</span>
                    <span class="text-xl font-bold text-green-600">₹{{ number_format($stats['total_spent'], 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Pending Orders</span>
                    <span class="text-xl font-bold text-yellow-600">{{ $stats['pending_orders'] }}</span>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Account Status</h3>
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Status</span>
                <span class="px-2 py-1 text-xs rounded-full {{ $customer->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-2">Member since {{ $customer->created_at->format('d M Y') }}</p>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-center mb-2">
                <i class="fas fa-edit mr-2"></i>Edit Customer
            </a>
            <a href="{{ route('admin.customers.index') }}" class="block w-full bg-gray-200 text-gray-700 py-2 rounded-lg text-center">
                Back to Customers
            </a>
        </div>
    </div>
</div>
@endsection
