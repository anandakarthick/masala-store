@extends('layouts.app')

@section('title', $currentCategory ? $currentCategory->name : 'All Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-orange-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            @if($currentCategory)
                <li><a href="{{ route('products.index') }}" class="hover:text-orange-600">Products</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-800">{{ $currentCategory->name }}</li>
            @else
                <li class="text-gray-800">All Products</li>
            @endif
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-lg mb-4">Categories</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('products.index') }}" 
                           class="block py-1 {{ !$currentCategory ? 'text-orange-600 font-medium' : 'text-gray-600 hover:text-orange-600' }}">
                            All Products
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('category.show', $category->slug) }}" 
                               class="flex justify-between items-center py-1 {{ $currentCategory && $currentCategory->id === $category->id ? 'text-orange-600 font-medium' : 'text-gray-600 hover:text-orange-600' }}">
                                <span>{{ $category->name }}</span>
                                <span class="text-sm text-gray-400">({{ $category->active_products_count }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Price Filter -->
                <h3 class="font-semibold text-lg mt-6 mb-4">Price Range</h3>
                <form action="{{ route('products.index') }}" method="GET">
                    @if($currentCategory)
                        <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
                    @endif
                    <div class="space-y-3">
                        <input type="number" name="min_price" placeholder="Min Price" 
                               value="{{ request('min_price') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                        <input type="number" name="max_price" placeholder="Max Price" 
                               value="{{ request('max_price') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-orange-500 focus:border-orange-500">
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg text-sm">
                            Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ $currentCategory ? $currentCategory->name : 'All Products' }}
                    </h1>
                    <p class="text-gray-500">{{ $products->total() }} products found</p>
                </div>
                
                <!-- Sort -->
                <form action="" method="GET" class="flex items-center gap-2">
                    @if($currentCategory)
                        <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
                    @endif
                    <label class="text-sm text-gray-600">Sort by:</label>
                    <select name="sort" onchange="this.form.submit()" 
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Popularity</option>
                    </select>
                </form>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        @include('frontend.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
                    <p class="text-gray-500">Try adjusting your filters or search criteria</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
