@extends('layouts.app')

@section('title', 'Order Status')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        
        <!-- Search Info Header -->
        <div class="mb-6">
            <a href="{{ route('tracking.index') }}" class="text-green-600 hover:text-green-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Track Order
            </a>
        </div>

        @if($searchType === 'phone')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-phone-alt text-green-600 mr-3"></i>
                    <div>
                        <p class="font-medium text-green-800">Orders for Mobile: {{ $searchValue }}</p>
                        <p class="text-sm text-green-600">Found {{ $orders->count() }} order(s)</p>
                    </div>
                </div>
            </div>
        @endif

        @foreach($orders as $order)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <!-- Order Header -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 pb-4 border-b">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $order->order_number }}</h2>
                        <p class="text-gray-500 text-sm">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="mt-3 md:mt-0">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            @if($order->status === 'delivered') bg-green-100 text-green-700
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                            @elseif($order->status === 'shipped') bg-blue-100 text-blue-700
                            @else bg-yellow-100 text-yellow-700
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="mb-6">
                    @php
                        $statuses = ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered'];
                        $currentIndex = array_search($order->status, $statuses);
                        if ($order->status === 'cancelled') $currentIndex = -1;
                    @endphp

                    @if($order->status === 'cancelled')
                        <div class="bg-red-100 text-red-600 p-4 rounded-lg text-center">
                            <i class="fas fa-times-circle text-2xl mb-2"></i>
                            <p class="font-semibold">This order has been cancelled</p>
                        </div>
                    @else
                        <div class="flex justify-between items-center overflow-x-auto pb-2">
                            @foreach($statuses as $index => $status)
                                <div class="flex flex-col items-center min-w-[60px] {{ $index > 0 ? 'flex-1' : '' }}">
                                    @if($index > 0)
                                        <div class="w-full h-1 {{ $index <= $currentIndex ? 'bg-green-500' : 'bg-gray-300' }} mb-2"></div>
                                    @endif
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                        {{ $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                        @if($index < $currentIndex)
                                            <i class="fas fa-check text-sm"></i>
                                        @elseif($index === $currentIndex)
                                            <i class="fas fa-circle text-xs"></i>
                                        @else
                                            <span class="text-xs">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs mt-1 text-center {{ $index <= $currentIndex ? 'text-green-600 font-medium' : 'text-gray-500' }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Order Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-credit-card text-green-600 mr-2"></i>Payment
                        </h3>
                        <p class="text-gray-600 text-sm">Method: <span class="font-medium">{{ strtoupper($order->payment_method) }}</span></p>
                        <p class="text-gray-600 text-sm">
                            Status: 
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-truck text-green-600 mr-2"></i>Delivery
                        </h3>
                        @if($order->tracking_number)
                            <p class="text-gray-600 text-sm">Tracking: <span class="font-medium">{{ $order->tracking_number }}</span></p>
                        @endif
                        @if($order->expected_delivery_date)
                            <p class="text-gray-600 text-sm">Expected: <span class="font-medium">{{ $order->expected_delivery_date->format('d M Y') }}</span></p>
                        @endif
                        @if($order->delivery_partner)
                            <p class="text-gray-600 text-sm">Partner: <span class="font-medium">{{ $order->delivery_partner }}</span></p>
                        @endif
                        @if(!$order->tracking_number && !$order->expected_delivery_date && !$order->delivery_partner)
                            <p class="text-gray-500 text-sm">Details will be updated soon</p>
                        @endif
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>Shipping
                        </h3>
                        <p class="text-gray-600 text-sm">{{ $order->customer_name }}</p>
                        <p class="text-gray-600 text-sm">{{ $order->shipping_city }}, {{ $order->shipping_state }}</p>
                        <p class="text-gray-600 text-sm">PIN: {{ $order->shipping_pincode }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="border rounded-lg overflow-hidden mb-4">
                    <div class="bg-gray-50 px-4 py-2 border-b">
                        <h3 class="font-semibold text-gray-700">Order Items</h3>
                    </div>
                    <div class="divide-y">
                        @foreach($order->items as $item)
                            <div class="flex items-center p-4">
                                <div class="w-12 h-12 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center">
                                    @if($item->product && $item->product->primary_image_url)
                                        <img src="{{ $item->product->primary_image_url }}" alt="" class="w-full h-full object-cover rounded">
                                    @else
                                        <i class="fas fa-box text-gray-400"></i>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="font-medium text-gray-800">{{ $item->product_name }}</p>
                                    @if($item->variant_name)
                                        <p class="text-sm text-green-600">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">₹{{ number_format($item->total_price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-gray-50 px-4 py-3 border-t">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-700">Total Amount</span>
                            <span class="font-bold text-lg text-green-600">₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Actions -->
                @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                    <div class="flex flex-wrap gap-2 pt-4 border-t">
                        @php
                            $whatsappNumber = \App\Models\Setting::get('whatsapp_number', '');
                            $whatsappMessage = "Hi! I need help with my order #{$order->order_number}";
                        @endphp
                        @if($whatsappNumber)
                            <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
                               target="_blank" rel="noopener"
                               class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg">
                                <i class="fab fa-whatsapp mr-2"></i>Need Help?
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
            <a href="{{ route('tracking.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg text-center font-medium">
                <i class="fas fa-search mr-2"></i>Track Another Order
            </a>
            <a href="{{ route('products.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg text-center font-medium">
                <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection
