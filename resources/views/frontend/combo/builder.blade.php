@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
@endphp

@section('title', $combo->name . ' - Build Your Combo')
@section('meta_description', $combo->description ?? 'Create your own ' . $combo->name . ' at ' . $businessName . '. ' . $combo->discount_display . ' on selected products.')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="comboBuilder()" x-init="init()">
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-green-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('combo.index') }}" class="hover:text-green-600">Build Combo</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-purple-600 font-medium">{{ $combo->name }}</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Products Selection (Left/Main Area) -->
        <div class="flex-1">
            <!-- Combo Header -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-5 mb-6 text-white">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold mb-1">{{ $combo->name }}</h1>
                        <p class="opacity-90">{{ $combo->description ?? 'Select your favorite products to create your custom combo' }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="bg-white/20 backdrop-blur px-4 py-2 rounded-lg text-lg font-bold">
                            {{ $combo->discount_display }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">Filter:</span>
                    <button @click="categoryFilter = null" 
                            :class="categoryFilter === null ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                        All Products
                    </button>
                    @foreach($categoryNames as $catId => $catName)
                        <button @click="categoryFilter = {{ $catId }}" 
                                :class="categoryFilter === {{ $catId }} ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-3 py-1.5 rounded-full text-sm font-medium transition">
                            {{ $catName }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-lg transition relative"
                         x-show="categoryFilter === null || categoryFilter === {{ $product->category_id }}"
                         x-data="{ 
                            productId: {{ $product->id }},
                            selectedVariant: {{ $product->has_variants && $product->activeVariants->count() > 0 ? ($product->defaultVariant->id ?? $product->activeVariants->first()->id) : 'null' }},
                            variants: {
                                @if($product->has_variants)
                                    @foreach($product->activeVariants as $variant)
                                    {{ $variant->id }}: { 
                                        name: '{{ $variant->name }}', 
                                        price: {{ (float) $variant->effective_price }},
                                        stock: {{ $variant->stock_quantity }}
                                    },
                                    @endforeach
                                @endif
                            },
                            basePrice: {{ (float) $product->effective_price }},
                            baseStock: {{ $product->stock_quantity }},
                            hasVariants: {{ $product->has_variants ? 'true' : 'false' }},
                            get currentPrice() {
                                if (this.hasVariants && this.selectedVariant && this.variants[this.selectedVariant]) {
                                    return this.variants[this.selectedVariant].price;
                                }
                                return this.basePrice;
                            },
                            get currentStock() {
                                if (this.hasVariants && this.selectedVariant && this.variants[this.selectedVariant]) {
                                    return this.variants[this.selectedVariant].stock;
                                }
                                return this.baseStock;
                            },
                            get variantName() {
                                if (this.hasVariants && this.selectedVariant && this.variants[this.selectedVariant]) {
                                    return this.variants[this.selectedVariant].name;
                                }
                                return '';
                            }
                         }">
                        
                        <!-- Check if already added -->
                        <template x-if="isProductInCombo(productId, selectedVariant)">
                            <div class="absolute top-2 right-2 z-10">
                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                    <i class="fas fa-check"></i> Added
                                </span>
                            </div>
                        </template>
                        
                        <!-- Product Image -->
                        <div class="relative h-32 bg-gray-100 overflow-hidden">
                            @if($product->primary_image_url)
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-3">
                            <p class="text-xs text-purple-600 mb-1">{{ $product->category->name }}</p>
                            <h3 class="font-medium text-gray-800 text-sm line-clamp-2 mb-1">{{ $product->name }}</h3>
                            
                            <!-- Price -->
                            <p class="text-green-600 font-bold text-sm mb-2">
                                ₹<span x-text="currentPrice.toFixed(0)"></span>
                            </p>
                            
                            @if($product->has_variants && $product->activeVariants->count() > 0)
                                <!-- Variant Selector -->
                                <select x-model="selectedVariant" 
                                        class="w-full text-xs border border-gray-300 rounded px-2 py-1.5 mb-2 focus:ring-purple-500 focus:border-purple-500">
                                    @foreach($product->activeVariants as $variant)
                                        <option value="{{ $variant->id }}" {{ $variant->isOutOfStock() ? 'disabled' : '' }}>
                                            {{ $variant->name }} - ₹{{ number_format($variant->effective_price, 0) }}
                                            {{ $variant->isOutOfStock() ? '(Out)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            
                            <!-- Add Button -->
                            <button @click="addProductToCombo(productId, '{{ addslashes($product->name) }}', currentPrice, selectedVariant, variantName, '{{ $product->primary_image_url }}')"
                                    :disabled="isAtMaxCapacity || currentStock <= 0"
                                    :class="(isAtMaxCapacity || currentStock <= 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-600 hover:bg-purple-700'"
                                    class="w-full text-white py-2 rounded-lg text-xs font-medium transition flex items-center justify-center gap-1">
                                <template x-if="currentStock <= 0">
                                    <span>Out of Stock</span>
                                </template>
                                <template x-if="currentStock > 0 && isAtMaxCapacity">
                                    <span>Combo Full</span>
                                </template>
                                <template x-if="currentStock > 0 && !isAtMaxCapacity">
                                    <span><i class="fas fa-plus"></i> Add to Combo</span>
                                </template>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Combo Summary Sidebar (Right) -->
        <div class="lg:w-80 flex-shrink-0">
            <div class="bg-white rounded-xl shadow-lg p-5 sticky top-24">
                <h2 class="text-lg font-bold mb-4 flex items-center">
                    <i class="fas fa-box-open text-purple-600 mr-2"></i>
                    Your Combo
                </h2>
                
                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600">Products Selected</span>
                        <span class="font-medium" x-text="comboItems.length + '/' + maxItems"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-2.5 rounded-full transition-all duration-300"
                             :style="'width: ' + (comboItems.length / maxItems * 100) + '%'"></div>
                    </div>
                    <template x-if="comboItems.length < minItems">
                        <p class="text-xs text-orange-600 mt-1">
                            <i class="fas fa-info-circle"></i> Add <span x-text="minItems - comboItems.length"></span> more to complete
                        </p>
                    </template>
                </div>
                
                <!-- Selected Items List -->
                <div class="space-y-3 max-h-64 overflow-y-auto mb-4" x-show="comboItems.length > 0">
                    <template x-for="(item, index) in comboItems" :key="index">
                        <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-2">
                            <img :src="item.image || '/images/placeholder.png'" :alt="item.name" 
                                 class="w-12 h-12 object-cover rounded">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate" x-text="item.name"></p>
                                <p class="text-xs text-gray-500" x-text="item.variantName || ''"></p>
                                <p class="text-sm text-green-600 font-medium">₹<span x-text="item.price.toFixed(0)"></span></p>
                            </div>
                            <button @click="removeFromCombo(index)" 
                                    class="text-red-500 hover:text-red-700 p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </template>
                </div>
                
                <!-- Empty State -->
                <div x-show="comboItems.length === 0" class="text-center py-8 text-gray-400">
                    <i class="fas fa-box-open text-4xl mb-2"></i>
                    <p class="text-sm">No products added yet</p>
                    <p class="text-xs mt-1">Select products from the left</p>
                </div>
                
                <!-- Price Summary -->
                <div class="border-t pt-4 space-y-2" x-show="comboItems.length > 0">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Original Price:</span>
                        <span class="text-gray-800">₹<span x-text="originalPrice.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Combo Discount:</span>
                        <span>-₹<span x-text="discountAmount.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Final Price:</span>
                        <span class="text-purple-600">₹<span x-text="finalPrice.toFixed(2)"></span></span>
                    </div>
                    <template x-if="discountAmount > 0">
                        <div class="bg-green-50 border border-green-200 rounded-lg px-3 py-2 text-center">
                            <span class="text-green-700 text-sm font-medium">
                                🎉 You save ₹<span x-text="discountAmount.toFixed(0)"></span>!
                            </span>
                        </div>
                    </template>
                </div>
                
                <!-- Add to Cart Button -->
                <button @click="addComboToCart()"
                        :disabled="comboItems.length < minItems || isLoading"
                        :class="(comboItems.length < minItems || isLoading) ? 'bg-gray-400 cursor-not-allowed' : 'bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800'"
                        class="w-full text-white py-3 rounded-lg font-semibold mt-4 transition-all flex items-center justify-center gap-2">
                    <template x-if="isLoading">
                        <span><i class="fas fa-spinner fa-spin"></i> Processing...</span>
                    </template>
                    <template x-if="!isLoading && comboItems.length < minItems">
                        <span>Add <span x-text="minItems - comboItems.length"></span> More Products</span>
                    </template>
                    <template x-if="!isLoading && comboItems.length >= minItems">
                        <span><i class="fas fa-cart-plus"></i> Add Combo to Cart</span>
                    </template>
                </button>
                
                <!-- Clear Combo -->
                <button @click="clearCombo()" 
                        x-show="comboItems.length > 0"
                        class="w-full text-red-600 hover:text-red-700 py-2 text-sm font-medium mt-2">
                    <i class="fas fa-trash-alt mr-1"></i> Clear Combo
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function comboBuilder() {
    return {
        comboCartId: null,
        comboItems: [],
        minItems: {{ $combo->min_products }},
        maxItems: {{ $combo->max_products }},
        discountType: '{{ $combo->discount_type }}',
        discountValue: {{ $combo->discount_value }},
        comboPrice: {{ $combo->combo_price ?? 'null' }},
        categoryFilter: null,
        isLoading: false,
        
        init() {
            // Check if there's an existing combo in progress
            @if($comboCart)
                this.comboCartId = {{ $comboCart->id }};
                this.loadExistingCombo();
            @endif
        },
        
        loadExistingCombo() {
            // Load existing combo items from server
            csrfHelper.fetchWithCSRF('{{ route("combo.status") }}', {
                method: 'POST',
                body: JSON.stringify({ combo_cart_id: this.comboCartId })
            }).then(r => r.json())
              .then(data => {
                  if (data.success && data.combo) {
                      this.comboItems = data.combo.items.map(item => ({
                          id: item.id,
                          productId: item.product_id,
                          variantId: item.variant_id,
                          name: item.name,
                          variantName: item.variant_id ? item.name.split(' - ')[1] || '' : '',
                          price: item.unit_price,
                          quantity: item.quantity,
                          image: item.image
                      }));
                  }
              });
        },
        
        get originalPrice() {
            return this.comboItems.reduce((sum, item) => sum + (item.price * (item.quantity || 1)), 0);
        },
        
        get discountAmount() {
            if (this.comboItems.length < this.minItems) return 0;
            
            if (this.comboPrice) {
                return Math.max(0, this.originalPrice - this.comboPrice);
            }
            
            switch (this.discountType) {
                case 'percentage':
                    return this.originalPrice * (this.discountValue / 100);
                case 'fixed':
                    return this.discountValue;
                case 'per_item':
                    return this.discountValue * this.comboItems.length;
                default:
                    return 0;
            }
        },
        
        get finalPrice() {
            if (this.comboPrice && this.comboItems.length >= this.minItems) {
                return this.comboPrice;
            }
            return Math.max(0, this.originalPrice - this.discountAmount);
        },
        
        get isAtMaxCapacity() {
            return this.comboItems.length >= this.maxItems;
        },
        
        isProductInCombo(productId, variantId) {
            return this.comboItems.some(item => 
                item.productId === productId && 
                (item.variantId === variantId || (!item.variantId && !variantId))
            );
        },
        
        async ensureComboStarted() {
            if (this.comboCartId) return true;
            
            try {
                const response = await csrfHelper.fetchWithCSRF('{{ route("combo.start", $combo->id) }}', {
                    method: 'POST',
                    body: JSON.stringify({})
                });
                const data = await response.json();
                
                if (data.success) {
                    this.comboCartId = data.combo_cart_id;
                    return true;
                }
                return false;
            } catch (error) {
                console.error('Error starting combo:', error);
                return false;
            }
        },
        
        async addProductToCombo(productId, name, price, variantId, variantName, image) {
            if (this.isAtMaxCapacity) {
                alert('Combo is full! Maximum ' + this.maxItems + ' products allowed.');
                return;
            }
            
            // Ensure combo is started
            const started = await this.ensureComboStarted();
            if (!started) {
                alert('Could not start combo. Please try again.');
                return;
            }
            
            // Add to server
            try {
                const response = await csrfHelper.fetchWithCSRF('{{ route("combo.add-product") }}', {
                    method: 'POST',
                    body: JSON.stringify({
                        combo_cart_id: this.comboCartId,
                        product_id: productId,
                        variant_id: variantId,
                        quantity: 1
                    })
                });
                const data = await response.json();
                
                if (data.success) {
                    // Refresh items from server response
                    this.comboItems = data.combo.items.map(item => ({
                        id: item.id,
                        productId: item.product_id,
                        variantId: item.variant_id,
                        name: item.name,
                        variantName: item.variant_id ? item.name.split(' - ')[1] || '' : '',
                        price: item.unit_price,
                        quantity: item.quantity,
                        image: item.image
                    }));
                } else {
                    alert(data.message || 'Could not add product');
                }
            } catch (error) {
                console.error('Error adding product:', error);
                alert('Error adding product. Please try again.');
            }
        },
        
        async removeFromCombo(index) {
            const item = this.comboItems[index];
            if (!item || !item.id) {
                this.comboItems.splice(index, 1);
                return;
            }
            
            try {
                const response = await csrfHelper.fetchWithCSRF('{{ route("combo.remove-product") }}', {
                    method: 'POST',
                    body: JSON.stringify({
                        combo_cart_id: this.comboCartId,
                        item_id: item.id
                    })
                });
                const data = await response.json();
                
                if (data.success) {
                    this.comboItems = data.combo.items.map(item => ({
                        id: item.id,
                        productId: item.product_id,
                        variantId: item.variant_id,
                        name: item.name,
                        variantName: item.variant_id ? item.name.split(' - ')[1] || '' : '',
                        price: item.unit_price,
                        quantity: item.quantity,
                        image: item.image
                    }));
                }
            } catch (error) {
                console.error('Error removing product:', error);
            }
        },
        
        async clearCombo() {
            if (!confirm('Are you sure you want to clear your combo?')) return;
            
            if (this.comboCartId) {
                try {
                    await csrfHelper.fetchWithCSRF('{{ route("combo.delete") }}', {
                        method: 'POST',
                        body: JSON.stringify({ combo_cart_id: this.comboCartId })
                    });
                } catch (error) {
                    console.error('Error clearing combo:', error);
                }
            }
            
            this.comboCartId = null;
            this.comboItems = [];
        },
        
        async addComboToCart() {
            if (this.comboItems.length < this.minItems) {
                alert('Please add at least ' + this.minItems + ' products to your combo');
                return;
            }
            
            this.isLoading = true;
            
            try {
                const response = await csrfHelper.fetchWithCSRF('{{ route("combo.add-to-cart") }}', {
                    method: 'POST',
                    body: JSON.stringify({ combo_cart_id: this.comboCartId })
                });
                const data = await response.json();
                
                if (data.success) {
                    // Update cart count
                    if (data.cart_count !== undefined) {
                        window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.cart_count } }));
                    }
                    
                    // Redirect to cart
                    window.location.href = data.redirect || '{{ route("cart.index") }}';
                } else {
                    alert(data.message || 'Could not add combo to cart');
                }
            } catch (error) {
                console.error('Error adding combo to cart:', error);
                alert('Error adding combo to cart. Please try again.');
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
@endsection
