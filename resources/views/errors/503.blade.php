<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Service Unavailable | {{ config('app.name', 'SV Products') }}</title>
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
        .animate-wrench {
            animation: wrench 2s ease-in-out infinite;
            transform-origin: center center;
        }
        @keyframes wrench {
            0%, 100% { transform: rotate(-10deg); }
            50% { transform: rotate(10deg); }
        }
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2316a34a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50 bg-pattern min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full text-center">
        <!-- Maintenance Illustration -->
        <div class="animate-float mb-8">
            <div class="relative inline-block">
                <div class="w-44 h-44 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center shadow-2xl">
                    <i class="fas fa-tools text-white text-6xl"></i>
                </div>
                <!-- Wrench Animation -->
                <div class="absolute -top-4 -right-4 w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-wrench text-yellow-800 text-2xl animate-wrench"></i>
                </div>
                <!-- Progress Indicator -->
                <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-20 h-20 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-hard-hat text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 mb-4">
            503
        </h1>

        <!-- Error Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
            Under Maintenance
        </h2>

        <!-- Error Description -->
        <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
            We're currently performing some scheduled maintenance to improve your shopping experience. 
            We'll be back shortly!
        </p>

        <!-- Progress Bar -->
        <div class="max-w-md mx-auto mb-8">
            <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-full rounded-full animate-pulse" style="width: 65%;"></div>
            </div>
            <p class="text-sm text-gray-500 mt-2">Estimated completion: ~65%</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <button onclick="location.reload()" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg shadow-lg hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
                <i class="fas fa-redo mr-2"></i>
                Try Again
            </button>
            <a href="javascript:history.back()" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg shadow-lg border border-gray-200 hover:bg-gray-50 transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left mr-2"></i>
                Go Back
            </a>
        </div>

        <!-- Status Updates -->
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 max-w-md mx-auto">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center justify-center gap-2">
                <i class="fas fa-bell text-green-600"></i>
                Stay Updated
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                Follow us on social media for real-time updates on our maintenance progress.
            </p>
            <div class="flex justify-center gap-4">
                <a href="#" class="w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center transition">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="w-10 h-10 bg-pink-500 hover:bg-pink-600 text-white rounded-full flex items-center justify-center transition">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center transition">
                    <i class="fab fa-whatsapp"></i>
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
