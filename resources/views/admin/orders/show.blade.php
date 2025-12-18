@extends('layouts.admin')

@section('title', 'Order Details')
@section('page_title', 'Order: ' . $order->order_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Order Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-medium">{{ $item->product_name }}</p>
                                    <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium">₹{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Subtotal:</td>
                            <td class="px-6 py-3 text-right font-medium">₹{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm text-green-600">Discount:</td>
                            <td class="px-6 py-3 text-right font-medium text-green-600">-₹{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">GST:</td>
                            <td class="px-6 py-3 text-right font-medium">₹{{ number_format($order->gst_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Shipping:</td>
                            <td class="px-6 py-3 text-right font-medium">₹{{ number_format($order->shipping_charge, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-lg font-bold">Total:</td>
                            <td class="px-6 py-3 text-right text-lg font-bold text-orange-600">₹{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Customer Details</h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Contact Information</h4>
                    <p class="text-gray-800">{{ $order->customer_name }}</p>
                    <p class="text-gray-600">{{ $order->customer_phone }}</p>
                    <p class="text-gray-600">{{ $order->customer_email ?? 'N/A' }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Shipping Address</h4>
                    <p class="text-gray-600">{{ $order->full_shipping_address }}</p>
                </div>
            </div>
            @if($order->customer_notes)
                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-medium text-gray-700 mb-2">Customer Notes</h4>
                    <p class="text-gray-600">{{ $order->customer_notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Order Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Order Status</h3>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-3 focus:ring-orange-500 focus:border-orange-500">
                    @foreach(['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Payment</h3>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Method:</span>
                    <span class="font-medium">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
            <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST">
                @csrf
                <select name="payment_status" class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-2 focus:ring-orange-500 focus:border-orange-500">
                    @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
                        <option value="{{ $status }}" {{ $order->payment_status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <input type="text" name="transaction_id" placeholder="Transaction ID" value="{{ $order->transaction_id }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-3 focus:ring-orange-500 focus:border-orange-500">
                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-lg">
                    Update Payment
                </button>
            </form>
        </div>

        <!-- Delivery -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Delivery</h3>
            <form action="{{ route('admin.orders.update-delivery', $order) }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <input type="text" name="delivery_partner" placeholder="Delivery Partner" value="{{ $order->delivery_partner }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <input type="text" name="tracking_number" placeholder="Tracking Number" value="{{ $order->tracking_number }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <input type="date" name="expected_delivery_date" value="{{ $order->expected_delivery_date?->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-lg">
                        Update Delivery
                    </button>
                </div>
            </form>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <a href="{{ route('admin.orders.invoice', $order) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-center mb-2">
                <i class="fas fa-file-pdf mr-2"></i> Download Invoice
            </a>
            <a href="{{ route('admin.orders.index') }}" class="block w-full bg-gray-200 text-gray-700 py-2 rounded-lg text-center">
                Back to Orders
            </a>
        </div>
    </div>
</div>
@endsection
