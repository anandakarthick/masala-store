@extends('layouts.app')

@section('title', $product->meta_title ?? $product->name)
@section('meta_description', $product->meta_description ?? $product->short_description)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-orange-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('products.index') }}" class="hover:text-orange-600">Products</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-orange-600">{{ $product->category->name }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-md p-6 lg:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Images -->
            <div x-data="{ activeImage: 0 }">
                <!-- Main Image -->
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-4">
                    @if($product->images->count() > 0)
                        @foreach($product->images as $index => $image)
                            <img x-show="activeImage === {{ $index }}" 
                                 src="{{ $image->url }}" 
                                 alt="{{ $image->alt_text ?? $product->name }}"
                                 class="w-full h-full object-cover">
                        @endforeach
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <i class="fas fa-image text-6xl"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach($product->images as $index => $image)
                            <button @click="activeImage = {{ $index }}"
                                    :class="activeImage === {{ $index }} ? 'ring-2 ring-orange-600' : ''"
                                    class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden">
                                <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div>
                <span class="text-sm text-orange-600 font-medium">{{ $product->category->name }}</span>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-800 mt-2">{{ $product->name }}</h1>
                
                <!-- SKU -->
                <p class="text-sm text-gray-500 mt-2">SKU: {{ $product->sku }}</p>
                
                <!-- Price -->
                <div class="mt-4">
                    @if($product->discount_price)
                        <div class="flex items-center gap-3">
                            <span class="text-3xl font-bold text-orange-600">₹{{ number_format($product->discount_price, 2) }}</span>
                            <span class="text-xl text-gray-400 line-through">₹{{ number_format($product->price, 2) }}</span>
                            <span class="bg-red-100 text-red-600 text-sm px-2 py-1 rounded">
                                Save {{ $product->discount_percentage }}%
                            </span>
                        </div>
                    @else
                        <span class="text-3xl font-bold text-orange-600">₹{{ number_format($product->price, 2) }}</span>
                    @endif
                    
                    @if($product->gst_percentage > 0)
                        <p class="text-sm text-gray-500 mt-1">(Inclusive of {{ $product->gst_percentage }}% GST)</p>
                    @endif
                </div>

                <!-- Weight -->
                <div class="mt-4">
                    <span class="text-gray-600">Weight/Quantity:</span>
                    <span class="font-medium">{{ $product->weight_display }}</span>
                </div>

                <!-- Stock Status -->
                <div class="mt-4">
                    @if($product->isOutOfStock())
                        <span class="inline-flex items-center bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-times-circle mr-1"></i> Out of Stock
                        </span>
                    @elseif($product->isLowStock())
                        <span class="inline-flex items-center bg-yellow-100 text-yellow-600 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i> Only {{ $product->stock_quantity }} left
                        </span>
                    @else
                        <span class="inline-flex items-center bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-check-circle mr-1"></i> In Stock
                        </span>
                    @endif
                </div>

                <!-- Short Description -->
                @if($product->short_description)
                    <p class="text-gray-600 mt-4">{{ $product->short_description }}</p>
                @endif

                <!-- Add to Cart -->
                @if(!$product->isOutOfStock())
                    <form action="{{ route('cart.add') }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button type="button" onclick="decrementQty()" class="px-4 py-2 text-gray-600 hover:bg-gray-100">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}"
                                       class="w-16 text-center border-0 focus:ring-0">
                                <button type="button" onclick="incrementQty()" class="px-4 py-2 text-gray-600 hover:bg-gray-100">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-3 px-6 rounded-lg font-semibold">
                                <i class="fas fa-cart-plus mr-2"></i> Add to Cart
                            </button>
                        </div>
                    </form>
                @endif

                <!-- Features -->
                <div class="grid grid-cols-2 gap-4 mt-8 pt-6 border-t">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-shipping-fast text-orange-600"></i>
                        <span>Free Shipping over ₹500</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-check-circle text-orange-600"></i>
                        <span>100% Authentic</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-lock text-orange-600"></i>
                        <span>Secure Payment</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-headset text-orange-600"></i>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Tab -->
        <div class="mt-12 pt-8 border-t">
            <h2 class="text-xl font-bold mb-4">Product Description</h2>
            <div class="prose max-w-none text-gray-600">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <section class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Related Products</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    @include('frontend.partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </section>
    @endif
</div>

@push('scripts')
<script>
    function incrementQty() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }
    
    function decrementQty() {
        const input = document.getElementById('quantity');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }
</script>
@endpush
@endsection
