@extends('layouts.app')

@section('title', 'Track Order')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shipping-fast text-2xl text-orange-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Track Your Order</h1>
                <p class="text-gray-600">Enter your order details to track</p>
            </div>

            <form action="{{ route('tracking.track') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                        <input type="text" name="order_number" value="{{ old('order_number') }}" required
                               placeholder="e.g., ORD-XXXXXXXX"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        @error('order_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required
                               placeholder="Your registered phone number"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-semibold">
                        Track Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
