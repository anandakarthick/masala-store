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
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Product</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-gray-500">Quantity</th>
                                <th class="px-6 py-3 text-right text-sm font-medium text-gray-500">Total</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($cart->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                                @if($item->product->primary_image_url)
                                                    <img src="{{ $item->product->primary_image_url }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ route('products.show', $item->product->slug) }}" 
                                                   class="font-medium text-gray-800 hover:text-orange-600">
                                                    {{ $item->product->name }}
                                                </a>
                                                <p class="text-sm text-gray-500">{{ $item->product->weight_display }}</p>
                                                <p class="text-sm text-orange-600 font-medium">
                                                    ₹{{ number_format($item->product->effective_price, 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('cart.update') }}" method="POST" class="flex items-center justify-center">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" 
                                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100" {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <span class="px-3 py-1">{{ $item->quantity }}</span>
                                                <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" 
                                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium">
                                        ₹{{ number_format($item->total, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-between">
                    <a href="{{ route('products.index') }}" class="text-orange-600 hover:text-orange-700">
                        <i class="fas fa-arrow-left mr-1"></i> Continue Shopping
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash mr-1"></i> Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ $cart->total_items }} items)</span>
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
                    </div>

                    <div class="border-t mt-4 pt-4">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-orange-600">
                                ₹{{ number_format($cart->subtotal + $cart->gst_amount + ($cart->subtotal >= 500 ? 0 : 50), 2) }}
                            </span>
                        </div>
                    </div>

                    @if($cart->subtotal < 500)
                        <p class="text-sm text-gray-500 mt-2">
                            Add ₹{{ number_format(500 - $cart->subtotal, 2) }} more for free shipping!
                        </p>
                    @endif

                    <a href="{{ route('checkout.index') }}" 
                       class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center py-3 rounded-lg font-semibold mt-6">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h2>
            <p class="text-gray-500 mb-6">Looks like you haven't added any products yet.</p>
            <a href="{{ route('products.index') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold">
                Start Shopping
            </a>
        </div>
    @endif
</div>
@endsection
