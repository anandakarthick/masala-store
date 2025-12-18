@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>

    @if($cart->items->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($cart->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                                @if($item->product->primary_image_url)
                                                    <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ route('products.show', $item->product->slug) }}" class="font-medium text-gray-800 hover:text-orange-600">
                                                    {{ $item->product->name }}
                                                </a>
                                                @if($item->variant)
                                                    <p class="text-sm text-orange-600 font-medium">{{ $item->variant->name }}</p>
                                                @endif
                                                <p class="text-sm text-gray-500">₹{{ number_format($item->unit_price, 2) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('cart.update') }}" method="POST" class="flex items-center justify-center">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                            @if($item->variant_id)
                                                <input type="hidden" name="variant_id" value="{{ $item->variant_id }}">
                                            @endif
                                            <button type="button" onclick="this.parentNode.querySelector('input[name=quantity]').stepDown(); this.parentNode.submit();"
                                                    class="w-8 h-8 rounded-l border border-gray-300 bg-gray-50 hover:bg-gray-100">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->stock_quantity }}"
                                                   class="w-12 h-8 text-center border-t border-b border-gray-300 focus:ring-0 focus:border-gray-300"
                                                   onchange="this.form.submit()">
                                            <button type="button" onclick="this.parentNode.querySelector('input[name=quantity]').stepUp(); this.parentNode.submit();"
                                                    class="w-8 h-8 rounded-r border border-gray-300 bg-gray-50 hover:bg-gray-100">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium">
                                        ₹{{ number_format($item->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('cart.remove') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                            @if($item->variant_id)
                                                <input type="hidden" name="variant_id" value="{{ $item->variant_id }}">
                                            @endif
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between mt-4">
                    <a href="{{ route('products.index') }}" class="text-orange-600 hover:text-orange-700">
                        <i class="fas fa-arrow-left mr-2"></i> Continue Shopping
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600" onclick="return confirm('Clear entire cart?')">
                            <i class="fas fa-trash mr-2"></i> Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ $cart->total_quantity }} items)</span>
                            <span class="font-medium">₹{{ number_format($cart->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">GST</span>
                            <span class="font-medium">₹{{ number_format($cart->gst_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            @if($cart->subtotal >= 500)
                                <span class="text-green-600 font-medium">FREE</span>
                            @else
                                <span class="font-medium">₹50.00</span>
                            @endif
                        </div>
                        
                        @if($cart->subtotal < 500)
                            <div class="bg-orange-50 text-orange-700 text-sm p-3 rounded-lg">
                                <i class="fas fa-info-circle mr-1"></i>
                                Add ₹{{ number_format(500 - $cart->subtotal, 2) }} more for FREE shipping!
                            </div>
                        @endif

                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-orange-600">
                                    ₹{{ number_format($cart->subtotal + $cart->gst_amount + ($cart->subtotal >= 500 ? 0 : 50), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon Code -->
                    <div class="mt-6">
                        <form action="{{ route('checkout.apply-coupon') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="coupon_code" placeholder="Coupon code" 
                                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                                Apply
                            </button>
                        </form>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center py-3 rounded-lg mt-6 font-semibold">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-shopping-cart text-6xl"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-lg font-semibold">
                Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection
