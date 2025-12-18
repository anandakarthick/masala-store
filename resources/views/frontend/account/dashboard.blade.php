@extends('layouts.app')

@section('title', 'My Account')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-green-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800">My Account</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        @include('frontend.account.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-xl font-bold text-gray-800 mb-6">Welcome, {{ $user->name }}!</h1>
                
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-bag text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total Orders</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_orders'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Pending Orders</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $stats['pending_orders'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-rupee-sign text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Total Spent</p>
                                <p class="text-2xl font-bold text-gray-800">₹{{ number_format($stats['total_spent'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Orders</h2>
                        <a href="{{ route('account.orders') }}" class="text-green-600 hover:text-green-700 text-sm">View All</a>
                    </div>
                    
                    @if($recentOrders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-gray-600">Order #</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-600">Date</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-600">Total</th>
                                        <th class="px-4 py-3 text-left font-medium text-gray-600">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-green-600">{{ $order->order_number }}</td>
                                            <td class="px-4 py-3 text-gray-600">{{ $order->created_at->format('d M Y') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($order->status == 'delivered') bg-green-100 text-green-700
                                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                                                    @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                                                    @else bg-yellow-100 text-yellow-700
                                                    @endif">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 font-medium">₹{{ number_format($order->total_amount, 2) }}</td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('account.orders.show', $order) }}" 
                                                   class="text-green-600 hover:text-green-700">
                                                    View <i class="fas fa-arrow-right ml-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No orders yet.</p>
                            <a href="{{ route('products.index') }}" class="inline-block mt-3 text-green-600 hover:text-green-700">
                                Start Shopping <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Links -->
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('account.orders') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-shopping-bag text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm text-gray-700">My Orders</span>
                    </a>
                    <a href="{{ route('account.profile') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-user text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm text-gray-700">Edit Profile</span>
                    </a>
                    <a href="{{ route('account.password') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-lock text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm text-gray-700">Change Password</span>
                    </a>
                    <a href="{{ route('tracking.index') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-truck text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm text-gray-700">Track Order</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
