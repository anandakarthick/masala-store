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

            <!-- Product Images - Enhanced with Drag & Drop -->
            <div class="bg-white rounded-lg shadow-md p-6" x-data="imageManager()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-images text-green-600 mr-2"></i>Product Images
                    </h3>
                    <span class="text-xs text-gray-500">Drag to reorder • Click star to set primary</span>
                </div>
                
                @if($product->images->count() > 0)
                    <!-- Image Grid with Drag & Drop -->
                    <div id="image-sortable" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-6">
                        @foreach($product->images->sortBy('sort_order') as $image)
                            <div class="image-item relative group rounded-xl overflow-hidden border-2 transition-all duration-200 cursor-move
                                        {{ $image->is_primary ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200 hover:border-gray-300' }}"
                                 data-id="{{ $image->id }}">
                                
                                <!-- Image -->
                                <div class="aspect-square bg-gray-100">
                                    <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover">
                                </div>
                                
                                <!-- Primary Badge -->
                                @if($image->is_primary)
                                    <div class="absolute top-2 left-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg flex items-center gap-1">
                                        <i class="fas fa-star text-yellow-300"></i> Primary
                                    </div>
                                @endif
                                
                                <!-- Drag Handle -->
                                <div class="absolute top-2 right-2 bg-white/90 text-gray-600 p-1.5 rounded-lg shadow opacity-0 group-hover:opacity-100 transition cursor-move">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                
                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                    @if(!$image->is_primary)
                                        <!-- Set Primary Button -->
                                        <button type="button" 
                                                onclick="setPrimaryImage({{ $image->id }})"
                                                class="w-10 h-10 bg-yellow-500 hover:bg-yellow-600 text-white rounded-full flex items-center justify-center transition transform hover:scale-110 shadow-lg"
                                                title="Set as Primary">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endif
                                    
                                    <!-- View Button -->
                                    <a href="{{ $image->url }}" target="_blank"
                                       class="w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center transition transform hover:scale-110 shadow-lg"
                                       title="View Full Size">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="deleteImage({{ $image->id }})"
                                            class="w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition transform hover:scale-110 shadow-lg"
                                            title="Delete Image">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Sort Order Indicator -->
                                <div class="absolute bottom-2 left-2 bg-black/70 text-white text-xs px-2 py-1 rounded-full">
                                    #<span class="sort-number">{{ $loop->iteration }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Reorder Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start gap-2">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                            <div class="text-sm text-blue-700">
                                <strong>Tip:</strong> Drag images to rearrange their display order. The primary image will be shown first on the product page.
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Images Placeholder -->
                    <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 mb-4">
                        <i class="fas fa-image text-4xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500">No images uploaded yet</p>
                    </div>
                @endif
                
                <!-- Upload New Images -->
                <div class="border-t pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Add More Images
                    </label>
                    <div class="relative">
                        <input type="file" name="images[]" multiple accept="image/*" id="image-upload"
                               class="hidden" @change="handleFileSelect($event)">
                        <label for="image-upload" 
                               class="flex items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="text-center">
                                <i class="fas fa-plus text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">Click to upload or drag & drop</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP up to 2MB each</p>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Preview New Uploads -->
                    <div x-show="newImages.length > 0" class="mt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">New images to upload:</p>
                        <div class="grid grid-cols-4 gap-3">
                            <template x-for="(img, index) in newImages" :key="index">
                                <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200">
                                    <img :src="img.preview" class="w-full h-full object-cover">
                                    <button type="button" @click="removeNewImage(index)"
                                            class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
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

<!-- Hidden forms for AJAX actions -->
<form id="set-primary-form" method="POST" style="display:none;">
    @csrf
</form>

<form id="delete-image-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<form id="reorder-form" method="POST" action="{{ route('admin.products.reorder-images', $product) }}" style="display:none;">
    @csrf
    <input type="hidden" name="order" id="image-order-input">
</form>

<!-- Sortable.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

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

function imageManager() {
    return {
        newImages: [],
        
        handleFileSelect(event) {
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.newImages.push({
                            file: file,
                            preview: e.target.result
                        });
                    };
                    reader.readAsDataURL(file);
                }
            }
        },
        
        removeNewImage(index) {
            this.newImages.splice(index, 1);
            // Clear the file input
            document.getElementById('image-upload').value = '';
        }
    }
}

// Initialize Sortable for drag & drop
document.addEventListener('DOMContentLoaded', function() {
    const sortableEl = document.getElementById('image-sortable');
    if (sortableEl) {
        new Sortable(sortableEl, {
            animation: 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.image-item',
            onEnd: function(evt) {
                updateSortNumbers();
                saveImageOrder();
            }
        });
    }
});

function updateSortNumbers() {
    const items = document.querySelectorAll('#image-sortable .image-item');
    items.forEach((item, index) => {
        const sortNumber = item.querySelector('.sort-number');
        if (sortNumber) {
            sortNumber.textContent = index + 1;
        }
    });
}

function saveImageOrder() {
    const items = document.querySelectorAll('#image-sortable .image-item');
    const order = Array.from(items).map(item => item.dataset.id);
    
    document.getElementById('image-order-input').value = JSON.stringify(order);
    
    // Send AJAX request to save order
    fetch('{{ route("admin.products.reorder-images", $product) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order: order })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Image order updated!', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update order', 'error');
    });
}

function setPrimaryImage(imageId) {
    fetch(`{{ url('admin/products/images') }}/${imageId}/set-primary`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Primary image updated!', 'success');
            location.reload();
        } else {
            showToast(data.message || 'Failed to update primary image', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update primary image', 'error');
    });
}

function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) return;
    
    fetch(`{{ url('admin/products/images') }}/${imageId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Image deleted!', 'success');
            location.reload();
        } else {
            showToast(data.message || 'Failed to delete image', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to delete image', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg text-white font-medium shadow-lg z-50 transition-all transform ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>

<style>
.sortable-ghost {
    opacity: 0.4 !important;
}
.sortable-chosen {
    transform: scale(1.02);
    box-shadow: 0 0 0 3px #22c55e !important;
    border-radius: 0.75rem;
}
.sortable-drag {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
}
</style>
@endsection
