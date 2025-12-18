@extends('layouts.admin')
@section('title', 'Stock Report')
@section('page_title', 'Stock Report')

@section('content')
<!-- Stock Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-sm text-gray-500">Total Products</div>
        <div class="text-2xl font-bold text-gray-800">{{ $stockSummary['total_products'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-sm text-gray-500">In Stock</div>
        <div class="text-2xl font-bold text-green-600">{{ $stockSummary['in_stock'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-sm text-gray-500">Low Stock</div>
        <div class="text-2xl font-bold text-yellow-600">{{ $stockSummary['low_stock'] }}</div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="text-sm text-gray-500">Out of Stock</div>
        <div class="text-2xl font-bold text-red-600">{{ $stockSummary['out_of_stock'] }}</div>
    </div>
</div>

<!-- Low Stock Products -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-yellow-600"><i class="fas fa-exclamation-triangle mr-2"></i>Low Stock Products</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Threshold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($lowStockProducts as $product)
                    <tr class="hover:bg-yellow-50">
                        <td class="px-6 py-4 font-medium">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->category->name }}</td>
                        <td class="px-6 py-4"><span class="text-yellow-600 font-bold">{{ $product->stock_quantity }}</span></td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->low_stock_threshold }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800">Update Stock</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No low stock products</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Out of Stock Products -->
<div class="bg-white rounded-lg shadow-md mb-6">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-red-600"><i class="fas fa-times-circle mr-2"></i>Out of Stock Products</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($outOfStockProducts as $product)
                    <tr class="hover:bg-red-50">
                        <td class="px-6 py-4 font-medium">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->category->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->sku }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800">Update Stock</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No out of stock products</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Expiring Products -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-orange-600"><i class="fas fa-clock mr-2"></i>Expiring Soon (30 Days)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($expiringProducts as $product)
                    <tr class="hover:bg-orange-50">
                        <td class="px-6 py-4 font-medium">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->batch_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4"><span class="text-orange-600 font-medium">{{ $product->expiry_date->format('d M Y') }}</span></td>
                        <td class="px-6 py-4">{{ $product->stock_quantity }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No products expiring soon</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
