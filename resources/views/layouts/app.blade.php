<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masala Store') - {{ \App\Models\Setting::businessName() }}</title>
    <meta name="description" content="@yield('meta_description', 'Premium quality masala, oils, candles and return gifts')">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
        .toast-enter { animation: toastIn 0.3s ease-out; }
        .toast-leave { animation: toastOut 0.3s ease-in forwards; }
        @keyframes toastIn { from { transform: translateY(-100%); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes toastOut { from { transform: translateY(0); opacity: 1; } to { transform: translateY(-100%); opacity: 0; } }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col" x-data="cartManager()" x-init="init()">
    <!-- Toast Notification -->
    <div x-show="toast.show" x-cloak
         x-transition:enter="toast-enter"
         x-transition:leave="toast-leave"
         :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
         class="fixed top-4 right-4 z-50 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
        <i :class="toast.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
        <span x-text="toast.message"></span>
    </div>

    <!-- Top Bar -->
    <div class="bg-orange-600 text-white text-sm py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span><i class="fas fa-phone mr-1"></i> {{ \App\Models\Setting::businessPhone() }}</span>
                <span class="hidden md:inline"><i class="fas fa-envelope mr-1"></i> {{ \App\Models\Setting::businessEmail() }}</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('tracking.index') }}" class="hover:text-orange-200">Track Order</a>
                @auth
                    <a href="{{ route('account.dashboard') }}" class="hover:text-orange-200">My Account</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-orange-200">Login</a>
                    <a href="{{ route('register') }}" class="hover:text-orange-200">Register</a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    @if(\App\Models\Setting::logo())
                        <img src="{{ \App\Models\Setting::logo() }}" alt="{{ \App\Models\Setting::businessName() }}" class="h-10">
                    @else
                        <span class="text-2xl font-bold text-orange-600">
                            <i class="fas fa-pepper-hot"></i> {{ \App\Models\Setting::businessName() }}
                        </span>
                    @endif
                </a>

                <!-- Search Bar -->
                <form action="{{ route('products.search') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-8">
                    <div class="relative w-full">
                        <input type="text" name="q" placeholder="Search products..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                               value="{{ request('q') }}">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-orange-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Cart & Mobile Menu -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-orange-600">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span x-show="cartCount > 0" 
                              x-text="cartCount"
                              class="absolute -top-2 -right-2 bg-orange-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        </span>
                    </a>
                    
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex items-center space-x-6 py-3 border-t">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 {{ request()->routeIs('home') ? 'text-orange-600 font-semibold' : '' }}">Home</a>
                
                @php $categories = \App\Models\Category::active()->parentCategories()->orderBy('sort_order')->get(); @endphp
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" 
                       class="text-gray-700 hover:text-orange-600 {{ request()->is('category/'.$category->slug.'*') ? 'text-orange-600 font-semibold' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
                
                <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-orange-600 {{ request()->routeIs('products.index') ? 'text-orange-600 font-semibold' : '' }}">All Products</a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-orange-600 {{ request()->routeIs('about') ? 'text-orange-600 font-semibold' : '' }}">About</a>
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-orange-600 {{ request()->routeIs('contact') ? 'text-orange-600 font-semibold' : '' }}">Contact</a>
            </nav>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-cloak class="md:hidden py-4 border-t">
                <form action="{{ route('products.search') }}" method="GET" class="mb-4">
                    <input type="text" name="q" placeholder="Search products..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </form>
                <div class="flex flex-col space-y-2">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 py-2">Home</a>
                    @foreach($categories as $category)
                        <a href="{{ route('category.show', $category->slug) }}" class="text-gray-700 hover:text-orange-600 py-2">{{ $category->name }}</a>
                    @endforeach
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-orange-600 py-2">All Products</a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-orange-600 py-2">About</a>
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-orange-600 py-2">Contact</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 relative" role="alert">
            <div class="container mx-auto">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 relative" role="alert">
            <div class="container mx-auto">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ \App\Models\Setting::businessName() }}</h3>
                    <p class="text-gray-400 text-sm">Premium quality masala, oils, candles and return gifts for all your needs.</p>
                    <div class="flex space-x-4 mt-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white">Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-white">Products</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                        <li><a href="{{ route('tracking.index') }}" class="hover:text-white">Track Order</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Categories</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        @foreach($categories as $category)
                            <li><a href="{{ route('category.show', $category->slug) }}" class="hover:text-white">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><i class="fas fa-map-marker-alt mr-2"></i> {{ \App\Models\Setting::businessAddress() }}</li>
                        <li><i class="fas fa-phone mr-2"></i> {{ \App\Models\Setting::businessPhone() }}</li>
                        <li><i class="fas fa-envelope mr-2"></i> {{ \App\Models\Setting::businessEmail() }}</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-700 py-4">
            <div class="container mx-auto px-4 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::businessName() }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function cartManager() {
            return {
                cartCount: {{ \App\Models\Cart::getCart()->total_items }},
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },
                
                init() {
                    // Listen for add to cart events
                    window.addEventListener('cart-updated', (e) => {
                        this.cartCount = e.detail.count;
                    });
                },
                
                showToast(message, type = 'success') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 3000);
                },
                
                async addToCart(productId, quantity = 1, variantId = null) {
                    try {
                        const body = {
                            product_id: productId,
                            quantity: quantity
                        };
                        if (variantId) {
                            body.variant_id = variantId;
                        }
                        
                        const response = await fetch('{{ route("cart.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(body)
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.cartCount = data.cart_count;
                            this.showToast(data.message, 'success');
                        } else {
                            this.showToast(data.message || 'Error adding to cart', 'error');
                        }
                    } catch (error) {
                        this.showToast('Error adding to cart', 'error');
                        console.error('Cart error:', error);
                    }
                }
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
