@extends('layouts.app')

@section('title', 'Home')

@php
    $companyName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
    $tagline = \App\Models\Setting::get('business_tagline', 'Premium Masala, Oils & Herbal Products');
@endphp

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
                 class="relative h-48 md:h-72 lg:h-80 bg-cover bg-center" 
                 style="background-image: url('{{ $banner->image_url }}')">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                    <div class="text-center text-white px-4">
                        <h2 class="text-2xl md:text-4xl font-bold mb-3">{{ $banner->title }}</h2>
                        @if($banner->subtitle)
                            <p class="text-base md:text-lg mb-4">{{ $banner->subtitle }}</p>
                        @endif
                        @if($banner->link)
                            <a href="{{ $banner->link }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-sm">
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
                class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-1.5">
            <i class="fas fa-chevron-left text-gray-800"></i>
        </button>
        <button @click="current = current === banners - 1 ? 0 : current + 1" 
                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-1.5">
            <i class="fas fa-chevron-right text-gray-800"></i>
        </button>
        
        <!-- Dots -->
        <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-2">
            @foreach($banners as $index => $banner)
                <button @click="current = {{ $index }}" 
                        :class="current === {{ $index }} ? 'bg-green-600' : 'bg-white bg-opacity-50'"
                        class="w-2 h-2 rounded-full"></button>
            @endforeach
        </div>
    @endif
</div>
@else
<!-- Default Hero -->
<div class="bg-gradient-to-r from-green-600 to-green-800 text-white py-12 md:py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-3">Welcome to {{ $companyName }}</h1>
        <p class="text-lg mb-6">{{ $tagline }}</p>
        <a href="{{ route('products.index') }}" class="bg-white text-green-600 hover:bg-gray-100 px-6 py-2 rounded-lg font-semibold text-sm">
            Shop Now
        </a>
    </div>
</div>
@endif

<!-- Categories Section -->
<section class="py-8 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-xl md:text-2xl font-bold text-center mb-6">Shop by Category</h2>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-3 md:gap-4">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="group bg-gray-50 rounded-lg p-3 text-center hover:bg-green-50 transition">
                    <div class="w-10 h-10 md:w-12 md:h-12 mx-auto mb-2 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition">
                        @if($category->slug === 'masala')
                            <i class="fas fa-pepper-hot text-lg md:text-xl text-green-600"></i>
                        @elseif($category->slug === 'oils')
                            <i class="fas fa-oil-can text-lg md:text-xl text-green-600"></i>
                        @elseif($category->slug === 'candles')
                            <i class="fas fa-fire text-lg md:text-xl text-green-600"></i>
                        @elseif($category->slug === 'combo-masala')
                            <i class="fas fa-boxes text-lg md:text-xl text-green-600"></i>
                        @elseif($category->slug === 'combo-gift-pack')
                            <i class="fas fa-gift text-lg md:text-xl text-green-600"></i>
                        @else
                            <i class="fas fa-leaf text-lg md:text-xl text-green-600"></i>
                        @endif
                    </div>
                    <h3 class="font-medium text-gray-800 group-hover:text-green-600 text-xs md:text-sm">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500 hidden md:block">{{ $category->active_products_count ?? $category->products_count ?? 0 }} Products</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
<section class="py-8 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl md:text-2xl font-bold">Featured Products</h2>
            <a href="{{ route('products.index') }}?featured=1" class="text-green-600 hover:text-green-700 font-medium text-sm">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">
            @foreach($featuredProducts as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Special Offer Banner -->
<section class="py-8 bg-gradient-to-r from-green-600 to-green-800 text-white">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-4 md:mb-0 text-center md:text-left">
                <h2 class="text-xl md:text-2xl font-bold mb-1">Free Shipping on Orders Above ₹500</h2>
                <p class="text-green-100 text-sm">Order now and get your products delivered to your doorstep for free!</p>
            </div>
            <a href="{{ route('products.index') }}" class="bg-white text-green-600 hover:bg-gray-100 px-6 py-2 rounded-lg font-semibold text-sm">
                Shop Now
            </a>
        </div>
    </div>
</section>

<!-- New Arrivals -->
@if($newArrivals->count() > 0)
<section class="py-8 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl md:text-2xl font-bold">New Arrivals</h2>
            <a href="{{ route('products.index') }}?sort=latest" class="text-green-600 hover:text-green-700 font-medium text-sm">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">
            @foreach($newArrivals as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us -->
<section class="py-8 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-xl md:text-2xl font-bold text-center mb-8">Why Choose {{ $companyName }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
                <h3 class="font-semibold text-sm mb-1">Premium Quality</h3>
                <p class="text-gray-600 text-xs">100% pure and authentic products</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-xl text-green-600"></i>
                </div>
                <h3 class="font-semibold text-sm mb-1">Fast Delivery</h3>
                <p class="text-gray-600 text-xs">Quick delivery to your doorstep</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-xl text-green-600"></i>
                </div>
                <h3 class="font-semibold text-sm mb-1">Best Prices</h3>
                <p class="text-gray-600 text-xs">Competitive prices with great value</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-leaf text-xl text-green-600"></i>
                </div>
                <h3 class="font-semibold text-sm mb-1">Natural & Herbal</h3>
                <p class="text-gray-600 text-xs">Pure natural products for health</p>
            </div>
        </div>
    </div>
</section>
@endsection
