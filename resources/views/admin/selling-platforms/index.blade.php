@extends('layouts.admin')

@section('title', 'Selling Platforms')
@section('page_title', 'Multi-Channel Selling')

@section('content')
<!-- Stats Overview -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-store text-blue-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-500">Active Platforms</p>
                <p class="text-xl font-bold">{{ $stats['active_platforms'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-list text-green-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-500">Active Listings</p>
                <p class="text-xl font-bold">{{ $stats['active_listings'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-500">Pending Listings</p>
                <p class="text-xl font-bold">{{ $stats['pending_listings'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-rupee-sign text-purple-600"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-gray-500">Platform Revenue</p>
                <p class="text-xl font-bold">Rs. {{ number_format($stats['total_platform_revenue'], 0) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Platforms Grid -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <h2 class="text-lg font-semibold">Selling Platforms</h2>
        <p class="text-gray-600 text-sm mt-1">Manage your multi-channel selling across different marketplaces</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
        @foreach($platforms as $platform)
        <div class="border rounded-lg hover:shadow-lg transition-shadow {{ $platform->is_active ? 'border-gray-200' : 'border-gray-100 opacity-60' }}">
            <div class="p-4">
                <!-- Platform Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            @if($platform->logo)
                                <img src="{{ $platform->logo }}" alt="{{ $platform->name }}" class="w-10 h-10 object-contain"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-store text-gray-400 text-xl\'></i>';">
                            @else
                                <i class="fas fa-store text-gray-400 text-xl"></i>
                            @endif
                        </div>
                        <div class="ml-3">
                            <h3 class="font-semibold text-gray-800">{{ $platform->name }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $platform->platform_type === 'marketplace' ? 'bg-blue-100 text-blue-600' : ($platform->platform_type === 'b2b' ? 'bg-purple-100 text-purple-600' : ($platform->platform_type === 'social_commerce' ? 'bg-pink-100 text-pink-600' : 'bg-green-100 text-green-600')) }}">
                                {{ $platform->platform_type_label }}
                            </span>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" {{ $platform->is_active ? 'checked' : '' }}
                               onchange="togglePlatform({{ $platform->id }}, this)">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                    <div class="bg-gray-50 rounded p-2">
                        <p class="text-lg font-bold text-green-600">{{ $platform->active_listings_count }}</p>
                        <p class="text-xs text-gray-500">Active</p>
                    </div>
                    <div class="bg-gray-50 rounded p-2">
                        <p class="text-lg font-bold text-gray-600">{{ $platform->product_listings_count }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="bg-gray-50 rounded p-2">
                        <p class="text-lg font-bold text-blue-600">{{ $platform->platform_orders_count }}</p>
                        <p class="text-xs text-gray-500">Orders</p>
                    </div>
                </div>

                <!-- Commission -->
                <div class="flex items-center justify-between text-sm mb-4">
                    <span class="text-gray-500">Commission</span>
                    <span class="font-medium {{ $platform->commission_percentage > 0 ? 'text-orange-600' : 'text-green-600' }}">
                        {{ $platform->commission_percentage > 0 ? $platform->commission_percentage . '%' : 'No Commission' }}
                    </span>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <a href="{{ route('admin.selling-platforms.show', $platform) }}" 
                       class="flex-1 text-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="{{ route('admin.selling-platforms.add-products', $platform) }}" 
                       class="flex-1 text-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                        <i class="fas fa-plus mr-1"></i> Add Products
                    </a>
                </div>

                <!-- External Links -->
                <div class="flex justify-center space-x-4 mt-3 pt-3 border-t">
                    @if($platform->website_url)
                    <a href="{{ $platform->website_url }}" target="_blank" class="text-gray-400 hover:text-gray-600 text-sm">
                        <i class="fas fa-globe mr-1"></i> Website
                    </a>
                    @endif
                    @if($platform->seller_portal_url)
                    <a href="{{ $platform->seller_portal_url }}" target="_blank" class="text-gray-400 hover:text-blue-600 text-sm">
                        <i class="fas fa-external-link-alt mr-1"></i> Seller Portal
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Quick Guide -->
<div class="mt-6 bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>How to Sell on Multiple Platforms
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="flex items-start">
            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold mr-3 flex-shrink-0">1</div>
            <div>
                <p class="font-medium text-gray-800">Enable Platform</p>
                <p class="text-sm text-gray-600">Toggle the platform ON and configure API settings</p>
            </div>
        </div>
        <div class="flex items-start">
            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold mr-3 flex-shrink-0">2</div>
            <div>
                <p class="font-medium text-gray-800">Add Products</p>
                <p class="text-sm text-gray-600">Select products to list on the platform</p>
            </div>
        </div>
        <div class="flex items-start">
            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold mr-3 flex-shrink-0">3</div>
            <div>
                <p class="font-medium text-gray-800">Set Prices</p>
                <p class="text-sm text-gray-600">Configure platform-specific pricing</p>
            </div>
        </div>
        <div class="flex items-start">
            <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center font-bold mr-3 flex-shrink-0">4</div>
            <div>
                <p class="font-medium text-gray-800">Track Orders</p>
                <p class="text-sm text-gray-600">Manage orders from all platforms in one place</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePlatform(id, checkbox) {
    fetch(`{{ url('admin/selling-platforms') }}/${id}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            checkbox.checked = !checkbox.checked;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        checkbox.checked = !checkbox.checked;
    });
}
</script>
@endpush
