@extends('layouts.admin')

@section('title', 'Product Details')
@section('page_title', $product->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Product Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="text-sm text-orange-600 font-medium">{{ $product->category->name }}</span>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h2>
                    <p class="text-gray-500">SKU: {{ $product->sku }}</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Pricing</h4>
                    <div class="space-y-1 text-sm">
                        <p>Price: <span class="font-medium">₹{{ number_format($product->price, 2) }}</span></p>
                        @if($product->discount_price)
                            <p>Discount Price: <span class="font-medium text-green-600">₹{{ number_format($product->discount_price, 2) }}</span></p>
                        @endif
                        @if($product->wholesale_price)
                            <p>Wholesale Price: <span class="font-medium">₹{{ number_format($product->wholesale_price, 2) }}</span></p>
                        @endif
                        <p>GST: <span class="font-medium">{{ $product->gst_percentage }}%</span></p>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Details</h4>
                    <div class="space-y-1 text-sm">
                        <p>Weight: <span class="font-medium">{{ $product->weight_display }}</span></p>
                        @if($product->batch_number)
                            <p>Batch: <span class="font-medium">{{ $product->batch_number }}</span></p>
                        @endif
                        @if($product->expiry_date)
                            <p>Expiry: <span class="font-medium {{ $product->isExpired() ? 'text-red-600' : '' }}">{{ $product->expiry_date->format('d M Y') }}</span></p>
                        @endif
                    </div>
                </div>
            </div>

            @if($product->description)
                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-medium text-gray-700 mb-2">Description</h4>
                    <p class="text-gray-600 text-sm">{{ $product->description }}</p>
                </div>
            @endif
        </div>

        <!-- Stock Management -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Stock Management</h3>
            
            <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <span class="text-sm text-gray-600">Current Stock</span>
                    <p class="text-3xl font-bold {{ $product->isOutOfStock() ? 'text-red-600' : ($product->isLowStock() ? 'text-yellow-600' : 'text-green-600') }}">
                        {{ $product->stock_quantity }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-sm text-gray-600">Low Stock Threshold</span>
                    <p class="text-xl font-medium">{{ $product->low_stock_threshold }}</p>
                </div>
            </div>

            <!-- Stock Update Form -->
            <form action="{{ route('admin.products.update-stock', $product) }}" method="POST" class="grid grid-cols-4 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" min="1" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="notes" placeholder="Optional"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg">
                        Update Stock
                    </button>
                </div>
            </form>
        </div>

        <!-- Stock History -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Stock History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Before → After</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($product->stockMovements()->latest()->take(20)->get() as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        {{ $movement->type === 'in' ? 'bg-green-100 text-green-600' : ($movement->type === 'out' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600') }}">
                                        {{ ucfirst($movement->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium">{{ $movement->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->stock_before }} → {{ $movement->stock_after }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->reference ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->createdBy?->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No stock movements yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Images -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Images</h3>
            @if($product->images->count() > 0)
                <div class="space-y-2">
                    @foreach($product->images as $image)
                        <img src="{{ $image->url }}" alt="" class="w-full rounded-lg {{ $image->is_primary ? 'ring-2 ring-orange-500' : '' }}">
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-image text-4xl mb-2"></i>
                    <p>No images</p>
                </div>
            @endif
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Status</h3>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Active</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                        {{ $product->is_active ? 'Yes' : 'No' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Featured</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $product->is_featured ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-600' }}">
                        {{ $product->is_featured ? 'Yes' : 'No' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <a href="{{ route('admin.products.edit', $product) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-center mb-2">
                <i class="fas fa-edit mr-2"></i>Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="block w-full bg-gray-200 text-gray-700 py-2 rounded-lg text-center">
                Back to Products
            </a>
        </div>
    </div>
</div>
@endsection
