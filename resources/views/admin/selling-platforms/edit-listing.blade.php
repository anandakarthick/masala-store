@extends('layouts.admin')

@section('title', 'Edit Listing - ' . $platform->name)
@section('page_title', 'Edit Listing')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.selling-platforms.show', $platform) }}" class="text-green-600 hover:text-green-700">
        <i class="fas fa-arrow-left mr-2"></i>Back to {{ $platform->name }}
    </a>
</div>

<div class="max-w-3xl">
    <form action="{{ route('admin.selling-platforms.update-listing', [$platform, $listing]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow">
            <!-- Product Info -->
            <div class="p-6 border-b">
                <div class="flex items-center">
                    <div class="w-20 h-20 bg-gray-100 rounded flex-shrink-0">
                        @if($listing->product->primary_image_url)
                            <img src="{{ $listing->product->primary_image_url }}" alt="" class="w-20 h-20 object-cover rounded">
                        @endif
                    </div>
                    <div class="ml-4">
                        <h2 class="text-lg font-semibold text-gray-800">{{ $listing->product->name }}</h2>
                        <p class="text-sm text-gray-500">Original SKU: {{ $listing->product->sku }}</p>
                        <p class="text-sm text-gray-500">Original Price: Rs. {{ number_format($listing->product->effective_price, 2) }}</p>
                        <p class="text-sm text-gray-500">Current Stock: {{ $listing->product->total_stock }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Platform IDs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform Product ID</label>
                        <input type="text" name="platform_product_id" value="{{ old('platform_product_id', $listing->platform_product_id) }}"
                               placeholder="Product ID on {{ $platform->name }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        <p class="text-xs text-gray-500 mt-1">The unique product identifier on {{ $platform->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform SKU</label>
                        <input type="text" name="platform_sku" value="{{ old('platform_sku', $listing->platform_sku) }}"
                               placeholder="SKU on {{ $platform->name }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                
                <!-- Listing URL -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Listing URL</label>
                    <input type="url" name="listing_url" value="{{ old('listing_url', $listing->listing_url) }}"
                           placeholder="https://www.{{ strtolower($platform->code) }}.com/product/..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Direct link to product on {{ $platform->name }}</p>
                </div>
                
                <!-- Pricing -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform Price (Rs.) *</label>
                        <input type="number" name="platform_price" step="0.01" min="0" required
                               value="{{ old('platform_price', $listing->platform_price) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Platform MRP (Rs.)</label>
                        <input type="number" name="platform_mrp" step="0.01" min="0"
                               value="{{ old('platform_mrp', $listing->platform_mrp) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                
                <!-- Commission Preview -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                    <h4 class="font-medium text-orange-800 mb-2">Earnings Estimate</h4>
                    <div class="grid grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Selling Price</p>
                            <p class="font-semibold text-gray-800">Rs. <span id="priceDisplay">{{ number_format($listing->platform_price, 2) }}</span></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Commission ({{ $platform->commission_percentage }}%)</p>
                            <p class="font-semibold text-red-600">- Rs. <span id="commissionDisplay">{{ number_format($listing->estimated_commission, 2) }}</span></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Net Earnings</p>
                            <p class="font-semibold text-green-600">Rs. <span id="netDisplay">{{ number_format($listing->net_earnings, 2) }}</span></p>
                        </div>
                    </div>
                </div>
                
                <!-- Stock -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Platform Stock</label>
                    <input type="number" name="platform_stock" min="0"
                           value="{{ old('platform_stock', $listing->platform_stock) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Stock allocated to this platform. Current inventory: {{ $listing->product->total_stock }}</p>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        <option value="draft" {{ $listing->status === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ $listing->status === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        <option value="active" {{ $listing->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $listing->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                @if($listing->rejection_reason)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-medium text-red-800 mb-1">Rejection Reason</h4>
                    <p class="text-sm text-red-600">{{ $listing->rejection_reason }}</p>
                </div>
                @endif
                
                <!-- Listing Info -->
                @if($listing->listed_at || $listing->last_synced_at)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        @if($listing->listed_at)
                        <div>
                            <p class="text-gray-500">Listed On</p>
                            <p class="font-medium">{{ $listing->listed_at->format('d M Y, h:i A') }}</p>
                        </div>
                        @endif
                        @if($listing->last_synced_at)
                        <div>
                            <p class="text-gray-500">Last Synced</p>
                            <p class="font-medium">{{ $listing->last_synced_at->format('d M Y, h:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t flex justify-between">
                <form action="{{ route('admin.selling-platforms.delete-listing', [$platform, $listing]) }}" method="POST"
                      onsubmit="return confirm('Remove this listing from {{ $platform->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-red-600 hover:text-red-700 font-medium">
                        <i class="fas fa-trash mr-1"></i> Remove Listing
                    </button>
                </form>
                
                <div class="space-x-3">
                    <a href="{{ route('admin.selling-platforms.show', $platform) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const commissionRate = {{ $platform->commission_percentage }};

document.querySelector('input[name="platform_price"]').addEventListener('input', function() {
    const price = parseFloat(this.value) || 0;
    const commission = (price * commissionRate) / 100;
    const net = price - commission;
    
    document.getElementById('priceDisplay').textContent = price.toFixed(2);
    document.getElementById('commissionDisplay').textContent = commission.toFixed(2);
    document.getElementById('netDisplay').textContent = net.toFixed(2);
});
</script>
@endpush
