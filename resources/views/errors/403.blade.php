<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden | {{ config('app.name', 'SV Products') }}</title>
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
        .animate-pulse-slow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2316a34a' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50 bg-pattern min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full text-center">
        <!-- Error Icon -->
        <div class="animate-float mb-8">
            <div class="relative inline-block">
                <div class="w-40 h-40 bg-gradient-to-br from-red-500 to-orange-500 rounded-full flex items-center justify-center shadow-2xl">
                    <i class="fas fa-ban text-white text-6xl"></i>
                </div>
                <div class="absolute -top-2 -right-2 w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-pulse-slow">
                    <i class="fas fa-lock text-yellow-800 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500 mb-4">
            403
        </h1>

        <!-- Error Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
            Access Forbidden
        </h2>

        <!-- Error Description -->
        <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
            Sorry, you don't have permission to access this page. Please check your credentials or contact support if you believe this is an error.
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ url('/') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg shadow-lg hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
                <i class="fas fa-home mr-2"></i>
                Go to Homepage
            </a>
            <a href="javascript:history.back()" 
               class="inline-flex items-center justify-center px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg shadow-lg border border-gray-200 hover:bg-gray-50 transition-all transform hover:scale-105">
                <i class="fas fa-arrow-left mr-2"></i>
                Go Back
            </a>
        </div>

        <!-- Help Section -->
        <div class="mt-12 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-3">
                <i class="fas fa-question-circle text-green-600 mr-2"></i>
                Need Help?
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                If you think you should have access to this page, please try:
            </p>
            <ul class="text-sm text-gray-600 space-y-2">
                <li class="flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt text-green-600"></i>
                    <a href="{{ route('login') }}" class="text-green-600 hover:underline">Login to your account</a>
                </li>
                <li class="flex items-center justify-center gap-2">
                    <i class="fas fa-envelope text-green-600"></i>
                    <a href="{{ route('contact') }}" class="text-green-600 hover:underline">Contact our support team</a>
                </li>
            </ul>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-gray-400 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'SV Products') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
