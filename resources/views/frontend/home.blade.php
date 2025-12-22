@extends('layouts.app')

@php
    $companyName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
    $tagline = \App\Models\Setting::get('business_tagline', 'Premium Masala, Oils & Herbal Products');
    $siteUrl = config('app.url', url('/'));
@endphp

@section('title', 'Buy Homemade Masala, Spices & Herbal Products Online')
@section('meta_description', $companyName . ' - Buy premium quality homemade masala powders, Indian spices, turmeric, coriander, garam masala, herbal oils & natural products online. 100% pure, chemical-free. Free delivery on orders above ₹500.')
@section('meta_keywords', 'buy masala online, homemade spices, turmeric powder, coriander powder, cumin powder, garam masala, kashmiri chilli powder, cardamom powder, herbal products, ayurvedic oils, hair growth oil, natural products India, organic spices Chennai')

@push('scripts')
<!-- Homepage ItemList Schema for Featured Products -->
@if($featuredProducts->count() > 0)
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Featured Products',
    'description' => 'Featured homemade masala and herbal products from ' . $companyName,
    'numberOfItems' => $featuredProducts->count(),
    'itemListElement' => $featuredProducts->map(function($product, $index) {
        return [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'url' => route('products.show', $product->slug),
            'name' => $product->name,
            'image' => $product->primary_image_url
        ];
    })->toArray()
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endif
@endpush

@push('styles')
<style>
    /* Auto-fit grid for categories */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 0.75rem;
    }
    @media (min-width: 640px) {
        .category-grid {
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        }
    }
    @media (min-width: 1024px) {
        .category-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        }
    }
    
    /* Flexible grid for products - shows all items in one row */
    .product-grid {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.75rem;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 0.5rem;
    }
    .product-grid > * {
        flex: 0 0 calc(50% - 0.375rem);
        min-width: 140px;
        max-width: 200px;
        scroll-snap-align: start;
    }
    @media (min-width: 640px) {
        .product-grid > * {
            flex: 0 0 calc(33.333% - 0.5rem);
        }
    }
    @media (min-width: 768px) {
        .product-grid > * {
            flex: 0 0 calc(25% - 0.5625rem);
        }
    }
    @media (min-width: 1024px) {
        .product-grid {
            flex-wrap: wrap;
            overflow-x: visible;
        }
        .product-grid > * {
            flex: 1 1 0;
            min-width: 150px;
            max-width: none;
        }
    }
    
    /* Hide scrollbar but keep functionality */
    .product-grid::-webkit-scrollbar {
        height: 4px;
    }
    .product-grid::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 2px;
    }
    .product-grid::-webkit-scrollbar-thumb {
        background: #16a34a;
        border-radius: 2px;
    }
</style>
@endpush

@section('content')
<!-- Hero Slider - 1920x600 Banner Size with Auto-slide -->
@if($banners->count() > 0)
    <section x-data="{ 
        current: 0, 
        banners: {{ $banners->count() }},
        autoSlideInterval: null,
        startAutoSlide() {
            this.autoSlideInterval = setInterval(() => {
                this.current = this.current === this.banners - 1 ? 0 : this.current + 1;
            }, 4000);
        },
        stopAutoSlide() {
            if (this.autoSlideInterval) {
                clearInterval(this.autoSlideInterval);
            }
        },
        goTo(index) {
            this.current = index;
            this.stopAutoSlide();
            this.startAutoSlide();
        },
        next() {
            this.current = this.current === this.banners - 1 ? 0 : this.current + 1;
            this.stopAutoSlide();
            this.startAutoSlide();
        },
        prev() {
            this.current = this.current === 0 ? this.banners - 1 : this.current - 1;
            this.stopAutoSlide();
            this.startAutoSlide();
        }
    }" 
    x-init="startAutoSlide()"
    @mouseenter="stopAutoSlide()"
    @mouseleave="startAutoSlide()"
    class="relative" 
    aria-label="Featured promotions">
        <div class="overflow-hidden">
            @foreach($banners as $index => $banner)
                <div x-show="current === {{ $index }}" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="relative"
                     style="aspect-ratio: 1920/600;">
                    @if($banner->link)
                        <a href="{{ $banner->link }}" class="block w-full h-full">
                            <img src="{{ $banner->image_url }}" 
                                 alt="{{ $banner->title }}" 
                                 class="w-full h-full object-cover"
                                 loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                        </a>
                    @else
                        <img src="{{ $banner->image_url }}" 
                             alt="{{ $banner->title }}" 
                             class="w-full h-full object-cover"
                             loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                    @endif
                </div>
            @endforeach
        </div>
        
        @if($banners->count() > 1)
            <button @click="prev()" 
                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2 md:p-3 shadow-lg transition"
                    aria-label="Previous slide">
                <i class="fas fa-chevron-left text-gray-800 text-lg" aria-hidden="true"></i>
            </button>
            <button @click="next()" 
                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/70 hover:bg-white rounded-full p-2 md:p-3 shadow-lg transition"
                    aria-label="Next slide">
                <i class="fas fa-chevron-right text-gray-800 text-lg" aria-hidden="true"></i>
            </button>
            
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2" role="tablist">
                @foreach($banners as $index => $banner)
                    <button @click="goTo({{ $index }})" 
                            :class="current === {{ $index }} ? 'bg-green-600 w-8' : 'bg-white/70 w-3'"
                            class="h-3 rounded-full transition-all duration-300 shadow"
                            role="tab"
                            aria-label="Slide {{ $index + 1 }}"
                            :aria-selected="current === {{ $index }}"></button>
                @endforeach
            </div>
        @endif
    </section>
@else
    <section class="bg-gradient-to-r from-green-600 to-green-800 text-white" style="aspect-ratio: 1920/600;" aria-label="Welcome banner">
        <div class="container mx-auto px-4 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl md:text-5xl font-bold mb-4">Welcome to {{ $companyName }}</h1>
                <p class="text-lg md:text-xl mb-6">{{ $tagline }}</p>
                <a href="{{ route('products.index') }}" class="bg-white text-green-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold text-base inline-block">
                    Shop Now
                </a>
            </div>
        </div>
    </section>
@endif

<!-- Categories Section - Auto-fit Grid -->
<section class="py-6 bg-white" aria-labelledby="categories-heading">
    <div class="container mx-auto px-4">
        <div class="text-center mb-5">
            <h2 id="categories-heading" class="text-xl md:text-2xl font-bold text-gray-800 mb-1">Shop by Category</h2>
            <p class="text-gray-500 text-xs">Explore our wide range of natural & homemade products</p>
        </div>
        
        <!-- Auto-fit grid that adjusts based on number of items -->
        <div class="category-grid">
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}" 
                   class="group relative overflow-hidden rounded-xl shadow hover:shadow-lg transition-all duration-300"
                   title="Shop {{ $category->name }}">
                    
                    <!-- Category Image -->
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
                                        'candle' => 'fa-fire',
                                        'gift' => 'fa-gift',
                                        'combo' => 'fa-box',
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
                    
                    <!-- Category Info -->
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

<!-- Featured Products - Auto-fit Grid -->
@if($featuredProducts->count() > 0)
    <section class="py-6 bg-gray-50" aria-labelledby="featured-heading">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-4">
                <h2 id="featured-heading" class="text-lg md:text-xl font-bold">Featured Products</h2>
                <a href="{{ route('products.index') }}?featured=1" class="text-green-600 hover:text-green-700 font-medium text-xs">
                    View All <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                </a>
            </div>
            <!-- Auto-fit grid that adjusts based on number of products -->
            <div class="product-grid">
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

<!-- New Arrivals - Auto-fit Grid -->
@if($newArrivals->count() > 0)
    <section class="py-6 bg-white" aria-labelledby="new-arrivals-heading">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-4">
                <h2 id="new-arrivals-heading" class="text-lg md:text-xl font-bold">New Arrivals</h2>
                <a href="{{ route('products.index') }}?sort=latest" class="text-green-600 hover:text-green-700 font-medium text-xs">
                    View All <i class="fas fa-arrow-right ml-1" aria-hidden="true"></i>
                </a>
            </div>
            <!-- Auto-fit grid that adjusts based on number of products -->
            <div class="product-grid">
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
