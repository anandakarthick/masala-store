@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Slider -->
@if($banners->count() > 0)
<div x-data="{ current: 0, banners: {{ $banners->count() }} }" class="relative">
    <div class="overflow-hidden">
        @foreach($banners as $index => $banner)
            <div x-show="current === {{ $index }}" 
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 transform translate-x-full"
                 x-transition:enter-end="opacity-100 transform translate-x-0"
                 class="relative h-64 md:h-96 bg-cover bg-center" 
                 style="background-image: url('{{ $banner->image_url }}')">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white px-4">
                        <h2 class="text-3xl md:text-5xl font-bold mb-4">{{ $banner->title }}</h2>
                        @if($banner->subtitle)
                            <p class="text-lg md:text-xl mb-6">{{ $banner->subtitle }}</p>
                        @endif
                        @if($banner->link)
                            <a href="{{ $banner->link }}" class="bg-orange-600 hover:bg-orange-700 text-white px-8 py-3 rounded-lg font-semibold">
                                {{ $banner->button_text ?? 'Shop Now' }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Slider Controls -->
    @if($banners->count() > 1)
        <button @click="current = current === 0 ? banners - 1 : current - 1" 
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-2">
            <i class="fas fa-chevron-left text-gray-800"></i>
        </button>
        <button @click="current = current === banners - 1 ? 0 : current + 1" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-2">
            <i class="fas fa-chevron-right text-gray-800"></i>
        </button>
        
        <!-- Dots -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            @foreach($banners as $index => $banner)
                <button @click="current = {{ $index }}" 
                        :class="current === {{ $index }} ? 'bg-orange-600' : 'bg-white bg-opacity-50'"
                        class="w-3 h-3 rounded-full"></button>
            @endforeach
        </div>
    @endif
</div>
@else
<!-- Default Hero -->
<div class="bg-gradient-to-r from-orange-500 to-red-600 text-white py-16 md:py-24">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Welcome to Masala Store</h1>
        <p class="text-xl mb-8">Premium Quality Spices, Oils, Candles & Return Gifts</p>
        <a href="{{ route('products.index') }}" class="bg-white text-orange-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold">
            Shop Now
        </a>
    </div>
</div>
@endif

<!-- Categories Section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">Shop by Category</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="group bg-gray-100 rounded-lg p-6 text-center hover:bg-orange-50 transition">
                    <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center group-hover:bg-orange-200 transition">
                        @if($category->slug === 'masala')
                            <i class="fas fa-pepper-hot text-2xl text-orange-600"></i>
                        @elseif($category->slug === 'oils')
                            <i class="fas fa-oil-can text-2xl text-orange-600"></i>
                        @elseif($category->slug === 'candles')
                            <i class="fas fa-fire text-2xl text-orange-600"></i>
                        @else
                            <i class="fas fa-gift text-2xl text-orange-600"></i>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-orange-600">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $category->active_products_count }} Products</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold">Featured Products</h2>
            <a href="{{ route('products.index') }}?featured=1" class="text-orange-600 hover:text-orange-700 font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Special Offer Banner -->
<section class="py-12 bg-gradient-to-r from-orange-600 to-red-600 text-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
                <h2 class="text-3xl font-bold mb-2">Free Shipping on Orders Above ₹500</h2>
                <p class="text-orange-100">Order now and get your products delivered to your doorstep for free!</p>
            </div>
            <a href="{{ route('products.index') }}" class="bg-white text-orange-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold">
                Shop Now
            </a>
        </div>
    </div>
</section>

<!-- New Arrivals -->
@if($newArrivals->count() > 0)
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl md:text-3xl font-bold">New Arrivals</h2>
            <a href="{{ route('products.index') }}?sort=latest" class="text-orange-600 hover:text-orange-700 font-medium">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($newArrivals as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-12">Why Choose Us</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-orange-600"></i>
                </div>
                <h3 class="font-semibold mb-2">Premium Quality</h3>
                <p class="text-gray-600 text-sm">100% pure and authentic products sourced from trusted suppliers</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-2xl text-orange-600"></i>
                </div>
                <h3 class="font-semibold mb-2">Fast Delivery</h3>
                <p class="text-gray-600 text-sm">Quick and reliable delivery to your doorstep</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-2xl text-orange-600"></i>
                </div>
                <h3 class="font-semibold mb-2">Best Prices</h3>
                <p class="text-gray-600 text-sm">Competitive prices with great value for money</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-headset text-2xl text-orange-600"></i>
                </div>
                <h3 class="font-semibold mb-2">24/7 Support</h3>
                <p class="text-gray-600 text-sm">Dedicated customer support for all your queries</p>
            </div>
        </div>
    </div>
</section>
@endsection
