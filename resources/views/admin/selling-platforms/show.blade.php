@extends('layouts.admin')

@section('title', $platform->name)
@section('page_title', $platform->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.selling-platforms.index') }}" class="text-green-600 hover:text-green-700">
        <i class="fas fa-arrow-left mr-2"></i>Back to Platforms
    </a>
</div>

<!-- Platform Header -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                    @if($platform->logo)
                        <img src="{{ $platform->logo }}" alt="{{ $platform->name }}" class="w-14 h-14 object-contain"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-store text-gray-400 text-2xl\'></i>';">
                    @else
                        <i class="fas fa-store text-gray-400 text-2xl"></i>
                    @endif
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $platform->name }}</h1>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="text-sm px-2 py-0.5 rounded-full {{ $platform->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600' }}">
                            {{ $platform->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="text-sm text-gray-500">{{ $platform->platform_type_label }}</span>
                        <span class="text-sm text-orange-600">Commission: {{ $platform->commission_percentage }}%</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                @if($platform->seller_portal_url)
                <a href="{{ $platform->seller_portal_url }}" target="_blank" 
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                    <i class="fas fa-external-link-alt mr-1"></i> Seller Portal
                </a>
                @endif
                <a href="{{ route('admin.selling-platforms.edit', $platform) }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm">
                    <i class="fas fa-cog mr-1"></i> Settings
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ $stats['active_listings'] }}</p>
        <p class="text-sm text-gray-500">Active Listings</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_listings'] }}</p>
        <p class="text-sm text-gray-500">Pending</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-gray-600">{{ $stats['total_listings'] }}</p>
        <p class="text-sm text-gray-500">Total Listings</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_orders'] }}</p>
        <p class="text-sm text-gray-500">Orders</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-purple-600">Rs. {{ number_format($stats['total_revenue'], 0) }}</p>
        <p class="text-sm text-gray-500">Revenue</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-red-600">Rs. {{ number_format($stats['total_commission'], 0) }}</p>
        <p class="text-sm text-gray-500">Commission Paid</p>
    </div>
</div>

<!-- Actions Bar -->
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.selling-platforms.add-products', $platform) }}" 
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> Add Products
            </a>
            <form action="{{ route('admin.selling-platforms.sync-stock', $platform) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-sync mr-1"></i> Sync Stock
                </button>
            </form>
            <a href="{{ route('admin.selling-platforms.orders', $platform) }}" 
               class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium">
                <i class="fas fa-shopping-bag mr-1"></i> View Orders
            </a>
        </div>
        <div class="flex items-center space-x-2">
            <select id="bulkAction" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Bulk Actions</option>
                <option value="active">Mark Active</option>
                <option value="inactive">Mark Inactive</option>
                <option value="pending">Mark Pending</option>
            </select>
            <button onclick="applyBulkAction()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm">
                Apply
            </button>
        </div>
    </div>
</div>

<!-- Listings Table -->
<div class="bg-white rounded-lg shadow">
    <div class="p-4 border-b">
        <h2 class="text-lg font-semibold">Product Listings</h2>
    </div>
    
    @if($listings->isEmpty())
        <div class="p-8 text-center">
            <i class="fas fa-box-open text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500 mb-4">No products listed on {{ $platform->name }} yet.</p>
            <a href="{{ route('admin.selling-platforms.add-products', $platform) }}" 
               class="inline-block px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                <i class="fas fa-plus mr-1"></i> Add Products
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Platform Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commission</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($listings as $listing)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="listing_ids[]" value="{{ $listing->id }}" class="listing-checkbox rounded">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 rounded flex-shrink-0">
                                    @if($listing->product->primary_image_url)
                                        <img src="{{ $listing->product->primary_image_url }}" alt="" class="w-10 h-10 object-cover rounded">
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-800">{{ Str::limit($listing->product->name, 30) }}</p>
                                    <p class="text-xs text-gray-500">SKU: {{ $listing->platform_sku ?? $listing->product->sku }}</p>
                                    @if($listing->listing_url)
                                        <a href="{{ $listing->listing_url }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                            <i class="fas fa-external-link-alt"></i> View on {{ $platform->name }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium">Rs. {{ number_format($listing->platform_price, 2) }}</p>
                            @if($listing->platform_mrp && $listing->platform_mrp > $listing->platform_price)
                                <p class="text-xs text-gray-500 line-through">Rs. {{ number_format($listing->platform_mrp, 2) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="{{ $listing->platform_stock > 10 ? 'text-green-600' : ($listing->platform_stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $listing->platform_stock ?? 0 }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-red-600">Rs. {{ number_format($listing->estimated_commission, 2) }}</p>
                            <p class="text-xs text-gray-500">Net: Rs. {{ number_format($listing->net_earnings, 2) }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $listing->status === 'active' ? 'bg-green-100 text-green-600' : 
                                   ($listing->status === 'pending' ? 'bg-yellow-100 text-yellow-600' : 
                                   ($listing->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600')) }}">
                                {{ $listing->status_label }}
                            </span>
                            @if($listing->listed_at)
                                <p class="text-xs text-gray-500 mt-1">Listed: {{ $listing->listed_at->format('d M Y') }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.selling-platforms.edit-listing', [$platform, $listing]) }}" 
                                   class="text-blue-600 hover:text-blue-700" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.selling-platforms.delete-listing', [$platform, $listing]) }}" 
                                      method="POST" class="inline" onsubmit="return confirm('Remove this listing?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700" title="Remove">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t">
            {{ $listings->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleSelectAll(checkbox) {
    document.querySelectorAll('.listing-checkbox').forEach(cb => cb.checked = checkbox.checked);
}

function applyBulkAction() {
    const action = document.getElementById('bulkAction').value;
    if (!action) {
        alert('Please select an action');
        return;
    }
    
    const selected = [...document.querySelectorAll('.listing-checkbox:checked')].map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select at least one listing');
        return;
    }
    
    fetch('{{ route("admin.selling-platforms.bulk-status", $platform) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            listing_ids: selected,
            status: action
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endpush
