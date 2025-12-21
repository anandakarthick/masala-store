@extends('layouts.admin')

@section('title', 'Create Custom Combo')
@section('page-title', 'Create Custom Combo')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.combos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Basic Info -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Basic Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Combo Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                           placeholder="e.g., Pick Any 3, Build Your Box">
                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg p-2">
                    @error('image')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Describe this combo offer...">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        
        <!-- Product Selection Rules -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Product Selection Rules</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Products *</label>
                    <input type="number" name="min_products" value="{{ old('min_products', 2) }}" min="1" max="50" required
                           class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-gray-500 text-xs mt-1">Minimum products required to complete combo</p>
                    @error('min_products')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Products *</label>
                    <input type="number" name="max_products" value="{{ old('max_products', 5) }}" min="1" max="50" required
                           class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <p class="text-gray-500 text-xs mt-1">Maximum products allowed in combo</p>
                    @error('max_products')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <div class="mt-4 space-y-3">
                <label class="flex items-center">
                    <input type="checkbox" name="allow_same_product" value="1" {{ old('allow_same_product') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-sm text-gray-700">Allow same product multiple times</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" name="allow_variants" value="1" {{ old('allow_variants', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-sm text-gray-700">Allow product variants selection</span>
                </label>
            </div>
        </div>
        
        <!-- Discount Settings -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Discount Settings</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount Type *</label>
                    <select name="discount_type" required
                            class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                        <option value="per_item" {{ old('discount_type') === 'per_item' ? 'selected' : '' }}>Per Item (₹)</option>
                    </select>
                    @error('discount_type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value *</label>
                    <input type="number" name="discount_value" value="{{ old('discount_value', 10) }}" min="0" step="0.01" required
                           class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('discount_value')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fixed Combo Price (Optional)</label>
                    <input type="number" name="combo_price" value="{{ old('combo_price') }}" min="0" step="0.01"
                           class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Leave empty to use discount calculation">
                    <p class="text-gray-500 text-xs mt-1">If set, this price will be used instead of calculating discount</p>
                    @error('combo_price')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
        
        <!-- Product Restrictions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Product Restrictions (Optional)</h2>
            <p class="text-gray-500 text-sm mb-4">Leave all empty to allow all products</p>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allowed Categories</label>
                    <select name="allowed_categories[]" multiple
                            class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" style="height: 120px">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, old('allowed_categories', [])) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-gray-500 text-xs mt-1">Hold Ctrl/Cmd to select multiple. Leave empty for all categories.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Excluded Products</label>
                    <select name="excluded_products[]" multiple
                            class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" style="height: 120px">
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ in_array($product->id, old('excluded_products', [])) ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->sku }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-gray-500 text-xs mt-1">Products that should NOT be available in this combo</p>
                </div>
            </div>
        </div>
        
        <!-- Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <p class="text-gray-500 text-xs">Enable or disable this combo</p>
                </div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                       class="w-32 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
        
        <!-- Submit -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.combos.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
                Create Combo
            </button>
        </div>
    </form>
</div>
@endsection
