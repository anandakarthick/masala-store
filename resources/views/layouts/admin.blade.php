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
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
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
                            @php $pendingCount = \App\Models\Order::pending()->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="ml-auto bg-red-500 text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                            @endif
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

    @stack('scripts')
</body>
</html>
