<article class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300 relative"
     x-data="offerCard_{{ $product->id }}()">
    <!-- Discount Badge - Dynamic -->
    <template x-if="discountPercent > 0">
        <div class="absolute top-2 left-2 z-10">
            <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg animate-pulse">
                <span x-text="discountPercent"></span>% OFF
            </span>
        </div>
    </template>
    
    @if($product->is_combo)
        <div class="absolute top-2 right-2 z-10">
            <span class="bg-green-600 text-white text-xs px-2 py-0.5 rounded">
                <i class="fas fa-gift mr-1" aria-hidden="true"></i>Pack
            </span>
        </div>
    @endif
    
    <!-- Product Image -->
    <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden">
        <div class="aspect-square bg-gray-100">
            @if($product->primary_image_url)
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                     loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <i class="fas fa-image text-4xl"></i>
                </div>
            @endif
        </div>
        
        <!-- Quick View Overlay -->
        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
            <span class="bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium">
                View Details
            </span>
        </div>
    </a>
    
    <!-- Product Info -->
    <div class="p-3">
        <p class="text-xs text-gray-500 mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
        <h3 class="font-semibold text-gray-800 text-sm line-clamp-2 mb-2 group-hover:text-green-600 transition-colors">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        
        <!-- Dynamic Price -->
        <div class="flex items-center gap-2 mb-2">
            <template x-if="currentDiscountPrice">
                <div class="flex items-center gap-2">
                    <span class="text-lg font-bold text-green-600">₹<span x-text="currentDiscountPrice.toFixed(0)"></span></span>
                    <span class="text-sm text-gray-400 line-through">₹<span x-text="currentPrice.toFixed(0)"></span></span>
                </div>
            </template>
            <template x-if="!currentDiscountPrice">
                <span class="text-lg font-bold text-green-600">₹<span x-text="currentPrice.toFixed(0)"></span></span>
            </template>
        </div>
        
        <!-- Savings Badge - Dynamic -->
        <template x-if="currentDiscountPrice && savings > 0">
            <div class="bg-green-50 border border-green-200 rounded-lg px-2 py-1 mb-3">
                <span class="text-xs text-green-700 font-medium">
                    <i class="fas fa-piggy-bank mr-1"></i>
                    You save ₹<span x-text="savings.toFixed(0)"></span>
                </span>
            </div>
        </template>
        
        @if(!$product->isOutOfStock())
            @if($product->has_variants && $product->activeVariants->count() > 0)
                <!-- Variant Selector -->
                <div class="mb-2">
                    <select x-model="selectedVariantId" 
                            @change="onVariantChange()"
                            class="w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-green-500 focus:border-green-500 bg-white">
                        @foreach($product->activeVariants as $variant)
                            <option value="{{ $variant->id }}" 
                                    {{ $variant->isOutOfStock() ? 'disabled' : '' }}>
                                {{ $variant->name }} - ₹{{ number_format($variant->effective_price, 0) }}
                                {{ $variant->isOutOfStock() ? '(Out of Stock)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Add to Cart with Quantity -->
                <div class="flex items-center gap-2" x-show="currentStock > 0">
                    <div class="flex items-center border border-gray-300 rounded-lg flex-shrink-0">
                        <button type="button" 
                                @click="decrementQty()"
                                class="w-7 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-l-lg"
                                aria-label="Decrease quantity">
                            <i class="fas fa-minus text-xs" aria-hidden="true"></i>
                        </button>
                        <input type="number" 
                               x-model.number="quantity" 
                               min="1" 
                               :max="currentStock"
                               class="w-8 h-8 text-center border-0 focus:ring-0 text-xs p-0"
                               readonly
                               aria-label="Quantity">
                        <button type="button" 
                                @click="incrementQty()"
                                class="w-7 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-r-lg"
                                aria-label="Increase quantity">
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button type="button" 
                            @click="addToCartWithVariant()"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-cart-plus"></i> Add
                    </button>
                </div>
                
                <!-- Out of Stock for selected variant -->
                <button x-show="currentStock <= 0" disabled 
                        class="w-full bg-gray-400 text-white py-2 rounded-lg text-xs font-medium cursor-not-allowed" 
                        aria-disabled="true">
                    Out of Stock
                </button>
            @else
                <!-- Non-variant product -->
                <div class="flex items-center gap-2">
                    <div class="flex items-center border border-gray-300 rounded-lg flex-shrink-0">
                        <button type="button" 
                                @click="decrementQty()"
                                class="w-7 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-l-lg"
                                aria-label="Decrease quantity">
                            <i class="fas fa-minus text-xs" aria-hidden="true"></i>
                        </button>
                        <input type="number" 
                               x-model.number="quantity" 
                               min="1" 
                               :max="currentStock"
                               class="w-8 h-8 text-center border-0 focus:ring-0 text-xs p-0"
                               readonly
                               aria-label="Quantity">
                        <button type="button" 
                                @click="incrementQty()"
                                class="w-7 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-r-lg"
                                aria-label="Increase quantity">
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                    <button type="button" 
                            @click="addToCartSimple()"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-2 rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-cart-plus"></i> Add
                    </button>
                </div>
            @endif
        @else
            <button disabled class="w-full bg-gray-400 text-white py-2 rounded-lg text-xs font-medium cursor-not-allowed" aria-disabled="true">
                Out of Stock
            </button>
        @endif
    </div>
</article>

<script>
function offerCard_{{ $product->id }}() {
    return {
        productId: {{ $product->id }},
        hasVariants: {{ $product->has_variants ? 'true' : 'false' }},
        quantity: 1,
        selectedVariantId: {{ $product->has_variants && $product->activeVariants->count() > 0 ? ($product->defaultVariant->id ?? $product->activeVariants->where('stock_quantity', '>', 0)->first()->id ?? $product->activeVariants->first()->id) : 'null' }},
        
        // Base product data (for non-variant products)
        basePrice: {{ (float) $product->price }},
        baseDiscountPrice: {{ $product->discount_price ? (float) $product->discount_price : 'null' }},
        baseStock: {{ $product->stock_quantity }},
        
        // Variants data
        variants: {
            @if($product->has_variants && $product->activeVariants->count() > 0)
                @foreach($product->activeVariants as $variant)
                {{ $variant->id }}: {
                    price: {{ (float) $variant->price }},
                    discountPrice: {{ $variant->discount_price ? (float) $variant->discount_price : 'null' }},
                    stock: {{ $variant->stock_quantity }}
                },
                @endforeach
            @endif
        },
        
        get currentPrice() {
            if (this.hasVariants && this.selectedVariantId && this.variants[this.selectedVariantId]) {
                return this.variants[this.selectedVariantId].price;
            }
            return this.basePrice;
        },
        
        get currentDiscountPrice() {
            if (this.hasVariants && this.selectedVariantId && this.variants[this.selectedVariantId]) {
                return this.variants[this.selectedVariantId].discountPrice;
            }
            return this.baseDiscountPrice;
        },
        
        get currentStock() {
            if (this.hasVariants && this.selectedVariantId && this.variants[this.selectedVariantId]) {
                return this.variants[this.selectedVariantId].stock;
            }
            return this.baseStock;
        },
        
        get discountPercent() {
            if (!this.currentDiscountPrice) return 0;
            return Math.round(((this.currentPrice - this.currentDiscountPrice) / this.currentPrice) * 100);
        },
        
        get savings() {
            if (!this.currentDiscountPrice) return 0;
            return this.currentPrice - this.currentDiscountPrice;
        },
        
        onVariantChange() {
            this.quantity = 1;
        },
        
        incrementQty() {
            if (this.quantity < this.currentStock && this.quantity < 99) {
                this.quantity++;
            }
        },
        
        decrementQty() {
            if (this.quantity > 1) {
                this.quantity--;
            }
        },
        
        addToCartWithVariant() {
            if (typeof addToCart === 'function') {
                addToCart(this.productId, this.quantity, this.selectedVariantId);
            }
        },
        
        addToCartSimple() {
            if (typeof addToCart === 'function') {
                addToCart(this.productId, this.quantity, null);
            }
        }
    };
}
</script>
