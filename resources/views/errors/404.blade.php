<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | {{ config('app.name', 'SV Products') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .animate-float { animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2316a34a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50 bg-pattern min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full text-center">
        <div class="animate-float mb-6">
            <div class="relative inline-block">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-search text-white text-3xl"></i>
                </div>
            </div>
        </div>

        <h1 class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-2">404</h1>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Page Not Found</h2>
        <p class="text-gray-600 text-sm mb-6">The page you're looking for doesn't exist or has been moved.</p>

        <!-- Search Box -->
        <form action="{{ route('products.search') }}" method="GET" class="mb-6">
            <div class="relative max-w-xs mx-auto">
                <input type="text" name="q" placeholder="Search products..." 
                       class="w-full px-4 py-2 pr-10 text-sm rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <div class="flex flex-col sm:flex-row gap-3 justify-center mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-home mr-2"></i>Homepage
            </a>
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                <i class="fas fa-shopping-bag mr-2"></i>Products
            </a>
            <a href="javascript:history.back()" class="inline-flex items-center justify-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i>Go Back
            </a>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-100">
            <h3 class="font-medium text-gray-800 text-sm mb-3">Quick Links</h3>
            <div class="grid grid-cols-4 gap-2">
                <a href="{{ route('products.index') }}" class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition">
                    <i class="fas fa-box text-green-600 text-lg mb-1"></i>
                    <span class="text-xs text-gray-600">Products</span>
                </a>
                <a href="{{ route('products.offers') }}" class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition">
                    <i class="fas fa-fire text-orange-500 text-lg mb-1"></i>
                    <span class="text-xs text-gray-600">Offers</span>
                </a>
                <a href="{{ route('tracking.index') }}" class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition">
                    <i class="fas fa-truck text-blue-600 text-lg mb-1"></i>
                    <span class="text-xs text-gray-600">Track</span>
                </a>
                <a href="{{ route('contact') }}" class="flex flex-col items-center p-2 rounded hover:bg-gray-50 transition">
                    <i class="fas fa-envelope text-purple-600 text-lg mb-1"></i>
                    <span class="text-xs text-gray-600">Contact</span>
                </a>
            </div>
        </div>

        <p class="mt-6 text-gray-400 text-xs">&copy; {{ date('Y') }} {{ config('app.name', 'SV Products') }}</p>
    </div>
</body>
</html>
