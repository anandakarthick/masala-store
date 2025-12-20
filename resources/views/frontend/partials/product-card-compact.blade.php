<article class="bg-white rounded-lg shadow overflow-hidden group hover:shadow-md transition" 
     itemscope 
     itemtype="https://schema.org/Product">
    <a href="{{ route('products.show', $product->slug) }}" class="block" itemprop="url">
        <!-- Product Image - Compact -->
        <div class="relative h-28 sm:h-32 md:h-36 bg-gray-100 overflow-hidden">
            @if($product->primary_image_url)
                <img src="{{ $product->primary_image_url }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                     loading="lazy"
                     itemprop="image">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <i class="fas fa-image text-2xl" aria-hidden="true"></i>
                </div>
            @endif
            
            @if($product->discount_percentage > 0)
                <span class="absolute top-1 left-1 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded font-medium">
                    -{{ round($product->discount_percentage) }}%
                </span>
            @endif
            
            @if($product->isOutOfStock())
                <span class="absolute top-1 right-1 bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded">
                    Out
                </span>
            @endif
        </div>
    </a>
    
    <!-- Product Info - Compact -->
    <div class="p-2">
        <h3 class="font-medium text-gray-800 text-xs leading-tight group-hover:text-green-600 transition line-clamp-2 min-h-[2rem]" itemprop="name">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        
        <div class="mt-1.5" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
            <meta itemprop="priceCurrency" content="INR">
            
            <!-- Price -->
            <div class="flex items-center gap-1 flex-wrap">
                @if($product->has_variants && $product->activeVariants->count() > 0)
                    <meta itemprop="price" content="{{ $product->activeVariants->min('effective_price') }}">
                    <span class="text-sm font-bold text-green-600">{{ $product->price_display }}</span>
                @elseif($product->discount_price)
                    <meta itemprop="price" content="{{ $product->discount_price }}">
                    <span class="text-sm font-bold text-green-600">₹{{ number_format($product->discount_price, 0) }}</span>
                    <span class="text-[10px] text-gray-400 line-through">₹{{ number_format($product->price, 0) }}</span>
                @else
                    <meta itemprop="price" content="{{ $product->price }}">
                    <span class="text-sm font-bold text-green-600">₹{{ number_format($product->price, 0) }}</span>
                @endif
            </div>
            
            <!-- Add to Cart Button - Compact -->
            @if(!$product->isOutOfStock())
                @if($product->has_variants)
                    <a href="{{ route('products.show', $product->slug) }}" 
                       class="mt-2 w-full flex items-center justify-center gap-1 bg-green-600 hover:bg-green-700 text-white py-1.5 rounded text-[11px] font-medium transition">
                        <i class="fas fa-eye" aria-hidden="true"></i> Options
                    </a>
                @else
                    <button type="button" 
                            onclick="addToCart({{ $product->id }}, 1)"
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
