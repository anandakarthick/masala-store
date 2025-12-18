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
            <p class="text-gray-600">SKU: {{ $product->sku }} | Category: {{ $product->category->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-500">Base Price: ₹{{ number_format($product->price, 2) }}</p>
            <p class="text-sm">
                <span class="px-2 py-1 rounded text-xs {{ $product->product_type === 'clothing' ? 'bg-purple-100 text-purple-600' : 'bg-green-100 text-green-600' }}">
                    {{ ucfirst($product->product_type ?? 'food') }} Product
                </span>
            </p>
        </div>
    </div>
</div>

<!-- Tabs -->
<div x-data="{ activeTab: 'single' }" class="mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8">
            <button @click="activeTab = 'single'" 
                    :class="activeTab === 'single' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-2 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-plus mr-1"></i> Add Single Variant
            </button>
            <button @click="activeTab = 'bulk'" 
                    :class="activeTab === 'bulk' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="py-2 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-layer-group mr-1"></i> Bulk Create (Size/Color)
            </button>
        </nav>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Single Variant Form -->
        <div x-show="activeTab === 'single'" class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-plus text-green-600 mr-2"></i>Add New Variant
            </h3>
            
            <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <!-- Basic Info -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Variant Name *</label>
                        <input type="text" name="name" required placeholder="e.g., 50g, S-Red, M-Blue"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" name="sku" placeholder="Auto-generated if empty"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Clothing Attributes -->
                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-tshirt text-purple-500 mr-1"></i> Clothing Attributes (Optional)
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Size</label>
                                <select name="size" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Select Size</option>
                                    @foreach($attributes->where('code', 'size')->first()?->activeValues ?? [] as $value)
                                        <option value="{{ $value->value }}">{{ $value->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Color</label>
                                <select name="color" id="colorSelect" onchange="updateColorCode(this)" 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="" data-code="">Select Color</option>
                                    @foreach($attributes->where('code', 'color')->first()?->activeValues ?? [] as $value)
                                        <option value="{{ $value->value }}" data-code="{{ $value->color_code }}">{{ $value->display_name }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="color_code" id="colorCodeInput">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Brand</label>
                                <input type="text" name="brand" placeholder="Brand name"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Material</label>
                                <select name="material" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Select Material</option>
                                    @foreach($attributes->where('code', 'material')->first()?->activeValues ?? [] as $value)
                                        <option value="{{ $value->value }}">{{ $value->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Pattern</label>
                                <select name="pattern" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Select Pattern</option>
                                    @foreach($attributes->where('code', 'pattern')->first()?->activeValues ?? [] as $value)
                                        <option value="{{ $value->value }}">{{ $value->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Fit</label>
                                <select name="fit" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Select Fit</option>
                                    @foreach($attributes->where('code', 'fit')->first()?->activeValues ?? [] as $value)
                                        <option value="{{ $value->value }}">{{ $value->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 mt-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Sleeve</label>
                                <select name="sleeve_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Select</option>
                                    <option value="Full Sleeve">Full Sleeve</option>
                                    <option value="Half Sleeve">Half Sleeve</option>
                                    <option value="Short Sleeve">Short Sleeve</option>
                                    <option value="Sleeveless">Sleeveless</option>
                                    <option value="3/4 Sleeve">3/4 Sleeve</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Neck</label>
                                <select name="neck_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Select</option>
                                    <option value="Round Neck">Round Neck</option>
                                    <option value="V-Neck">V-Neck</option>
                                    <option value="Collar">Collar</option>
                                    <option value="Mandarin">Mandarin</option>
                                    <option value="Boat Neck">Boat Neck</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Occasion</label>
                                <select name="occasion" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Select</option>
                                    <option value="Casual">Casual</option>
                                    <option value="Formal">Formal</option>
                                    <option value="Party">Party</option>
                                    <option value="Sports">Sports</option>
                                    <option value="Ethnic">Ethnic</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Weight/Price Section -->
                    <div class="border-t pt-4 mt-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-balance-scale text-blue-500 mr-1"></i> Weight & Pricing
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Weight</label>
                                <input type="number" name="weight" step="0.01"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Unit *</label>
                                <select name="unit" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="g">Grams (g)</option>
                                    <option value="kg">Kilograms (kg)</option>
                                    <option value="ml">Milliliters (ml)</option>
                                    <option value="L">Liters (L)</option>
                                    <option value="piece" selected>Piece</option>
                                    <option value="pair">Pair</option>
                                    <option value="set">Set</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Price (₹) *</label>
                                <input type="number" name="price" step="0.01" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Discount Price</label>
                                <input type="number" name="discount_price" step="0.01"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Stock Qty *</label>
                                <input type="number" name="stock_quantity" required value="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Low Stock Alert</label>
                                <input type="number" name="low_stock_threshold" value="10"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                    </div>

                    <!-- Variant Image -->
                    <div class="border-t pt-4 mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Variant Image</label>
                        <input type="file" name="variant_image" accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                        <p class="text-xs text-gray-500 mt-1">Optional: Specific image for this variant</p>
                    </div>

                    <!-- Options -->
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

        <!-- Bulk Create Form -->
        <div x-show="activeTab === 'bulk'" x-cloak class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-layer-group text-purple-600 mr-2"></i>Bulk Create Variants
            </h3>
            <p class="text-sm text-gray-500 mb-4">Create multiple variants at once by selecting sizes and colors.</p>
            
            <form action="{{ route('admin.products.variants.bulk', $product) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <!-- Select Sizes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Sizes</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($attributes->where('code', 'size')->first()?->activeValues ?? [] as $value)
                                <label class="flex items-center px-3 py-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" name="sizes[]" value="{{ $value->value }}" 
                                           class="text-green-600 focus:ring-green-500 rounded">
                                    <span class="ml-2 text-sm">{{ $value->value }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Select Colors -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Colors</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($attributes->where('code', 'color')->first()?->activeValues ?? [] as $index => $value)
                                <label class="flex items-center px-3 py-2 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" name="colors[]" value="{{ $value->value }}" 
                                           class="text-green-600 focus:ring-green-500 rounded">
                                    @if($value->color_code)
                                        <span class="ml-2 w-4 h-4 rounded-full border" style="background-color: {{ $value->color_code }}"></span>
                                    @endif
                                    <span class="ml-2 text-sm">{{ $value->value }}</span>
                                    <input type="hidden" name="color_codes[]" value="{{ $value->color_code }}">
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Base Settings -->
                    <div class="border-t pt-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Base Price (₹) *</label>
                                <input type="number" name="base_price" step="0.01" required
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Stock per Variant *</label>
                                <input type="number" name="base_stock" required value="10"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs text-gray-600 mb-1">Unit *</label>
                            <select name="unit" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                <option value="piece" selected>Piece</option>
                                <option value="pair">Pair</option>
                                <option value="set">Set</option>
                                <option value="g">Grams (g)</option>
                                <option value="kg">Kilograms (kg)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg font-semibold">
                        <i class="fas fa-magic mr-1"></i> Create All Combinations
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
                            <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <!-- Header -->
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        @if($variant->variant_image)
                                            <img src="{{ asset('storage/' . $variant->variant_image) }}" alt="" class="w-10 h-10 rounded object-cover">
                                        @elseif($variant->color_code)
                                            <span class="w-8 h-8 rounded-full border-2" style="background-color: {{ $variant->color_code }}"></span>
                                        @endif
                                        <span class="font-semibold text-lg">{{ $variant->display_name }}</span>
                                        @if($variant->is_default)
                                            <span class="bg-green-600 text-white text-xs px-2 py-1 rounded">Default</span>
                                        @endif
                                        @if(!$variant->is_active)
                                            <span class="bg-gray-500 text-white text-xs px-2 py-1 rounded">Inactive</span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">SKU: {{ $variant->sku }}</span>
                                </div>

                                <!-- Attributes Display -->
                                @if($variant->hasClothingAttributes())
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @if($variant->size)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs rounded">Size: {{ $variant->size }}</span>
                                    @endif
                                    @if($variant->color)
                                        <span class="px-2 py-1 bg-pink-100 text-pink-600 text-xs rounded flex items-center gap-1">
                                            @if($variant->color_code)
                                                <span class="w-3 h-3 rounded-full" style="background-color: {{ $variant->color_code }}"></span>
                                            @endif
                                            {{ $variant->color }}
                                        </span>
                                    @endif
                                    @if($variant->brand)
                                        <span class="px-2 py-1 bg-purple-100 text-purple-600 text-xs rounded">{{ $variant->brand }}</span>
                                    @endif
                                    @if($variant->material)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-600 text-xs rounded">{{ $variant->material }}</span>
                                    @endif
                                    @if($variant->pattern)
                                        <span class="px-2 py-1 bg-orange-100 text-orange-600 text-xs rounded">{{ $variant->pattern }}</span>
                                    @endif
                                    @if($variant->fit)
                                        <span class="px-2 py-1 bg-teal-100 text-teal-600 text-xs rounded">{{ $variant->fit }}</span>
                                    @endif
                                </div>
                                @endif

                                <!-- Editable Fields -->
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Name</label>
                                        <input type="text" name="name" value="{{ $variant->name }}" required
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Size</label>
                                        <select name="size" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                            <option value="">-</option>
                                            @foreach($attributes->where('code', 'size')->first()?->activeValues ?? [] as $value)
                                                <option value="{{ $value->value }}" {{ $variant->size === $value->value ? 'selected' : '' }}>
                                                    {{ $value->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Color</label>
                                        <select name="color" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500"
                                                onchange="this.form.querySelector('[name=color_code]').value = this.selectedOptions[0].dataset.code || ''">
                                            <option value="" data-code="">-</option>
                                            @foreach($attributes->where('code', 'color')->first()?->activeValues ?? [] as $value)
                                                <option value="{{ $value->value }}" data-code="{{ $value->color_code }}" 
                                                        {{ $variant->color === $value->value ? 'selected' : '' }}>
                                                    {{ $value->value }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="color_code" value="{{ $variant->color_code }}">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Price (₹)</label>
                                        <input type="number" name="price" value="{{ $variant->price }}" step="0.01" required
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Stock</label>
                                        <input type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" required
                                               class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500 {{ $variant->stock_quantity <= $variant->low_stock_threshold ? 'bg-yellow-50 border-yellow-500' : '' }}">
                                    </div>
                                </div>

                                <!-- More Fields (Collapsible) -->
                                <div x-data="{ expanded: false }" class="mt-3">
                                    <button type="button" @click="expanded = !expanded" class="text-sm text-blue-600 hover:underline">
                                        <span x-show="!expanded"><i class="fas fa-chevron-down mr-1"></i>More Options</span>
                                        <span x-show="expanded"><i class="fas fa-chevron-up mr-1"></i>Less Options</span>
                                    </button>
                                    
                                    <div x-show="expanded" x-cloak class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-3">
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
                                                @foreach(['g', 'kg', 'ml', 'L', 'piece', 'pair', 'set'] as $unit)
                                                    <option value="{{ $unit }}" {{ $variant->unit === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Discount Price</label>
                                            <input type="number" name="discount_price" value="{{ $variant->discount_price }}" step="0.01"
                                                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Low Stock Alert</label>
                                            <input type="number" name="low_stock_threshold" value="{{ $variant->low_stock_threshold }}"
                                                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Brand</label>
                                            <input type="text" name="brand" value="{{ $variant->brand }}"
                                                   class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Material</label>
                                            <select name="material" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                                <option value="">-</option>
                                                @foreach($attributes->where('code', 'material')->first()?->activeValues ?? [] as $value)
                                                    <option value="{{ $value->value }}" {{ $variant->material === $value->value ? 'selected' : '' }}>
                                                        {{ $value->value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Pattern</label>
                                            <select name="pattern" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                                <option value="">-</option>
                                                @foreach($attributes->where('code', 'pattern')->first()?->activeValues ?? [] as $value)
                                                    <option value="{{ $value->value }}" {{ $variant->pattern === $value->value ? 'selected' : '' }}>
                                                        {{ $value->value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Fit</label>
                                            <select name="fit" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                                <option value="">-</option>
                                                @foreach($attributes->where('code', 'fit')->first()?->activeValues ?? [] as $value)
                                                    <option value="{{ $value->value }}" {{ $variant->fit === $value->value ? 'selected' : '' }}>
                                                        {{ $value->value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Sleeve Type</label>
                                            <select name="sleeve_type" class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:ring-green-500 focus:border-green-500">
                                                <option value="">-</option>
                                                @foreach(['Full Sleeve', 'Half Sleeve', 'Short Sleeve', 'Sleeveless', '3/4 Sleeve'] as $sleeve)
                                                    <option value="{{ $sleeve }}" {{ $variant->sleeve_type === $sleeve ? 'selected' : '' }}>{{ $sleeve }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer Actions -->
                                <div class="flex items-center justify-between pt-3 mt-3 border-t">
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
                    <p class="text-sm">Add variants for size, color, weight, or any combination.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateColorCode(select) {
    const colorCode = select.selectedOptions[0].dataset.code || '';
    document.getElementById('colorCodeInput').value = colorCode;
}
</script>
@endpush
