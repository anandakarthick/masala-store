<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ \App\Models\Setting::businessName() }}</title>
    
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
                <span x-show="sidebarOpen" class="text-xl font-bold">
                    <i class="fas fa-pepper-hot text-orange-500"></i> Admin Panel
                </span>
                <span x-show="!sidebarOpen" class="text-xl font-bold">
                    <i class="fas fa-pepper-hot text-orange-500"></i>
                </span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1 px-3">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-tachometer-alt w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Catalog</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-folder w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.products.*') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-box w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Products</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Sales</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-orange-600' : '' }}">
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
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.customers.*') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-users w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Customers</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.coupons.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.coupons.*') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-ticket-alt w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Coupons</span>
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Reports</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports.sales') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.reports.sales') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-chart-line w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Sales Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports.stock') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.reports.stock') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-warehouse w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Stock Report</span>
                            @php $lowStock = \App\Models\Product::lowStock()->count(); @endphp
                            @if($lowStock > 0)
                                <span class="ml-auto bg-yellow-500 text-xs px-2 py-1 rounded-full">{{ $lowStock }}</span>
                            @endif
                        </a>
                    </li>
                    
                    <li class="pt-4">
                        <span x-show="sidebarOpen" class="px-3 text-xs text-gray-400 uppercase">Settings</span>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.settings.index') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-cog w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">General Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.banners') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.settings.banners') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-image w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Banners</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.delivery-partners') }}" 
                           class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.settings.delivery-partners') ? 'bg-orange-600' : '' }}">
                            <i class="fas fa-truck w-6"></i>
                            <span x-show="sidebarOpen" class="ml-3">Delivery Partners</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Menu -->
            <div class="border-t border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center">
                        <span class="font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div x-show="sidebarOpen" class="ml-3">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ auth()->user()->role?->name }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="ml-4 text-xl font-semibold text-gray-800">@yield('page_title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Visit Store -->
                        <a href="{{ route('home') }}" target="_blank" class="text-gray-600 hover:text-orange-600">
                            <i class="fas fa-external-link-alt"></i> Visit Store
                        </a>
                        
                        <!-- Logout -->
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
