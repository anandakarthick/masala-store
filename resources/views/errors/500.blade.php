<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | {{ config('app.name', 'SV Products') }}</title>
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
        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }
        .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2316a34a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .gear-container {
            position: relative;
            width: 180px;
            height: 180px;
        }
        .gear-large {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .gear-small {
            position: absolute;
            top: 15px;
            right: 15px;
        }
    </style>
</head>
<body class="bg-gray-50 bg-pattern min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full text-center">
        <!-- Error Illustration - Gears -->
        <div class="animate-float mb-8">
            <div class="gear-container mx-auto">
                <!-- Large Gear -->
                <div class="gear-large">
                    <div class="w-36 h-36 bg-gradient-to-br from-gray-700 to-gray-900 rounded-full flex items-center justify-center shadow-2xl animate-spin-slow">
                        <i class="fas fa-cog text-gray-400 text-7xl"></i>
                    </div>
                </div>
                <!-- Small Gear -->
                <div class="gear-small">
                    <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-700 rounded-full flex items-center justify-center shadow-lg animate-spin-slow" style="animation-direction: reverse;">
                        <i class="fas fa-cog text-red-300 text-3xl"></i>
                    </div>
                </div>
                <!-- Warning Badge -->
                <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-2 w-14 h-14 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-pulse-slow">
                    <i class="fas fa-exclamation-triangle text-yellow-800 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-gray-700 to-gray-900 mb-4">
            500
        </h1>

        <!-- Error Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
            Internal Server Error
        </h2>

        <!-- Error Description -->
        <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
            Oops! Something went wrong on our end. Our team has been notified and we're working to fix it. 
            Please try again in a few moments.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg shadow-lg hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
                <i class="fas fa-home mr-2"></i>
                Go to Homepage
            </a>
            <button onclick="location.reload()" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-600 hover:to-blue-700 transition-all transform hover:scale-105">
                <i class="fas fa-redo mr-2"></i>
                Try Again
            </button>
            <a href="javascript:history.back()" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg shadow-lg border border-gray-200 hover:bg-gray-50 transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left mr-2"></i>
                Go Back
            </a>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 mb-6">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                <span class="font-semibold text-gray-700">We're on it!</span>
            </div>
            <p class="text-gray-500 text-sm">
                Our technical team has been automatically notified of this issue. 
                We apologize for the inconvenience.
            </p>
        </div>

        <!-- Help Section -->
        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-6 border border-green-100">
            <h3 class="font-semibold text-gray-800 mb-4">
                <i class="fas fa-life-ring text-green-600 mr-2"></i>
                While you wait, you can:
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('products.index') }}" class="flex items-center justify-center gap-2 p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                    <i class="fas fa-shopping-bag text-green-600"></i>
                    <span class="text-sm text-gray-600">Browse Products</span>
                </a>
                <a href="{{ route('tracking.index') }}" class="flex items-center justify-center gap-2 p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                    <i class="fas fa-truck text-blue-600"></i>
                    <span class="text-sm text-gray-600">Track Order</span>
                </a>
                <a href="{{ route('contact') }}" class="flex items-center justify-center gap-2 p-3 bg-white rounded-lg shadow hover:shadow-md transition">
                    <i class="fas fa-headset text-purple-600"></i>
                    <span class="text-sm text-gray-600">Contact Support</span>
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
