@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-green-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('account.dashboard') }}" class="hover:text-green-600">My Account</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('account.orders') }}" class="hover:text-green-600">My Orders</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800">{{ $order->order_number }}</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        @include('frontend.account.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Order Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-6 border-b">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Order {{ $order->order_number }}</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                    <div class="mt-3 md:mt-0 flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium
                            @if($order->status == 'delivered') bg-green-100 text-green-700
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                            @elseif($order->status == 'shipped') bg-blue-100 text-blue-700
                            @else bg-yellow-100 text-yellow-700
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        <a href="{{ route('tracking.show', $order) }}" 
                           class="text-green-600 hover:text-green-700 text-sm">
                            <i class="fas fa-truck mr-1"></i> Track Order
                        </a>
                    </div>
                </div>

                <!-- Order Progress -->
                @if($order->status != 'cancelled')
                <div class="mb-8">
                    <div class="flex justify-between items-center relative">
                        @php
                            $statuses = ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered'];
                            $currentIndex = array_search($order->status, $statuses);
                        @endphp
                        
                        <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200">
                            <div class="h-full bg-green-500 transition-all" 
                                 style="width: {{ ($currentIndex / (count($statuses) - 1)) * 100 }}%"></div>
                        </div>
                        
                        @foreach($statuses as $index => $status)
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                    {{ $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                    @if($status == 'pending')
                                        <i class="fas fa-clock text-sm"></i>
                                    @elseif($status == 'confirmed')
                                        <i class="fas fa-check text-sm"></i>
                                    @elseif($status == 'processing')
                                        <i class="fas fa-cog text-sm"></i>
                                    @elseif($status == 'packed')
                                        <i class="fas fa-box text-sm"></i>
                                    @elseif($status == 'shipped')
                                        <i class="fas fa-truck text-sm"></i>
                                    @elseif($status == 'delivered')
                                        <i class="fas fa-home text-sm"></i>
                                    @endif
                                </div>
                                <span class="text-xs mt-2 text-center {{ $index <= $currentIndex ? 'text-green-600 font-medium' : 'text-gray-500' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Order Items -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold mb-4">Order Items</h2>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                                <div class="w-16 h-16 bg-gray-200 rounded overflow-hidden flex-shrink-0">
                                    @if($item->product && $item->product->primary_image_url)
                                        <img src="{{ $item->product->primary_image_url }}" 
                                             alt="{{ $item->product_name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-800">{{ $item->product_name }}</h3>
                                    @if($item->variant_name)
                                        <p class="text-sm text-gray-500">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium">₹{{ number_format($item->total, 2) }}</p>
                                    <p class="text-xs text-gray-500">₹{{ number_format($item->price, 2) }} each</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary & Address -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Delivery Address -->
                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-3">
                            <i class="fas fa-map-marker-alt text-green-600 mr-2"></i> Delivery Address
                        </h3>
                        <p class="text-gray-700">{{ $order->customer_name }}</p>
                        <p class="text-gray-600 text-sm">{{ $order->shipping_address }}</p>
                        <p class="text-gray-600 text-sm">{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}</p>
                        <p class="text-gray-600 text-sm mt-2">
                            <i class="fas fa-phone mr-1"></i> {{ $order->customer_phone }}
                        </p>
                    </div>

                    <!-- Order Summary -->
                    <div class="border rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-3">
                            <i class="fas fa-receipt text-green-600 mr-2"></i> Order Summary
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span>₹{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Discount</span>
                                    <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span>{{ $order->shipping_amount > 0 ? '₹' . number_format($order->shipping_amount, 2) : 'Free' }}</span>
                            </div>
                            @if($order->tax_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tax</span>
                                    <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-bold text-base pt-2 border-t">
                                <span>Total</span>
                                <span class="text-green-600">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t">
                            <p class="text-sm">
                                <span class="text-gray-600">Payment Method:</span>
                                <span class="font-medium">{{ ucfirst($order->payment_method) }}</span>
                            </p>
                            <p class="text-sm mt-1">
                                <span class="text-gray-600">Payment Status:</span>
                                <span class="font-medium @if($order->payment_status == 'paid') text-green-600 @else text-yellow-600 @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mt-6 pt-6 border-t">
                    <a href="{{ route('account.orders') }}" class="text-green-600 hover:text-green-700">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
