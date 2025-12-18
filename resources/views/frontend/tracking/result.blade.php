@extends('layouts.app')

@section('title', 'Order Status - ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <!-- Order Header -->
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Order {{ $order->order_number }}</h1>
                <p class="text-gray-600">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>

            <!-- Status Timeline -->
            <div class="mb-8">
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
                    <div class="flex justify-between items-center">
                        @foreach($statuses as $index => $status)
                            <div class="flex flex-col items-center {{ $index > 0 ? 'flex-1' : '' }}">
                                @if($index > 0)
                                    <div class="w-full h-1 {{ $index <= $currentIndex ? 'bg-green-500' : 'bg-gray-300' }} -ml-1"></div>
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
                                <span class="text-xs mt-1 {{ $index <= $currentIndex ? 'text-green-600 font-medium' : 'text-gray-500' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Order Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold mb-2">Payment Details</h3>
                    <p class="text-gray-600">Method: <span class="font-medium">{{ ucfirst($order->payment_method) }}</span></p>
                    <p class="text-gray-600">Status: 
                        <span class="px-2 py-1 text-xs rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold mb-2">Delivery Details</h3>
                    @if($order->tracking_number)
                        <p class="text-gray-600">Tracking: <span class="font-medium">{{ $order->tracking_number }}</span></p>
                    @endif
                    @if($order->expected_delivery_date)
                        <p class="text-gray-600">Expected: <span class="font-medium">{{ $order->expected_delivery_date->format('d M Y') }}</span></p>
                    @endif
                    @if($order->delivery_partner)
                        <p class="text-gray-600">Partner: <span class="font-medium">{{ $order->delivery_partner }}</span></p>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="border rounded-lg overflow-hidden mb-6">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Product</th>
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Qty</th>
                            <th class="px-4 py-2 text-right text-sm font-medium text-gray-700">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->product_name }}</td>
                                <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right">₹{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="2" class="px-4 py-2 text-right font-medium">Total:</td>
                            <td class="px-4 py-2 text-right font-bold text-orange-600">₹{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Shipping Address -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold mb-2">Shipping Address</h3>
                <p class="text-gray-600">
                    {{ $order->customer_name }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
                    Phone: {{ $order->customer_phone }}
                </p>
            </div>

            <div class="flex justify-center gap-4">
                <a href="{{ route('tracking.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg">
                    Track Another Order
                </a>
                <a href="{{ route('products.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
