@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-green-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('account.dashboard') }}" class="hover:text-green-600">My Account</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800">My Orders</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        @include('frontend.account.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-xl font-bold text-gray-800 mb-6">My Orders</h1>
                
                @if($orders->count() > 0)
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                    <div class="mb-3 md:mb-0">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="font-semibold text-green-600">{{ $order->order_number }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                @if($order->status == 'delivered') bg-green-100 text-green-700
                                                @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                                                @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                                                @else bg-yellow-100 text-yellow-700
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-calendar mr-1"></i> {{ $order->created_at->format('d M Y, h:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fas fa-box mr-1"></i> {{ $order->items->count() }} item(s)
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-800">₹{{ number_format($order->total_amount, 2) }}</p>
                                        <p class="text-xs text-gray-500 mb-2">
                                            Payment: 
                                            <span class="@if($order->payment_status == 'paid') text-green-600 @else text-yellow-600 @endif">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </p>
                                        <a href="{{ route('account.orders.show', $order) }}" 
                                           class="inline-flex items-center text-sm text-green-600 hover:text-green-700">
                                            View Details <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-bag text-5xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No orders yet</h3>
                        <p class="text-gray-500 mb-4">You haven't placed any orders yet.</p>
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                            Start Shopping <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
