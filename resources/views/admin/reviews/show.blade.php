@extends('layouts.admin')

@section('title', 'Review Details')
@section('page_title', 'Review Details')

@section('content')
<div class="mb-6">
    <!-- Back Button -->
    <a href="{{ route('admin.reviews.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-4">
        <i class="fas fa-arrow-left mr-2"></i> Back to Reviews
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Review Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Header -->
                <div class="flex items-start justify-between mb-6 pb-6 border-b">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <div class="flex text-yellow-400 text-xl">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <span class="text-lg font-semibold text-gray-800">{{ $review->rating }}/5</span>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            @if($review->is_approved)
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-sm rounded-full">
                                    <i class="fas fa-check mr-1"></i>Approved
                                </span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-sm rounded-full">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            @endif
                            
                            @if($review->is_featured)
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 text-sm rounded-full">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            @endif
                            
                            @if($review->is_verified_purchase)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-sm rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>Verified Purchase
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-sm text-gray-500">
                        {{ $review->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>
                
                <!-- Review Title -->
                @if($review->title)
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ $review->title }}</h2>
                @endif
                
                <!-- Review Comment -->
                @if($review->comment)
                    <div class="prose max-w-none mb-6">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $review->comment }}</p>
                    </div>
                @else
                    <p class="text-gray-500 italic mb-6">No comment provided</p>
                @endif
                
                <!-- Review Images -->
                @if($review->images && count($review->images) > 0)
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Customer Photos</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($review->images as $image)
                                <a href="{{ asset('storage/' . $image) }}" target="_blank" class="block">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Review image" 
                                         class="w-24 h-24 object-cover rounded-lg hover:opacity-80 transition-opacity">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Actions -->
                <div class="flex items-center gap-3 pt-6 border-t">
                    @if(!$review->is_approved)
                        <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-check mr-2"></i> Approve Review
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                <i class="fas fa-times mr-2"></i> Unapprove Review
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('admin.reviews.toggle-featured', $review) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 {{ $review->is_featured ? 'bg-purple-600' : 'bg-gray-600' }} text-white rounded-lg hover:opacity-90">
                            <i class="fas fa-star mr-2"></i> {{ $review->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Customer Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user text-green-600 mr-2"></i> Customer
                </h3>
                <div class="space-y-2">
                    <p class="text-gray-700 font-medium">{{ $review->user->name ?? 'Unknown User' }}</p>
                    <p class="text-gray-500 text-sm">{{ $review->user->email ?? 'N/A' }}</p>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-box text-green-600 mr-2"></i> Product
                </h3>
                <div class="flex items-center gap-4">
                    @if($review->product && $review->product->primary_image_url)
                        <img src="{{ $review->product->primary_image_url }}" 
                             alt="{{ $review->product->name }}" 
                             class="w-16 h-16 object-cover rounded-lg">
                    @else
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <p class="font-medium text-gray-800">{{ $review->product->name ?? 'Unknown Product' }}</p>
                        @if($review->orderItem && $review->orderItem->variant_name)
                            <p class="text-sm text-orange-600">{{ $review->orderItem->variant_name }}</p>
                        @endif
                        <a href="{{ route('admin.products.show', $review->product_id) }}" class="text-sm text-green-600 hover:underline">
                            View Product →
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Order Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    <i class="fas fa-receipt text-green-600 mr-2"></i> Order
                </h3>
                <div class="space-y-2">
                    <p class="font-medium text-gray-800">#{{ $review->order->order_number ?? 'N/A' }}</p>
                    <p class="text-gray-500 text-sm">
                        Ordered: {{ $review->order->created_at->format('d M Y') ?? 'N/A' }}
                    </p>
                    @if($review->order->delivered_at)
                        <p class="text-gray-500 text-sm">
                            Delivered: {{ \Carbon\Carbon::parse($review->order->delivered_at)->format('d M Y') }}
                        </p>
                    @endif
                    <a href="{{ route('admin.orders.show', $review->order_id) }}" class="text-sm text-green-600 hover:underline">
                        View Order →
                    </a>
                </div>
            </div>
            
            <!-- Approval Info -->
            @if($review->is_approved && $review->approved_at)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i> Approval
                    </h3>
                    <p class="text-gray-500 text-sm">
                        Approved on {{ $review->approved_at->format('d M Y, h:i A') }}
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
