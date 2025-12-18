@extends('layouts.app')

@section('title', 'Order Placed Successfully')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-4xl text-green-600"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Order Placed Successfully!</h1>
            <p class="text-gray-600 mb-6">Thank you for your order. We've received your order and will process it soon.</p>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-left">
                        <span class="text-gray-500">Order Number</span>
                        <p class="font-semibold text-lg">{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-gray-500">Total Amount</span>
                        <p class="font-semibold text-lg text-orange-600">₹{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                    <div class="text-left">
                        <span class="text-gray-500">Payment Method</span>
                        <p class="font-medium">{{ ucfirst($order->payment_method) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-gray-500">Payment Status</span>
                        <p class="font-medium">
                            <span class="px-2 py-1 rounded text-xs {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="border rounded-lg overflow-hidden mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Product</th>
                            <th class="px-4 py-2 text-center">Qty</th>
                            <th class="px-4 py-2 text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-4 py-2 text-left">{{ $item->product_name }}</td>
                                <td class="px-4 py-2 text-center">{{ $item->quantity }}</td>
                                <td class="px-4 py-2 text-right">₹{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Shipping Address -->
            <div class="text-left bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold mb-2">Shipping Address</h3>
                <p class="text-gray-600">
                    {{ $order->customer_name }}<br>
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
                    Phone: {{ $order->customer_phone }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('tracking.show', $order) }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Track Order
                </a>
                <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-semibold">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
