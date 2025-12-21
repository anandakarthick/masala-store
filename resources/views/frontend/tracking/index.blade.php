@extends('layouts.app')

@section('title', 'Track Order')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shipping-fast text-2xl text-green-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Track Your Order</h1>
                <p class="text-gray-600">Enter Order Number or Mobile Number</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <form action="{{ route('tracking.track') }}" method="POST" x-data="{ searchType: 'order' }">
                @csrf
                
                <!-- Search Type Toggle -->
                <div class="flex mb-6 bg-gray-100 rounded-lg p-1">
                    <button type="button" 
                            @click="searchType = 'order'" 
                            :class="searchType === 'order' ? 'bg-white shadow text-green-600' : 'text-gray-500'"
                            class="flex-1 py-2 px-4 rounded-lg font-medium transition-all">
                        <i class="fas fa-receipt mr-2"></i>Order Number
                    </button>
                    <button type="button" 
                            @click="searchType = 'phone'" 
                            :class="searchType === 'phone' ? 'bg-white shadow text-green-600' : 'text-gray-500'"
                            class="flex-1 py-2 px-4 rounded-lg font-medium transition-all">
                        <i class="fas fa-phone mr-2"></i>Mobile Number
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Order Number Input -->
                    <div x-show="searchType === 'order'" x-transition>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-hashtag"></i>
                            </span>
                            <input type="text" name="order_number" value="{{ old('order_number') }}"
                                   placeholder="e.g., ORD-XXXXXXXX"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Enter your order number to see order details</p>
                        @error('order_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number Input -->
                    <div x-show="searchType === 'phone'" x-transition>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-mobile-alt"></i>
                            </span>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   placeholder="e.g., 9876543210"
                                   class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-3 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Enter mobile number to see all your orders</p>
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition-colors">
                        <i class="fas fa-search mr-2"></i>Track Order
                    </button>
                </div>
            </form>

            <!-- Help Text -->
            <div class="mt-6 pt-6 border-t text-center">
                <p class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Can't find your order? <a href="{{ route('contact') }}" class="text-green-600 hover:underline">Contact us</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
