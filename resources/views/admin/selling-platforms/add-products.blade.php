@extends('layouts.admin')

@section('title', 'Add Products to ' . $platform->name)
@section('page_title', 'Add Products to ' . $platform->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.selling-platforms.show', $platform) }}" class="text-green-600 hover:text-green-700">
        <i class="fas fa-arrow-left mr-2"></i>Back to {{ $platform->name }}
    </a>
</div>

<form action="{{ route('admin.selling-platforms.store-products', $platform) }}" method="POST">
    @csrf
    
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold">Select Products</h2>
                <p class="text-sm text-gray-500">Choose products to list on {{ $platform->name }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500" id="selectedCount">0 selected</span>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    <i class="fas fa-plus mr-1"></i> Add Selected Products
                </button>
            </div>
        </div>
        
        @if($products->isEmpty())
            <div class="p-8 text-center">
                <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
                <p class="text-gray-500">All products are already listed on {{ $platform->name }}!</p>
            </div>
        @else
            <div class="p-4">
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="rounded text-green-600">
                        <span class="ml-2 text-sm font-medium">Select All</span>
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($products as $product)
                    <label class="border rounded-lg p-4 cursor-pointer hover:border-green-500 hover:bg-green-50 transition-colors product-card">
                        <input type="checkbox" name="products[]" value="{{ $product->id }}" 
                               class="product-checkbox hidden" onchange="updateSelectedCount()">
                        <div class="flex items-start">
                            <div class="w-16 h-16 bg-gray-100 rounded flex-shrink-0">
                                @if($product->primary_image_url)
                                    <img src="{{ $product->primary_image_url }}" alt="" class="w-16 h-16 object-cover rounded">
                                @else
                                    <div class="w-16 h-16 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="font-medium text-gray-800 text-sm">{{ Str::limit($product->name, 40) }}</p>
                                <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                <p class="text-sm font-semibold text-green-600 mt-1">Rs. {{ number_format($product->effective_price, 2) }}</p>
                                <p class="text-xs text-gray-500">Stock: {{ $product->total_stock }}</p>
                            </div>
                            <div class="ml-2">
                                <div class="w-5 h-5 border-2 rounded flex items-center justify-center check-indicator">
                                    <i class="fas fa-check text-white text-xs hidden"></i>
                                </div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            
            <div class="p-4 border-t">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</form>

<style>
.product-card {
    position: relative;
}
.product-checkbox:checked + div {
    border-color: #16a34a;
    background-color: #f0fdf4;
}
.product-checkbox:checked ~ .check-indicator,
.product-card:has(.product-checkbox:checked) .check-indicator {
    background-color: #16a34a;
    border-color: #16a34a;
}
.product-card:has(.product-checkbox:checked) .check-indicator i {
    display: block !important;
}
</style>
@endsection

@push('scripts')
<script>
function toggleSelectAll(checkbox) {
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = checkbox.checked;
        cb.dispatchEvent(new Event('change'));
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.product-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count + ' selected';
    
    // Update visual state
    document.querySelectorAll('.product-card').forEach(card => {
        const checkbox = card.querySelector('.product-checkbox');
        const indicator = card.querySelector('.check-indicator');
        const icon = indicator.querySelector('i');
        
        if (checkbox.checked) {
            card.classList.add('border-green-500', 'bg-green-50');
            indicator.classList.add('bg-green-600', 'border-green-600');
            icon.classList.remove('hidden');
        } else {
            card.classList.remove('border-green-500', 'bg-green-50');
            indicator.classList.remove('bg-green-600', 'border-green-600');
            icon.classList.add('hidden');
        }
    });
}

// Handle card click
document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.type !== 'checkbox') {
            const checkbox = this.querySelector('.product-checkbox');
            checkbox.checked = !checkbox.checked;
            updateSelectedCount();
        }
    });
});
</script>
@endpush
