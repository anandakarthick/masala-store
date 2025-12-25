@extends('layouts.admin')

@section('title', 'Order Details')
@section('page_title', 'Order: ' . $order->order_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Order Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-medium">{{ $item->product_name }}</p>
                                    <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium">₹{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Subtotal:</td>
                            <td class="px-6 py-3 text-right font-medium">₹{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm text-green-600">
                                Discount:
                                @if($order->first_time_discount_applied > 0)
                                    <span class="ml-1 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                        🎉 First-Time Customer
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right font-medium text-green-600">-₹{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @if($order->first_time_discount_applied > 0 && $order->discount_amount > $order->first_time_discount_applied)
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-xs text-gray-500 italic">
                                (Includes ₹{{ number_format($order->first_time_discount_applied, 2) }} first-time discount)
                            </td>
                            <td></td>
                        </tr>
                        @endif
                        @endif
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">GST:</td>
                            <td class="px-6 py-3 text-right font-medium">₹{{ number_format($order->gst_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-600">Shipping:</td>
                            <td class="px-6 py-3 text-right font-medium">₹{{ number_format($order->shipping_charge, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-lg font-bold">Total:</td>
                            <td class="px-6 py-3 text-right text-lg font-bold text-orange-600">₹{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Customer Details</h3>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Contact Information</h4>
                    <p class="text-gray-800">{{ $order->customer_name }}</p>
                    <p class="text-gray-600">{{ $order->customer_phone }}</p>
                    <p class="text-gray-600">{{ $order->customer_email ?? 'N/A' }}</p>
                </div>
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Shipping Address</h4>
                    <p class="text-gray-600">{{ $order->full_shipping_address }}</p>
                </div>
            </div>
            @if($order->customer_notes)
                <div class="mt-4 pt-4 border-t">
                    <h4 class="font-medium text-gray-700 mb-2">Customer Notes</h4>
                    <p class="text-gray-600">{{ $order->customer_notes }}</p>
                </div>
            @endif
        </div>

        <!-- Delivery Attachments Display -->
        @if($order->hasDeliveryAttachments() || $order->delivery_notes)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-paperclip text-orange-600 mr-2"></i>Delivery Attachments
            </h3>
            
            @if($order->delivery_notes)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h4 class="font-medium text-blue-800 mb-2">📝 Delivery Notes</h4>
                <p class="text-blue-700">{{ $order->delivery_notes }}</p>
            </div>
            @endif
            
            @if($order->hasDeliveryAttachments())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($order->delivery_attachments as $index => $attachment)
                    @php
                        $extension = pathinfo($attachment, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isPdf = strtolower($extension) === 'pdf';
                    @endphp
                    <div class="border rounded-lg p-2 text-center">
                        @if($isImage)
                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank">
                                <img src="{{ asset('storage/' . $attachment) }}" 
                                     alt="Attachment {{ $index + 1 }}" 
                                     class="w-full h-24 object-cover rounded mb-2">
                            </a>
                        @elseif($isPdf)
                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="block">
                                <div class="w-full h-24 bg-red-100 rounded mb-2 flex items-center justify-center">
                                    <i class="fas fa-file-pdf text-red-600 text-3xl"></i>
                                </div>
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="block">
                                <div class="w-full h-24 bg-gray-100 rounded mb-2 flex items-center justify-center">
                                    <i class="fas fa-file text-gray-600 text-3xl"></i>
                                </div>
                            </a>
                        @endif
                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank" 
                           class="text-xs text-blue-600 hover:underline">
                            View File {{ $index + 1 }}
                        </a>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <!-- Customer Reviews -->
        @if($order->reviews->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>Customer Reviews
                </h3>
                <span class="text-sm text-gray-500">{{ $order->reviews->count() }} {{ Str::plural('review', $order->reviews->count()) }}</span>
            </div>
            <div class="space-y-4">
                @foreach($order->reviews as $review)
                    <div class="border rounded-lg p-4 {{ $review->is_approved ? 'bg-green-50 border-green-200' : 'bg-yellow-50 border-yellow-200' }}">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <!-- Star Rating -->
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="font-semibold">{{ $review->rating }}/5</span>
                                    
                                    <!-- Status Badge -->
                                    @if($review->is_approved)
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Approved</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">Pending</span>
                                    @endif
                                    
                                    @if($review->is_featured)
                                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Product Name -->
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-box mr-1"></i>
                                    {{ $review->product->name ?? 'Unknown Product' }}
                                    @if($review->orderItem && $review->orderItem->variant_name)
                                        <span class="text-orange-600">({{ $review->orderItem->variant_name }})</span>
                                    @endif
                                </p>
                            </div>
                            <span class="text-xs text-gray-500">{{ $review->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        
                        @if($review->title)
                            <h4 class="font-semibold text-gray-800 mb-1">{{ $review->title }}</h4>
                        @endif
                        
                        @if($review->comment)
                            <p class="text-gray-700 text-sm mb-3">{{ $review->comment }}</p>
                        @endif
                        
                        @if($review->images && count($review->images) > 0)
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($review->images as $image)
                                    <a href="{{ asset('storage/' . $image) }}" target="_blank" class="block">
                                        <img src="{{ asset('storage/' . $image) }}" 
                                             alt="Review image" 
                                             class="w-16 h-16 object-cover rounded-lg hover:opacity-80 transition-opacity border">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Review Actions -->
                        <div class="flex items-center gap-2 pt-2 border-t border-gray-200 mt-2">
                            <a href="{{ route('admin.reviews.show', $review) }}" class="text-xs text-blue-600 hover:underline">
                                <i class="fas fa-eye mr-1"></i>View Details
                            </a>
                            @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-600 hover:underline">
                                        <i class="fas fa-check mr-1"></i>Approve
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-yellow-600 hover:underline">
                                        <i class="fas fa-times mr-1"></i>Unapprove
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @elseif($order->status === 'delivered')
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>Customer Reviews
                </h3>
            </div>
            <div class="text-center py-6 bg-gray-50 rounded-lg">
                <i class="fas fa-star text-gray-300 text-3xl mb-2"></i>
                <p class="text-gray-500">No reviews yet for this order.</p>
                @if($order->review_requested_at)
                    <p class="text-sm text-gray-400 mt-1">
                        Review request sent on {{ $order->review_requested_at->format('d M Y, h:i A') }}
                    </p>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Order Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Order Status</h3>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-3 focus:ring-orange-500 focus:border-orange-500">
                    @foreach(['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Payment Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Payment</h3>
            <div class="space-y-2 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Method:</span>
                    <span class="font-medium">{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
            <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST">
                @csrf
                <select name="payment_status" class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-2 focus:ring-orange-500 focus:border-orange-500">
                    @foreach(['pending', 'paid', 'failed', 'refunded'] as $status)
                        <option value="{{ $status }}" {{ $order->payment_status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <input type="text" name="transaction_id" placeholder="Transaction ID" value="{{ $order->transaction_id }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-3 focus:ring-orange-500 focus:border-orange-500">
                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded-lg">
                    Update Payment
                </button>
            </form>
        </div>

        <!-- Delivery with Attachments -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">
                <i class="fas fa-truck text-orange-600 mr-2"></i>Delivery Details
            </h3>
            <form action="{{ route('admin.orders.update-delivery', $order) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Partner</label>
                        <input type="text" name="delivery_partner" placeholder="e.g. Delhivery, BlueDart" 
                               value="{{ $order->delivery_partner }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                        <input type="text" name="tracking_number" placeholder="AWB / Tracking Number" 
                               value="{{ $order->tracking_number }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery Date</label>
                        <input type="date" name="expected_delivery_date" 
                               value="{{ $order->expected_delivery_date?->format('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Notes</label>
                        <textarea name="delivery_notes" rows="2" 
                                  placeholder="Any notes for delivery..."
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">{{ $order->delivery_notes }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-paperclip mr-1"></i>Attachments (Optional)
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Upload images or PDFs (shipping label, proof of dispatch, etc.)</p>
                        <input type="file" name="attachments[]" multiple 
                               accept="image/*,.pdf"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Max 5 files. Supported: JPG, PNG, PDF</p>
                    </div>
                    
                    @if($order->hasDeliveryAttachments())
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-sm font-medium text-gray-700 mb-2">Current Attachments ({{ count($order->delivery_attachments) }})</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($order->delivery_attachments as $index => $attachment)
                                <a href="{{ asset('storage/' . $attachment) }}" target="_blank" 
                                   class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200">
                                    📎 File {{ $index + 1 }}
                                </a>
                            @endforeach
                        </div>
                        <label class="flex items-center mt-2 text-sm text-red-600">
                            <input type="checkbox" name="remove_attachments" value="1" class="mr-2">
                            Remove existing attachments
                        </label>
                    </div>
                    @endif
                    
                    <div class="flex items-center pt-2">
                        <input type="checkbox" name="send_notification" value="1" id="send_notification" checked
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="send_notification" class="ml-2 text-sm text-gray-700">
                            Send notification to customer
                        </label>
                    </div>
                    
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i>Update Delivery
                    </button>
                </div>
            </form>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.orders.invoice', $order) }}" 
                   class="block w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-center">
                    <i class="fas fa-file-pdf mr-2"></i>Download Invoice
                </a>
                <a href="{{ route('admin.orders.index') }}" 
                   class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded-lg text-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
