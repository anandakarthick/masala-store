<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | {{ config('app.name', 'SV Products') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-bounce-slow {
            animation: bounce 2s infinite;
        }
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2316a34a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .spice-icon {
            position: absolute;
            opacity: 0.1;
            font-size: 2rem;
        }
    </style>
</head>
<body class="bg-gray-50 bg-pattern min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Floating Spice Icons Background -->
    <div class="spice-icon text-green-600" style="top: 10%; left: 5%;"><i class="fas fa-leaf"></i></div>
    <div class="spice-icon text-orange-500" style="top: 20%; right: 10%;"><i class="fas fa-pepper-hot"></i></div>
    <div class="spice-icon text-yellow-600" style="bottom: 15%; left: 15%;"><i class="fas fa-mortar-pestle"></i></div>
    <div class="spice-icon text-red-500" style="bottom: 25%; right: 5%;"><i class="fas fa-seedling"></i></div>
    <div class="spice-icon text-green-700" style="top: 50%; left: 3%;"><i class="fas fa-spa"></i></div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <!-- Error Illustration -->
        <div class="animate-float mb-8">
            <div class="relative inline-block">
                <!-- Main Circle with Search Icon -->
                <div class="w-44 h-44 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-2xl">
                    <i class="fas fa-search text-white text-6xl opacity-90"></i>
                </div>
                <!-- Question Mark Badge -->
                <div class="absolute -top-2 -right-2 w-14 h-14 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-bounce-slow">
                    <i class="fas fa-question text-yellow-800 text-2xl"></i>
                </div>
                <!-- Broken Link Icon -->
                <div class="absolute -bottom-1 -left-1 w-10 h-10 bg-red-500 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-unlink text-white text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-4">
            404
        </h1>

        <!-- Error Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
            Oops! Page Not Found
        </h2>

        <!-- Error Description -->
        <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
            The page you're looking for seems to have wandered off like a lost spice! 
            Don't worry, let's help you find your way back.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg shadow-lg hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
                <i class="fas fa-home mr-2"></i>
                Go to Homepage
            </a>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-semibold rounded-lg shadow-lg hover:from-orange-600 hover:to-red-600 transition-all transform hover:scale-105">
                <i class="fas fa-shopping-bag mr-2"></i>
                Browse Products
            </a>
            <a href="javascript:history.back()" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg shadow-lg border border-gray-200 hover:bg-gray-50 transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left mr-2"></i>
                Go Back
            </a>
        </div>

        <!-- Search Box -->
        <div class="max-w-md mx-auto mb-8">
            <form action="{{ route('products.search') }}" method="GET" class="relative">
                <input type="text" name="q" placeholder="Search for products..." 
                       class="w-full px-5 py-4 pr-12 rounded-xl border border-gray-200 shadow-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-green-600 hover:text-green-700">
                    <i class="fas fa-search text-xl"></i>
                </button>
            </form>
        </div>

        <!-- Popular Links -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">
                <i class="fas fa-star text-yellow-500 mr-2"></i>
                Popular Pages
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <a href="{{ route('products.index') }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-green-50 transition group">
                    <i class="fas fa-box text-green-600 text-2xl mb-2 group-hover:scale-110 transition"></i>
                    <span class="text-sm text-gray-600">All Products</span>
                </a>
                <a href="{{ route('products.offers') }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-orange-50 transition group">
                    <i class="fas fa-fire text-orange-500 text-2xl mb-2 group-hover:scale-110 transition"></i>
                    <span class="text-sm text-gray-600">Offers</span>
                </a>
                <a href="{{ route('tracking.index') }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-blue-50 transition group">
                    <i class="fas fa-truck text-blue-600 text-2xl mb-2 group-hover:scale-110 transition"></i>
                    <span class="text-sm text-gray-600">Track Order</span>
                </a>
                <a href="{{ route('contact') }}" class="flex flex-col items-center p-3 rounded-lg hover:bg-purple-50 transition group">
                    <i class="fas fa-envelope text-purple-600 text-2xl mb-2 group-hover:scale-110 transition"></i>
                    <span class="text-sm text-gray-600">Contact Us</span>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-gray-400 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'SV Products') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
