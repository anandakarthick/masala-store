<article class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-lg transition" 
     itemscope 
     itemtype="https://schema.org/Product"
     x-data="productCard_{{ $product->id }}()">
    <a href="{{ route('products.show', $product->slug) }}" class="block" itemprop="url">
        <div class="relative h-40 sm:h-44 md:h-48 bg-gray-100 overflow-hidden">
            @if($product->primary_image_url)
                <img src="{{ $product->primary_image_url }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                     loading="lazy"
                     itemprop="image"
                     width="200"
                     height="200">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <i class="fas fa-image text-3xl" aria-hidden="true"></i>
                </div>
            @endif
            
            <!-- Discount Badge - Dynamic -->
            <template x-if="discountPercent > 0">
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded">
                    -<span x-text="discountPercent"></span>%
                </span>
            </template>
            
            @if($product->is_combo)
                <span class="absolute top-2 right-2 bg-green-600 text-white text-xs px-2 py-0.5 rounded">
                    <i class="fas fa-gift mr-1" aria-hidden="true"></i>Pack
                </span>
            @elseif($product->isOutOfStock())
                <span class="absolute top-2 right-2 bg-gray-800 text-white text-xs px-2 py-0.5 rounded">
                    Out of Stock
                </span>
            @endif
        </div>
    </a>
    
    <div class="p-3">
        <a href="{{ route('category.show', $product->category->slug) }}" 
           class="text-xs text-green-600 hover:text-green-700">
            {{ $product->category->name }}
        </a>
        
        <h3 class="font-medium text-gray-800 mt-1 text-sm leading-tight group-hover:text-green-600 transition line-clamp-2" itemprop="name">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        
        <!-- Weight/Variant Display -->
        <p class="text-xs text-gray-500 mt-1" x-text="currentWeightDisplay">{{ $product->weight_display }}</p>
        
        <div class="mt-2" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
            <meta itemprop="priceCurrency" content="INR">
            <link itemprop="availability" href="{{ $product->isOutOfStock() ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock' }}">
            
            <!-- Dynamic Price Display -->
            <div class="mb-2">
                <template x-if="currentDiscountPrice">
                    <div>
                        <span class="text-sm font-bold text-green-600">₹<span x-text="currentDiscountPrice.toFixed(2)"></span></span>
                        <span class="text-xs text-gray-400 line-through ml-1">₹<span x-text="currentPrice.toFixed(2)"></span></span>
                    </div>
                </template>
                <template x-if="!currentDiscountPrice">
                    <span class="text-sm font-bold text-green-600">₹<span x-text="currentPrice.toFixed(2)"></span></span>
                </template>
            </div>
            
            @if(!$product->isOutOfStock())
                @if($product->has_variants && $product->activeVariants->count() > 0)
                    <!-- Variant Selector -->
                    <div class="mb-2">
                        <select x-model="selectedVariantId" 
                                @change="onVariantChange()"
                                class="w-full text-xs border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-green-500 focus:border-green-500 bg-white">
                            @foreach($product->activeVariants as $variant)
                                <option value="{{ $variant->id }}" 
                                        {{ $variant->isOutOfStock() ? 'disabled' : '' }}
                                        data-price="{{ $variant->price }}"
                                        data-discount="{{ $variant->discount_price ?? '' }}"
                                        data-stock="{{ $variant->stock_quantity }}"
                                        data-weight="{{ $variant->name }}">
                                    {{ $variant->name }} - ₹{{ number_format($variant->effective_price, 0) }}
                                    {{ $variant->isOutOfStock() ? '(Out of Stock)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Add to Cart with Quantity for Variants -->
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
                                class="flex-1 flex items-center justify-center gap-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition text-xs font-medium"
                                aria-label="Add {{ $product->name }} to cart">
                            <i class="fas fa-cart-plus" aria-hidden="true"></i> Add
                        </button>
                    </div>
                    
                    <!-- Out of Stock for selected variant -->
                    <button x-show="currentStock <= 0" disabled 
                            class="w-full bg-gray-400 text-white py-2 rounded-lg text-xs font-medium cursor-not-allowed" 
                            aria-disabled="true">
                        Out of Stock
                    </button>
                @else
                    <!-- Non-variant product - Original behavior -->
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
                                class="flex-1 flex items-center justify-center gap-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition text-xs font-medium"
                                aria-label="Add {{ $product->name }} to cart">
                            <i class="fas fa-cart-plus" aria-hidden="true"></i> Add
                        </button>
                    </div>
                @endif
            @else
                <button disabled class="w-full bg-gray-400 text-white py-2 rounded-lg text-xs font-medium cursor-not-allowed" aria-disabled="true">
                    Out of Stock
                </button>
            @endif
        </div>
    </div>
</article>

<script>
function productCard_{{ $product->id }}() {
    return {
        productId: {{ $product->id }},
        hasVariants: {{ $product->has_variants ? 'true' : 'false' }},
        quantity: 1,
        selectedVariantId: {{ $product->has_variants && $product->activeVariants->count() > 0 ? ($product->defaultVariant->id ?? $product->activeVariants->where('stock_quantity', '>', 0)->first()->id ?? $product->activeVariants->first()->id) : 'null' }},
        
        // Base product data (for non-variant products)
        basePrice: {{ (float) $product->price }},
        baseDiscountPrice: {{ $product->discount_price ? (float) $product->discount_price : 'null' }},
        baseStock: {{ $product->stock_quantity }},
        baseWeight: '{{ $product->weight }} {{ $product->unit }}',
        
        // Variants data
        variants: {
            @if($product->has_variants && $product->activeVariants->count() > 0)
                @foreach($product->activeVariants as $variant)
                {{ $variant->id }}: {
                    price: {{ (float) $variant->price }},
                    discountPrice: {{ $variant->discount_price ? (float) $variant->discount_price : 'null' }},
                    stock: {{ $variant->stock_quantity }},
                    weight: '{{ $variant->name }}'
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
        
        get currentWeightDisplay() {
            if (this.hasVariants && this.selectedVariantId && this.variants[this.selectedVariantId]) {
                return this.variants[this.selectedVariantId].weight;
            }
            return this.baseWeight;
        },
        
        get discountPercent() {
            if (!this.currentDiscountPrice) return 0;
            return Math.round(((this.currentPrice - this.currentDiscountPrice) / this.currentPrice) * 100);
        },
        
        onVariantChange() {
            this.quantity = 1; // Reset quantity when variant changes
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
