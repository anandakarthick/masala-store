<article class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-lg transition" 
     itemscope 
     itemtype="https://schema.org/Product"
     x-data="{ 
        quantity: 1, 
        maxStock: {{ $product->has_variants ? ($product->activeVariants->first()->stock_quantity ?? 100) : $product->stock_quantity }},
        increment() { if(this.quantity < this.maxStock && this.quantity < 99) this.quantity++ },
        decrement() { if(this.quantity > 1) this.quantity-- }
     }">
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
            
            @if($product->discount_percentage > 0)
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded" aria-label="{{ $product->discount_percentage }}% discount">
                    -{{ $product->discount_percentage }}%
                </span>
            @endif
            
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
        
        <p class="text-xs text-gray-500 mt-1">{{ $product->weight_display }}</p>
        
        <div class="mt-2" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
            <meta itemprop="priceCurrency" content="INR">
            <link itemprop="availability" href="{{ $product->isOutOfStock() ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock' }}">
            
            <div class="mb-2">
                @if($product->has_variants && $product->activeVariants->count() > 0)
                    <meta itemprop="price" content="{{ $product->activeVariants->min('effective_price') }}">
                    <span class="text-sm font-bold text-green-600">{{ $product->price_display }}</span>
                @elseif($product->discount_price)
                    <meta itemprop="price" content="{{ $product->discount_price }}">
                    <span class="text-sm font-bold text-green-600">₹{{ number_format($product->discount_price, 2) }}</span>
                    <span class="text-xs text-gray-400 line-through ml-1">₹{{ number_format($product->price, 2) }}</span>
                @else
                    <meta itemprop="price" content="{{ $product->price }}">
                    <span class="text-sm font-bold text-green-600">₹{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            
            @if(!$product->isOutOfStock())
                @if($product->has_variants)
                    <a href="{{ route('products.show', $product->slug) }}" 
                       class="w-full flex items-center justify-center gap-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition text-xs font-medium"
                       aria-label="View options for {{ $product->name }}">
                        <i class="fas fa-eye" aria-hidden="true"></i> Select Options
                    </a>
                @else
                    <div class="flex items-center gap-2">
                        <div class="flex items-center border border-gray-300 rounded-lg flex-shrink-0">
                            <button type="button" 
                                    @click="decrement()"
                                    class="w-7 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-l-lg"
                                    aria-label="Decrease quantity">
                                <i class="fas fa-minus text-xs" aria-hidden="true"></i>
                            </button>
                            <input type="number" 
                                   x-model.number="quantity" 
                                   min="1" 
                                   :max="maxStock"
                                   class="w-8 h-8 text-center border-0 focus:ring-0 text-xs p-0"
                                   readonly
                                   aria-label="Quantity">
                            <button type="button" 
                                    @click="increment()"
                                    class="w-7 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 rounded-r-lg"
                                    aria-label="Increase quantity">
                                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            </button>
                        </div>
                        <button type="button" 
                                @click="addToCart({{ $product->id }}, quantity)"
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
