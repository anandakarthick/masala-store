@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">About Us</h1>
            <p class="text-lg text-gray-600">Your trusted source for premium quality masala, oils, candles & gifts</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Our Story</h2>
            <p class="text-gray-600 mb-4">
                Welcome to {{ \App\Models\Setting::businessName() }}, your one-stop destination for authentic, high-quality spices, oils, candles, and return gifts. We are passionate about bringing the finest products to your doorstep, sourced directly from trusted suppliers across India.
            </p>
            <p class="text-gray-600 mb-4">
                Our journey began with a simple mission: to provide families with pure, unadulterated products that enhance their daily lives. Whether it's the aromatic spices that make your dishes extraordinary, the pure oils that keep you healthy, or the beautiful candles and gifts that brighten special occasions - we ensure every product meets the highest standards of quality.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-orange-50 rounded-lg p-6">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bullseye text-orange-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Our Mission</h3>
                <p class="text-gray-600">
                    To deliver the finest quality products that enrich everyday life, while maintaining the highest standards of purity and authenticity.
                </p>
            </div>
            <div class="bg-orange-50 rounded-lg p-6">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-eye text-orange-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Our Vision</h3>
                <p class="text-gray-600">
                    To become the most trusted name for traditional Indian products, reaching every household with quality and authenticity.
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Why Choose Us?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">100% Pure Products</h4>
                        <p class="text-gray-600 text-sm">No adulteration, no compromise on quality</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Competitive Prices</h4>
                        <p class="text-gray-600 text-sm">Best value for premium quality</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Fast Delivery</h4>
                        <p class="text-gray-600 text-sm">Quick and reliable shipping</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Customer Support</h4>
                        <p class="text-gray-600 text-sm">Always here to help you</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
