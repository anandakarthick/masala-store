@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
@endphp

@section('title', 'Search Results for "' . $search . '"')
@section('meta_description', 'Search results for ' . $search . ' at ' . $businessName . '. Find homemade masala, spices, and herbal products.')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
                <a href="{{ route('home') }}" class="hover:text-green-600">Home</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li>
                <a href="{{ route('products.index') }}" class="hover:text-green-600">Products</a>
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li class="text-gray-800">Search Results</li>
        </ol>
    </nav>

    <!-- Search Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-search text-green-600 mr-2"></i>
                    Search Results for "{{ $search }}"
                </h1>
                <p class="text-sm text-gray-500 mt-1">{{ $products->total() }} products found</p>
            </div>
            
            <!-- Search Form -->
            <form action="{{ route('products.search') }}" method="GET" class="w-full md:w-auto">
                <div class="flex">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search products..." 
                           class="flex-1 md:w-64 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-green-500 focus:border-green-500">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-r-lg">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($products->count() > 0)
        <!-- Products Grid - 6 per row -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            @foreach($products as $product)
                @include('frontend.partials.product-card', ['product' => $product])
            @endforeach
        </div>

        <!-- Pagination -->
        <nav class="mt-6" aria-label="Pagination">
            {{ $products->withQueryString()->links() }}
        </nav>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-search text-4xl text-gray-300"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-600 mb-2">No products found</h2>
            <p class="text-gray-500 mb-6">We couldn't find any products matching "{{ $search }}"</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                    <i class="fas fa-shopping-bag"></i> Browse All Products
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium">
                    <i class="fas fa-home"></i> Go Home
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
