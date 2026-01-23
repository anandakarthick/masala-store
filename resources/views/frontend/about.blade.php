@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
    $businessTagline = \App\Models\Setting::get('business_tagline', 'Premium Homemade Masala, Spices & Herbal Products');
@endphp

@section('title', 'About Us - ' . $businessName)
@section('meta_description', 'Learn about ' . $businessName . '. We are your trusted source for 100% pure, homemade masala powders, Indian spices, herbal products & ayurvedic oils. Chemical-free, preservative-free products made with love.')
@section('meta_keywords', 'about ' . $businessName . ', homemade masala company, Indian spices seller, natural herbal products, ayurvedic products Chennai, pure spices India')

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="{{ route('home') }}" class="hover:text-orange-600" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="text-gray-800">
                <span itemprop="name">About Us</span>
                <meta itemprop="position" content="2">
            </li>
        </ol>
    </nav>

    <article class="max-w-4xl mx-auto">
        <header class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">About {{ $businessName }}</h1>
            <p class="text-lg text-gray-600">{{ $businessTagline }}</p>
        </header>

        <section class="bg-white rounded-lg shadow-md p-8 mb-8" aria-labelledby="our-story">
    <h2 id="our-story" class="text-2xl font-bold text-gray-800 mb-4">Our Story</h2>

    <p class="text-gray-600 mb-4">
        Welcome to <strong>{{ $businessName }}</strong>, your trusted destination for authentic, homemade masala powders, premium Indian spices, and natural herbal products. We are passionate about bringing the finest, chemical-free products to your doorstep, made with traditional recipes passed down through generations.
    </p>

    <p class="text-gray-600 mb-4">
        Our journey began with a simple mission: to provide families with <strong>100% pure, preservative-free products</strong> that enhance their health and well-being. Every product we offer – from aromatic turmeric powder and coriander powder to nourishing hair growth oils and herbal bath powders – is freshly prepared in small batches to ensure maximum freshness and quality.
    </p>

    <p class="text-gray-600 mb-4">
        Based in <strong>Chennai, Tamil Nadu</strong>, we source the finest raw ingredients and process them using traditional methods. Our commitment to quality means you'll never find artificial colors, chemicals, or preservatives in any of our products.
    </p>

    <p class="text-sm text-gray-500 italic">
        Website developed and maintained by <strong>ANANDAKARTHICK S</strong>.
    </p>
</section>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <section class="bg-orange-50 rounded-lg p-6" aria-labelledby="our-mission">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-bullseye text-orange-500 text-xl" aria-hidden="true"></i>
                </div>
                <h3 id="our-mission" class="text-xl font-bold text-gray-800 mb-2">Our Mission</h3>
                <p class="text-gray-600">
                    To deliver the finest quality homemade products that enrich everyday life, while maintaining the highest standards of purity, authenticity, and natural goodness.
                </p>
            </section>
            <section class="bg-orange-50 rounded-lg p-6" aria-labelledby="our-vision">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-eye text-orange-500 text-xl" aria-hidden="true"></i>
                </div>
                <h3 id="our-vision" class="text-xl font-bold text-gray-800 mb-2">Our Vision</h3>
                <p class="text-gray-600">
                    To become the most trusted name for traditional homemade Indian products, reaching every household with quality, authenticity, and health-conscious choices.
                </p>
            </section>
        </div>

        <section class="bg-white rounded-lg shadow-md p-8 mb-8" aria-labelledby="why-choose-us">
            <h2 id="why-choose-us" class="text-2xl font-bold text-gray-800 mb-6">Why Choose {{ $businessName }}?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-leaf text-orange-500" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">100% Pure & Natural</h4>
                        <p class="text-gray-600 text-sm">No chemicals, preservatives, or artificial colors</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-home text-orange-500" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Homemade Fresh</h4>
                        <p class="text-gray-600 text-sm">Freshly ground in small batches for maximum flavor</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-shipping-fast text-orange-500" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Free Delivery</h4>
                        <p class="text-gray-600 text-sm">Free shipping on orders above ₹500</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-heart text-orange-500" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Made with Love</h4>
                        <p class="text-gray-600 text-sm">Traditional recipes from generations</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-certificate text-orange-500" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Quality Assured</h4>
                        <p class="text-gray-600 text-sm">Every product meets highest quality standards</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                        <i class="fas fa-headset text-orange-500" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800">Customer Support</h4>
                        <p class="text-gray-600 text-sm">Always here to help you</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-lg shadow-md p-8" aria-labelledby="our-products">
            <h2 id="our-products" class="text-2xl font-bold text-gray-800 mb-4">Our Products</h2>
            <p class="text-gray-600 mb-4">At {{ $businessName }}, we offer a wide range of homemade products:</p>
            <ul class="text-gray-600 space-y-2">
                <li><strong>Spices & Masalas:</strong> Turmeric Powder, Coriander Powder, Cumin Powder, Garam Masala, Kashmiri Chilli Powder, Cardamom Powder</li>
                <li><strong>Health & Millet Products:</strong> Ragi Powder, Black Urad Dal Powder, and other nutritious powders</li>
                <li><strong>Baby Care:</strong> Natural herbal bath powders safe for the whole family</li>
                <li><strong>Ayurvedic & Wellness:</strong> Hair Growth Oil, Knee Pain Relief Oil, and other herbal remedies</li>
            </ul>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold text-sm transition">
                    <i class="fas fa-shopping-bag mr-2" aria-hidden="true"></i> Browse Our Products
                </a>
            </div>
        </section>
    </article>
</div>
@endsection
