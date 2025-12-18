@extends('layouts.admin')

@section('title', 'Products')
@section('page_title', 'Products')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold">All Products</h2>
            <p class="text-sm text-gray-500">Manage your product catalog</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Add Product
        </a>
    </div>

    <!-- Filters -->
    <div class="p-4 border-b bg-gray-50">
        <form action="" method="GET" class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
            <select name="category" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            <select name="stock" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Stock</option>
                <option value="low" {{ request('stock') === 'low' ? 'selected' : '' }}>Low Stock</option>
                <option value="out" {{ request('stock') === 'out' ? 'selected' : '' }}>Out of Stock</option>
            </select>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg">Filter</button>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">Reset</a>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($product->primary_image_url)
                                    <img src="{{ $product->primary_image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover mr-3">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $product->weight_display }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->sku }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name }}</td>
                        <td class="px-6 py-4">
                            @if($product->discount_price)
                                <span class="font-medium text-orange-600">₹{{ number_format($product->discount_price, 2) }}</span>
                                <span class="text-sm text-gray-400 line-through ml-1">₹{{ number_format($product->price, 2) }}</span>
                            @else
                                <span class="font-medium">₹{{ number_format($product->price, 2) }}</span>
                            @endif
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
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.products.show', $product) }}" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-box-open text-4xl mb-2"></i>
                            <p>No products found</p>
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
