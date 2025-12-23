<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error | {{ config('app.name', 'SV Products') }}</title>
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
                <div class="w-40 h-40 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center shadow-2xl">
                    <i class="fas fa-exclamation-circle text-white text-6xl"></i>
                </div>
            </div>
        </div>

        <!-- Error Title -->
        <h2 class="text-3xl font-bold text-gray-800 mb-4">
            Something Went Wrong
        </h2>

        <!-- Error Description -->
        <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
            @if(isset($exception) && $exception->getMessage())
                {{ $exception->getMessage() }}
            @else
                An unexpected error occurred. Please try again later.
            @endif
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
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

        <!-- Footer -->
        <p class="mt-12 text-gray-400 text-sm">
            &copy; {{ date('Y') }} {{ config('app.name', 'SV Products') }}. All rights reserved.
        </p>
    </div>
</body>
</html>
