@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
@endphp

@section('title', 'Build Your Own Combo - Create Custom Bundles')
@section('meta_description', 'Create your own custom combo packs at ' . $businessName . '. Choose your favorite products and save with bundle discounts. Free delivery on orders above ₹500.')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 via-pink-500 to-red-500 rounded-2xl p-6 md:p-10 mb-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 opacity-10">
            <i class="fas fa-box-open text-[200px] transform rotate-12"></i>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-3">
                <span class="bg-white/20 backdrop-blur px-3 py-1 rounded-full text-sm font-medium animate-pulse">
                    🎁 Special Feature
                </span>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold mb-3">Build Your Own Combo</h1>
            <p class="text-lg opacity-90 mb-4">Choose your favorite products and create custom bundles with exclusive discounts!</p>
            <div class="flex flex-wrap gap-4">
                <div class="bg-white/20 backdrop-blur rounded-lg px-4 py-2">
                    <span class="text-2xl font-bold">{{ $comboSettings->count() }}</span>
                    <span class="text-sm opacity-90 ml-1">Combo Options</span>
                </div>
                <div class="bg-white/20 backdrop-blur rounded-lg px-4 py-2">
                    <span class="text-2xl font-bold">Upto 20%</span>
                    <span class="text-sm opacity-90 ml-1">Savings</span>
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
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-purple-600 font-medium">Build Your Combo</li>
        </ol>
    </nav>

    @if($comboSettings->count() > 0)
        <!-- Combo Options Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($comboSettings as $combo)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden group hover:shadow-xl transition-all duration-300 border-2 border-transparent hover:border-purple-300">
                    <!-- Combo Image -->
                    <div class="relative h-48 bg-gradient-to-br from-purple-100 to-pink-100 overflow-hidden">
                        @if($combo->image_url)
                            <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-boxes text-6xl text-purple-300 mb-2"></i>
                                    <p class="text-purple-400 text-sm font-medium">{{ $combo->min_products }}-{{ $combo->max_products }} Products</p>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Discount Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="bg-gradient-to-r from-red-500 to-pink-500 text-white text-sm font-bold px-3 py-1 rounded-full shadow-lg">
                                {{ $combo->discount_display }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Combo Info -->
                    <div class="p-5">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-purple-600 transition-colors">
                            {{ $combo->name }}
                        </h3>
                        
                        @if($combo->description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $combo->description }}</p>
                        @endif
                        
                        <!-- Combo Rules -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check-circle text-green-500 w-5"></i>
                                <span>Select {{ $combo->min_products }} to {{ $combo->max_products }} products</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-tags text-purple-500 w-5"></i>
                                <span>{{ $combo->discount_display }} on total</span>
                            </div>
                            @if($combo->allow_same_product)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-clone text-blue-500 w-5"></i>
                                    <span>Same product multiple times allowed</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Start Building Button -->
                        <a href="{{ route('combo.builder', $combo->slug) }}" 
                           class="block w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white text-center py-3 rounded-lg font-semibold transition-all transform hover:scale-[1.02]">
                            <i class="fas fa-plus-circle mr-2"></i> Start Building
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Combos Available -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-box-open text-4xl text-purple-300"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-600 mb-2">No Combo Options Available</h2>
            <p class="text-gray-500 mb-6">Check back soon for exciting combo offers!</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                <i class="fas fa-shopping-bag"></i> Browse All Products
            </a>
        </div>
    @endif

    <!-- How It Works Section -->
    <section class="mt-12 bg-white rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold mb-6 text-center">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-purple-600">1</span>
                </div>
                <h3 class="font-semibold mb-2">Choose a Combo</h3>
                <p class="text-sm text-gray-600">Select from available combo options</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-pink-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-pink-600">2</span>
                </div>
                <h3 class="font-semibold mb-2">Pick Products</h3>
                <p class="text-sm text-gray-600">Select your favorite products</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-red-600">3</span>
                </div>
                <h3 class="font-semibold mb-2">Get Discount</h3>
                <p class="text-sm text-gray-600">Automatic discount applied</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-green-600">4</span>
                </div>
                <h3 class="font-semibold mb-2">Checkout</h3>
                <p class="text-sm text-gray-600">Add to cart and checkout</p>
            </div>
        </div>
    </section>
</div>
@endsection
