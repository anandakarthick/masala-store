@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
    $pageTitle = $currentCategory ? $currentCategory->name . ' - Buy Online' : 'All Products - Buy Masala & Herbal Products Online';
    $pageDescription = $currentCategory 
        ? ($currentCategory->meta_description ?? 'Buy ' . $currentCategory->name . ' online from ' . $businessName . '. 100% pure, homemade, chemical-free products. Free delivery above ₹500.')
        : 'Shop premium homemade masala powders, Indian spices, herbal products & ayurvedic oils. 100% pure and natural. Free delivery on orders above ₹500.';
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDescription)
@section('meta_keywords', $currentCategory 
    ? $currentCategory->name . ', buy ' . $currentCategory->name . ' online, homemade ' . $currentCategory->name . ', natural products, ' . $businessName
    : 'buy masala online, homemade spices, turmeric powder, coriander powder, garam masala, herbal products, ayurvedic oils, natural products India')

@if($currentCategory)
@section('canonical', route('category.show', $currentCategory->slug))
@else
@section('canonical', route('products.index'))
@endif

@section('structured_data')
<!-- BreadcrumbList Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "{{ route('home') }}"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "Products",
            "item": "{{ route('products.index') }}"
        }
        @if($currentCategory)
        ,{
            "@type": "ListItem",
            "position": 3,
            "name": "{{ $currentCategory->name }}",
            "item": "{{ route('category.show', $currentCategory->slug) }}"
        }
        @endif
    ]
}
</script>

<!-- ItemList Schema for Products -->
@if($products->count() > 0)
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "{{ $currentCategory ? $currentCategory->name : 'All Products' }}",
    "description": "{{ $pageDescription }}",
    "numberOfItems": {{ $products->total() }},
    "itemListElement": [
        @foreach($products as $index => $product)
        {
            "@type": "ListItem",
            "position": {{ ($products->currentPage() - 1) * $products->perPage() + $index + 1 }},
            "item": {
                "@type": "Product",
                "name": "{{ $product->name }}",
                "url": "{{ route('products.show', $product->slug) }}",
                "image": "{{ $product->primary_image_url ?? asset('images/no-image.jpg') }}",
                "description": "{{ Str::limit($product->short_description, 100) }}",
                "offers": {
                    "@type": "Offer",
                    "price": "{{ $product->effective_price }}",
                    "priceCurrency": "INR",
                    "availability": "{{ $product->isOutOfStock() ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock' }}"
                }
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif

@if($currentCategory)
<!-- CollectionPage Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "{{ $currentCategory->name }}",
    "description": "{{ $pageDescription }}",
    "url": "{{ route('category.show', $currentCategory->slug) }}",
    "isPartOf": {
        "@type": "WebSite",
        "name": "{{ $businessName }}",
        "url": "{{ config('app.url') }}"
    },
    "about": {
        "@type": "Thing",
        "name": "{{ $currentCategory->name }}"
    }
}
</script>
@endif
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="{{ route('home') }}" class="hover:text-green-600" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            @if($currentCategory)
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="{{ route('products.index') }}" class="hover:text-green-600" itemprop="item">
                        <span itemprop="name">Products</span>
                    </a>
                    <meta itemprop="position" content="2">
                </li>
                <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="text-gray-800">
                    <span itemprop="name">{{ $currentCategory->name }}</span>
                    <meta itemprop="position" content="3">
                </li>
            @else
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="text-gray-800">
                    <span itemprop="name">All Products</span>
                    <meta itemprop="position" content="2">
                </li>
            @endif
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Filters -->
        <aside class="lg:w-56 flex-shrink-0" aria-label="Product filters">
            <div class="bg-white rounded-lg shadow-md p-4">
                <h2 class="font-semibold text-base mb-3">Categories</h2>
                <nav aria-label="Category navigation">
                    <ul class="space-y-1 text-sm">
                        <li>
                            <a href="{{ route('products.index') }}" 
                               class="block py-1 {{ !$currentCategory ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600' }}">
                                All Products
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('category.show', $category->slug) }}" 
                                   class="flex justify-between items-center py-1 {{ $currentCategory && $currentCategory->id === $category->id ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600' }}"
                                   title="View {{ $category->name }} products">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-xs text-gray-400">({{ $category->active_products_count }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <!-- Price Filter -->
                <h2 class="font-semibold text-base mt-5 mb-3">Price Range</h2>
                <form action="{{ route('products.index') }}" method="GET" aria-label="Filter by price">
                    @if($currentCategory)
                        <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
                    @endif
                    <div class="space-y-2">
                        <label for="min_price" class="sr-only">Minimum Price</label>
                        <input type="number" id="min_price" name="min_price" placeholder="Min Price" 
                               value="{{ request('min_price') }}"
                               class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-green-500 focus:border-green-500">
                        <label for="max_price" class="sr-only">Maximum Price</label>
                        <input type="number" id="max_price" name="max_price" placeholder="Max Price" 
                               value="{{ request('max_price') }}"
                               class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-green-500 focus:border-green-500">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-1.5 rounded text-sm">
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        {{ $currentCategory ? $currentCategory->name : 'All Products' }}
                    </h1>
                    <p class="text-sm text-gray-500">{{ $products->total() }} products found</p>
                    @if($currentCategory && $currentCategory->description)
                        <p class="text-sm text-gray-600 mt-2 max-w-2xl">{{ Str::limit($currentCategory->description, 150) }}</p>
                    @endif
                </div>
                
                <!-- Sort -->
                <form action="" method="GET" class="flex items-center gap-2" aria-label="Sort products">
                    @if($currentCategory)
                        <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
                    @endif
                    <label for="sort" class="text-sm text-gray-600">Sort:</label>
                    <select id="sort" name="sort" onchange="this.form.submit()" 
                            class="border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Popularity</option>
                    </select>
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        @include('frontend.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination -->
                <nav class="mt-6" aria-label="Pagination">
                    {{ $products->withQueryString()->links() }}
                </nav>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4" aria-hidden="true"></i>
                    <h2 class="text-lg font-semibold text-gray-600 mb-2">No products found</h2>
                    <p class="text-gray-500 text-sm">Try adjusting your filters or search criteria</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 text-green-600 hover:text-green-700 text-sm font-medium">
                        <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i> View all products
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- SEO Content for Category Pages -->
    @if($currentCategory)
    <section class="mt-8 bg-white rounded-lg shadow-md p-6" aria-labelledby="category-info-heading">
        <h2 id="category-info-heading" class="text-lg font-bold mb-3">About {{ $currentCategory->name }}</h2>
        <div class="text-gray-600 text-sm leading-relaxed">
            @if($currentCategory->description)
                <p>{{ $currentCategory->description }}</p>
            @else
                <p>Explore our range of premium {{ strtolower($currentCategory->name) }} at {{ $businessName }}. All products are 100% pure, homemade, and free from chemicals and preservatives. We ensure the highest quality standards to bring you authentic, natural products that are good for your health and well-being.</p>
            @endif
            <p class="mt-2">Enjoy free delivery on orders above ₹500. Shop with confidence knowing that all our products are freshly prepared in small batches to maintain their natural aroma and flavor.</p>
        </div>
    </section>
    @endif
</div>
@endsection
