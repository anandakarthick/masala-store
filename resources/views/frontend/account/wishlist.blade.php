@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-green-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('account.dashboard') }}" class="hover:text-green-600">My Account</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800">Wishlist</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        @include('frontend.account.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h1 class="text-xl font-bold text-gray-800 mb-6">My Wishlist</h1>
                
                @if($wishlists->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($wishlists as $wishlist)
                            @if($wishlist->product)
                                @include('frontend.partials.product-card', ['product' => $wishlist->product])
                            @endif
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        {{ $wishlists->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-heart text-5xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">Your wishlist is empty</h3>
                        <p class="text-gray-500 mb-4">Save your favorite products here!</p>
                        <a href="{{ route('products.index') }}" 
                           class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                            Browse Products <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
