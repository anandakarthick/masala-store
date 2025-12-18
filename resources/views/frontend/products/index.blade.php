@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name : 'All Products')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-green-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            @if($currentCategory)
                <li><a href="{{ route('products.index') }}" class="hover:text-green-600">Products</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-800">{{ $currentCategory->name }}</li>
            @else
                <li class="text-gray-800">All Products</li>
            @endif
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar Filters -->
        <aside class="lg:w-56 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-semibold text-base mb-3">Categories</h3>
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
                               class="flex justify-between items-center py-1 {{ $currentCategory && $currentCategory->id === $category->id ? 'text-green-600 font-medium' : 'text-gray-600 hover:text-green-600' }}">
                                <span>{{ $category->name }}</span>
                                <span class="text-xs text-gray-400">({{ $category->active_products_count }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Price Filter -->
                <h3 class="font-semibold text-base mt-5 mb-3">Price Range</h3>
                <form action="{{ route('products.index') }}" method="GET">
                    @if($currentCategory)
                        <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
                    @endif
                    <div class="space-y-2">
                        <input type="number" name="min_price" placeholder="Min Price" 
                               value="{{ request('min_price') }}"
                               class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:ring-green-500 focus:border-green-500">
                        <input type="number" name="max_price" placeholder="Max Price" 
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
                </div>
                
                <!-- Sort -->
                <form action="" method="GET" class="flex items-center gap-2">
                    @if($currentCategory)
                        <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
                    @endif
                    <label class="text-sm text-gray-600">Sort:</label>
                    <select name="sort" onchange="this.form.submit()" 
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
                <div class="mt-6">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No products found</h3>
                    <p class="text-gray-500 text-sm">Try adjusting your filters or search criteria</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
