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
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                    <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shopping-bag text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs text-gray-600">Total Orders</p>
                                <p class="text-xl font-bold text-gray-800">{{ $stats['total_orders'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs text-gray-600">Pending</p>
                                <p class="text-xl font-bold text-gray-800">{{ $stats['pending_orders'] }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-rupee-sign text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs text-gray-600">Total Spent</p>
                                <p class="text-xl font-bold text-gray-800">₹{{ number_format($stats['total_spent'], 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Balance -->
                    <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-wallet text-emerald-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs text-gray-600">Wallet</p>
                                <p class="text-xl font-bold text-emerald-700">₹{{ number_format($stats['wallet_balance'], 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Referral Earnings -->
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs text-gray-600">Referrals</p>
                                <p class="text-xl font-bold text-purple-700">{{ $stats['successful_referrals'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referral Banner -->
                @if(\App\Services\ReferralService::isEnabled())
                    <div class="mb-6 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-4 text-white">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                    <i class="fas fa-gift text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold">Refer & Earn!</h3>
                                    <p class="text-sm text-blue-100">Share your code <span class="font-mono bg-white/20 px-2 py-0.5 rounded">{{ $user->referral_code }}</span> with friends</p>
                                </div>
                            </div>
                            <a href="{{ route('account.referrals') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition text-sm">
                                View Referrals <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endif

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
                    <a href="{{ route('account.wallet') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-wallet text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm text-gray-700">My Wallet</span>
                    </a>
                    <a href="{{ route('account.referrals') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-users text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm text-gray-700">Refer & Earn</span>
                    </a>
                    <a href="{{ route('account.profile') }}" class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition">
                        <i class="fas fa-user text-2xl text-green-600 mb-2"></i>
                        <span class="text-sm text-gray-700">Edit Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
