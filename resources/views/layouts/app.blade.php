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
        
        // WhatsApp Settings
        $whatsappNumber = \App\Models\Setting::get('whatsapp_number', '');
        $whatsappEnabled = \App\Models\Setting::get('whatsapp_enabled', '1');
        $whatsappMessage = \App\Models\Setting::get('whatsapp_default_message', 'Hello! I would like to place an order.');
        
        // Social Media Links
        $socialLinks = \App\Models\SocialMediaLink::active()->get();
        
        // Footer Pages
        $footerPages = \App\Models\Page::active()->footer()->get();
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
    
    @php
        $faviconUrl = \App\Models\Setting::favicon();
        $logoUrl = \App\Models\Setting::logo();
    @endphp
    
    @if($faviconUrl)
        <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    @if($logoUrl)
        <meta property="og:image" content="{{ $logoUrl }}">
        <meta name="twitter:image" content="{{ $logoUrl }}">
    @endif
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script type="application/ld+json">
    {
        "{{"@"}}context": "https://schema.org",
        "{{"@"}}type": "Organization",
        "name": "{{ $businessName }}",
        "url": "{{ $siteUrl }}",
        "description": "{{ $businessTagline }}"
    }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type="number"] { -moz-appearance: textfield; }
        .whatsapp-float { animation: whatsapp-pulse 2s infinite; }
        @keyframes whatsapp-pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6); }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col" x-data="cartManager()" x-init="init()">
    <div x-show="toast.show" x-cloak
         :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
         class="fixed top-4 right-4 z-50 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
        <span x-text="toast.message"></span>
    </div>

    <!-- Top Bar -->
    <div class="bg-green-700 text-white text-xs sm:text-sm py-1.5">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex justify-between items-center">
                <!-- Left: Phone & Social -->
                <div class="flex items-center gap-2 sm:gap-4">
                    <a href="tel:{{ $businessPhone }}" class="flex items-center hover:text-green-200">
                        <i class="fas fa-phone text-xs mr-1"></i>
                        <span class="hidden sm:inline">{{ $businessPhone }}</span>
                    </a>
                    
                    <!-- Social Media Links in Top Bar -->
                    @if($socialLinks->count() > 0)
                        <div class="hidden md:flex items-center gap-2 border-l border-green-600 pl-3">
                            @foreach($socialLinks->take(5) as $social)
                                <a href="{{ $social->url }}" target="_blank" rel="noopener" 
                                   class="hover:text-green-200 transition" title="{{ $social->name }}">
                                    <i class="{{ $social->icon }}"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Right: WhatsApp, Track, Account -->
                <div class="flex items-center gap-2 sm:gap-3">
                    @if($whatsappEnabled == '1' && $whatsappNumber)
                        <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
                           target="_blank" rel="noopener"
                           class="hidden sm:flex items-center gap-1 bg-green-600 hover:bg-green-500 px-2 py-0.5 rounded-full text-xs">
                            <i class="fab fa-whatsapp"></i>
                            <span>Order on WhatsApp</span>
                        </a>
                    @endif
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

    <header class="bg-white shadow-md sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-2 sm:px-4">
            <div class="flex items-center justify-between py-2 sm:py-3">
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
            
            <nav class="hidden lg:block border-t">
                <ul class="flex items-center justify-center space-x-5 py-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-700 hover:text-green-600 font-medium">Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="text-gray-700 hover:text-green-600 font-medium">All Products</a></li>
                    @foreach($navCategories as $category)
                        <li><a href="{{ route('category.show', $category->slug) }}" class="text-gray-700 hover:text-green-600 font-medium">{{ $category->name }}</a></li>
                    @endforeach
                    <li>
                        <a href="{{ route('products.offers') }}" class="inline-flex items-center gap-1 bg-gradient-to-r from-red-500 to-orange-500 text-white px-3 py-1 rounded-full font-medium hover:from-red-600 hover:to-orange-600 transition-all text-xs">
                            <i class="fas fa-fire animate-pulse"></i> Offers
                        </a>
                    </li>
                    <li><a href="{{ route('about') }}" class="text-gray-700 hover:text-green-600 font-medium">About</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-700 hover:text-green-600 font-medium">Contact</a></li>
                </ul>
            </nav>

            <div x-show="mobileMenuOpen" x-cloak class="lg:hidden border-t py-3">
                <form action="{{ route('products.search') }}" method="GET" class="mb-4">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <ul class="space-y-1">
                    <li><a href="{{ route('home') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-home w-5 mr-2"></i>Home</a></li>
                    <li><a href="{{ route('products.index') }}" class="block py-2 px-3 rounded text-green-600 bg-green-50 font-medium"><i class="fas fa-th-large w-5 mr-2"></i>All Products</a></li>
                    <li>
                        <a href="{{ route('products.offers') }}" class="block py-2 px-3 rounded bg-gradient-to-r from-red-500 to-orange-500 text-white font-medium">
                            <i class="fas fa-fire w-5 mr-2 animate-pulse"></i>🔥 Special Offers
                        </a>
                    </li>
                    @foreach($navCategories as $category)
                        <li><a href="{{ route('category.show', $category->slug) }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-tag w-5 mr-2"></i>{{ $category->name }}</a></li>
                    @endforeach
                    <li class="border-t pt-2 mt-2"><a href="{{ route('about') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-info-circle w-5 mr-2"></i>About</a></li>
                    <li><a href="{{ route('contact') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-envelope w-5 mr-2"></i>Contact</a></li>
                    <li class="border-t pt-2 mt-2"><a href="{{ route('tracking.index') }}" class="block py-2 px-3 rounded text-gray-700 hover:bg-green-50"><i class="fas fa-truck w-5 mr-2"></i>Track Order</a></li>
                    @if($whatsappEnabled == '1' && $whatsappNumber)
                        <li>
                            <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
                               target="_blank" rel="noopener"
                               class="block py-2 px-3 rounded bg-green-500 text-white font-medium">
                                <i class="fab fa-whatsapp w-5 mr-2"></i>Order on WhatsApp
                            </a>
                        </li>
                    @endif
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

    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-white text-lg font-bold mb-4"><i class="fas fa-leaf text-green-500"></i> {{ $businessName }}</h3>
                    <p class="text-sm mb-4">{{ $businessTagline }}</p>
                    
                    <!-- Social Media Links -->
                    @if($socialLinks->count() > 0)
                        <div class="flex items-center gap-3 mt-4">
                            @foreach($socialLinks as $social)
                                <a href="{{ $social->url }}" target="_blank" rel="noopener" 
                                   class="w-9 h-9 rounded-full flex items-center justify-center transition transform hover:scale-110"
                                   style="background-color: {{ $social->color ?? '#6B7280' }}"
                                   title="{{ $social->name }}">
                                    <i class="{{ $social->icon }} text-white"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-green-400">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-green-400">All Products</a></li>
                        <li><a href="{{ route('products.offers') }}" class="hover:text-green-400 flex items-center gap-1"><i class="fas fa-fire text-orange-400"></i> Offers</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-green-400">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-green-400">Contact</a></li>
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
                    <ul class="space-y-2 text-sm">
                        <li><i class="fas fa-map-marker-alt mr-2 text-green-500"></i>{{ $businessAddress }}</li>
                        <li><a href="tel:{{ $businessPhone }}" class="hover:text-green-400"><i class="fas fa-phone mr-2 text-green-500"></i>{{ $businessPhone }}</a></li>
                        <li><a href="mailto:{{ $businessEmail }}" class="hover:text-green-400"><i class="fas fa-envelope mr-2 text-green-500"></i>{{ $businessEmail }}</a></li>
                        @if($whatsappEnabled == '1' && $whatsappNumber)
                            <li>
                                <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
                                   target="_blank" rel="noopener" class="hover:text-green-400">
                                    <i class="fab fa-whatsapp mr-2 text-green-500"></i>WhatsApp Order
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="border-t border-gray-800 py-4">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center gap-3 text-sm">
                    <p>&copy; {{ date('Y') }} {{ $businessName }}. All rights reserved.</p>
                    
                    <!-- Legal Pages Links -->
                    @if($footerPages->count() > 0)
                        <div class="flex items-center gap-4">
                            @foreach($footerPages as $page)
                                <a href="{{ route('page.show', $page->slug) }}" class="hover:text-green-400">{{ $page->title }}</a>
                                @if(!$loop->last)
                                    <span class="text-gray-600">|</span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    @if($whatsappEnabled == '1' && $whatsappNumber)
        <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
           target="_blank" rel="noopener"
           class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center shadow-lg whatsapp-float"
           title="Order on WhatsApp">
            <i class="fab fa-whatsapp text-2xl"></i>
        </a>
    @endif

    <script>
        var csrfHelper = {
            getToken: function() { 
                var meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.content : ''; 
            },
            updateToken: function(t) { 
                var m = document.querySelector('meta[name="csrf-token"]'); 
                if(m) m.content = t; 
            },
            refreshToken: function() {
                var self = this;
                return fetch('{{ route("csrf.token") }}', { 
                    method: 'GET', 
                    headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, 
                    credentials: 'same-origin' 
                }).then(function(r) {
                    if(r.ok) { 
                        return r.json().then(function(d) { 
                            if(d.csrf_token) { 
                                self.updateToken(d.csrf_token); 
                                return d.csrf_token; 
                            }
                            return null;
                        });
                    }
                    return null;
                }).catch(function(e) { 
                    console.error('CSRF refresh failed:', e); 
                    return null;
                });
            },
            fetchWithCSRF: function(url, options) {
                var self = this;
                options = options || {};
                options.headers = { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': this.getToken(), 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest'
                };
                options.credentials = 'same-origin';
                return fetch(url, options).then(function(r) {
                    if(r.status === 419) {
                        return self.refreshToken().then(function(t) {
                            if(t) { 
                                options.headers['X-CSRF-TOKEN'] = t; 
                                return fetch(url, options); 
                            } else { 
                                setTimeout(function() { window.location.reload(); }, 1000); 
                                return null; 
                            }
                        });
                    }
                    return r;
                });
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
                    var self = this;
                    this.toast.show = true;
                    this.toast.message = msg;
                    this.toast.type = type || 'success';
                    setTimeout(function() { self.toast.show = false; }, 3000);
                },
                addToCart: function(productId, quantity, variantId) {
                    var self = this;
                    var body = { product_id: productId, quantity: quantity || 1 };
                    if(variantId) body.variant_id = variantId;
                    csrfHelper.fetchWithCSRF('{{ route("cart.add") }}', { 
                        method: 'POST', 
                        body: JSON.stringify(body) 
                    }).then(function(r) {
                        if(!r) return;
                        return r.json();
                    }).then(function(d) {
                        if(d && d.success) { 
                            self.cartCount = d.cart_count; 
                            self.showToast(d.message, 'success'); 
                        } else if(d) {
                            self.showToast(d.message || 'Error adding to cart', 'error');
                        }
                    }).catch(function(e) { 
                        self.showToast('Session expired. Refreshing...', 'error'); 
                        setTimeout(function() { window.location.reload(); }, 1500); 
                    });
                }
            };
        }
    </script>
    @stack('scripts')
</body>
</html>
