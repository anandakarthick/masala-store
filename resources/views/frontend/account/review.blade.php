@extends('layouts.app')

@section('title', 'Write a Review - Order ' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4">
        <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-green-600">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('account.dashboard') }}" class="hover:text-green-600">My Account</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('account.orders') }}" class="hover:text-green-600">My Orders</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('account.orders.show', $order) }}" class="hover:text-green-600">{{ $order->order_number }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800">Write Review</li>
        </ol>
    </nav>

    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Sidebar -->
        @include('frontend.account.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Header -->
                <div class="mb-6 pb-6 border-b">
                    <h1 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        Write a Review
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Share your experience with the products from order #{{ $order->order_number }}
                    </p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                @if($order->isFullyReviewed())
                    <div class="text-center py-8">
                        <div class="text-5xl mb-4">🎉</div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">All Products Reviewed!</h2>
                        <p class="text-gray-600 mb-4">Thank you for sharing your feedback on all products from this order.</p>
                        <a href="{{ route('account.orders.show', $order) }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Order
                        </a>
                    </div>
                @else
                    <form action="{{ route('account.orders.review.store', $order) }}" method="POST" enctype="multipart/form-data" id="reviewForm">
                        @csrf
                        
                        <div class="space-y-6">
                            @foreach($order->items as $item)
                                @if(!in_array($item->id, $reviewedItemIds))
                                    <div class="border rounded-lg p-4 review-item" data-item-id="{{ $item->id }}">
                                        <div class="flex items-start gap-4 mb-4">
                                            <!-- Product Image -->
                                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                                @if($item->product && $item->product->primary_image_url)
                                                    <img src="{{ $item->product->primary_image_url }}" 
                                                         alt="{{ $item->product_name }}"
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <i class="fas fa-image text-2xl"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Product Info -->
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-800">{{ $item->product_name }}</h3>
                                                @if($item->variant_name)
                                                    <p class="text-sm text-orange-600">{{ $item->variant_name }}</p>
                                                @endif
                                                <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                            </div>
                                        </div>

                                        <input type="hidden" name="reviews[{{ $item->id }}][order_item_id]" value="{{ $item->id }}">

                                        <!-- Star Rating -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Your Rating <span class="text-red-500">*</span>
                                            </label>
                                            <div class="star-rating flex items-center gap-1" data-item="{{ $item->id }}">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button" 
                                                            class="star-btn text-3xl text-gray-300 hover:text-yellow-400 transition-colors focus:outline-none"
                                                            data-rating="{{ $i }}">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                @endfor
                                                <input type="hidden" 
                                                       name="reviews[{{ $item->id }}][rating]" 
                                                       class="rating-input"
                                                       required>
                                                <span class="ml-3 text-sm text-gray-500 rating-text"></span>
                                            </div>
                                            @error("reviews.{$item->id}.rating")
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Review Title -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Review Title (Optional)
                                            </label>
                                            <input type="text" 
                                                   name="reviews[{{ $item->id }}][title]"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                   placeholder="Sum up your experience in a few words"
                                                   maxlength="255">
                                        </div>

                                        <!-- Review Comment -->
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Your Review (Optional)
                                            </label>
                                            <textarea name="reviews[{{ $item->id }}][comment]"
                                                      rows="4"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                      placeholder="Tell others what you think about this product. Was it good quality? Did it meet your expectations?"
                                                      maxlength="2000"></textarea>
                                            <p class="mt-1 text-xs text-gray-500">Maximum 2000 characters</p>
                                        </div>

                                        <!-- Image Upload -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Add Photos (Optional)
                                            </label>
                                            <div class="flex items-center gap-4">
                                                <label class="cursor-pointer px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors">
                                                    <i class="fas fa-camera text-gray-400 mr-2"></i>
                                                    <span class="text-sm text-gray-600">Add Photos</span>
                                                    <input type="file" 
                                                           name="reviews[{{ $item->id }}][images][]"
                                                           accept="image/jpeg,image/png,image/jpg,image/webp"
                                                           multiple
                                                           class="hidden image-input"
                                                           data-preview="preview-{{ $item->id }}">
                                                </label>
                                                <span class="text-xs text-gray-500">Max 5 photos, 2MB each</span>
                                            </div>
                                            <div id="preview-{{ $item->id }}" class="mt-3 flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Already Reviewed Item -->
                                    <div class="border rounded-lg p-4 bg-gray-50 opacity-75">
                                        <div class="flex items-center gap-4">
                                            <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                                @if($item->product && $item->product->primary_image_url)
                                                    <img src="{{ $item->product->primary_image_url }}" 
                                                         alt="{{ $item->product_name }}"
                                                         class="w-full h-full object-cover">
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-600">{{ $item->product_name }}</h3>
                                                @if($item->variant_name)
                                                    <p class="text-sm text-gray-500">{{ $item->variant_name }}</p>
                                                @endif
                                            </div>
                                            <div class="text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                <span class="text-sm">Reviewed</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        @if(count($reviewedItemIds) < $order->items->count())
                            <div class="mt-6 pt-6 border-t flex items-center justify-between">
                                <a href="{{ route('account.orders.show', $order) }}" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-arrow-left mr-1"></i> Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Submit Review(s)
                                </button>
                            </div>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Star rating functionality
    document.querySelectorAll('.star-rating').forEach(function(container) {
        const stars = container.querySelectorAll('.star-btn');
        const input = container.querySelector('.rating-input');
        const text = container.querySelector('.rating-text');
        
        const ratingTexts = {
            1: 'Poor',
            2: 'Fair',
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent'
        };
        
        stars.forEach(function(star) {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                input.value = rating;
                text.textContent = ratingTexts[rating];
                
                stars.forEach(function(s, index) {
                    if (index < rating) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                stars.forEach(function(s, index) {
                    if (index < rating) {
                        s.classList.add('text-yellow-400');
                    }
                });
            });
            
            star.addEventListener('mouseleave', function() {
                const currentRating = parseInt(input.value) || 0;
                stars.forEach(function(s, index) {
                    if (index < currentRating) {
                        s.classList.remove('text-gray-300');
                        s.classList.add('text-yellow-400');
                    } else {
                        s.classList.remove('text-yellow-400');
                        s.classList.add('text-gray-300');
                    }
                });
            });
        });
    });
    
    // Image preview functionality
    document.querySelectorAll('.image-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const previewId = this.dataset.preview;
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';
            
            if (this.files.length > 5) {
                alert('You can only upload up to 5 images');
                this.value = '';
                return;
            }
            
            Array.from(this.files).forEach(function(file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('File ' + file.name + ' is too large. Maximum size is 2MB.');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative w-16 h-16';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    });
    
    // Form validation
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        const ratingInputs = document.querySelectorAll('.rating-input');
        let hasRating = false;
        
        ratingInputs.forEach(function(input) {
            if (input.value) {
                hasRating = true;
            }
        });
        
        if (!hasRating) {
            e.preventDefault();
            alert('Please provide at least one rating');
        }
    });
});
</script>
@endpush
@endsection
