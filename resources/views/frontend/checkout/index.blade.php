@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Customer Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('customer_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone', $user?->phone) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('customer_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Shipping Address</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                            <textarea name="shipping_address" rows="2" required
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">{{ old('shipping_address', $user?->address) }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', $user?->city) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                <input type="text" name="shipping_state" value="{{ old('shipping_state', $user?->state) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pincode *</label>
                                <input type="text" name="shipping_pincode" value="{{ old('shipping_pincode', $user?->pincode) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Type -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Order Type</h2>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="order_type" value="retail" checked class="text-orange-600 focus:ring-orange-500">
                            <span class="ml-2">Retail Order</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="order_type" value="bulk" class="text-orange-600 focus:ring-orange-500">
                            <span class="ml-2">Bulk Order</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="order_type" value="return_gift" class="text-orange-600 focus:ring-orange-500">
                            <span class="ml-2">Return Gift Order</span>
                        </label>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Payment Method</h2>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="cod" checked class="text-orange-600 focus:ring-orange-500">
                            <span class="ml-3 flex items-center">
                                <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                                Cash on Delivery (COD)
                            </span>
                        </label>
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="upi" class="text-orange-600 focus:ring-orange-500">
                            <span class="ml-3 flex items-center">
                                <i class="fas fa-mobile-alt text-purple-600 mr-2"></i>
                                UPI Payment
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Order Notes (Optional)</h2>
                    <textarea name="customer_notes" rows="3" placeholder="Any special instructions for your order..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">{{ old('customer_notes') }}</textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                    
                    <!-- Items -->
                    <div class="space-y-3 max-h-60 overflow-y-auto">
                        @foreach($cart->items as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded flex-shrink-0">
                                    @if($item->product->primary_image_url)
                                        <img src="{{ $item->product->primary_image_url }}" alt="" class="w-full h-full object-cover rounded">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                </div>
                                <span class="text-sm font-medium">₹{{ number_format($item->total, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Coupon -->
                    <div class="border-t mt-4 pt-4">
                        @if(session('coupon'))
                            <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg mb-3">
                                <div>
                                    <span class="text-green-600 font-medium">{{ session('coupon')->code }}</span>
                                    <p class="text-xs text-green-600">{{ session('coupon')->name }}</p>
                                </div>
                                <form action="{{ route('checkout.remove-coupon') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('checkout.apply-coupon') }}" method="POST" class="flex gap-2 mb-3">
                                @csrf
                                <input type="text" name="coupon_code" placeholder="Coupon Code"
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm">
                                    Apply
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Totals -->
                    <div class="border-t mt-4 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>₹{{ number_format($cart->subtotal, 2) }}</span>
                        </div>
                        @if(session('coupon'))
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-₹{{ number_format(session('coupon')->calculateDiscount($cart->subtotal), 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">GST</span>
                            <span>₹{{ number_format($cart->gst_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            @if($shippingCharge == 0)
                                <span class="text-green-600">FREE</span>
                            @else
                                <span>₹{{ number_format($shippingCharge, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t mt-4 pt-4">
                        @php
                            $discount = session('coupon') ? session('coupon')->calculateDiscount($cart->subtotal) : 0;
                            $total = $cart->subtotal - $discount + $cart->gst_amount + $shippingCharge;
                        @endphp
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-orange-600">₹{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-semibold mt-6">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
