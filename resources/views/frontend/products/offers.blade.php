@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
@endphp

@section('title', 'Special Offers & Discounts - Best Deals on Masala & Herbal Products')
@section('meta_description', 'Grab the best deals on homemade masala, spices & herbal products. Up to 50% off on selected items. Limited time offers on ' . $businessName . '. Free delivery above ₹500.')
@section('meta_keywords', 'masala offers, spice discounts, herbal products sale, best deals, discount masala online, cheap spices India')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-red-600 via-orange-500 to-yellow-500 rounded-2xl p-6 md:p-10 mb-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 opacity-10">
            <i class="fas fa-percentage text-[200px] transform rotate-12"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-3">
                <span class="bg-white/20 backdrop-blur px-3 py-1 rounded-full text-sm font-medium animate-pulse">
                    🔥 Limited Time
                </span>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold mb-3">Special Offers & Discounts</h1>
            <p class="text-lg opacity-90 mb-4">Save big on premium homemade masala & herbal products!</p>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/20 backdrop-blur rounded-lg px-4 py-2">
                    <span class="text-2xl font-bold">{{ $products->total() }}</span>
                    <span class="text-sm opacity-90 ml-1">Products on Sale</span>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-lg px-4 py-2">
                    <span class="text-2xl font-bold">Up to 50%</span>
                    <span class="text-sm opacity-90 ml-1">Off</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('home') }}" class="hover:text-green-600">Home</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li class="text-red-600 font-medium">Offers</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        <aside class="lg:w-56 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-md p-4 sticky top-20">
                <h2 class="font-semibold text-base mb-3 flex items-center">
                    <i class="fas fa-filter text-red-500 mr-2"></i>
                    Filter Offers
                </h2>
                
                <div class="space-y-3">
                    <a href="{{ route('products.offers') }}" 
                       class="block py-2 px-3 rounded-lg {{ !request('category') ? 'bg-red-100 text-red-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-tags mr-2"></i> All Offers
                    </a>
                    
                    @foreach($categories as $category)
                        @php
                            $offerCount = \App\Models\Product::active()
                                ->where('category_id', $category->id)
                                ->whereNotNull('discount_price')
                                ->where('discount_price', '>', 0)
                                ->whereColumn('discount_price', '<', 'price')
                                ->count();
                        @endphp
                        @if($offerCount > 0)
                            <a href="{{ route('products.offers', ['category' => $category->slug]) }}" 
                               class="flex justify-between items-center py-2 px-3 rounded-lg {{ request('category') === $category->slug ? 'bg-red-100 text-red-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                <span>{{ $category->name }}</span>
                                <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">{{ $offerCount }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>

                <!-- Discount Range -->
                <h3 class="font-semibold text-sm mt-6 mb-3 text-gray-700">Discount Range</h3>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('products.offers', ['min_discount' => 10]) }}" class="block py-1.5 px-3 rounded hover:bg-gray-50 text-gray-600">
                        10% & Above
                    </a>
                    <a href="{{ route('products.offers', ['min_discount' => 20]) }}" class="block py-1.5 px-3 rounded hover:bg-gray-50 text-gray-600">
                        20% & Above
                    </a>
                    <a href="{{ route('products.offers', ['min_discount' => 30]) }}" class="block py-1.5 px-3 rounded hover:bg-gray-50 text-gray-600">
                        30% & Above
                    </a>
                    <a href="{{ route('products.offers', ['min_discount' => 50]) }}" class="block py-1.5 px-3 rounded hover:bg-gray-50 text-gray-600">
                        50% & Above
                    </a>
                </div>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Header & Sort -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-fire text-orange-500 mr-2"></i>
                        {{ $products->total() }} Hot Deals
                    </h2>
                    <p class="text-sm text-gray-500">Grab these offers before they're gone!</p>
                </div>
                
                <form action="" method="GET" class="flex items-center gap-2">
                    <label for="sort" class="text-sm text-gray-600">Sort:</label>
                    <select id="sort" name="sort" onchange="this.form.submit()" 
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-red-500 focus:border-red-500">
                        <option value="discount" {{ request('sort') === 'discount' || !request('sort') ? 'selected' : '' }}>Biggest Discount</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                    </select>
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                    @foreach($products as $product)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300 relative">
                            <!-- Discount Badge -->
                            @if($product->discount_percentage > 0)
                                <div class="absolute top-2 left-2 z-10">
                                    <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg animate-pulse">
                                        {{ round($product->discount_percentage) }}% OFF
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Product Image -->
                            <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden">
                                <div class="aspect-square bg-gray-100">
                                    @if($product->primary_image_url)
                                        <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-image text-4xl"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Quick View Overlay -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium">
                                        View Details
                                    </span>
                                </div>
                            </a>
                            
                            <!-- Product Info -->
                            <div class="p-3">
                                <p class="text-xs text-gray-500 mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 mb-2 group-hover:text-green-600 transition-colors">
                                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                                
                                <!-- Price -->
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-lg font-bold text-green-600">₹{{ number_format($product->discount_price, 0) }}</span>
                                    <span class="text-sm text-gray-400 line-through">₹{{ number_format($product->price, 0) }}</span>
                                </div>
                                
                                <!-- Savings Badge -->
                                <div class="bg-green-50 border border-green-200 rounded-lg px-2 py-1 mb-3">
                                    <span class="text-xs text-green-700 font-medium">
                                        <i class="fas fa-piggy-bank mr-1"></i>
                                        You save ₹{{ number_format($product->price - $product->discount_price, 0) }}
                                    </span>
                                </div>
                                
                                <!-- Add to Cart -->
                                <button onclick="addToCart({{ $product->id }}, 1)" 
                                        class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <nav class="mt-8" aria-label="Pagination">
                    {{ $products->withQueryString()->links() }}
                </nav>
            @else
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tags text-4xl text-gray-300"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-600 mb-2">No Offers Available</h2>
                    <p class="text-gray-500 mb-6">Check back soon for exciting deals and discounts!</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                        <i class="fas fa-shopping-bag"></i> Browse All Products
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- SEO Content -->
    <section class="mt-12 bg-white rounded-xl shadow-md p-6">
        <h2 class="text-lg font-bold mb-3">Best Deals on Homemade Masala & Herbal Products</h2>
        <div class="text-gray-600 text-sm leading-relaxed space-y-2">
            <p>Discover amazing discounts on our premium range of homemade masala powders, spices, and herbal products at {{ $businessName }}. Our special offers bring you the best quality products at unbeatable prices.</p>
            <p>All products on sale are 100% pure, chemical-free, and made with traditional recipes. Don't miss out on these limited-time offers – stock up on your favorites and save big!</p>
        </div>
    </section>
</div>
@endsection
