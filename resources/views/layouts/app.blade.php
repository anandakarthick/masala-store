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
        $defaultDescription = $businessTagline . '. Buy authentic Indian spices online.';
        $defaultKeywords = 'homemade masala, Indian spices, turmeric powder, coriander powder, garam masala';
    @endphp
    
    <title>@yield('title', 'Home') - {{ $businessName }}</title>
    <meta name="description" content="@yield('meta_description', $defaultDescription)">
    <meta name="keywords" content="@yield('meta_keywords', $defaultKeywords)">
    <meta name="author" content="{{ $businessName }}">
    <meta name="robots" content="index, follow">
    
    <link rel="canonical" href="{{ url()->current() }}">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Home') - {{ $businessName }}">
    <meta property="og:description" content="@yield('meta_description', $defaultDescription)">
    <meta property="og:site_name" content="{{ $businessName }}">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Home') - {{ $businessName }}">
    <meta name="twitter:description" content="@yield('meta_description', $defaultDescription)">
    
    <meta name="geo.region" content="IN-TN">
    <meta name="geo.placename" content="Chennai">
    <meta name="theme-color" content="#16a34a">
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @yield('structured_data')
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $businessName }}",
        "url": "{{ $siteUrl }}",
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
            "contactType": "customer service"
        }
    }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type="number"] { -moz-appearance: textfield; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col" x-data="cartManager()" x-init="init()">
    <!-- Toast Notification -->
    <div x-show="toast.show" x-cloak
         :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
         class="fixed top-4 right-4 z-50 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
        <span x-text="toast.message"></span>
    </div>

    <!-- Top Bar -->
    <div class="bg-green-700 text-white text-xs sm:text-sm py-1.5">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="tel:{{ $businessPhone }}" class="flex items-center hover:text-green-200">
                        <i class="fas fa-phone text-xs mr-1"></i>
                        <span class="hidden sm:inline">{{ $businessPhone }}</span>
                    </a>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('tracking.index') }}" class="hover:text-green-200 flex items-center">
                        <i class="fas fa-truck text-xs"></i>
                        <span class="hidden md:inline ml-1">Track</span>
                    </a>
                    @auth
                        <a href="{{ route('account.dashboard') }}" class="hover:text-green-200">Account</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-green-200">Login</a>
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
                <a href="{{ route('home') }}" class="flex items-center">
                    @if(\App\Models\Setting::logo())
                        <img src="{{ \App\Models\Setting::logo() }}" alt="{{ $businessName }}" class="h-8 sm:h-10">
                    @else
                        <span class="text-sm sm:text-lg font-bold text-green-700 flex items-center">
                            <i class="fas fa-leaf mr-1"></i>
                            <span class="hidden md:inline">{{ $businessName }}</span>
                            <span class="md:hidden">SV Masala</span>
                        </span>
                    @endif
                </a>

                <!-- Desktop Search -->
                <form action="{{ route('products.search') }}" method="GET" class="hidden lg:flex flex-1 max-w-md mx-4">
                    <div class="relative w-full">
                        <input type="text" name="q" placeholder="Search masala, spices, oils..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                               value="{{ request('q') }}">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-green-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Header Icons -->
                <div class="flex items-center gap-1 sm:gap-2">
                    <a href="{{ route('products.search') }}" class="lg:hidden text-gray-700 hover:text-green-600 p-2">
                        <i class="fas fa-search text-lg"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-green-600 p-2">
                        <i class="fas fa-shopping-cart text-lg sm:text-xl"></i>
                        <span x-show="cartCount > 0" x-text="cartCount"
                              class="absolute top-0 right-0 bg-green-600 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-medium"></span>
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-gray-700 p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            @php
                $navCategories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->orderBy('sort_order')->get();
            @endphp
            
            <!-- Desktop Navigation -->
            <nav class="hidden lg:block border-t">
                <ul class="flex items-center justify-center space-x-5 py-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-700 hover:text-green-600 font-medium">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-700 hover:text-green-600 font-medium">All Products</a></li>
                    @foreach($navCategories as $category)
                        <li><a href="{{ route('category.show', $category->slug) }}" class="text-gray-700 hover:text-green-600 font-medium">{{ $category->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('about') }}" class="text-gray-700 hover:text-green-600 font-medium">About</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-700 hover:text-green-600 font-medium">Contact</a></li>
                </ul>
            </nav>

            <!-- Mobile Navigation -->
            <div x-show="mobileMenuOpen" x-cloak class="lg:hidden border-t py-3">
                <form action="{{ route('products.search') }}" method="GET" class="mb-4">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Search products..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <ul class="space-y-1">
                    <li><a href="{{ route('home') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-home w-5 mr-2"></i>Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="block py-2 px-3 rounded text-green-600 bg-green-50 font-medium"><i class="fas fa-th-large w-5 mr-2"></i>All Products</a></li>
                    @foreach($navCategories as $category)
                        <li><a href="{{ route('category.show', $category->slug) }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-tag w-5 mr-2"></i>{{ $category->name }}</a></li>
                    @endforeach
                    <li class="border-t pt-2 mt-2"><a href="{{ route('about') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-info-circle w-5 mr-2"></i>About</a></li>
                    <li><a href="{{ route('contact') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-envelope w-5 mr-2"></i>Contact</a></li>
                    <li class="border-t pt-2 mt-2"><a href="{{ route('tracking.index') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-truck w-5 mr-2"></i>Track Order</a></li>
                    @auth
                        <li><a href="{{ route('account.dashboard') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-user w-5 mr-2"></i>My Account</a></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left py-2 px-3 rounded text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt w-5 mr-2"></i>Logout</button>
                            </form>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-sign-in-alt w-5 mr-2"></i>Login</a></li>
                        <li><a href="{{ route('register') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-user-plus w-5 mr-2"></i>Register</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 container mx-auto mt-4 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 container mx-auto mt-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white text-lg font-bold mb-4"><i class="fas fa-leaf text-green-500"></i> {{ $businessName }}</h3>
                    <p class="text-sm">{{ $businessTagline }}</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-green-400">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-green-400">All Products</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-green-400">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-green-400">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Categories</h4>
                    <ul class="space-y-2 text-sm">
                        @foreach(\App\Models\Category::whereNull('parent_id')->where('is_active', true)->take(5)->get() as $cat)
                            <li><a href="{{ route('category.show', $cat->slug) }}" class="hover:text-green-400">{{ $cat->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-sm">
                        <li><i class="fas fa-map-marker-alt mr-2 text-green-500"></i>{{ $businessAddress }}</li>
                        <li><a href="tel:{{ $businessPhone }}" class="hover:text-green-400"><i class="fas fa-phone mr-2 text-green-500"></i>{{ $businessPhone }}</a></li>
                        <li><a href="mailto:{{ $businessEmail }}" class="hover:text-green-400"><i class="fas fa-envelope mr-2 text-green-500"></i>{{ $businessEmail }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 py-4 text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ $businessName }}. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        const csrfHelper = {
            getToken() { 
                return document.querySelector('meta[name="csrf-token"]')?.content || ''; 
            },
            updateToken(t) { 
                const m = document.querySelector('meta[name="csrf-token"]'); 
                if(m) m.content = t; 
            },
            async refreshToken() {
                try {
                    const r = await fetch('{{ route("csrf.token") }}', { 
                        method: 'GET', 
                        headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, 
                        credentials: 'same-origin' 
                    });
                    if(r.ok) { 
                        const d = await r.json(); 
                        if(d.csrf_token) { 
                            this.updateToken(d.csrf_token); 
                            return d.csrf_token; 
                        } 
                    }
                } catch(e) { 
                    console.error('CSRF refresh failed:', e); 
                }
                return null;
            },
            async fetchWithCSRF(url, options) {
                options = options || {};
                options.headers = { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': this.getToken(), 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest'
                };
                options.credentials = 'same-origin';
                let r = await fetch(url, options);
                if(r.status === 419) {
                    const t = await this.refreshToken();
                    if(t) { 
                        options.headers['X-CSRF-TOKEN'] = t; 
                        r = await fetch(url, options); 
                    } else { 
                        setTimeout(function() { window.location.reload(); }, 1000); 
                        return null; 
                    }
                }
                return r;
            }
        };
        
        setInterval(function() { csrfHelper.refreshToken(); }, 30*60*1000);

        function cartManager() {
            return {
                cartCount: {{ \App\Models\Cart::getCart()->total_items }},
                toast: { show: false, message: '', type: 'success' },
                init: function() {
                    var self = this;
                    window.addEventListener('cart-updated', function(e) { self.cartCount = e.detail.count; });
                    window.addToCart = function(p, q, v) { self.addToCart(p, q, v); };
                },
                showToast: function(msg, type) {
                    this.toast.show = true;
                    this.toast.message = msg;
                    this.toast.type = type || 'success';
                    var self = this;
                    setTimeout(function() { self.toast.show = false; }, 3000);
                },
                addToCart: async function(productId, quantity, variantId) {
                    try {
                        var body = { product_id: productId, quantity: quantity || 1 };
                        if(variantId) body.variant_id = variantId;
                        var r = await csrfHelper.fetchWithCSRF('{{ route("cart.add") }}', { 
                            method: 'POST', 
                            body: JSON.stringify(body) 
                        });
                        if(!r) return;
                        var d = await r.json();
                        if(d.success) { 
                            this.cartCount = d.cart_count; 
                            this.showToast(d.message, 'success'); 
                        } else {
                            this.showToast(d.message || 'Error adding to cart', 'error');
                        }
                    } catch(e) { 
                        this.showToast('Session expired. Refreshing...', 'error'); 
                        setTimeout(function() { window.location.reload(); }, 1500); 
                    }
                }
            };
        }
    </script>
    @stack('scripts')
</body>
</html>