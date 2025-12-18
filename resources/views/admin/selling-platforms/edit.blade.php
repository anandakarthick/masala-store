@extends('layouts.admin')

@section('title', 'Configure ' . $platform->name)
@section('page_title', 'Configure ' . $platform->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.selling-platforms.show', $platform) }}" class="text-green-600 hover:text-green-700">
        <i class="fas fa-arrow-left mr-2"></i>Back to {{ $platform->name }}
    </a>
</div>

<div class="max-w-4xl">
    <form action="{{ route('admin.selling-platforms.update', $platform) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b flex items-center justify-between">
                <div class="flex items-center">
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
                <button type="button" onclick="testConnection()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                    <i class="fas fa-plug mr-1"></i> Test Connection
                </button>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Basic Settings -->
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
                
                <!-- API Configuration -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">
                        <i class="fas fa-plug text-blue-500 mr-2"></i>API Configuration
                    </h3>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-blue-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Configure API credentials to automatically sync products, inventory, and orders.
                        </p>
                    </div>
                    
                    @if($platform->code === 'shopify')
                    <!-- Shopify Settings -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Store URL *</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-500 text-sm">https://</span>
                                <input type="text" name="store_url" value="{{ old('store_url', $platform->getSetting('store_url')) }}"
                                       placeholder="your-store.myshopify.com"
                                       class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Access Token *</label>
                            <input type="password" name="access_token" value="{{ old('access_token', $platform->getSetting('access_token')) }}"
                                   placeholder="shpat_xxxxxxxxxxxxx"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Get from Shopify Admin → Apps → Develop apps</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                                <input type="text" name="api_key" value="{{ old('api_key', $platform->getSetting('api_key')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">API Secret</label>
                                <input type="password" name="api_secret" value="{{ old('api_secret', $platform->getSetting('api_secret')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                    </div>
                    
                    @elseif($platform->code === 'woocommerce')
                    <!-- WooCommerce Settings -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Store URL *</label>
                            <input type="url" name="store_url" value="{{ old('store_url', $platform->getSetting('store_url')) }}"
                                   placeholder="https://your-store.com"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Consumer Key *</label>
                                <input type="text" name="consumer_key" value="{{ old('consumer_key', $platform->getSetting('consumer_key')) }}"
                                       placeholder="ck_xxxxxxxxxxxxx"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Consumer Secret *</label>
                                <input type="password" name="consumer_secret" value="{{ old('consumer_secret', $platform->getSetting('consumer_secret')) }}"
                                       placeholder="cs_xxxxxxxxxxxxx"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Get from WooCommerce → Settings → Advanced → REST API</p>
                    </div>
                    
                    @elseif($platform->code === 'amazon')
                    <!-- Amazon SP-API Settings -->
                    <div class="space-y-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Amazon SP-API requires developer registration. <a href="https://developer.amazonservices.in/" target="_blank" class="underline">Register here</a>
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Seller ID</label>
                                <input type="text" name="seller_id" value="{{ old('seller_id', $platform->getSetting('seller_id')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Marketplace ID</label>
                                <input type="text" name="marketplace_id" value="{{ old('marketplace_id', $platform->getSetting('marketplace_id')) }}"
                                       placeholder="A21TJRUUN4KGV (India)"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client ID</label>
                                <input type="text" name="client_id" value="{{ old('client_id', $platform->getSetting('client_id')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret</label>
                                <input type="password" name="client_secret" value="{{ old('client_secret', $platform->getSetting('client_secret')) }}"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Refresh Token</label>
                            <input type="password" name="refresh_token" value="{{ old('refresh_token', $platform->getSetting('refresh_token')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    
                    @elseif($platform->code === 'flipkart')
                    <!-- Flipkart Settings -->
                    <div class="space-y-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Flipkart API requires seller approval. <a href="https://seller.flipkart.com" target="_blank" class="underline">Apply here</a>
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
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">API Secret</label>
                            <input type="password" name="api_secret" value="{{ old('api_secret', $platform->getSetting('api_secret')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    
                    @else
                    <!-- Generic API Settings -->
                    <div class="space-y-4">
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
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">API Secret</label>
                            <input type="password" name="api_secret" value="{{ old('api_secret', $platform->getSetting('api_secret')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            This platform may not have full API support. Products may need to be added manually.
                        </p>
                    </div>
                    @endif
                </div>
                
                <!-- Enable/Disable -->
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

<!-- Connection Test Modal -->
<div id="testModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div id="testLoading">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                <p class="text-gray-600">Testing connection...</p>
            </div>
            <div id="testResult" class="hidden">
                <i id="testIcon" class="text-5xl mb-4"></i>
                <h3 id="testTitle" class="text-lg font-semibold mb-2"></h3>
                <p id="testMessage" class="text-gray-600"></p>
                <button onclick="closeTestModal()" class="mt-4 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testConnection() {
    document.getElementById('testModal').classList.remove('hidden');
    document.getElementById('testLoading').classList.remove('hidden');
    document.getElementById('testResult').classList.add('hidden');
    
    fetch('{{ route("admin.selling-platforms.test-connection", $platform) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('testLoading').classList.add('hidden');
        document.getElementById('testResult').classList.remove('hidden');
        
        if (data.success) {
            document.getElementById('testIcon').className = 'fas fa-check-circle text-5xl mb-4 text-green-600';
            document.getElementById('testTitle').textContent = 'Connection Successful!';
            document.getElementById('testTitle').className = 'text-lg font-semibold mb-2 text-green-600';
        } else {
            document.getElementById('testIcon').className = 'fas fa-times-circle text-5xl mb-4 text-red-600';
            document.getElementById('testTitle').textContent = 'Connection Failed';
            document.getElementById('testTitle').className = 'text-lg font-semibold mb-2 text-red-600';
        }
        document.getElementById('testMessage').textContent = data.message || '';
    })
    .catch(error => {
        document.getElementById('testLoading').classList.add('hidden');
        document.getElementById('testResult').classList.remove('hidden');
        document.getElementById('testIcon').className = 'fas fa-exclamation-triangle text-5xl mb-4 text-yellow-600';
        document.getElementById('testTitle').textContent = 'Error';
        document.getElementById('testMessage').textContent = 'Could not connect to the server.';
    });
}

function closeTestModal() {
    document.getElementById('testModal').classList.add('hidden');
}
</script>
@endpush
