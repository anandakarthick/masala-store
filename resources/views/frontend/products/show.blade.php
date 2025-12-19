@extends('layouts.app')

@php
    $businessName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
    $productTitle = $product->meta_title ?? $product->name . ' - Buy Online';
    $productDescription = $product->meta_description ?? Str::limit(strip_tags($product->short_description ?? $product->description), 160);
    $productImage = $product->primary_image_url ?? asset('images/no-image.jpg');
    $productUrl = route('products.show', $product->slug);
@endphp

@section('title', $productTitle)
@section('meta_description', $productDescription)
@section('meta_keywords', $product->name . ', buy ' . $product->name . ' online, ' . $product->category->name . ', homemade ' . $product->name . ', natural ' . $product->name . ', ' . $businessName)

@section('canonical', $productUrl)
@section('og_type', 'product')
@section('og_url', $productUrl)
@section('og_title', $product->name . ' | ' . $businessName)
@section('og_description', $productDescription)
@section('og_image', $productImage)

@section('structured_data')
<!-- Product Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ $product->name }}",
    "image": [
        @foreach($product->images as $image)
        "{{ $image->url }}"@if(!$loop->last),@endif
        @endforeach
        @if($product->images->isEmpty())
        "{{ asset('images/no-image.jpg') }}"
        @endif
    ],
    "description": "{{ Str::limit(strip_tags($product->description ?? $product->short_description), 500) }}",
    "sku": "{{ $product->sku }}",
    "brand": {
        "@type": "Brand",
        "name": "{{ $businessName }}"
    },
    "category": "{{ $product->category->name }}",
    @if($product->has_variants && $product->activeVariants->count() > 0)
    "offers": [
        @foreach($product->activeVariants as $variant)
        {
            "@type": "Offer",
            "name": "{{ $product->name }} - {{ $variant->name }}",
            "sku": "{{ $variant->sku }}",
            "price": "{{ $variant->effective_price }}",
            "priceCurrency": "INR",
            "availability": "{{ $variant->isOutOfStock() ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock' }}",
            "url": "{{ $productUrl }}",
            "seller": {
                "@type": "Organization",
                "name": "{{ $businessName }}"
            },
            "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
            "itemCondition": "https://schema.org/NewCondition"
        }@if(!$loop->last),@endif
        @endforeach
    ]
    @else
    "offers": {
        "@type": "Offer",
        "price": "{{ $product->effective_price }}",
        "priceCurrency": "INR",
        "availability": "{{ $product->isOutOfStock() ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock' }}",
        "url": "{{ $productUrl }}",
        "seller": {
            "@type": "Organization",
            "name": "{{ $businessName }}"
        },
        "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
        "itemCondition": "https://schema.org/NewCondition"
    }
    @endif
}
</script>

<!-- BreadcrumbList Schema -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "{{ route('home') }}"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "Products",
            "item": "{{ route('products.index') }}"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "{{ $product->category->name }}",
            "item": "{{ route('category.show', $product->category->slug) }}"
        },
        {
            "@type": "ListItem",
            "position": 4,
            "name": "{{ $product->name }}",
            "item": "{{ $productUrl }}"
        }
    ]
}
</script>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="{{ route('home') }}" class="hover:text-green-600" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="{{ route('products.index') }}" class="hover:text-green-600" itemprop="item">
                    <span itemprop="name">Products</span>
                </a>
                <meta itemprop="position" content="2">
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-green-600" itemprop="item">
                    <span itemprop="name">{{ $product->category->name }}</span>
                </a>
                <meta itemprop="position" content="3">
            </li>
            <li><i class="fas fa-chevron-right text-xs" aria-hidden="true"></i></li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="text-gray-800 truncate max-w-32">
                <span itemprop="name">{{ $product->name }}</span>
                <meta itemprop="position" content="4">
            </li>
        </ol>
    </nav>

    <article class="bg-white rounded-lg shadow-md p-4 md:p-6" itemscope itemtype="https://schema.org/Product">
        <meta itemprop="sku" content="{{ $product->sku }}">
        <meta itemprop="brand" content="{{ $businessName }}">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Images -->
            <div x-data="{ activeImage: 0 }">
                <!-- Main Image -->
                <div class="relative h-64 md:h-80 bg-gray-100 rounded-lg overflow-hidden mb-3">
                    @if($product->images->count() > 0)
                        @foreach($product->images as $index => $image)
                            <img x-show="activeImage === {{ $index }}" 
                                 src="{{ $image->url }}" 
                                 alt="{{ $image->alt_text ?? $product->name . ' - Image ' . ($index + 1) }}"
                                 class="w-full h-full object-contain"
                                 loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                 @if($index === 0) itemprop="image" @endif
                                 width="400"
                                 height="400">
                        @endforeach
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <i class="fas fa-image text-5xl" aria-hidden="true"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="flex gap-2 overflow-x-auto pb-2" role="tablist" aria-label="Product images">
                        @foreach($product->images as $index => $image)
                            <button @click="activeImage = {{ $index }}"
                                    :class="activeImage === {{ $index }} ? 'ring-2 ring-green-600' : 'ring-1 ring-gray-200'"
                                    class="flex-shrink-0 w-14 h-14 rounded overflow-hidden"
                                    role="tab"
                                    aria-label="View image {{ $index + 1 }}"
                                    :aria-selected="activeImage === {{ $index }}">
                                <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover" loading="lazy" width="56" height="56">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div x-data="productDetail({{ json_encode([
                'hasVariants' => $product->has_variants,
                'variants' => $product->has_variants ? $product->activeVariants->map(function($v) {
                    return [
                        'id' => $v->id,
                        'name' => $v->name,
                        'sku' => $v->sku,
                        'price' => (float) $v->price,
                        'discount_price' => $v->discount_price ? (float) $v->discount_price : null,
                        'effective_price' => (float) $v->effective_price,
                        'stock' => $v->stock_quantity,
                        'is_default' => $v->is_default,
                    ];
                }) : [],
                'basePrice' => (float) $product->price,
                'baseDiscountPrice' => $product->discount_price ? (float) $product->discount_price : null,
                'baseStock' => $product->stock_quantity,
                'productId' => $product->id,
            ]) }})">
                @if($product->is_combo)
                    <span class="inline-flex items-center bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full mb-2">
                        <i class="fas fa-gift mr-1" aria-hidden="true"></i> Combo Pack
                    </span>
                @endif
                
                <span class="text-xs text-green-600 font-medium">{{ $product->category->name }}</span>
                <h1 class="text-xl md:text-2xl font-bold text-gray-800 mt-1" itemprop="name">{{ $product->name }}</h1>
                
                <!-- SKU -->
                <p class="text-xs text-gray-500 mt-1">SKU: <span x-text="selectedVariant ? selectedVariant.sku : '{{ $product->sku }}'"></span></p>
                
                <!-- Price -->
                <div class="mt-3" itemprop="offers" itemscope itemtype="https://schema.org/AggregateOffer">
                    <meta itemprop="priceCurrency" content="INR">
                    @if($product->has_variants && $product->activeVariants->count() > 0)
                        <meta itemprop="lowPrice" content="{{ $product->activeVariants->min('effective_price') }}">
                        <meta itemprop="highPrice" content="{{ $product->activeVariants->max('effective_price') }}">
                        <meta itemprop="offerCount" content="{{ $product->activeVariants->count() }}">
                    @else
                        <meta itemprop="price" content="{{ $product->effective_price }}">
                    @endif
                    <link itemprop="availability" href="{{ $product->isOutOfStock() ? 'https://schema.org/OutOfStock' : 'https://schema.org/InStock' }}">
                    
                    <template x-if="currentDiscountPrice">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-green-600">₹<span x-text="currentDiscountPrice.toFixed(2)"></span></span>
                            <span class="text-lg text-gray-400 line-through">₹<span x-text="currentPrice.toFixed(2)"></span></span>
                            <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded" x-text="'Save ' + discountPercent + '%'"></span>
                        </div>
                    </template>
                    <template x-if="!currentDiscountPrice">
                        <span class="text-2xl font-bold text-green-600">₹<span x-text="currentPrice.toFixed(2)"></span></span>
                    </template>
                    
                    @if($product->gst_percentage > 0)
                        <p class="text-xs text-gray-500 mt-1">(Inclusive of {{ $product->gst_percentage }}% GST)</p>
                    @endif
                </div>

                <!-- Combo Contents -->
                @if($product->is_combo && $product->comboItems->count() > 0)
                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-3">
                        <h2 class="font-semibold text-green-800 flex items-center text-sm mb-2">
                            <i class="fas fa-box-open mr-2" aria-hidden="true"></i> What's Inside This Pack
                        </h2>
                        <ul class="space-y-1.5">
                            @foreach($product->comboItems as $item)
                                <li class="flex items-start text-sm">
                                    <i class="fas fa-check text-green-600 mt-0.5 mr-2 text-xs" aria-hidden="true"></i>
                                    <div>
                                        <span class="text-gray-800">{{ $item->item_name }}</span>
                                        @if($item->item_quantity)
                                            <span class="text-green-600 font-medium ml-1">({{ $item->item_quantity }})</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Variants Selection -->
                @if($product->has_variants && $product->activeVariants->count() > 0)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2" id="variant-label">Select Size/Pack</label>
                        <div class="flex flex-wrap gap-2" role="radiogroup" aria-labelledby="variant-label">
                            @foreach($product->activeVariants as $variant)
                                <button type="button"
                                        @click="selectVariant({{ $variant->id }})"
                                        :class="selectedVariantId === {{ $variant->id }} 
                                            ? 'border-green-600 bg-green-50 text-green-600' 
                                            : 'border-gray-300 hover:border-green-400'"
                                        class="px-3 py-1.5 border-2 rounded-lg text-sm font-medium transition {{ $variant->isOutOfStock() ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $variant->isOutOfStock() ? 'disabled' : '' }}
                                        role="radio"
                                        :aria-checked="selectedVariantId === {{ $variant->id }}"
                                        aria-label="{{ $variant->name }}{{ $variant->isOutOfStock() ? ' - Out of Stock' : '' }}">
                                    {{ $variant->name }}
                                    @if($variant->isOutOfStock())
                                        <span class="text-xs text-red-500 block">Out of Stock</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Stock Status -->
                <div class="mt-3">
                    <template x-if="currentStock <= 0">
                        <span class="inline-flex items-center bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs">
                            <i class="fas fa-times-circle mr-1" aria-hidden="true"></i> Out of Stock
                        </span>
                    </template>
                    <template x-if="currentStock > 0 && currentStock <= 10">
                        <span class="inline-flex items-center bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs">
                            <i class="fas fa-exclamation-circle mr-1" aria-hidden="true"></i> Only <span x-text="currentStock"></span> left
                        </span>
                    </template>
                    <template x-if="currentStock > 10">
                        <span class="inline-flex items-center bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs">
                            <i class="fas fa-check-circle mr-1" aria-hidden="true"></i> In Stock
                        </span>
                    </template>
                </div>

                <!-- Short Description -->
                @if($product->short_description)
                    <p class="text-gray-600 mt-3 text-sm" itemprop="description">{{ $product->short_description }}</p>
                @endif

                <!-- Add to Cart -->
                <div class="mt-4" x-show="currentStock > 0">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button type="button" @click="quantity = Math.max(1, quantity - 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100" aria-label="Decrease quantity">
                                <i class="fas fa-minus text-sm" aria-hidden="true"></i>
                            </button>
                            <label for="quantity" class="sr-only">Quantity</label>
                            <input type="number" id="quantity" x-model.number="quantity" min="1" :max="currentStock"
                                   class="w-12 text-center border-0 focus:ring-0 text-sm" aria-label="Quantity">
                            <button type="button" @click="quantity = Math.min(currentStock, quantity + 1)" class="px-3 py-2 text-gray-600 hover:bg-gray-100" aria-label="Increase quantity">
                                <i class="fas fa-plus text-sm" aria-hidden="true"></i>
                            </button>
                        </div>
                        <button type="button" 
                                @click="addToCartWithVariant()"
                                :disabled="hasVariants && !selectedVariantId"
                                :class="(hasVariants && !selectedVariantId) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                                class="flex-1 text-white py-2.5 px-4 rounded-lg font-semibold text-sm">
                            <i class="fas fa-cart-plus mr-2" aria-hidden="true"></i> 
                            <span x-text="hasVariants && !selectedVariantId ? 'Select a Size' : 'Add to Cart'"></span>
                        </button>
                    </div>
                </div>

                <!-- Features -->
                <div class="grid grid-cols-2 gap-3 mt-6 pt-4 border-t">
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-shipping-fast text-green-600" aria-hidden="true"></i>
                        <span>Free Shipping over ₹500</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-check-circle text-green-600" aria-hidden="true"></i>
                        <span>100% Pure & Natural</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-lock text-green-600" aria-hidden="true"></i>
                        <span>Secure Payment</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i class="fas fa-leaf text-green-600" aria-hidden="true"></i>
                        <span>Chemical-Free</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Tab -->
        @if($product->description)
        <section class="mt-8 pt-6 border-t" aria-labelledby="description-heading">
            <h2 id="description-heading" class="text-lg font-bold mb-3">Product Description</h2>
            <div class="prose max-w-none text-gray-600 text-sm leading-relaxed">
                {!! nl2br(e($product->description)) !!}
            </div>
        </section>
        @endif
    </article>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <section class="mt-8" aria-labelledby="related-heading">
            <h2 id="related-heading" class="text-xl font-bold mb-4">Related Products</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-4">
                @foreach($relatedProducts as $relatedProduct)
                    @include('frontend.partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </section>
    @endif
</div>

@push('scripts')
<script>
function productDetail(config) {
    return {
        hasVariants: config.hasVariants,
        variants: config.variants,
        selectedVariantId: null,
        selectedVariant: null,
        quantity: 1,
        productId: config.productId,
        basePrice: config.basePrice,
        baseDiscountPrice: config.baseDiscountPrice,
        baseStock: config.baseStock,
        
        init() {
            if (this.hasVariants && this.variants.length > 0) {
                var defaultVariant = this.variants.find(function(v) { return v.is_default && v.stock > 0; });
                if (!defaultVariant) {
                    defaultVariant = this.variants.find(function(v) { return v.stock > 0; });
                }
                if (defaultVariant) {
                    this.selectVariant(defaultVariant.id);
                }
            }
        },
        
        selectVariant(variantId) {
            var self = this;
            this.selectedVariantId = variantId;
            this.selectedVariant = this.variants.find(function(v) { return v.id === variantId; });
            this.quantity = 1;
        },
        
        get currentPrice() {
            if (this.selectedVariant) {
                return this.selectedVariant.price;
            }
            return this.basePrice;
        },
        
        get currentDiscountPrice() {
            if (this.selectedVariant) {
                return this.selectedVariant.discount_price;
            }
            return this.baseDiscountPrice;
        },
        
        get currentStock() {
            if (this.selectedVariant) {
                return this.selectedVariant.stock;
            }
            return this.baseStock;
        },
        
        get discountPercent() {
            if (!this.currentDiscountPrice) return 0;
            return Math.round(((this.currentPrice - this.currentDiscountPrice) / this.currentPrice) * 100);
        },
        
        addToCartWithVariant() {
            if (this.hasVariants && !this.selectedVariantId) {
                alert('Please select a size/variant');
                return;
            }
            
            if (typeof addToCart === 'function') {
                addToCart(this.productId, this.quantity, this.selectedVariantId);
            } else {
                var event = new CustomEvent('add-to-cart', { 
                    detail: { 
                        productId: this.productId, 
                        quantity: this.quantity, 
                        variantId: this.selectedVariantId 
                    } 
                });
                window.dispatchEvent(event);
            }
        }
    }
}
</script>
@endpush
@endsection
