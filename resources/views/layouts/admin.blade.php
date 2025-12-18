<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products') }}</title>
    
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
    <!-- New Order Notification Sound -->
    <audio id="notificationSound" preload="auto">
        <source src="data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAABhgC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7//////////////////////////////////////////////////////////////////8AAAAATGF2YzU4LjEzAAAAAAAAAAAAAAAAJAAAAAAAAAAAAYYNbRTHAAAAAAD/+9DEAAAIAANIAAAAgAAA0gAAABBGBGhiCM0AS+GYJg/GCYM0jCYJjNV5/8uD4P//y4OAgGP/BwEAQDH/ygIAgQ/+XB8HwfBAEP/KAg+D4f/+sRETM0YjRqJjNMX+f3PwgCAgICH/8QBD//ygIP/E/5QEH/ygIP/E/8T/xP+gAAAAKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq//tQxAADwAADSAAAAAAAANIAAAASqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqo=" type="audio/mp3">
    </audio>

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
                            <a :href="notification.url" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">
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
               class="bg-gray-800 text-white transition-all duration-300 flex flex-col flex-shrink-0">
            <!-- Logo -->
            <div class="flex items-center justify-between p-4 border-b border-gray-700">
                <span x-show="sidebarOpen" class="text-lg font-bold">
                    <i class="fas fa-leaf text-green-500"></i> SV Masala Admin
                </span>
                <span x-show="!sidebarOpen" class="text-xl font-bold">
                    <i class="fas fa-leaf text-green-500"></i>
                </span>
            </div>

            <!-- Navigation -->
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
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Sales</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-shopping-bag w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Orders</span>
                            <span x-show="pendingOrdersCount > 0" 
                                  x-text="pendingOrdersCount"
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
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Settings</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.settings.*') ? 'bg-green-600' : '' }}">
                            <i class="fas fa-cog w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Info -->
            <div class="border-t border-gray-700 p-4">
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
                        <!-- Notification Bell -->
                        <button @click="toggleSound()" 
                                class="relative text-gray-600 hover:text-green-600" 
                                :title="soundEnabled ? 'Sound On' : 'Sound Off'">
                            <i class="fas" :class="soundEnabled ? 'fa-volume-up' : 'fa-volume-mute'"></i>
                        </button>
                        
                        <!-- Pending Orders Badge -->
                        <a href="{{ route('admin.orders.index') }}?status=pending" class="relative text-gray-600 hover:text-green-600">
                            <i class="fas fa-bell text-xl"></i>
                            <span x-show="pendingOrdersCount > 0" 
                                  x-text="pendingOrdersCount"
                                  class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full"></span>
                        </a>
                        
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
            pendingOrdersCount: {{ \App\Models\Order::where('status', 'pending')->count() }},
            soundEnabled: localStorage.getItem('adminSoundEnabled') !== 'false',
            checkInterval: null,
            
            init() {
                // Start checking for new orders every 10 seconds
                this.startPolling();
                
                // Also update pending count
                this.updatePendingCount();
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
                        
                        // Update pending count
                        this.updatePendingCount();
                    }
                } catch (error) {
                    console.error('Error checking for new orders:', error);
                }
            },
            
            async updatePendingCount() {
                try {
                    const response = await fetch('{{ route('admin.notifications.pending-count') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();
                    this.pendingOrdersCount = data.count;
                } catch (error) {
                    console.error('Error updating pending count:', error);
                }
            },
            
            showNotification(order) {
                const notification = {
                    ...order,
                    show: true
                };
                
                this.notifications.unshift(notification);
                
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
            
            playNotificationSound() {
                // Create a simple beep sound using Web Audio API
                try {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    
                    // Play a pleasant notification sound (two tones)
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
                    playTone(880, now, 0.15);        // A5
                    playTone(1108.73, now + 0.15, 0.15);  // C#6
                    playTone(1318.51, now + 0.3, 0.2);    // E6
                    
                } catch (e) {
                    console.log('Could not play notification sound');
                }
            },
            
            toggleSound() {
                this.soundEnabled = !this.soundEnabled;
                localStorage.setItem('adminSoundEnabled', this.soundEnabled);
                
                if (this.soundEnabled) {
                    // Play a test sound
                    this.playNotificationSound();
                }
            }
        }
    }
    </script>

    @stack('scripts')
</body>
</html>
