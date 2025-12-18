@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page_title', 'Edit Product')

@section('content')
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" x-data="productForm()">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                            <select name="category_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                        <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="500"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Description</label>
                        <textarea name="description" rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Pricing</h3>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price (₹) *</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount Price (₹)</label>
                        <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Wholesale Price (₹)</label>
                        <input type="number" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HSN Code</label>
                        <input type="text" name="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">GST %</label>
                        <input type="number" name="gst_percentage" value="{{ old('gst_percentage', $product->gst_percentage) }}" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
            </div>

            <!-- Inventory -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Inventory</h3>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight</label>
                        <input type="text" name="weight" value="{{ old('weight', $product->weight) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                        <select name="unit" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            <option value="g" {{ old('unit', $product->unit) === 'g' ? 'selected' : '' }}>Grams (g)</option>
                            <option value="kg" {{ old('unit', $product->unit) === 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                            <option value="ml" {{ old('unit', $product->unit) === 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                            <option value="L" {{ old('unit', $product->unit) === 'L' ? 'selected' : '' }}>Liters (L)</option>
                            <option value="piece" {{ old('unit', $product->unit) === 'piece' ? 'selected' : '' }}>Piece</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                        <input type="text" name="batch_number" value="{{ old('batch_number', $product->batch_number) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturing Date</label>
                        <input type="date" name="manufacturing_date" value="{{ old('manufacturing_date', $product->manufacturing_date?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-600">Current Stock:</span>
                            <span class="text-xl font-bold ml-2 {{ $product->isLowStock() ? 'text-yellow-600' : ($product->isOutOfStock() ? 'text-red-600' : 'text-green-600') }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </div>
                        <a href="{{ route('admin.products.show', $product) }}" class="text-green-600 hover:text-green-700 text-sm">
                            Manage Stock →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Combo/Pack Items -->
            <div class="bg-white rounded-lg shadow-md p-6" x-show="isCombo">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-boxes text-green-600 mr-2"></i>Pack Contents
                    </h3>
                    <button type="button" @click="addComboItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-plus mr-1"></i> Add Item
                    </button>
                </div>
                
                <p class="text-sm text-gray-600 mb-4">Add items that are included in this combo/gift pack. Customers will see this list on the product page.</p>
                
                <div class="space-y-4">
                    <template x-for="(item, index) in comboItems" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-start justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Item #<span x-text="index + 1"></span></span>
                                <button type="button" @click="removeComboItem(index)" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Item Name *</label>
                                    <input type="text" 
                                           :name="'combo_items[' + index + '][item_name]'" 
                                           x-model="item.item_name"
                                           placeholder="e.g., Turmeric Powder"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Quantity</label>
                                    <input type="text" 
                                           :name="'combo_items[' + index + '][item_quantity]'" 
                                           x-model="item.item_quantity"
                                           placeholder="e.g., 100g, 50ml"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm text-gray-600 mb-1">Description (optional)</label>
                                <input type="text" 
                                       :name="'combo_items[' + index + '][item_description]'" 
                                       x-model="item.item_description"
                                       placeholder="Brief description of this item"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm text-gray-600 mb-1">Link to Product (optional)</label>
                                <select :name="'combo_items[' + index + '][included_product_id]'" 
                                        x-model="item.included_product_id"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">-- Not linked --</option>
                                    @foreach($allProducts as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="comboItems.length === 0" class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl mb-2"></i>
                    <p>No items added yet. Click "Add Item" to add pack contents.</p>
                </div>
            </div>

            <!-- Images -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Product Images</h3>
                
                @if($product->images->count() > 0)
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        @foreach($product->images as $image)
                            <div class="relative group">
                                <img src="{{ $image->url }}" alt="" class="w-full h-24 object-cover rounded-lg {{ $image->is_primary ? 'ring-2 ring-green-500' : '' }}">
                                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center space-x-2">
                                    @if(!$image->is_primary)
                                        <form action="{{ route('admin.products.set-primary-image', $image) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-white hover:text-green-400" title="Set as primary">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.products.delete-image', $image) }}" method="POST" class="inline" onsubmit="return confirm('Delete this image?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-white hover:text-red-400" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @if($image->is_primary)
                                    <span class="absolute top-1 left-1 bg-green-500 text-white text-xs px-1 rounded">Primary</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <input type="file" name="images[]" multiple accept="image/*"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                <p class="text-sm text-gray-500 mt-1">Upload additional images</p>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Status</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500 rounded">
                        <span class="ml-2">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500 rounded">
                        <span class="ml-2">Featured Product</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_combo" value="1" x-model="isCombo"
                               {{ old('is_combo', $product->is_combo) ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500 rounded">
                        <span class="ml-2">This is a Combo/Gift Pack</span>
                    </label>
                </div>
                <p x-show="isCombo" class="text-sm text-green-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> Add pack contents below
                </p>
            </div>

            <!-- SEO -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">SEO</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">{{ old('meta_description', $product->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Variants -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Product Variants</h3>
                <p class="text-sm text-gray-600 mb-3">Add different sizes/packs for this product (50g, 100g, 500ml, etc.)</p>
                <a href="{{ route('admin.products.variants.index', $product) }}" 
                   class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
                    <i class="fas fa-layer-group mr-1"></i> Manage Variants ({{ $product->variants->count() }})
                </a>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold">
                    Update Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="block w-full text-center bg-gray-200 text-gray-700 py-3 rounded-lg mt-2">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<script>
function productForm() {
    return {
        isCombo: {{ $product->is_combo ? 'true' : 'false' }},
        comboItems: {!! json_encode($product->comboItems->map(function($item) {
            return [
                'item_name' => $item->item_name,
                'item_quantity' => $item->item_quantity,
                'item_description' => $item->item_description,
                'included_product_id' => $item->included_product_id,
            ];
        })) !!},
        
        addComboItem() {
            this.comboItems.push({
                item_name: '',
                item_quantity: '',
                item_description: '',
                included_product_id: ''
            });
        },
        
        removeComboItem(index) {
            this.comboItems.splice(index, 1);
        }
    }
}
</script>
@endsection
