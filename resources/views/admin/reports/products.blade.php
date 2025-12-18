@extends('layouts.admin')
@section('title', 'Product Report')
@section('page_title', 'Product Report')

@section('content')
<!-- Date Filter -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <form action="" method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
            <input type="date" name="start_date" value="{{ $startDate }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
            <input type="date" name="end_date" value="{{ $endDate }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg">
            Apply Filter
        </button>
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Product Performance</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Units Sold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.products.edit', $product) }}" class="font-medium text-gray-800 hover:text-orange-600">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->sku }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium">{{ $product->total_sold ?? 0 }}</span>
                        </td>
                        <td class="px-6 py-4 text-green-600 font-medium">
                            ₹{{ number_format($product->total_revenue ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($product->stock_quantity <= 0)
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">Out of Stock</span>
                            @elseif($product->isLowStock())
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-600">{{ $product->stock_quantity }} (Low)</span>
                            @else
                                <span class="text-gray-600">{{ $product->stock_quantity }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No product data found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">
        {{ $products->links() }}
    </div>
</div>
@endsection
