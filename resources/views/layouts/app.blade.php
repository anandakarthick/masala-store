<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @php
        $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
        $businessTagline = \App\Models\Setting::get('business_tagline', 'Premium Homemade Masala, Spices & Herbal Products');
        $businessPhone = \App\Models\Setting::get('business_phone', '+919876543210');
        $businessEmail = \App\Models\Setting::get('business_email', 'support@svmasala.com');
        $businessAddress = \App\Models\Setting::get('business_address', 'Chennai, Tamil Nadu, India');
        $siteUrl = config('app.url', url('/'));
    @endphp
    
    <!-- Primary Meta Tags -->
    <title>@yield('title', 'Home') - {{ $businessName }}</title>
    <meta name="title" content="@yield('title', 'Home') - {{ $businessName }}">
    <meta name="description" content="@yield('meta_description', $businessTagline . '. Buy authentic Indian spices, turmeric powder, coriander powder, garam masala, herbal oils & natural products online.')">
    <meta name="keywords" content="@yield('meta_keywords', 'homemade masala, Indian spices, turmeric powder, coriander powder, cumin powder, garam masala, kashmiri chilli, herbal products, ayurvedic oils, natural products, buy spices online, organic spices')">
    <meta name="author" content="{{ $businessName }}">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="language" content="English">
    <meta name="revisit-after" content="7 days">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:title" content="@yield('og_title', View::yieldContent('title', 'Home') . ' - ' . $businessName)">
    <meta property="og:description" content="@yield('og_description', View::yieldContent('meta_description', $businessTagline))">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:site_name" content="{{ $businessName }}">
    <meta property="og:locale" content="en_IN">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="@yield('og_url', url()->current())">
    <meta name="twitter:title" content="@yield('og_title', View::yieldContent('title', 'Home') . ' - ' . $businessName)">
    <meta name="twitter:description" content="@yield('og_description', View::yieldContent('meta_description', $businessTagline))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    
    <!-- Geo Tags for Local SEO -->
    <meta name="geo.region" content="IN-TN">
    <meta name="geo.placename" content="Chennai">
    <meta name="geo.position" content="13.0827;80.2707">
    <meta name="ICBM" content="13.0827, 80.2707">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#16a34a">
    <meta name="msapplication-TileColor" content="#16a34a">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- JSON-LD Structured Data -->
    @yield('structured_data')
    
    <!-- Default Organization Schema -->
    @if(!View::hasSection('structured_data'))
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $businessName }}",
        "url": "{{ $siteUrl }}",
        "logo": "{{ \App\Models\Setting::logo() ?? asset('images/logo.png') }}",
        "description": "{{ $businessTagline }}",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Chennai",
            "addressRegion": "Tamil Nadu",
            "addressCountry": "IN"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ $businessPhone }}",
            "contactType": "customer service",
            "email": "{{ $businessEmail }}",
            "availableLanguage": ["English", "Tamil", "Hindi"]
        },
        "sameAs": [
            "{{ \App\Models\Setting::get('facebook_url', '') }}",
            "{{ \App\Models\Setting::get('instagram_url', '') }}",
            "{{ \App\Models\Setting::get('twitter_url', '') }}"
        ]
    }
    </script>
    
    <!-- WebSite Schema for Sitelinks Search Box -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ $businessName }}",
        "url": "{{ $siteUrl }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ route('products.search') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <!-- Local Business Schema -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "{{ $siteUrl }}/#business",
        "name": "{{ $businessName }}",
        "image": "{{ \App\Models\Setting::logo() ?? asset('images/logo.png') }}",
        "description": "{{ $businessTagline }}. We offer premium quality homemade masala powders, spices, herbal products, and ayurvedic oils.",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ $businessAddress }}",
            "addressLocality": "Chennai",
            "addressRegion": "Tamil Nadu",
            "postalCode": "600001",
            "addressCountry": "IN"
        },
        "telephone": "{{ $businessPhone }}",
        "email": "{{ $businessEmail }}",
        "url": "{{ $siteUrl }}",
        "priceRange": "₹₹",
        "openingHours": "Mo-Sa 09:00-21:00",
        "paymentAccepted": ["Cash", "UPI", "Credit Card", "Debit Card", "Net Banking"],
        "currenciesAccepted": "INR"
    }
    </script>
    @endif
    
    <style>
        [x-cloak] { display: none !important; }
        .toast-enter { animation: toastIn 0.3s ease-out; }
        .toast-leave { animation: toastOut 0.3s ease-in forwards; }
        @keyframes toastIn { from { transform: translateY(-100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes toastOut { from { transform: translateY(0); opacity: 1; } to { transform: translateY(-100%); opacity: 0; } }
        
        /* Line clamp for product names */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Hide number input spinners */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="number"] {
            -moz-appearance: textfield;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col" x-data="cartManager()" x-init="init()">
    <!-- Toast Notification -->
    <div x-show="toast.show" x-cloak
         x-transition:enter="toast-enter"
         x-transition:leave="toast-leave"
         :class="toast.type === 'success' ? 'bg-green-500' : (toast.type === 'warning' ? 'bg-yellow-500' : 'bg-red-500')"
         class="fixed top-4 right-4 z-50 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
        <i :class="toast.type === 'success' ? 'fas fa-check-circle' : (toast.type === 'warning' ? 'fas fa-exclamation-triangle' : 'fas fa-exclamation-circle')"></i>
        <span x-text="toast.message"></span>
    </div>

    <!-- Top Bar -->
    <div class="bg-green-700 text-white text-xs sm:text-sm py-1.5">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex justify-between items-center">
                <!-- Contact Info - Left -->
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    <a href="tel:{{ $businessPhone }}" class="flex items-center hover:text-green-200" aria-label="Call us">
                        <i class="fas fa-phone text-xs mr-1"></i>
                        <span class="hidden sm:inline truncate">{{ $businessPhone }}</span>
                    </a>
                    <span class="hidden lg:flex items-center">
                        <i class="fas fa-envelope text-xs mr-1"></i>
                        <span class="truncate">{{ $businessEmail }}</span>
                    </span>
                </div>
                
                <!-- User Links - Right -->
                <div class="flex items-center gap-2 sm:gap-3 ml-2">
                    <a href="{{ route('tracking.index') }}" class="hover:text-green-200 flex items-center" aria-label="Track Order">
                        <i class="fas fa-truck text-xs"></i>
                        <span class="hidden md:inline ml-1">Track</span>
                    </a>
                    @auth
                        <a href="{{ route('account.dashboard') }}" class="hover:text-green-200 flex items-center" aria-label="My Account">
                            <i class="fas fa-user text-xs"></i>
                            <span class="hidden md:inline ml-1">Account</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-green-200">Login</a>
                        <a href="{{ route('register') }}" class="hover:text-green-200 hidden sm:block">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex items-center justify-between py-2 sm:py-3">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center min-w-0" aria-label="{{ $businessName }} - Home">
                    @if(\App\Models\Setting::logo())
                        <img src="{{ \App\Models\Setting::logo() }}" alt="{{ $businessName }} Logo" class="h-8 sm:h-10" width="150" height="40">
                    @else
                        <span class="text-sm sm:text-lg font-bold text-green-700 flex items-center">
                            <i class="fas fa-leaf mr-1" aria-hidden="true"></i>
                            <span class="hidden md:inline">{{ $businessName }}</span>
                            <span class="md:hidden">SV Masala</span>
                        </span>
                    @endif
                </a>

                <!-- Search Bar - Desktop -->
                <form action="{{ route('products.search') }}" method="GET" class="hidden lg:flex flex-1 max-w-md mx-4" role="search">
                    <div class="relative w-full">
                        <label for="desktop-search" class="sr-only">Search products</label>
                        <input type="text" id="desktop-search" name="q" placeholder="Search masala, spices, oils..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                               value="{{ request('q') }}"
                               aria-label="Search products">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-green-600" aria-label="Search">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>

                <!-- Right Side Icons -->
                <div class="flex items-center gap-1 sm:gap-2">
                    <!-- Search Icon - Mobile/Tablet -->
                    <a href="{{ route('products.search') }}" class="lg:hidden text-gray-700 hover:text-green-600 p-2" aria-label="Search">
                        <i class="fas fa-search text-lg" aria-hidden="true"></i>
                    </a>
                    
                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-green-600 p-2" aria-label="Shopping Cart">
                        <i class="fas fa-shopping-cart text-lg sm:text-xl" aria-hidden="true"></i>
                        <span x-show="cartCount > 0" 
                              x-text="cartCount"
                              class="absolute top-0 right-0 bg-green-600 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-medium"
                              aria-label="Cart items count">
                        </span>
                    </a>
                    
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-gray-700 p-2" aria-label="Toggle menu" aria-expanded="false">
                        <i class="fas fa-bars text-xl" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation - Desktop -->
            <nav class="hidden lg:block border-t" aria-label="Main navigation">
                <ul class="flex items-center justify-center space-x-5 py-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-700 hover:text-green-600 font-medium">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-700 hover:text-green-600 font-medium">All Products</a></li>
                    @php $categories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get(); @endphp
                    @foreach($categories as $category)
                        <li class="relative group">
                            <a href="{{ route('category.show', $category->slug) }}" class="text-gray-700 hover:text-green-600 font-medium flex items-center">
                                {{ $category->name }}
                                @if($category->children->count() > 0)
                                    <i class="fas fa-chevron-down text-xs ml-1" aria-hidden="true"></i>
                                @endif
                            </a>
                            @if($category->children->count() > 0)
                                <div class="absolute left-0 top-full mt-2 w-48 bg-white shadow-lg rounded-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    @foreach($category->children as $child)
                                        <a href="{{ route('category.show', $child->slug) }}" class="block px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                    <li><a href="{{ route('about') }}" class="text-gray-700 hover:text-green-600 font-medium">About</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-700 hover:text-green-600 font-medium">Contact</a></li>
                </ul>
            </nav>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="lg:hidden border-t py-3"
                 aria-label="Mobile navigation">
                <!-- Mobile Search -->
                <form action="{{ route('products.search') }}" method="GET" class="mb-4" role="search">
                    <div class="relative">
                        <label for="mobile-search" class="sr-only">Search products</label>
                        <input type="text" id="mobile-search" name="q" placeholder="Search products..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500" aria-label="Search">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Mobile Nav Links -->
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('home') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50 hover:text-green-600">
                            <i class="fas fa-home w-5 mr-2" aria-hidden="true"></i>Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}" class="block py-2 px-3 rounded text-green-600 bg-green-50 font-medium">
                            <i class="fas fa-th-large w-5 mr-2" aria-hidden="true"></i>All Products
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('category.show', $category->slug) }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50 hover:text-green-600">
                                <i class="fas fa-tag w-5 mr-2" aria-hidden="true"></i>{{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                    <li class="border-t pt-2 mt-2">
                        <a href="{{ route('about') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50 hover:text-green-600">
                            <i class="fas fa-info-circle w-5 mr-2" aria-hidden="true"></i>About
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50 hover:text-green-600">
                            <i class="fas fa-envelope w-5 mr-2" aria-hidden="true"></i>Contact
                        </a>
                    </li>
                    <li class="border-t pt-2 mt-2">
                        <a href="{{ route('tracking.index') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50 hover:text-green-600">
                            <i class="fas fa-truck w-5 mr-2" aria-hidden="true"></i>Track Order
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('account.dashboard') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50 hover:text-green-600">
                                <i class="fas fa-user w-5 mr-2" aria-hidden="true"></i>My Account
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left py-2 px-3 rounded text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt w-5 mr-2" aria-hidden="true"></i>Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50 hover:text-green-600">
                                <i class="fas fa-sign-in-alt w-5 mr-2" aria-hidden="true"></i>Login
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50 hover:text-green-600">
                                <i class="fas fa-user-plus w-5 mr-2" aria-hidden="true"></i>Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </header>

    <!-- Breadcrumb Schema (if breadcrumbs exist) -->
    @yield('breadcrumb_schema')

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 container mx-auto mt-4 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 container mx-auto mt-4 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1" role="main">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-12" role="contentinfo">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-white text-lg font-bold mb-4">
                        <i class="fas fa-leaf text-green-500" aria-hidden="true"></i> {{ $businessName }}
                    </h3>
                    <p class="text-sm">{{ $businessTagline }}</p>
                    <p class="text-sm mt-2">Your trusted source for authentic homemade spices, natural oils, and herbal products. 100% pure and chemical-free.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-green-400">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-green-400">All Products</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-green-400">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-green-400">Contact</a></li>
                        <li><a href="{{ route('tracking.index') }}" class="hover:text-green-400">Track Order</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Categories</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach(\App\Models\Category::whereNull('parent_id')->where('is_active', true)->take(5)->get() as $cat)
                            <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-green-400">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact Us</h4>
                    <address class="not-italic">
                        <ul class="space-y-2 text-sm">
                            <li><i class="fas fa-map-marker-alt mr-2 text-green-500" aria-hidden="true"></i>{{ $businessAddress }}</li>
                            <li>
                                <a href="tel:{{ $businessPhone }}" class="hover:text-green-400">
                                    <i class="fas fa-phone mr-2 text-green-500" aria-hidden="true"></i>{{ $businessPhone }}
                                </a>
                            </li>
                            <li>
                                <a href="mailto:{{ $businessEmail }}" class="hover:text-green-400">
                                    <i class="fas fa-envelope mr-2 text-green-500" aria-hidden="true"></i>{{ $businessEmail }}
                                </a>
                            </li>
                        </ul>
                    </address>
                    <div class="flex space-x-4 mt-4">
                        <a href="{{ \App\Models\Setting::get('facebook_url', '#') }}" class="text-gray-400 hover:text-green-400" aria-label="Facebook" rel="noopener noreferrer" target="_blank"><i class="fab fa-facebook text-xl" aria-hidden="true"></i></a>
                        <a href="{{ \App\Models\Setting::get('instagram_url', '#') }}" class="text-gray-400 hover:text-green-400" aria-label="Instagram" rel="noopener noreferrer" target="_blank"><i class="fab fa-instagram text-xl" aria-hidden="true"></i></a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $businessPhone) }}" class="text-gray-400 hover:text-green-400" aria-label="WhatsApp" rel="noopener noreferrer" target="_blank"><i class="fab fa-whatsapp text-xl" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 py-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ $businessName }}. All rights reserved.</p>
            <p class="mt-1 text-xs text-gray-500">Premium Homemade Masala & Herbal Products | Chennai, Tamil Nadu</p>
        </div>
    </footer>

    <script>
        // CSRF Token Helper
        const csrfHelper = {
            getToken() {
                const meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.content : '';
            },
            
            updateToken(newToken) {
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) {
                    meta.content = newToken;
                }
            },
            
            async refreshToken() {
                try {
                    const response = await fetch('{{ route("csrf.token") }}', {
                        method: 'GET',
                        headers: { 
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        if (data.csrf_token) {
                            this.updateToken(data.csrf_token);
                            return data.csrf_token;
                        }
                    }
                } catch (e) {
                    console.error('Failed to refresh CSRF token:', e);
                }
                return null;
            },
            
            async fetchWithCSRF(url, options = {}) {
                const defaultHeaders = {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                };
                
                options.headers = { ...defaultHeaders, ...options.headers };
                options.credentials = 'same-origin';
                
                let response = await fetch(url, options);
                
                if (response.status === 419) {
                    try {
                        const errorData = await response.json();
                        if (errorData.csrf_token) {
                            this.updateToken(errorData.csrf_token);
                            options.headers['X-CSRF-TOKEN'] = errorData.csrf_token;
                            return await fetch(url, options);
                        }
                    } catch (e) {}
                    
                    const newToken = await this.refreshToken();
                    if (newToken) {
                        options.headers['X-CSRF-TOKEN'] = newToken;
                        response = await fetch(url, options);
                    } else {
                        setTimeout(() => window.location.reload(), 1000);
                        return null;
                    }
                }
                
                return response;
            }
        };
        
        setInterval(() => { csrfHelper.refreshToken(); }, 30 * 60 * 1000);

        function cartManager() {
            return {
                cartCount: {{ \App\Models\Cart::getCart()->total_items }},
                toast: { show: false, message: '', type: 'success' },
                
                init() {
                    window.addEventListener('cart-updated', (e) => { this.cartCount = e.detail.count; });
                    window.addEventListener('add-to-cart', (e) => { this.addToCart(e.detail.productId, e.detail.quantity, e.detail.variantId); });
                    window.addToCart = (productId, quantity, variantId) => { this.addToCart(productId, quantity, variantId); };
                },
                
                showToast(message, type = 'success') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3000);
                },
                
                async addToCart(productId, quantity = 1, variantId = null) {
                    try {
                        const body = { product_id: productId, quantity: quantity };
                        if (variantId) { body.variant_id = variantId; }
                        
                        const response = await csrfHelper.fetchWithCSRF('{{ route("cart.add") }}', {
                            method: 'POST',
                            body: JSON.stringify(body)
                        });
                        
                        if (!response) return;
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.cartCount = data.cart_count;
                            this.showToast(data.message, 'success');
                        } else {
                            this.showToast(data.message || 'Error adding to cart', 'error');
                        }
                    } catch (error) {
                        this.showToast('Session expired. Refreshing...', 'warning');
                        setTimeout(() => window.location.reload(), 1500);
                    }
                }
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
