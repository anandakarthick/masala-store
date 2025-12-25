@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>

    {{-- First-Time Customer Offer Banner --}}
    @php
        $ftcService = \App\Services\FirstTimeCustomerService::class;
        $ftcEnabled = $ftcService::isEnabled();
        $ftcRemaining = $ftcService::getRemainingSlots();
        $ftcPercentage = $ftcService::getDiscountPercentage();
        $ftcMinOrder = $ftcService::getMinOrderAmount();
        $ftcMaxDiscount = $ftcService::getMaxDiscountAmount();
    @endphp
    @if($ftcEnabled && $ftcRemaining > 0)
        <div class="mb-6 bg-gradient-to-r from-orange-500 to-yellow-500 rounded-lg p-4 text-white shadow-lg">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-3xl">🎁</span>
                    <div>
                        <h3 class="font-bold text-lg">First-Time Customer Offer!</h3>
                        <p class="text-sm opacity-90">
                            Get <span class="font-bold text-xl">{{ $ftcPercentage }}% OFF</span> on your first order!
                            @if($ftcMinOrder > 0)
                                (Min. order ₹{{ number_format($ftcMinOrder, 0) }})
                            @endif
                            @if($ftcMaxDiscount > 0)
                                (Max ₹{{ number_format($ftcMaxDiscount, 0) }} discount)
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-center">
                    <span class="bg-white text-orange-600 font-bold px-4 py-2 rounded-full text-sm">
                        Only {{ $ftcRemaining }} slots left!
                    </span>
                    @if(!auth()->check())
                        <p class="text-xs mt-2 opacity-90">
                            <a href="{{ route('login') }}" class="underline font-semibold">Login</a> to avail this offer
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($cart->items->count() > 0 || $cart->customCombos->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Custom Combos Section -->
                @if($cart->customCombos->count() > 0)
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg shadow-md overflow-hidden border border-purple-200">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-3">
                            <h3 class="text-white font-semibold flex items-center">
                                <i class="fas fa-box-open mr-2"></i> Custom Combos
                            </h3>
                        </div>
                        <div class="divide-y divide-purple-100">
                            @foreach($cart->customCombos as $combo)
                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $combo->combo_name }}</h4>
                                            <p class="text-sm text-purple-600">{{ $combo->comboSetting->discount_display }}</p>
                                        </div>
                                        <form action="{{ route('cart.remove-combo') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="combo_id" value="{{ $combo->id }}">
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Combo Items -->
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        @foreach($combo->items as $item)
                                            <div class="flex items-center bg-white rounded-lg p-2 shadow-sm border">
                                                <div class="w-10 h-10 flex-shrink-0 bg-gray-100 rounded overflow-hidden mr-2">
                                                    @if($item->product->primary_image_url)
                                                        <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="text-xs">
                                                    <p class="font-medium text-gray-800 truncate max-w-24">{{ $item->product->name }}</p>
                                                    @if($item->variant)
                                                        <p class="text-purple-600">{{ $item->variant->name }}</p>
                                                    @endif
                                                    <p class="text-gray-500">₹{{ number_format($item->unit_price, 0) }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Combo Price & Quantity -->
                                    <div class="flex items-center justify-between bg-white rounded-lg p-3">
                                        <div class="flex items-center gap-4">
                                            <div class="text-sm">
                                                <span class="text-gray-500 line-through">₹{{ number_format($combo->calculated_price, 2) }}</span>
                                                <span class="text-lg font-bold text-orange-500 ml-2">₹{{ number_format($combo->final_price, 2) }}</span>
                                            </div>
                                            @if($combo->discount_amount > 0)
                                                <span class="bg-orange-100 text-orange-700 text-xs font-medium px-2 py-1 rounded-full">
                                                    Save ₹{{ number_format($combo->discount_amount, 0) }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Quantity -->
                                        <form action="{{ route('cart.update-combo') }}" method="POST" class="flex items-center">
                                            @csrf
                                            <input type="hidden" name="combo_id" value="{{ $combo->id }}">
                                            <button type="button" onclick="this.parentNode.querySelector('input[name=quantity]').stepDown(); this.parentNode.submit();"
                                                    class="w-8 h-8 rounded-l border border-gray-300 bg-gray-50 hover:bg-gray-100">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" name="quantity" value="{{ $combo->quantity }}" min="1" max="10"
                                                   class="w-12 h-8 text-center border-t border-b border-gray-300 focus:ring-0 focus:border-gray-300"
                                                   onchange="this.form.submit()">
                                            <button type="button" onclick="this.parentNode.querySelector('input[name=quantity]').stepUp(); this.parentNode.submit();"
                                                    class="w-8 h-8 rounded-r border border-gray-300 bg-gray-50 hover:bg-gray-100">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Regular Items -->
                @if($cart->items->count() > 0)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="bg-gray-50 px-6 py-3 border-b">
                            <h3 class="font-semibold text-gray-700">Products</h3>
                        </div>
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
                                                    <a href="{{ route('products.show', $item->product->slug) }}" class="font-medium text-gray-800 hover:text-orange-500">
                                                        {{ $item->product->name }}
                                                    </a>
                                                    @if($item->variant)
                                                        <p class="text-sm text-orange-500 font-medium">{{ $item->variant->name }}</p>
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
                @endif

                <div class="flex flex-wrap justify-between gap-4">
                    <div class="flex gap-4">
                        <a href="{{ route('products.index') }}" class="text-orange-500 hover:text-orange-600">
                            <i class="fas fa-arrow-left mr-2"></i> Continue Shopping
                        </a>
                        <a href="{{ route('combo.index') }}" class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-box-open mr-2"></i> Build a Combo
                        </a>
                    </div>
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
                        @if($cart->items->count() > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Products Subtotal</span>
                                <span class="font-medium">₹{{ number_format($cart->items_subtotal, 2) }}</span>
                            </div>
                        @endif
                        
                        @if($cart->customCombos->count() > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Combos Subtotal</span>
                                <span class="font-medium">₹{{ number_format($cart->combos_subtotal, 2) }}</span>
                            </div>
                            @if($cart->combo_savings > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Combo Savings</span>
                                    <span class="font-medium">-₹{{ number_format($cart->combo_savings, 2) }}</span>
                                </div>
                            @endif
                        @endif
                        
                        <div class="flex justify-between border-t pt-2">
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
                                <span class="text-orange-500 font-medium">FREE</span>
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

                        {{-- First-Time Customer Discount Preview --}}
                        @if($ftcEnabled && $ftcRemaining > 0)
                            @php
                                $isFirstTime = auth()->check() ? $ftcService::isFirstTimeCustomer() : false;
                                $potentialDiscount = ($cart->subtotal * $ftcPercentage) / 100;
                                if ($ftcMaxDiscount > 0 && $potentialDiscount > $ftcMaxDiscount) {
                                    $potentialDiscount = $ftcMaxDiscount;
                                }
                                $meetsMinOrder = $ftcMinOrder <= 0 || $cart->subtotal >= $ftcMinOrder;
                            @endphp
                            @if(auth()->check() && $isFirstTime && $meetsMinOrder)
                                <div class="bg-orange-50 border border-orange-200 text-orange-700 text-sm p-3 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">🎉</span>
                                        <div>
                                            <p class="font-semibold">First-Time Discount Applied!</p>
                                            <p class="text-xs">You'll save ₹{{ number_format($potentialDiscount, 2) }} at checkout</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif(auth()->check() && $isFirstTime && !$meetsMinOrder)
                                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm p-3 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">💡</span>
                                        <div>
                                            <p class="font-semibold">Almost there!</p>
                                            <p class="text-xs">Add ₹{{ number_format($ftcMinOrder - $cart->subtotal, 2) }} more to get {{ $ftcPercentage }}% off!</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif(!auth()->check())
                                <div class="bg-blue-50 border border-blue-200 text-blue-700 text-sm p-3 rounded-lg">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">👤</span>
                                        <div>
                                            <p class="font-semibold">New here?</p>
                                            <p class="text-xs"><a href="{{ route('login') }}" class="underline font-medium">Login</a> to get {{ $ftcPercentage }}% OFF on your first order!</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="border-t pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-orange-500">
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

                    <a href="{{ route('checkout.index') }}" class="block w-full bg-orange-500 hover:bg-orange-600 text-white text-center py-3 rounded-lg mt-6 font-semibold transition">
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
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('products.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-shopping-bag mr-2"></i> Start Shopping
                </a>
                <a href="{{ route('combo.index') }}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-semibold">
                    <i class="fas fa-box-open mr-2"></i> Build a Combo
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
