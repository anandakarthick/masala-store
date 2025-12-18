<div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-lg transition">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="relative aspect-square bg-gray-100">
            @if($product->primary_image_url)
                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">
                    <i class="fas fa-image text-4xl"></i>
                </div>
            @endif
            
            @if($product->discount_percentage > 0)
                <span class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                    -{{ $product->discount_percentage }}%
                </span>
            @endif
            
            @if($product->isOutOfStock())
                <span class="absolute top-2 right-2 bg-gray-800 text-white text-xs px-2 py-1 rounded">
                    Out of Stock
                </span>
            @endif
        </div>
    </a>
    
    <div class="p-4">
        <a href="{{ route('category.show', $product->category->slug) }}" 
           class="text-xs text-orange-600 hover:text-orange-700">
            {{ $product->category->name }}
        </a>
        
        <h3 class="font-semibold text-gray-800 mt-1 group-hover:text-orange-600 transition">
            <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        
        <p class="text-sm text-gray-500 mt-1">{{ $product->weight_display }}</p>
        
        <div class="flex items-center justify-between mt-3">
            <div>
                @if($product->discount_price)
                    <span class="text-lg font-bold text-orange-600">₹{{ number_format($product->discount_price, 2) }}</span>
                    <span class="text-sm text-gray-400 line-through ml-1">₹{{ number_format($product->price, 2) }}</span>
                @else
                    <span class="text-lg font-bold text-orange-600">₹{{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            
            @if(!$product->isOutOfStock())
                <button type="button" 
                        @click="addToCart({{ $product->id }})"
                        class="bg-orange-600 hover:bg-orange-700 text-white p-2 rounded-lg transition"
                        title="Add to Cart">
                    <i class="fas fa-cart-plus"></i>
                </button>
            @endif
        </div>
    </div>
</div>
