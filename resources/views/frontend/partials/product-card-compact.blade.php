<article class="bg-white rounded-lg shadow overflow-hidden group hover:shadow-md transition" 
     x-data="compactCard_{{ $product->id }}()">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <!-- Product Image - Compact -->
        <div class="relative h-28 sm:h-32 md:h-36 bg-gray-100 overflow-hidden">
            @if($product->primary_image_url)
                <img src="{{ $product->primary_image_url }}" 
                     alt="{{ $product->name }} - {{ $product->category->name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                     loading="lazy"
                     decoding="async"
                     width="150"
                     height="150">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <i class="fas fa-image text-2xl" aria-hidden="true"></i>
                </div>
            @endif
            
            <!-- Discount Badge - Dynamic -->
            <template x-if="discountPercent > 0">
                <span class="absolute top-1 left-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded font-medium">
                    -<span x-text="discountPercent"></span>%
                </span>
            </template>
            
            @if($product->isOutOfStock())
                <span class="absolute top-1 right-1 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded">
                    Out
                </span>
            @endif
        </div>
    </a>
    
    <!-- Product Info - Compact -->
    <div class="p-2">
        <h3 class="font-medium text-gray-800 text-xs leading-tight group-hover:text-green-600 transition line-clamp-2 min-h-[2rem]">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        
        <div class="mt-1.5">
            
            <!-- Dynamic Price -->
            <div class="flex items-center gap-1 flex-wrap">
                <template x-if="currentDiscountPrice">
                    <div class="flex items-center gap-1">
                        <span class="text-sm font-bold text-green-600">₹<span x-text="currentDiscountPrice.toFixed(0)"></span></span>
                        <span class="text-[10px] text-gray-400 line-through">₹<span x-text="currentPrice.toFixed(0)"></span></span>
                    </div>
                </template>
                <template x-if="!currentDiscountPrice">
                    <span class="text-sm font-bold text-green-600">₹<span x-text="currentPrice.toFixed(0)"></span></span>
                </template>
            </div>
            
            @if(!$product->isOutOfStock())
                @if($product->has_variants && $product->activeVariants->count() > 0)
                    <!-- Variant Selector - Compact -->
                    <select x-model="selectedVariantId" 
                            @change="onVariantChange()"
                            class="mt-1.5 w-full text-[10px] border border-gray-300 rounded px-1.5 py-1 focus:ring-green-500 focus:border-green-500 bg-white">
                        @foreach($product->activeVariants as $variant)
                            <option value="{{ $variant->id }}" 
                                    {{ $variant->isOutOfStock() ? 'disabled' : '' }}>
                                {{ $variant->name }} - ₹{{ number_format($variant->effective_price, 0) }}
                                {{ $variant->isOutOfStock() ? '(Out)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    
                    <!-- Add to Cart Button -->
                    <button type="button" 
                            x-show="currentStock > 0"
                            @click="addToCartWithVariant()"
                            class="mt-1.5 w-full flex items-center justify-center gap-1 bg-green-600 hover:bg-green-700 text-white py-1.5 rounded text-[11px] font-medium transition"
                            aria-label="Add to cart">
                        <i class="fas fa-cart-plus" aria-hidden="true"></i> Add
                    </button>
                    
                    <button x-show="currentStock <= 0" disabled 
                            class="mt-1.5 w-full bg-gray-400 text-white py-1.5 rounded text-[11px] font-medium cursor-not-allowed" 
                            aria-disabled="true">
                        Out of Stock
                    </button>
                @else
                    <button type="button" 
                            @click="addToCartSimple()"
                            class="mt-2 w-full flex items-center justify-center gap-1 bg-green-600 hover:bg-green-700 text-white py-1.5 rounded text-[11px] font-medium transition"
                            aria-label="Add {{ $product->name }} to cart">
                        <i class="fas fa-cart-plus" aria-hidden="true"></i> Add
                    </button>
                @endif
            @else
                <button disabled class="mt-2 w-full bg-gray-400 text-white py-1.5 rounded text-[11px] font-medium cursor-not-allowed" aria-disabled="true">
                    Out of Stock
                </button>
            @endif
        </div>
    </div>
</article>

<script>
function compactCard_{{ $product->id }}() {
    return {
        productId: {{ $product->id }},
        hasVariants: {{ $product->has_variants ? 'true' : 'false' }},
        selectedVariantId: {{ $product->has_variants && $product->activeVariants->count() > 0 ? ($product->defaultVariant->id ?? $product->activeVariants->where('stock_quantity', '>', 0)->first()->id ?? $product->activeVariants->first()->id) : 'null' }},
        
        // Base product data
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
        
        onVariantChange() {
            // Nothing special needed
        },
        
        addToCartWithVariant() {
            if (typeof addToCart === 'function') {
                addToCart(this.productId, 1, this.selectedVariantId);
            }
        },
        
        addToCartSimple() {
            if (typeof addToCart === 'function') {
                addToCart(this.productId, 1, null);
            }
        }
    };
}
</script>
