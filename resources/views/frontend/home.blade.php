@extends('layouts.app')

@php
    $companyName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
    $tagline = \App\Models\Setting::get('business_tagline', 'Premium Masala, Oils & Herbal Products');
    $siteUrl = config('app.url', url('/'));
@endphp

@section('title', 'Buy Homemade Masala, Spices & Herbal Products Online')
@section('meta_description', $companyName . ' - Buy premium quality homemade masala powders, Indian spices, turmeric, coriander, garam masala, herbal oils & natural products online. 100% pure, chemical-free. Free delivery on orders above ₹500.')
@section('meta_keywords', 'buy masala online, homemade spices, turmeric powder, coriander powder, cumin powder, garam masala, kashmiri chilli powder, cardamom powder, herbal products, ayurvedic oils, hair growth oil, natural products India, organic spices Chennai')

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
                     class="relative h-40 md:h-56 lg:h-64 bg-cover bg-center" 
                     style="background-image: url('{{ $banner->image_url }}')"
                     role="img"
                     aria-label="{{ $banner->title }}">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                        <div class="text-center text-white px-4">
                            <h2 class="text-xl md:text-3xl font-bold mb-2">{{ $banner->title }}</h2>
                            @if($banner->subtitle)
                                <p class="text-sm md:text-base mb-3">{{ $banner->subtitle }}</p>
                            @endif
                            @if($banner->link)
                                <a href="{{ $banner->link }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-1.5 rounded-lg font-semibold text-sm inline-block">
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
                <i class="fas fa-chevron-left text-gray-800 text-sm" aria-hidden="true"></i>
            </button>
            <button @click="current = current === banners - 1 ? 0 : current + 1" 
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 hover:bg-opacity-75 rounded-full p-1.5"
                    aria-label="Next slide">
                <i class="fas fa-chevron-right text-gray-800 text-sm" aria-hidden="true"></i>
            </button>
            
            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-1.5" role="tablist">
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
    <section class="bg-gradient-to-r from-green-600 to-green-800 text-white py-8 md:py-10" aria-label="Welcome banner">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-2xl md:text-3xl font-bold mb-2">Welcome to {{ $companyName }}</h1>
            <p class="text-sm md:text-base mb-4">{{ $tagline }}</p>
            <a href="{{ route('products.index') }}" class="bg-white text-green-600 hover:bg-gray-100 px-5 py-1.5 rounded-lg font-semibold text-sm inline-block">
                Shop Now
            </a>
        </div>
    </section>
@endif

<!-- Categories Section - Compact -->
<section class="py-6 bg-white" aria-labelledby="categories-heading">
    <div class="container mx-auto px-4">
        <div class="text-center mb-5">
            <h2 id="categories-heading" class="text-xl md:text-2xl font-bold text-gray-800 mb-1">Shop by Category</h2>
            <p class="text-gray-500 text-xs">Explore our wide range of natural & homemade products</p>
        </div>
        
        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="group relative overflow-hidden rounded-xl shadow hover:shadow-lg transition-all duration-300"
                   title="Shop {{ $category->name }}">
                    
                    <!-- Category Image - Smaller -->
                    <div class="aspect-square relative">
                        @if($category->image_url)
                            <img src="{{ $category->image_url }}" 
                                 alt="{{ $category->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <!-- Default gradient background with icon -->
                            <div class="w-full h-full flex items-center justify-center {{ $loop->index % 4 == 0 ? 'bg-gradient-to-br from-green-400 to-green-600' : ($loop->index % 4 == 1 ? 'bg-gradient-to-br from-amber-400 to-orange-500' : ($loop->index % 4 == 2 ? 'bg-gradient-to-br from-pink-400 to-rose-500' : 'bg-gradient-to-br from-purple-400 to-indigo-500')) }}">
                                @php
                                    $icons = [
                                        'spice' => 'fa-pepper-hot',
                                        'masala' => 'fa-mortar-pestle',
                                        'health' => 'fa-heartbeat',
                                        'millet' => 'fa-seedling',
                                        'baby' => 'fa-baby',
                                        'ayurvedic' => 'fa-leaf',
                                        'wellness' => 'fa-spa',
                                        'oil' => 'fa-tint',
                                        'herbal' => 'fa-pagelines',
                                    ];
                                    $icon = 'fa-leaf';
                                    $categoryNameLower = strtolower($category->name);
                                    foreach($icons as $key => $iconClass) {
                                        if(str_contains($categoryNameLower, $key)) {
                                            $icon = $iconClass;
                                            break;
                                        }
                                    }
                                @endphp
                                <i class="fas {{ $icon }} text-white text-2xl md:text-3xl opacity-90" aria-hidden="true"></i>
                            </div>
                        @endif
                        
                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    </div>
                    
                    <!-- Category Info - Compact -->
                    <div class="absolute bottom-0 left-0 right-0 p-2 text-white text-center">
                        <h3 class="font-semibold text-xs md:text-sm leading-tight group-hover:text-green-300 transition-colors line-clamp-1">
                            {{ $category->name }}
                        </h3>
                        <p class="text-[10px] text-gray-300 mt-0.5">
                            {{ $category->active_products_count ?? $category->products_count ?? 0 }} Items
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($featuredProducts->count() > 0)
    <section class="py-6 bg-gray-50" aria-labelledby="featured-heading">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-4">
                <h2 id="featured-heading" class="text-lg md:text-xl font-bold">Featured Products</h2>
                <a href="{{ route('products.index') }}?featured=1" class="text-green-600 hover:text-green-700 font-medium text-xs">
                    View All <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($featuredProducts as $product)
                    @include('frontend.partials.product-card-compact', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>
@endif

<!-- USP Banner - Compact -->
<section class="py-5 bg-gradient-to-r from-green-600 to-green-800 text-white" aria-label="Special offer">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="text-center md:text-left">
                <h2 class="text-base md:text-lg font-bold mb-0.5">Free Shipping on Orders Above ₹500</h2>
                <p class="text-green-100 text-xs">100% Pure & Natural | Homemade with Love | Chemical-Free</p>
            </div>
            <a href="{{ route('products.index') }}" class="bg-white text-green-600 hover:bg-gray-100 px-5 py-1.5 rounded-lg font-semibold text-sm inline-block">
                Shop Now
            </a>
        </div>
    </div>
</section>

<!-- New Arrivals -->
@if($newArrivals->count() > 0)
    <section class="py-6 bg-white" aria-labelledby="new-arrivals-heading">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-4">
                <h2 id="new-arrivals-heading" class="text-lg md:text-xl font-bold">New Arrivals</h2>
                <a href="{{ route('products.index') }}?sort=latest" class="text-green-600 hover:text-green-700 font-medium text-xs">
                    View All <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($newArrivals as $product)
                    @include('frontend.partials.product-card-compact', ['product' => $product])
                @endforeach
            </div>
        </div>
    </section>
@endif

<!-- Why Choose Us - Compact -->
<section class="py-6 bg-gray-50" aria-labelledby="why-choose-heading">
    <div class="container mx-auto px-4">
        <h2 id="why-choose-heading" class="text-lg md:text-xl font-bold text-center mb-5">Why Choose {{ $companyName }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <article class="text-center bg-white p-3 rounded-lg shadow-sm">
                <div class="w-10 h-10 mx-auto mb-2 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-lg text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-xs mb-0.5">100% Pure & Natural</h3>
                <p class="text-gray-600 text-[10px]">No chemicals or preservatives</p>
            </article>
            <article class="text-center bg-white p-3 rounded-lg shadow-sm">
                <div class="w-10 h-10 mx-auto mb-2 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-home text-lg text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-xs mb-0.5">Homemade Fresh</h3>
                <p class="text-gray-600 text-[10px]">Freshly ground in small batches</p>
            </article>
            <article class="text-center bg-white p-3 rounded-lg shadow-sm">
                <div class="w-10 h-10 mx-auto mb-2 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-lg text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-xs mb-0.5">Fast Delivery</h3>
                <p class="text-gray-600 text-[10px]">Free shipping above ₹500</p>
            </article>
            <article class="text-center bg-white p-3 rounded-lg shadow-sm">
                <div class="w-10 h-10 mx-auto mb-2 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-heart text-lg text-green-600" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-xs mb-0.5">Made with Love</h3>
                <p class="text-gray-600 text-[10px]">Traditional family recipes</p>
            </article>
        </div>
    </div>
</section>

<!-- SEO Content Section -->
<section class="py-6 bg-white" aria-labelledby="about-products-heading">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h2 id="about-products-heading" class="text-lg md:text-xl font-bold mb-3">Premium Homemade Masala & Herbal Products</h2>
            <div class="text-gray-600 text-xs leading-relaxed space-y-2">
                <p>Welcome to <strong>{{ $companyName }}</strong>, your trusted destination for authentic homemade masala powders, Indian spices, and natural herbal products.</p>
                <p>Our products are freshly ground in small batches to preserve their natural aroma, flavor, and health benefits. All products made using traditional recipes with no preservatives or chemicals.</p>
            </div>
        </div>
    </div>
</section>
@endsection
