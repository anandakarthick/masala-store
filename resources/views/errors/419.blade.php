<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Session Expired | {{ config('app.name', 'SV Products') }}</title>
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
                <div class="w-24 h-24 bg-gradient-to-br from-amber-500 to-yellow-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-hourglass-end text-white text-3xl"></i>
                </div>
            </div>
        </div>

        <h1 class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-yellow-600 mb-2">419</h1>
        <h2 class="text-xl font-bold text-gray-800 mb-2">Session Expired</h2>
        <p class="text-gray-600 text-sm mb-6">Your session has expired. Please refresh the page and try again.</p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="javascript:location.reload()" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition">
                <i class="fas fa-redo mr-2"></i>Refresh Page
            </a>
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-home mr-2"></i>Homepage
            </a>
        </div>

        <p class="mt-6 text-gray-400 text-xs">&copy; {{ date('Y') }} {{ config('app.name', 'SV Products') }}</p>
    </div>
</body>
</html>
