@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-user text-green-600 mr-2"></i>Customer Details
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            @error('customer_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone', $user?->phone) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            @error('customer_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>Shipping Address
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                            <textarea name="shipping_address" rows="2" required
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">{{ old('shipping_address', $user?->address) }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <input type="text" name="shipping_city" value="{{ old('shipping_city', $user?->city) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                <input type="text" name="shipping_state" value="{{ old('shipping_state', $user?->state) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pincode *</label>
                                <input type="text" name="shipping_pincode" value="{{ old('shipping_pincode', $user?->pincode) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Type -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-box text-green-600 mr-2"></i>Order Type
                    </h2>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="order_type" value="retail" checked class="text-green-600 focus:ring-green-500">
                            <span class="ml-2">Retail Order</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="order_type" value="bulk" class="text-green-600 focus:ring-green-500">
                            <span class="ml-2">Bulk Order</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="order_type" value="return_gift" class="text-green-600 focus:ring-green-500">
                            <span class="ml-2">Return Gift Order</span>
                        </label>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-credit-card text-green-600 mr-2"></i>Payment Method
                    </h2>
                    <div class="space-y-3">
                        @forelse($paymentMethods as $index => $method)
                            <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors payment-option {{ $index === 0 ? 'border-green-500 bg-green-50' : 'border-gray-200' }}"
                                   data-payment="{{ $method->code }}">
                                <input type="radio" name="payment_method" value="{{ $method->code }}" 
                                       {{ $index === 0 ? 'checked' : '' }}
                                       class="mt-1 text-green-600 focus:ring-green-500">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <i class="fas {{ $method->icon ?? 'fa-credit-card' }} text-lg mr-2 {{ $method->is_online ? 'text-blue-600' : 'text-green-600' }}"></i>
                                        <span class="font-medium text-gray-800">{{ $method->display_name }}</span>
                                        @if($method->is_online)
                                            <span class="ml-2 text-xs bg-blue-100 text-blue-600 px-2 py-0.5 rounded">Online</span>
                                        @endif
                                    </div>
                                    @if($method->description)
                                        <p class="text-sm text-gray-500 mt-1">{{ $method->description }}</p>
                                    @endif
                                    @if($method->extra_charge > 0)
                                        <p class="text-xs text-orange-600 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Extra charge: {{ $method->extra_charge_type === 'percentage' ? $method->extra_charge . '%' : '₹' . number_format($method->extra_charge, 2) }}
                                        </p>
                                    @endif
                                </div>
                                @if($method->code === 'razorpay')
                                    <div class="flex items-center space-x-1 ml-2">
                                        <img src="https://cdn.razorpay.com/static/assets/logo/payment/visa.svg" class="h-6" alt="Visa">
                                        <img src="https://cdn.razorpay.com/static/assets/logo/payment/mastercard.svg" class="h-6" alt="Mastercard">
                                        <img src="https://cdn.razorpay.com/static/assets/logo/payment/upi.svg" class="h-6" alt="UPI">
                                    </div>
                                @endif
                            </label>
                        @empty
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                                <p>No payment methods available.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Payment Method Details (Instructions, QR Code, Bank Details) -->
                    @foreach($paymentMethods as $method)
                        <div id="payment-details-{{ $method->code }}" class="payment-details mt-4 hidden">
                            {{-- UPI Payment Details --}}
                            @if($method->code === 'upi')
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                    <div class="flex flex-col md:flex-row items-center gap-4">
                                        {{-- QR Code --}}
                                        @php
                                            $qrCode = $method->getSetting('qr_code');
                                        @endphp
                                        @if($qrCode)
                                            <div class="flex-shrink-0">
                                                <div class="p-3 bg-white rounded-lg border-2 border-purple-300 shadow-sm">
                                                    <img src="{{ asset('storage/' . $qrCode) }}" alt="UPI QR Code" class="w-40 h-40 object-contain">
                                                </div>
                                                <p class="text-xs text-purple-600 text-center mt-2">
                                                    <i class="fas fa-camera mr-1"></i>Scan with any UPI app
                                                </p>
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1 text-center md:text-left">
                                            @if($method->getSetting('upi_id'))
                                                <p class="text-sm text-gray-600 mb-1">UPI ID:</p>
                                                <p class="font-mono text-lg font-bold text-purple-700 mb-2">{{ $method->getSetting('upi_id') }}</p>
                                            @endif
                                            @if($method->getSetting('upi_name'))
                                                <p class="text-sm text-gray-500">Payee: {{ $method->getSetting('upi_name') }}</p>
                                            @endif
                                            @if($method->instructions)
                                                <p class="text-sm text-purple-700 mt-3">
                                                    <i class="fas fa-info-circle mr-1"></i>{{ $method->instructions }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            {{-- Bank Transfer Details --}}
                            @if($method->code === 'bank_transfer')
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                    <h4 class="font-medium text-indigo-800 mb-3">
                                        <i class="fas fa-university mr-2"></i>Bank Account Details
                                    </h4>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        @if($method->getSetting('account_name'))
                                            <span class="text-gray-600">Account Name:</span>
                                            <span class="font-medium">{{ $method->getSetting('account_name') }}</span>
                                        @endif
                                        @if($method->getSetting('account_number'))
                                            <span class="text-gray-600">Account No:</span>
                                            <span class="font-medium font-mono">{{ $method->getSetting('account_number') }}</span>
                                        @endif
                                        @if($method->getSetting('bank_name'))
                                            <span class="text-gray-600">Bank:</span>
                                            <span class="font-medium">{{ $method->getSetting('bank_name') }}</span>
                                        @endif
                                        @if($method->getSetting('ifsc_code'))
                                            <span class="text-gray-600">IFSC:</span>
                                            <span class="font-medium font-mono">{{ $method->getSetting('ifsc_code') }}</span>
                                        @endif
                                        @if($method->getSetting('branch'))
                                            <span class="text-gray-600">Branch:</span>
                                            <span class="font-medium">{{ $method->getSetting('branch') }}</span>
                                        @endif
                                    </div>
                                    @if($method->instructions)
                                        <p class="text-sm text-indigo-700 mt-3">
                                            <i class="fas fa-info-circle mr-1"></i>{{ $method->instructions }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                            
                            {{-- COD / Razorpay Instructions --}}
                            @if($method->code === 'cod' || $method->code === 'razorpay')
                                @if($method->instructions)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <p class="text-sm text-gray-700">
                                            <i class="fas fa-info-circle mr-1 text-green-600"></i>{{ $method->instructions }}
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Notes -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-sticky-note text-green-600 mr-2"></i>Order Notes (Optional)
                    </h2>
                    <textarea name="customer_notes" rows="3" placeholder="Any special instructions for your order..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">{{ old('customer_notes') }}</textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-shopping-bag text-green-600 mr-2"></i>Order Summary
                    </h2>
                    
                    <!-- Items -->
                    <div class="space-y-3 max-h-60 overflow-y-auto">
                        @foreach($cart->items as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded flex-shrink-0">
                                    @if($item->product->primary_image_url)
                                        <img src="{{ $item->product->primary_image_url }}" alt="" class="w-full h-full object-cover rounded">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ $item->product->name }}</p>
                                    @if($item->variant)
                                        <p class="text-xs text-green-600">{{ $item->variant->name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                </div>
                                <span class="text-sm font-medium">₹{{ number_format($item->total, 2) }}</span>
                            </div>
                        @endforeach
                        
                        {{-- Custom Combos --}}
                        @foreach($cart->customCombos as $combo)
                            <div class="flex items-center gap-3 bg-purple-50 p-2 rounded-lg">
                                <div class="w-12 h-12 bg-purple-100 rounded flex-shrink-0 flex items-center justify-center">
                                    <i class="fas fa-box-open text-purple-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate text-purple-700">{{ $combo->combo_name }}</p>
                                    <p class="text-xs text-purple-500">{{ $combo->items->count() }} items</p>
                                </div>
                                <span class="text-sm font-medium text-purple-700">₹{{ number_format($combo->final_price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Coupon -->
                    <div class="border-t mt-4 pt-4">
                        @if(session('coupon'))
                            <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg mb-3">
                                <div>
                                    <span class="text-green-600 font-medium">{{ session('coupon')->code }}</span>
                                    <p class="text-xs text-green-600">{{ session('coupon')->name }}</p>
                                </div>
                                <form action="{{ route('checkout.remove-coupon') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('checkout.apply-coupon') }}" method="POST" class="flex gap-2 mb-3">
                                @csrf
                                <input type="text" name="coupon_code" placeholder="Coupon Code"
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500">
                                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm">
                                    Apply
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Totals -->
                    <div class="border-t mt-4 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>₹{{ number_format($cart->subtotal, 2) }}</span>
                        </div>
                        @if($cart->combo_savings > 0)
                            <div class="flex justify-between text-purple-600">
                                <span>Combo Savings</span>
                                <span>-₹{{ number_format($cart->combo_savings, 2) }}</span>
                            </div>
                        @endif
                        @if(session('coupon'))
                            <div class="flex justify-between text-green-600">
                                <span>Coupon Discount</span>
                                <span>-₹{{ number_format(session('coupon')->calculateDiscount($cart->subtotal), 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">GST</span>
                            <span>₹{{ number_format($cart->gst_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            @if($shippingCharge == 0)
                                <span class="text-green-600">FREE</span>
                            @else
                                <span>₹{{ number_format($shippingCharge, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t mt-4 pt-4">
                        @php
                            $discount = session('coupon') ? session('coupon')->calculateDiscount($cart->subtotal) : 0;
                            $total = $cart->subtotal - $discount + $cart->gst_amount + $shippingCharge;
                        @endphp
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-green-600">₹{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold mt-6 transition-colors">
                        <i class="fas fa-lock mr-2"></i>Place Order
                    </button>
                    
                    <p class="text-xs text-gray-500 text-center mt-3">
                        <i class="fas fa-shield-alt mr-1"></i>Secure checkout
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Payment method selection styling and show/hide details
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', function() {
        // Remove highlight from all
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('border-green-500', 'bg-green-50');
            opt.classList.add('border-gray-200');
        });
        
        // Highlight selected
        this.classList.remove('border-gray-200');
        this.classList.add('border-green-500', 'bg-green-50');
        
        // Hide all payment details
        document.querySelectorAll('.payment-details').forEach(detail => {
            detail.classList.add('hidden');
        });
        
        // Show selected payment details
        const code = this.dataset.payment;
        const detailBox = document.getElementById('payment-details-' + code);
        if (detailBox) {
            detailBox.classList.remove('hidden');
        }
    });
});

// Show details for default selected payment method on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkedOption = document.querySelector('.payment-option input:checked');
    if (checkedOption) {
        checkedOption.closest('.payment-option').click();
    }
});
</script>
@endpush
