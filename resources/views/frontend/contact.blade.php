@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Contact Us</h1>
        <p class="text-lg text-gray-600">We'd love to hear from you. Get in touch with us!</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <!-- Contact Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Address</h3>
                <p class="text-gray-600">{{ \App\Models\Setting::businessAddress() }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-phone text-orange-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Phone</h3>
                <p class="text-gray-600">{{ \App\Models\Setting::businessPhone() }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-envelope text-orange-600 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Email</h3>
                <p class="text-gray-600">{{ \App\Models\Setting::businessEmail() }}</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h2>
                
                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                            <input type="text" name="subject" value="{{ old('subject') }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('subject')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                        <textarea name="message" rows="5" required
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">{{ old('message') }}</textarea>
                        @error('message')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 rounded-lg font-semibold">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
