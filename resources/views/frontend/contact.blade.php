@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
    $businessPhone = \App\Models\Setting::get('business_phone', '+919876543210');
    $businessEmail = \App\Models\Setting::get('business_email', 'support@svmasala.com');
    $businessAddress = \App\Models\Setting::get('business_address', 'Chennai, Tamil Nadu, India');
@endphp

@section('title', 'Contact Us - ' . $businessName)
@section('meta_description', 'Contact ' . $businessName . ' for inquiries about our homemade masala powders, spices, and herbal products. Call us at ' . $businessPhone . ' or email us.')
@section('meta_keywords', 'contact ' . $businessName . ', masala shop contact, spice seller Chennai, herbal products inquiry')

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Breadcrumb -->
    <nav class="mb-6" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="{{ route('home') }}" class="hover:text-green-600" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="text-gray-800">
                <span itemprop="name">Contact Us</span>
                <meta itemprop="position" content="2">
            </li>
        </ol>
    </nav>

    <header class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Contact Us</h1>
        <p class="text-lg text-gray-600">We'd love to hear from you. Get in touch with us!</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <!-- Contact Info -->
        <aside class="space-y-6" aria-label="Contact information">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-map-marker-alt text-green-600 text-xl" aria-hidden="true"></i>
                </div>
                <h2 class="font-semibold text-gray-800 mb-2">Address</h2>
                <address class="text-gray-600 not-italic">{{ $businessAddress }}</address>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-phone text-green-600 text-xl" aria-hidden="true"></i>
                </div>
                <h2 class="font-semibold text-gray-800 mb-2">Phone</h2>
                <a href="tel:{{ $businessPhone }}" class="text-gray-600 hover:text-green-600">{{ $businessPhone }}</a>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-envelope text-green-600 text-xl" aria-hidden="true"></i>
                </div>
                <h2 class="font-semibold text-gray-800 mb-2">Email</h2>
                <a href="mailto:{{ $businessEmail }}" class="text-gray-600 hover:text-green-600">{{ $businessEmail }}</a>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fab fa-whatsapp text-green-600 text-xl" aria-hidden="true"></i>
                </div>
                <h2 class="font-semibold text-gray-800 mb-2">WhatsApp</h2>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $businessPhone) }}" 
                   class="text-gray-600 hover:text-green-600"
                   target="_blank" 
                   rel="noopener noreferrer">
                    Chat with us on WhatsApp
                </a>
            </div>
        </aside>

        <!-- Contact Form -->
        <section class="lg:col-span-2" aria-labelledby="contact-form-heading">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 id="contact-form-heading" class="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h2>
                
                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                                   autocomplete="name">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                                   autocomplete="email">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                                   autocomplete="tel">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            @error('subject')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea id="message" name="message" rows="5" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold">
                        <i class="fas fa-paper-plane mr-2" aria-hidden="true"></i> Send Message
                    </button>
                </form>
            </div>
        </section>
    </div>

    <!-- FAQ Section -->
    <section class="mt-12 max-w-4xl mx-auto" aria-labelledby="faq-heading">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 id="faq-heading" class="text-2xl font-bold text-gray-800 mb-6">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-800">What are your delivery timelines?</h3>
                    <p class="text-gray-600 mt-1">We typically deliver within 3-5 business days for orders within India. Orders above ₹500 qualify for free delivery.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Are your products 100% natural?</h3>
                    <p class="text-gray-600 mt-1">Yes, all our products are 100% natural, homemade, and free from chemicals, preservatives, and artificial colors. We use only pure, traditional ingredients.</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">How can I track my order?</h3>
                    <p class="text-gray-600 mt-1">You can track your order using our <a href="{{ route('tracking.index') }}" class="text-green-600 hover:underline">order tracking page</a>. Simply enter your order number to see the current status.</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
