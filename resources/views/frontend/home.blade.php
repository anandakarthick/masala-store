@extends('layouts.app')

@php
    $companyName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
    $tagline = \App\Models\Setting::get('business_tagline', 'Premium Masala, Oils & Herbal Products');
    $siteUrl = config('app.url', url('/'));
@endphp

@section('title', 'Buy Homemade Masala, Spices & Herbal Products Online')
@section('meta_description', $companyName . ' - Buy premium quality homemade masala powders, Indian spices, turmeric, coriander, garam masala, herbal oils & natural products online. 100% pure, chemical-free. Free delivery on orders above ₹500.')
@section('meta_keywords', 'buy masala online, homemade spices, turmeric powder, coriander powder, cumin powder, garam masala, kashmiri chilli powder, cardamom powder, herbal products, ayurvedic oils, hair growth oil, natural products India, organic spices Chennai')

@section('og_type', 'website')
@section('og_title', $companyName . ' - Premium Homemade Masala & Herbal Products')
@section('og_description', 'Buy authentic homemade masala powders, Indian spices & herbal products. 100% pure and natural. Free delivery above ₹500.')

@section('content')
<!-- Hero Slider -->
@if($banners->count() > 0)
    <section x-data="{ current: 0, banners: {{ $banners->count() }} }" class="relative" aria-label="Featured promotions">
        <div class="overflow-hidden">
            @foreach($banners as $index => $banner)
                <div x-show="current === {{ $index }}" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 transform translate-x-full"
                     x-transition:enter-end="opacity-100 transform translate-x-0"
                     class="relative h-48 md:h-72 lg:h-80 bg-cover bg-center" 
                     style="background-image: url('{{ $banner->image_url }}')"
                     role="img"
                     aria-label="{{ $banner->title }}">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                        <div class="text-center text-white px-4">
                            <h2 class="text-2xl md:text-4xl font-bold mb-3">{{ $banner->title }}</h2>
                            @if($banner->subtitle)
                                <p class="text-base md:text-lg mb-4">{{ $banner->subtitle }}</p>
                            @endif
                            @if($banner->link)
                                <a href="{{ $banner->link }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-sm inline-block">
                                    {{ $banner->button_text ?? 'Shop Now' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($banners->count() > 1)
            <button @click="current = current === 0 ? banners - 1 : current - 1" 
                    class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-1.5"
                    aria-label="Previous slide">
                <i class="fas fa-chevron-left text-gray-800" aria-hidden="true"></i>
            </button>
            <button @click="current = current === banners - 1 ? 0 : current + 1" 
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-1.5"
                    aria-label="Next slide">
                <i class="fas fa-chevron-right text-gray-800" aria-hidden="true"></i>
            </button>
            
            <div class="absolute bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-2" role="tablist">
                @foreach($banners as $index => $banner)
                    <button @click="current = {{ $index }}" 
                            :class="current === {{ $index }} ? 'bg-green-600' : 'bg-white bg-opacity-50'"
                            class="w-2 h-2 rounded-full"
                            role="tab"
                            aria-label="Slide {{ $index + 1 }}"
                            :aria-selected="current === {{ $index }}"></button>
                @endforeach
            </div>
        @endif
    </section>
@else
    <!-- Default Hero -->
    <section class="bg-gradient-to-r from-green-600 to-green-800 text-white py-12 md:py-16" aria-label="Welcome banner">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">Welcome to {{ $companyName }}</h1>
            <p class="text-lg mb-6">{{ $tagline }}</p>
            <a href="{{ route('products.index') }}" class="bg-white text-green-600 hover:bg-gray-100 px-6 py-2 rounded-lg font-semibold text-sm inline-block">
                Shop Now
            </a>
        </div>
    </section>
@endif

<!-- Categories Section -->
<section class="py-8 bg-white" aria-labelledby="categories-heading">
    <div class="container mx-auto px-4">
        <h2 id="categories-heading" class="text-xl md:text-2xl font-bold text-center mb-6">Shop by Category</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 md:gap-4">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="group bg-gray-50 rounded-lg p-4 text-center hover:bg-green-50 hover:shadow-md transition"
                   title="Shop {{ $category->name }}">
                    <div class="w-12 h-12 md:w-14 md:h-14 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition">
                        <i class="fas fa-leaf text-xl md:text-2xl text-green-600" aria-hidden="true"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-green-600 text-sm md:text-base">{{ $category->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $category->active_products_count ?? $category->products_count ?? 0 }} Products</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
    <section class="py-8 bg-gray-50" aria-labelledby="featured-heading">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-5">
                <h2 id="featured-heading" class="text-xl md:text-2xl font-bold">Featured Products</h2>
                <a href="{{ route('products.index') }}?featured=1" class="text-green-600 hover:text-green-700 font-medium text-sm">
                    View All <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
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

<!-- USP Banner -->
<section class="py-8 bg-gradient-to-r from-green-600 to-green-800 text-white" aria-label="Special offer">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-4 md:mb-0 text-center md:text-left">
                <h2 class="text-xl md:text-2xl font-bold mb-1">Free Shipping on Orders Above ₹500</h2>
                <p class="text-green-100 text-sm">100% Pure & Natural | Homemade with Love | Chemical-Free Products</p>
            </div>
            <a href="{{ route('products.index') }}" class="bg-white text-green-600 hover:bg-gray-100 px-6 py-2 rounded-lg font-semibold text-sm inline-block">
                Shop Now
            </a>
        </div>
    </div>
</section>

<!-- New Arrivals -->
@if($newArrivals->count() > 0)
    <section class="py-8 bg-white" aria-labelledby="new-arrivals-heading">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-5">
                <h2 id="new-arrivals-heading" class="text-xl md:text-2xl font-bold">New Arrivals</h2>
                <a href="{{ route('products.index') }}?sort=latest" class="text-green-600 hover:text-green-700 font-medium text-sm">
                    View All <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
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
<section class="py-10 bg-gray-50" aria-labelledby="why-choose-heading">
    <div class="container mx-auto px-4">
        <h2 id="why-choose-heading" class="text-xl md:text-2xl font-bold text-center mb-8">Why Choose {{ $companyName }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <article class="text-center bg-white p-4 rounded-lg shadow-sm">
                <div class="w-14 h-14 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-sm mb-1">100% Pure & Natural</h3>
                <p class="text-gray-600 text-xs">No chemicals, preservatives or artificial colors</p>
            </article>
            <article class="text-center bg-white p-4 rounded-lg shadow-sm">
                <div class="w-14 h-14 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-home text-2xl text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-sm mb-1">Homemade Fresh</h3>
                <p class="text-gray-600 text-xs">Freshly ground in small batches</p>
            </article>
            <article class="text-center bg-white p-4 rounded-lg shadow-sm">
                <div class="w-14 h-14 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-2xl text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-sm mb-1">Fast Delivery</h3>
                <p class="text-gray-600 text-xs">Free shipping on orders above ₹500</p>
            </article>
            <article class="text-center bg-white p-4 rounded-lg shadow-sm">
                <div class="w-14 h-14 mx-auto mb-3 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-heart text-2xl text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-sm mb-1">Made with Love</h3>
                <p class="text-gray-600 text-xs">Traditional recipes passed down generations</p>
            </article>
        </div>
    </div>
</section>

<!-- SEO Content Section -->
<section class="py-8 bg-white" aria-labelledby="about-products-heading">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 id="about-products-heading" class="text-xl md:text-2xl font-bold mb-4">Premium Homemade Masala & Herbal Products</h2>
            <div class="text-gray-600 text-sm leading-relaxed space-y-3">
                <p>Welcome to <strong>{{ $companyName }}</strong>, your trusted destination for authentic homemade masala powders, Indian spices, and natural herbal products. We specialize in providing 100% pure, chemical-free spices including <strong>Turmeric Powder</strong>, <strong>Coriander Powder</strong>, <strong>Cumin Powder</strong>, <strong>Garam Masala</strong>, <strong>Kashmiri Chilli Powder</strong>, and <strong>Cardamom Powder</strong>.</p>
                <p>Our products are freshly ground in small batches to preserve their natural aroma, flavor, and health benefits. We also offer nutritious <strong>Ragi Powder</strong>, <strong>Black Urad Dal Powder</strong>, and natural <strong>Bath Powders</strong> for the whole family. For your wellness needs, explore our range of <strong>Ayurvedic oils</strong> including Hair Growth Oil and Knee Pain Relief Oil.</p>
                <p>All our products are made using traditional recipes with no preservatives, artificial colors, or chemicals. Experience the authentic taste of Indian spices delivered right to your doorstep with free shipping on orders above ₹500.</p>
            </div>
        </div>
    </div>
</section>
@endsection
