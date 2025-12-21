<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products') }}</title>
    
    @php
        $faviconUrl = \App\Models\Setting::favicon();
    @endphp
    
    @if($faviconUrl)
        <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Prevent body scroll - only main content should scroll */
        html, body {
            overflow: hidden;
            height: 100%;
        }
        
        /* Notification animation */
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .notification-enter {
            animation: slideIn 0.3s ease-out;
        }
        .pulse-animation {
            animation: pulse 0.5s ease-in-out 3;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100" x-data="adminNotifications()">
    <!-- Notification Toast Container -->
    <div class="fixed top-4 right-4 z-50 space-y-2" id="notification-container">
        <template x-for="notification in notifications" :key="notification.id">
            <div class="notification-enter bg-white rounded-lg shadow-2xl border-l-4 border-green-500 p-4 max-w-sm"
                 x-show="notification.show"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-x-0"
                 x-transition:leave-end="opacity-0 transform translate-x-full">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center pulse-animation">
                            <i class="fas fa-shopping-bag text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-semibold text-gray-900">🎉 New Order Received!</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Order <span class="font-medium text-green-600" x-text="notification.order_number"></span>
                        </p>
                        <p class="text-sm text-gray-500">
                            <span x-text="notification.customer_name"></span> • <span class="font-medium" x-text="notification.formatted_total"></span>
                        </p>
                        <div class="mt-2 flex space-x-2">
                            <a :href="notification.url" 
                               @click="markOrderSeen(notification.id)"
                               class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
                                View Order
                            </a>
                            <button @click="dismissNotification(notification.id)" class="text-xs text-gray-500 hover:text-gray-700">
                                Dismiss
                            </button>
                        </div>
                    </div>
                    <button @click="dismissNotification(notification.id)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" 
               class="bg-gray-800 text-white transition-all duration-300 flex flex-col flex-shrink-0 h-screen">
            <!-- Logo - Fixed at top -->
            <div class="flex items-center justify-between p-4 border-b border-gray-700 flex-shrink-0">
                <span x-show="sidebarOpen" class="text-lg font-bold">
                    <i class="fas fa-leaf text-green-500"></i> SV Masala Admin
                </span>
                <span x-show="!sidebarOpen" class="text-xl font-bold">
                    <i class="fas fa-leaf text-green-500"></i>
                </span>
            </div>

            <!-- Navigation - Scrollable -->
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Catalog</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-folder w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.products.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-box w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.variant-attributes.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.variant-attributes.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-tags w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Variant Attributes</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.combos.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.combos.*') ? 'bg-purple-600' : '' }}">
                            <i class="fas fa-box-open w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Custom Combos</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Sales</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-shopping-bag w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Orders</span>
                            <!-- Unseen Orders Badge (new orders not yet viewed) -->
                            <span x-show="unseenOrdersCount > 0" 
                                  x-text="unseenOrdersCount"
                                  class="ml-auto bg-red-500 text-xs px-2 py-1 rounded-full"></span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.customers.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.customers.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-users w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Customers</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.coupons.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.coupons.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-ticket-alt w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Coupons</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Reports</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.reports.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-chart-bar w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Reports</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Marketing</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.banner-generator.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.banner-generator.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-image w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Banner Generator</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Channels</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.selling-platforms.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.selling-platforms.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-store w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Selling Platforms</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Settings</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.settings.index') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-cog w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">General Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.banners') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.settings.banners') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-images w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Store Banners</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.social-media') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.settings.social-media') ? 'bg-green-600' : '' }}">
                            <i class="fab fa-whatsapp w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">WhatsApp & Social</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.pages.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.pages.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-file-alt w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Pages</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.payment-methods.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.payment-methods.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-credit-card w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Payment Methods</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Info - Fixed at bottom -->
            <div class="border-t border-gray-700 p-4 flex-shrink-0">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center">
                        <span class="text-sm font-bold">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                    </div>
                    <div x-show="sidebarOpen" class="ml-3">
                        <p class="text-sm font-medium">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="ml-4 text-xl font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Sound Toggle -->
                        <button @click="toggleSound()" 
                                class="relative text-gray-600 hover:text-green-600" 
                                :title="soundEnabled ? 'Sound On' : 'Sound Off'">
                            <i class="fas" :class="soundEnabled ? 'fa-volume-up' : 'fa-volume-mute'"></i>
                        </button>
                        
                        <!-- Notification Bell with Unseen Count -->
                        <div class="relative" x-data="{ showDropdown: false }">
                            <button @click="showDropdown = !showDropdown" 
                                    class="relative text-gray-600 hover:text-green-600">
                                <i class="fas fa-bell text-xl"></i>
                                <span x-show="unseenOrdersCount > 0" 
                                      x-text="unseenOrdersCount"
                                      class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full"></span>
                            </button>
                            
                            <!-- Dropdown -->
                            <div x-show="showDropdown" 
                                 @click.away="showDropdown = false"
                                 x-cloak
                                 class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg border z-50">
                                <div class="p-3 border-b flex justify-between items-center">
                                    <span class="font-semibold text-gray-700">Notifications</span>
                                    <button x-show="unseenOrdersCount > 0" 
                                            @click="markAllSeen()" 
                                            class="text-xs text-green-600 hover:text-green-700">
                                        Mark all as read
                                    </button>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    <template x-if="unseenOrdersCount === 0">
                                        <p class="p-4 text-center text-gray-500 text-sm">No new notifications</p>
                                    </template>
                                    <template x-if="unseenOrdersCount > 0">
                                        <a href="{{ route('admin.orders.index') }}" 
                                           class="block p-3 hover:bg-gray-50 border-b">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-shopping-bag text-green-600 text-sm"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-700">
                                                        <span x-text="unseenOrdersCount"></span> new order(s)
                                                    </p>
                                                    <p class="text-xs text-gray-500">Click to view all orders</p>
                                                </div>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('home') }}" target="_blank" class="text-gray-600 hover:text-green-600" title="View Store">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600" title="Logout">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    <script>
    function adminNotifications() {
        return {
            notifications: [],
            lastOrderId: {{ \App\Models\Order::max('id') ?? 0 }},
            unseenOrdersCount: {{ \App\Models\Order::where('is_seen_by_admin', false)->count() }},
            pendingOrdersCount: {{ \App\Models\Order::where('status', 'pending')->count() }},
            soundEnabled: localStorage.getItem('adminSoundEnabled') !== 'false',
            checkInterval: null,
            
            init() {
                // Start checking for new orders every 10 seconds
                this.startPolling();
            },
            
            startPolling() {
                // Check immediately
                this.checkNewOrders();
                
                // Then check every 10 seconds
                this.checkInterval = setInterval(() => {
                    this.checkNewOrders();
                }, 10000);
            },
            
            async checkNewOrders() {
                try {
                    const response = await fetch(`{{ route('admin.notifications.check-orders') }}?last_order_id=${this.lastOrderId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success && data.count > 0) {
                        // Update last order ID
                        this.lastOrderId = data.latest_order_id;
                        
                        // Show notifications for new orders
                        data.new_orders.forEach(order => {
                            this.showNotification(order);
                        });
                        
                        // Play sound
                        if (this.soundEnabled) {
                            this.playNotificationSound();
                        }
                        
                        // Update counts
                        this.updateCounts();
                    }
                } catch (error) {
                    console.error('Error checking for new orders:', error);
                }
            },
            
            async updateCounts() {
                try {
                    const response = await fetch('{{ route('admin.notifications.pending-count') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    this.pendingOrdersCount = data.pending_count;
                    this.unseenOrdersCount = data.unseen_count;
                } catch (error) {
                    console.error('Error updating counts:', error);
                }
            },
            
            showNotification(order) {
                const notification = {
                    ...order,
                    show: true
                };
                
                this.notifications.unshift(notification);
                
                // Update unseen count
                this.unseenOrdersCount++;
                
                // Auto dismiss after 15 seconds
                setTimeout(() => {
                    this.dismissNotification(order.id);
                }, 15000);
                
                // Update page title
                document.title = `🔔 New Order! - Admin`;
                setTimeout(() => {
                    document.title = `@yield('title', 'Admin') - {{ \App\Models\Setting::get('business_name', 'SV Masala') }}`;
                }, 5000);
            },
            
            dismissNotification(id) {
                const index = this.notifications.findIndex(n => n.id === id);
                if (index > -1) {
                    this.notifications[index].show = false;
                    setTimeout(() => {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                    }, 300);
                }
            },
            
            async markOrderSeen(orderId) {
                try {
                    await fetch(`{{ url('admin/notifications/mark-seen') }}/${orderId}`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    // Dismiss the notification
                    this.dismissNotification(orderId);
                    
                    // Update counts
                    this.updateCounts();
                } catch (error) {
                    console.error('Error marking order as seen:', error);
                }
            },
            
            async markAllSeen() {
                try {
                    await fetch('{{ route('admin.notifications.mark-all-seen') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    // Clear all notifications
                    this.notifications = [];
                    this.unseenOrdersCount = 0;
                } catch (error) {
                    console.error('Error marking all as seen:', error);
                }
            },
            
            playNotificationSound() {
                try {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    
                    const playTone = (frequency, startTime, duration) => {
                        const oscillator = audioContext.createOscillator();
                        const gainNode = audioContext.createGain();
                        
                        oscillator.connect(gainNode);
                        gainNode.connect(audioContext.destination);
                        
                        oscillator.frequency.value = frequency;
                        oscillator.type = 'sine';
                        
                        gainNode.gain.setValueAtTime(0.3, startTime);
                        gainNode.gain.exponentialRampToValueAtTime(0.01, startTime + duration);
                        
                        oscillator.start(startTime);
                        oscillator.stop(startTime + duration);
                    };
                    
                    const now = audioContext.currentTime;
                    playTone(880, now, 0.15);
                    playTone(1108.73, now + 0.15, 0.15);
                    playTone(1318.51, now + 0.3, 0.2);
                    
                } catch (e) {
                    console.log('Could not play notification sound');
                }
            },
            
            toggleSound() {
                this.soundEnabled = !this.soundEnabled;
                localStorage.setItem('adminSoundEnabled', this.soundEnabled);
                
                if (this.soundEnabled) {
                    this.playNotificationSound();
                }
            }
        }
    }
    </script>

    @stack('scripts')
</body>
</html>
