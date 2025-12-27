@extends('layouts.app')

@php
    $companyName = \App\Models\Setting::get('business_name', 'SV Products');
    $tagline = \App\Models\Setting::get('business_tagline', 'Premium Homemade Masala, Spices & Herbal Products');
    $siteUrl = config('app.url', url('/'));
@endphp

@section('title', 'Buy Homemade Masala Powder Online | Pure Indian Spices')
@section('meta_description', 'Buy premium homemade masala powder online at ' . $companyName . '. 100% pure & natural Indian spices - turmeric powder, coriander powder, garam masala, sambar powder, rasam powder. Chemical-free, traditional recipes. Free delivery above ₹500 across India.')
@section('meta_keywords', 'homemade masala, homemade masala powder, buy masala online, Indian spices online, pure turmeric powder, haldi powder, coriander powder, dhania powder, garam masala, sambar powder, rasam powder, natural spices, chemical-free masala, organic spices, traditional masala, authentic Indian spices, buy spices online India, homemade spice mix, pure masala powder Chennai, ' . $companyName)
@section('og_type', 'website')

@push('scripts')
<!-- Homepage ItemList Schema for Featured Products -->
@if($featuredProducts->count() > 0)
@php
    // Shipping details for all products
    $shippingDetails = [
        '@type' => 'OfferShippingDetails',
        'shippingRate' => [
            '@type' => 'MonetaryAmount',
            'value' => '0',
            'currency' => 'INR'
        ],
        'shippingDestination' => [
            '@type' => 'DefinedRegion',
            'addressCountry' => 'IN'
        ],
        'deliveryTime' => [
            '@type' => 'ShippingDeliveryTime',
            'handlingTime' => [
                '@type' => 'QuantitativeValue',
                'minValue' => 1,
                'maxValue' => 2,
                'unitCode' => 'DAY'
            ],
            'transitTime' => [
                '@type' => 'QuantitativeValue',
                'minValue' => 3,
                'maxValue' => 7,
                'unitCode' => 'DAY'
            ]
        ]
    ];
    
    // Merchant return policy
    $returnPolicy = [
        '@type' => 'MerchantReturnPolicy',
        'applicableCountry' => 'IN',
        'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
        'merchantReturnDays' => 7,
        'returnMethod' => 'https://schema.org/ReturnByMail',
        'returnFees' => 'https://schema.org/FreeReturn'
    ];
@endphp
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'Featured Products',
    'description' => 'Featured homemade masala and herbal products from ' . $companyName,
    'numberOfItems' => $featuredProducts->count(),
    'itemListElement' => $featuredProducts->map(function($product, $index) use ($siteUrl, $companyName, $shippingDetails, $returnPolicy) {
        $price = $product->discount_price ?? $product->price;
        if ($product->has_variants && $product->activeVariants->count() > 0) {
            $defaultVariant = $product->defaultVariant ?? $product->activeVariants->first();
            $price = $defaultVariant->discount_price ?? $defaultVariant->price;
        }
        
        $description = $product->short_description ?? '';
        if (empty($description) && $product->description) {
            $description = \Str::limit(strip_tags($product->description), 160);
        }
        
        return [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'item' => [
                '@type' => 'Product',
                '@id' => route('products.show', $product->slug),
                'name' => $product->name,
                'url' => route('products.show', $product->slug),
                'image' => $product->primary_image_url,
                'description' => $description ?: $product->name . ' - Buy online from ' . $companyName,
                'sku' => $product->sku ?? 'SKU-' . $product->id,
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $companyName
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'url' => route('products.show', $product->slug),
                    'priceCurrency' => 'INR',
                    'price' => number_format((float) $price, 2, '.', ''),
                    'availability' => $product->isOutOfStock() ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => $companyName,
                        'url' => $siteUrl
                    ],
                    'shippingDetails' => $shippingDetails,
                    'hasMerchantReturnPolicy' => $returnPolicy
                ]
            ]
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
        background: #F97316;
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
                            :class="current === {{ $index }} ? 'bg-orange-500 w-8' : 'bg-white/70 w-3'"
                            class="h-3 rounded-full transition-all duration-300 shadow"
                            role="tab"
                            aria-label="Slide {{ $index + 1 }}"
                            :aria-selected="current === {{ $index }}"></button>
                @endforeach
            </div>
        @endif
    </section>
@else
    <section class="bg-gradient-to-r from-orange-500 to-orange-600 text-white" style="aspect-ratio: 1920/600;" aria-label="Welcome banner">
        <div class="container mx-auto px-4 h-full flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-3xl md:text-5xl font-bold mb-4">Welcome to {{ $companyName }}</h1>
                <p class="text-lg md:text-xl mb-6">{{ $tagline }}</p>
                <a href="{{ route('products.index') }}" class="bg-white text-orange-600 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold text-base inline-block">
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
                        <h3 class="font-semibold text-xs md:text-sm leading-tight group-hover:text-orange-300 transition-colors line-clamp-1">
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
                <a href="{{ route('products.index') }}?featured=1" class="text-orange-600 hover:text-orange-700 font-medium text-xs">
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
<section class="py-5 bg-gradient-to-r from-orange-500 to-orange-600 text-white" aria-label="Special offer">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-3">
            <div class="text-center md:text-left">
                <h2 class="text-base md:text-lg font-bold mb-0.5">Free Shipping on Orders Above ₹500</h2>
                <p class="text-orange-100 text-xs">100% Pure & Natural | Homemade with Love | Chemical-Free</p>
            </div>
            <a href="{{ route('products.index') }}" class="bg-white text-orange-600 hover:bg-gray-100 px-5 py-1.5 rounded-lg font-semibold text-sm inline-block">
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
                <a href="{{ route('products.index') }}?sort=latest" class="text-orange-600 hover:text-orange-700 font-medium text-xs">
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
                <div class="w-10 h-10 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-lg text-orange-500" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-xs mb-0.5">100% Pure & Natural</h3>
                <p class="text-gray-600 text-[10px]">No chemicals or preservatives</p>
            </article>
            <article class="text-center bg-white p-3 rounded-lg shadow-sm">
                <div class="w-10 h-10 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-home text-lg text-orange-500" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-xs mb-0.5">Homemade Fresh</h3>
                <p class="text-gray-600 text-[10px]">Freshly ground in small batches</p>
            </article>
            <article class="text-center bg-white p-3 rounded-lg shadow-sm">
                <div class="w-10 h-10 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shipping-fast text-lg text-orange-500" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-xs mb-0.5">Fast Delivery</h3>
                <p class="text-gray-600 text-[10px]">Free shipping above ₹500</p>
            </article>
            <article class="text-center bg-white p-3 rounded-lg shadow-sm">
                <div class="w-10 h-10 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-heart text-lg text-orange-500" aria-hidden="true"></i>
                </div>
                <h3 class="font-semibold text-xs mb-0.5">Made with Love</h3>
                <p class="text-gray-600 text-[10px]">Traditional family recipes</p>
            </article>
        </div>
    </div>
</section>

<!-- SEO Content Section - Keyword Rich -->
<section class="py-8 bg-white" aria-labelledby="about-products-heading">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h2 id="about-products-heading" class="text-xl md:text-2xl font-bold mb-4 text-center">Premium Homemade Masala Powder Online - 100% Pure & Natural</h2>
            <div class="text-gray-600 text-sm leading-relaxed space-y-4">
                <p class="text-center">
                    Welcome to <strong>{{ $companyName }}</strong> - Your trusted destination for authentic <strong>homemade masala powder</strong> and pure <strong>Indian spices online</strong>. We bring you the finest quality traditional spices, freshly ground and packed to preserve their natural aroma and flavor.
                </p>
                
                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-mortar-pestle text-orange-500 mr-2"></i>Our Homemade Masala Range</h3>
                        <p>Discover our extensive collection of <strong>homemade masala powders</strong> including <strong>turmeric powder (haldi)</strong>, <strong>coriander powder (dhania)</strong>, <strong>red chilli powder</strong>, <strong>garam masala</strong>, <strong>sambar powder</strong>, <strong>rasam powder</strong>, and many more traditional South Indian spices. Each product is made using time-tested family recipes.</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-semibold text-gray-800 mb-2"><i class="fas fa-leaf text-green-500 mr-2"></i>Why Choose Our Spices?</h3>
                        <p>All our <strong>Indian spices</strong> are <strong>100% pure and natural</strong>, with no added chemicals, preservatives, or artificial colors. We source premium quality raw materials and grind them fresh in small batches to ensure maximum freshness and potency of flavors.</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg mt-4">
                    <h3 class="font-semibold text-gray-800 mb-2 text-center"><i class="fas fa-shipping-fast text-orange-500 mr-2"></i>Buy Masala Online with Free Delivery</h3>
                    <p class="text-center">
                        Shopping for <strong>masala powder online</strong> has never been easier! Enjoy <strong>free delivery on orders above ₹500</strong> across India. Whether you're looking for <strong>authentic South Indian masala</strong>, <strong>traditional garam masala</strong>, or <strong>pure turmeric powder</strong>, we deliver the finest homemade spices right to your doorstep.
                    </p>
                </div>
                
                <div class="text-center mt-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Popular Products</h3>
                    <div class="flex flex-wrap justify-center gap-2 text-xs">
                        <a href="{{ route('products.search') }}?q=turmeric" class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full hover:bg-yellow-200">Turmeric Powder</a>
                        <a href="{{ route('products.search') }}?q=coriander" class="bg-green-100 text-green-700 px-3 py-1 rounded-full hover:bg-green-200">Coriander Powder</a>
                        <a href="{{ route('products.search') }}?q=chilli" class="bg-red-100 text-red-700 px-3 py-1 rounded-full hover:bg-red-200">Chilli Powder</a>
                        <a href="{{ route('products.search') }}?q=garam+masala" class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full hover:bg-orange-200">Garam Masala</a>
                        <a href="{{ route('products.search') }}?q=sambar" class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full hover:bg-amber-200">Sambar Powder</a>
                        <a href="{{ route('products.search') }}?q=rasam" class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full hover:bg-rose-200">Rasam Powder</a>
                        <a href="{{ route('products.search') }}?q=cumin" class="bg-stone-100 text-stone-700 px-3 py-1 rounded-full hover:bg-stone-200">Cumin Powder</a>
                        <a href="{{ route('products.search') }}?q=pepper" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full hover:bg-gray-300">Black Pepper</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Schema Section for SEO -->
<section class="py-6 bg-gray-50" aria-labelledby="faq-heading">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <h2 id="faq-heading" class="text-lg md:text-xl font-bold mb-4 text-center">Frequently Asked Questions</h2>
            <div class="space-y-3" x-data="{ open: null }">
                <div class="bg-white rounded-lg shadow-sm">
                    <button @click="open = open === 1 ? null : 1" class="w-full px-4 py-3 text-left flex justify-between items-center">
                        <span class="font-medium text-sm">What makes your homemade masala different from market brands?</span>
                        <i class="fas" :class="open === 1 ? 'fa-minus' : 'fa-plus'" class="text-orange-500"></i>
                    </button>
                    <div x-show="open === 1" x-collapse class="px-4 pb-3 text-sm text-gray-600">
                        Our homemade masala powders are freshly ground in small batches using premium quality raw materials. We use traditional stone-grinding methods and family recipes passed down through generations. No chemicals, preservatives, or artificial colors are added.
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm">
                    <button @click="open = open === 2 ? null : 2" class="w-full px-4 py-3 text-left flex justify-between items-center">
                        <span class="font-medium text-sm">Do you deliver masala powder across India?</span>
                        <i class="fas" :class="open === 2 ? 'fa-minus' : 'fa-plus'" class="text-orange-500"></i>
                    </button>
                    <div x-show="open === 2" x-collapse class="px-4 pb-3 text-sm text-gray-600">
                        Yes! We deliver our homemade masala and spices across India. Enjoy free delivery on orders above ₹500. Orders are typically delivered within 3-7 business days depending on your location.
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm">
                    <button @click="open = open === 3 ? null : 3" class="w-full px-4 py-3 text-left flex justify-between items-center">
                        <span class="font-medium text-sm">How long do your masala powders stay fresh?</span>
                        <i class="fas" :class="open === 3 ? 'fa-minus' : 'fa-plus'" class="text-orange-500"></i>
                    </button>
                    <div x-show="open === 3" x-collapse class="px-4 pb-3 text-sm text-gray-600">
                        Our masala powders stay fresh for 6-12 months when stored properly in airtight containers away from direct sunlight and moisture. The manufacturing and expiry dates are clearly mentioned on each package.
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm">
                    <button @click="open = open === 4 ? null : 4" class="w-full px-4 py-3 text-left flex justify-between items-center">
                        <span class="font-medium text-sm">Are your spices suitable for South Indian cooking?</span>
                        <i class="fas" :class="open === 4 ? 'fa-minus' : 'fa-plus'" class="text-orange-500"></i>
                    </button>
                    <div x-show="open === 4" x-collapse class="px-4 pb-3 text-sm text-gray-600">
                        Absolutely! We specialize in authentic South Indian masala powders including sambar powder, rasam powder, and traditional Tamil Nadu style spice mixes. Our recipes are perfect for preparing authentic South Indian dishes.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<!-- FAQ Schema -->
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        [
            '@type' => 'Question',
            'name' => 'What makes your homemade masala different from market brands?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Our homemade masala powders are freshly ground in small batches using premium quality raw materials. We use traditional stone-grinding methods and family recipes passed down through generations. No chemicals, preservatives, or artificial colors are added.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Do you deliver masala powder across India?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Yes! We deliver our homemade masala and spices across India. Enjoy free delivery on orders above ₹500. Orders are typically delivered within 3-7 business days depending on your location.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'How long do your masala powders stay fresh?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Our masala powders stay fresh for 6-12 months when stored properly in airtight containers away from direct sunlight and moisture. The manufacturing and expiry dates are clearly mentioned on each package.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'Are your spices suitable for South Indian cooking?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'Absolutely! We specialize in authentic South Indian masala powders including sambar powder, rasam powder, and traditional Tamil Nadu style spice mixes. Our recipes are perfect for preparing authentic South Indian dishes.'
            ]
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush
@endsection
