@extends('layouts.admin')

@section('title', 'Configure ' . $platform->name)
@section('page_title', 'Configure ' . $platform->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.selling-platforms.show', $platform) }}" class="text-green-600 hover:text-green-700">
        <i class="fas fa-arrow-left mr-2"></i>Back to {{ $platform->name }}
    </a>
</div>

<div class="max-w-3xl">
    <form action="{{ route('admin.selling-platforms.update', $platform) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b flex items-center">
                <div class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                    @if($platform->logo)
                        <img src="{{ $platform->logo }}" alt="{{ $platform->name }}" class="w-12 h-12 object-contain">
                    @else
                        <i class="fas fa-store text-gray-400 text-xl"></i>
                    @endif
                </div>
                <div class="ml-4">
                    <h2 class="text-xl font-semibold">{{ $platform->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $platform->platform_type_label }}</p>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Basic Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                            <input type="text" name="name" value="{{ old('name', $platform->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Commission (%)</label>
                            <input type="number" name="commission_percentage" step="0.01" min="0" max="100"
                                   value="{{ old('commission_percentage', $platform->commission_percentage) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                            <input type="url" name="website_url" value="{{ old('website_url', $platform->website_url) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Seller Portal URL</label>
                            <input type="url" name="seller_portal_url" value="{{ old('seller_portal_url', $platform->seller_portal_url) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">{{ old('description', $platform->description) }}</textarea>
                    </div>
                </div>
                
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">
                        <i class="fas fa-key text-yellow-500 mr-2"></i>API Configuration
                    </h3>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Get these credentials from your seller account on {{ $platform->name }}.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Seller ID</label>
                            <input type="text" name="seller_id" value="{{ old('seller_id', $platform->getSetting('seller_id')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                            <input type="text" name="api_key" value="{{ old('api_key', $platform->getSetting('api_key')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">API Secret</label>
                            <input type="password" name="api_secret" value="{{ old('api_secret', $platform->getSetting('api_secret')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    
                    @if($platform->code === 'shopify')
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Store URL</label>
                        <input type="text" name="store_url" value="{{ old('store_url', $platform->getSetting('store_url')) }}"
                               placeholder="your-store.myshopify.com"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    @endif
                </div>
                
                <div class="border-t pt-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $platform->is_active ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable this platform</span>
                    </label>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3">
                <a href="{{ route('admin.selling-platforms.show', $platform) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    <i class="fas fa-save mr-1"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
