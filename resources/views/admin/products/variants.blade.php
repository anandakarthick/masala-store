@extends('layouts.admin')

@section('title', 'Manage Variants - ' . $product->name)
@section('page_title', 'Manage Variants')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.products.edit', $product) }}" class="text-green-600 hover:text-green-700">
        <i class="fas fa-arrow-left mr-1"></i> Back to Product
    </a>
</div>

<!-- Product Info -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center gap-4">
        @if($product->primary_image_url)
            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-lg">
        @else
            <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-image text-gray-400 text-2xl"></i>
            </div>
        @endif
        <div>
            <h2 class="text-xl font-bold">{{ $product->name }}</h2>
            <p class="text-gray-600">SKU: {{ $product->sku }} | Category: {{ $product->category->name }}</p>
            <p class="text-sm text-gray-500">Base Price: ₹{{ number_format($product->price, 2) }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add New Variant Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">
            <i class="fas fa-plus text-green-600 mr-2"></i>Add New Variant
        </h3>
        
        <form action="{{ route('admin.products.variants.store', $product) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Variant Name *</label>
                    <input type="text" name="name" required placeholder="e.g., 50g, 100g, 500ml"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">This will be shown to customers</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" placeholder="Auto-generated if empty"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight</label>
                        <input type="number" name="weight" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                        <select name="unit" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <option value="g">Grams (g)</option>
                            <option value="kg">Kilograms (kg)</option>
                            <option value="ml">Milliliters (ml)</option>
                            <option value="L">Liters (L)</option>
                            <option value="piece">Piece</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price (₹) *</label>
                        <input type="number" name="price" step="0.01" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price</label>
                        <input type="number" name="discount_price" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Qty *</label>
                        <input type="number" name="stock_quantity" required value="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Alert</label>
                        <input type="number" name="low_stock_threshold" value="10"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="text-green-600 focus:ring-green-500 rounded">
                        <span class="ml-2 text-sm">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_default" value="1"
                               class="text-green-600 focus:ring-green-500 rounded">
                        <span class="ml-2 text-sm">Default</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold">
                    <i class="fas fa-plus mr-1"></i> Add Variant
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Variants -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">
            <i class="fas fa-list text-green-600 mr-2"></i>Existing Variants ({{ $product->variants->count() }})
        </h3>

        @if($product->variants->count() > 0)
            <div class="space-y-4">
                @foreach($product->variants as $variant)
                    <div class="border rounded-lg p-4 {{ $variant->is_default ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                        <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" class="space-y-3">
                            @csrf
                            @method('PUT')
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-lg">{{ $variant->name }}</span>
                                    @if($variant->is_default)
                                        <span class="bg-green-600 text-white text-xs px-2 py-1 rounded">Default</span>
                                    @endif
                                    @if(!$variant->is_active)
                                        <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded">Inactive</span>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-500">SKU: {{ $variant->sku }}</span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Name</label>
                                    <input type="text" name="name" value="{{ $variant->name }}" required
                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">SKU</label>
                                    <input type="text" name="sku" value="{{ $variant->sku }}"
                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Weight</label>
                                    <input type="number" name="weight" value="{{ $variant->weight }}" step="0.01"
                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Unit</label>
                                    <select name="unit" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                        <option value="g" {{ $variant->unit === 'g' ? 'selected' : '' }}>g</option>
                                        <option value="kg" {{ $variant->unit === 'kg' ? 'selected' : '' }}>kg</option>
                                        <option value="ml" {{ $variant->unit === 'ml' ? 'selected' : '' }}>ml</option>
                                        <option value="L" {{ $variant->unit === 'L' ? 'selected' : '' }}>L</option>
                                        <option value="piece" {{ $variant->unit === 'piece' ? 'selected' : '' }}>piece</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Price (₹)</label>
                                    <input type="number" name="price" value="{{ $variant->price }}" step="0.01" required
                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Discount Price</label>
                                    <input type="number" name="discount_price" value="{{ $variant->discount_price }}" step="0.01"
                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Stock</label>
                                    <input type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" required
                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500 {{ $variant->stock_quantity <= $variant->low_stock_threshold ? 'bg-yellow-50 border-yellow-500' : '' }}">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Low Stock Alert</label>
                                    <input type="number" name="low_stock_threshold" value="{{ $variant->low_stock_threshold }}"
                                           class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t">
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_active" value="1" {{ $variant->is_active ? 'checked' : '' }}
                                               class="text-green-600 focus:ring-green-500 rounded">
                                        <span class="ml-2 text-sm">Active</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_default" value="1" {{ $variant->is_default ? 'checked' : '' }}
                                               class="text-green-600 focus:ring-green-500 rounded">
                                        <span class="ml-2 text-sm">Default</span>
                                    </label>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-save mr-1"></i> Save
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <form action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" method="POST" class="inline mt-2" 
                              onsubmit="return confirm('Delete this variant?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl mb-2"></i>
                <p>No variants added yet.</p>
                <p class="text-sm">Add variants like 50g, 100g, 500ml to sell this product in different sizes.</p>
            </div>
        @endif
    </div>
</div>
@endsection
