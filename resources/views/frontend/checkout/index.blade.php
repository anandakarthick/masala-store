@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        {{-- Hidden fields for lat/long and order type --}}
        <input type="hidden" name="shipping_latitude" id="shipping_latitude" value="{{ old('shipping_latitude') }}">
        <input type="hidden" name="shipping_longitude" id="shipping_longitude" value="{{ old('shipping_longitude') }}">
        <input type="hidden" name="order_type" value="retail">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Details -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-user text-orange-500 mr-2"></i>Customer Details
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('customer_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" name="customer_phone" value="{{ old('customer_phone', $user?->phone) }}" required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                            @error('customer_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                        </div>
                    </div>
                </div>

                <!-- Shipping Address with Google Maps Autocomplete -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>Shipping Address
                    </h2>
                    <div class="space-y-4">
                        <!-- Address Search with Autocomplete -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Search Address
                                <span class="text-gray-400 font-normal text-xs ml-1">(Type or paste to search)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="address_search" 
                                       placeholder="Search by building name, area, or full address..."
                                       autocomplete="off"
                                       class="w-full pl-10 border border-gray-300 rounded-lg px-4 py-3 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Type building name (e.g., "Suriya Sri Flats") or address, then select from dropdown
                            </p>
                        </div>

                        <!-- Full Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Complete Address *</label>
                            <textarea name="shipping_address" id="shipping_address" rows="2" required
                                      placeholder="House/Flat No., Building, Street, Area, Landmark"
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">{{ old('shipping_address', $user?->address) }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City, State, Pincode -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                <input type="text" name="shipping_city" id="shipping_city" 
                                       value="{{ old('shipping_city', $user?->city) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                                @error('shipping_city')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State *</label>
                                <input type="text" name="shipping_state" id="shipping_state" 
                                       value="{{ old('shipping_state', $user?->state) }}" required
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                                @error('shipping_state')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pincode *</label>
                                <input type="text" name="shipping_pincode" id="shipping_pincode" 
                                       value="{{ old('shipping_pincode', $user?->pincode) }}" required
                                       pattern="[0-9]{6}" maxlength="6"
                                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                                @error('shipping_pincode')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Location Selected Indicator -->
                        <div id="location_selected" class="hidden bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-center text-green-700">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span class="text-sm font-medium">Location detected successfully!</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wallet Payment Option (Only for logged in users with balance) -->
                @if(auth()->check() && $user->wallet_balance > 0)
                    @php
                        $couponDiscount = session('coupon') ? session('coupon')->calculateDiscount($cart->subtotal) : 0;
                        $firstTimeDiscountAmt = (isset($firstTimeDiscount) && $firstTimeDiscount['eligible']) ? $firstTimeDiscount['discount_amount'] : 0;
                        $totalDiscount = $couponDiscount + $firstTimeDiscountAmt;
                        $orderTotal = $cart->subtotal - $totalDiscount + $cart->gst_amount + $shippingCharge;
                        $walletBalance = $user->wallet_balance;
                        $maxWalletUsable = min($walletBalance, $orderTotal);
                    @endphp
                    <div class="bg-white rounded-lg shadow-md p-6" x-data="{ useWallet: false, walletAmount: {{ $maxWalletUsable }} }">
                        <h2 class="text-lg font-semibold mb-4">
                            <i class="fas fa-wallet text-orange-500 mr-2"></i>Pay with Wallet
                        </h2>
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-sm text-gray-600">Available Balance</p>
                                    <p class="text-2xl font-bold text-orange-500">₹{{ number_format($walletBalance, 2) }}</p>
                                </div>
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="use_wallet" value="1" x-model="useWallet"
                                           class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500">
                                    <span class="ml-2 font-medium text-gray-700">Use Wallet</span>
                                </label>
                            </div>
                            
                            <div x-show="useWallet" x-collapse>
                                <div class="pt-3 border-t border-orange-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount to use from wallet</label>
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex-1">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₹</span>
                                            <input type="number" name="wallet_amount" x-model="walletAmount" 
                                                   min="0" max="{{ $maxWalletUsable }}" step="0.01"
                                                   class="w-full pl-8 border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                                        </div>
                                        <button type="button" @click="walletAmount = {{ $maxWalletUsable }}"
                                                class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-3 py-2 rounded-lg text-sm font-medium">
                                            Use Max
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Maximum usable: ₹{{ number_format($maxWalletUsable, 2) }}
                                        @if($walletBalance >= $orderTotal)
                                            <span class="text-orange-500">(Full order can be paid with wallet!)</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-credit-card text-orange-500 mr-2"></i>Payment Method
                    </h2>
                    <div class="space-y-3">
                        @forelse($paymentMethods as $index => $method)
                            <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors payment-option {{ $index === 0 ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}"
                                   data-payment="{{ $method->code }}">
                                <input type="radio" name="payment_method" value="{{ $method->code }}" 
                                       {{ $index === 0 ? 'checked' : '' }}
                                       class="mt-1 text-orange-500 focus:ring-orange-500">
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center">
                                        <i class="fas {{ $method->icon ?? 'fa-credit-card' }} text-lg mr-2 {{ $method->is_online ? 'text-blue-600' : 'text-orange-500' }}"></i>
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
                                        @php $qrCode = $method->getSetting('qr_code'); @endphp
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
                                            <i class="fas fa-info-circle mr-1 text-orange-500"></i>{{ $method->instructions }}
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
                        <i class="fas fa-sticky-note text-orange-500 mr-2"></i>Order Notes (Optional)
                    </h2>
                    <textarea name="customer_notes" rows="3" placeholder="Any special instructions for your order..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">{{ old('customer_notes') }}</textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-lg font-semibold mb-4">
                        <i class="fas fa-shopping-bag text-orange-500 mr-2"></i>Order Summary
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
                                        <p class="text-xs text-orange-500">{{ $item->variant->name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                </div>
                                <span class="text-sm font-medium">₹{{ number_format($item->total, 2) }}</span>
                            </div>
                        @endforeach
                        
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

                    <!-- First-Time Customer Discount -->
                    @if(isset($firstTimeDiscount) && $firstTimeDiscount['eligible'])
                        <div class="border-t mt-4 pt-4">
                            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 p-3 rounded-lg">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-lg">🎉</span>
                                    <span class="font-semibold text-orange-700">First-Time Customer Offer!</span>
                                </div>
                                <p class="text-sm text-orange-600">You get {{ $firstTimeDiscount['discount_percentage'] }}% OFF on this order</p>
                                <p class="text-xs text-gray-500 mt-1">Only {{ $firstTimeDiscount['remaining_slots'] }} slots remaining!</p>
                            </div>
                        </div>
                    @elseif(!auth()->check() && \App\Services\FirstTimeCustomerService::isEnabled())
                        <div class="border-t mt-4 pt-4">
                            <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                                <p class="text-sm text-blue-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    <a href="{{ route('login') }}" class="font-semibold underline">Login</a> to avail first-time customer discount!
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Coupon -->
                    <div class="border-t mt-4 pt-4">
                        @if(session('coupon'))
                            <div class="flex justify-between items-center bg-orange-50 p-3 rounded-lg mb-3">
                                <div>
                                    <span class="text-orange-500 font-medium">{{ session('coupon')->code }}</span>
                                    <p class="text-xs text-orange-500">{{ session('coupon')->name }}</p>
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
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-orange-500 focus:border-orange-500">
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
                            <div class="flex justify-between text-orange-500">
                                <span>Coupon Discount</span>
                                <span>-₹{{ number_format(session('coupon')->calculateDiscount($cart->subtotal), 2) }}</span>
                            </div>
                        @endif
                        @if(isset($firstTimeDiscount) && $firstTimeDiscount['eligible'] && $firstTimeDiscount['discount_amount'] > 0)
                            <div class="flex justify-between text-orange-500">
                                <span>🎉 First-Time Discount ({{ $firstTimeDiscount['discount_percentage'] }}%)</span>
                                <span>-₹{{ number_format($firstTimeDiscount['discount_amount'], 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">GST</span>
                            <span>₹{{ number_format($cart->gst_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            @if($shippingCharge == 0)
                                <span class="text-green-600 font-medium">FREE</span>
                            @else
                                <span>₹{{ number_format($shippingCharge, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="border-t mt-4 pt-4">
                        @php
                            $couponDiscount = session('coupon') ? session('coupon')->calculateDiscount($cart->subtotal) : 0;
                            $firstTimeDiscountAmt = (isset($firstTimeDiscount) && $firstTimeDiscount['eligible']) ? $firstTimeDiscount['discount_amount'] : 0;
                            $totalDiscount = $couponDiscount + $firstTimeDiscountAmt;
                            $total = $cart->subtotal - $totalDiscount + $cart->gst_amount + $shippingCharge;
                        @endphp
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-orange-500">₹{{ number_format($total, 2) }}</span>
                        </div>
                        @if($totalDiscount > 0)
                            <p class="text-xs text-orange-500 text-right mt-1">
                                You save ₹{{ number_format($totalDiscount, 2) }} on this order!
                            </p>
                        @endif
                    </div>

                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold mt-6 transition-colors">
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
<!-- Google Maps Places API -->
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initAutocomplete" async defer></script>

<script>
let autocomplete;
let placesService;

function initAutocomplete() {
    const addressInput = document.getElementById('address_search');
    
    if (!addressInput) return;
    
    // Initialize Google Places Autocomplete - removed 'types' restriction to allow all place types
    // This allows searching for establishments (like "Suriya Sri Flats"), addresses, and regions
    autocomplete = new google.maps.places.Autocomplete(addressInput, {
        componentRestrictions: { country: 'IN' }, // Restrict to India
        fields: ['address_components', 'geometry', 'formatted_address', 'name', 'place_id']
        // No 'types' restriction - allows all types including establishments, addresses, etc.
    });
    
    // Listen for place selection
    autocomplete.addListener('place_changed', fillInAddress);
    
    // Handle paste event - trigger search after paste
    addressInput.addEventListener('paste', function(e) {
        // Wait for paste to complete, then trigger autocomplete
        setTimeout(() => {
            // Trigger input event to activate autocomplete
            const inputEvent = new Event('input', { bubbles: true });
            addressInput.dispatchEvent(inputEvent);
            
            // Also trigger a focus to show dropdown
            addressInput.focus();
            
            // Simulate keydown to trigger Google's autocomplete
            const keyEvent = new KeyboardEvent('keydown', {
                key: 'ArrowDown',
                keyCode: 40,
                bubbles: true
            });
            addressInput.dispatchEvent(keyEvent);
        }, 100);
    });
    
    // Show loading indicator while typing
    addressInput.addEventListener('input', function() {
        document.getElementById('location_selected').classList.add('hidden');
    });
}

function fillInAddress() {
    const place = autocomplete.getPlace();
    
    if (!place.geometry) {
        console.log("No geometry found for the selected place");
        // Try to show an error message
        alert('Please select an address from the dropdown suggestions.');
        return;
    }
    
    // Clear existing values
    document.getElementById('shipping_address').value = '';
    document.getElementById('shipping_city').value = '';
    document.getElementById('shipping_state').value = '';
    document.getElementById('shipping_pincode').value = '';
    
    // Get latitude and longitude
    const lat = place.geometry.location.lat();
    const lng = place.geometry.location.lng();
    
    document.getElementById('shipping_latitude').value = lat;
    document.getElementById('shipping_longitude').value = lng;
    
    // Parse address components
    let streetNumber = '';
    let route = '';
    let sublocality = '';
    let locality = '';
    let adminArea1 = '';
    let adminArea2 = '';
    let postalCode = '';
    let premise = '';
    let neighborhood = '';
    let placeName = place.name || ''; // For establishments like "Suriya Sri Flats"
    
    if (place.address_components) {
        for (const component of place.address_components) {
            const type = component.types[0];
            
            switch (type) {
                case 'street_number':
                    streetNumber = component.long_name;
                    break;
                case 'route':
                    route = component.long_name;
                    break;
                case 'premise':
                    premise = component.long_name;
                    break;
                case 'neighborhood':
                    neighborhood = component.long_name;
                    break;
                case 'sublocality_level_3':
                case 'sublocality_level_2':
                case 'sublocality_level_1':
                case 'sublocality':
                    if (!sublocality) sublocality = component.long_name;
                    break;
                case 'locality':
                    locality = component.long_name;
                    break;
                case 'administrative_area_level_2':
                    adminArea2 = component.long_name;
                    break;
                case 'administrative_area_level_1':
                    adminArea1 = component.long_name;
                    break;
                case 'postal_code':
                    postalCode = component.long_name;
                    break;
            }
        }
    }
    
    // Build full address - include place name for establishments
    let fullAddress = [];
    if (placeName && placeName !== locality && placeName !== sublocality) {
        fullAddress.push(placeName); // Add establishment name (e.g., "Suriya Sri Flats")
    }
    if (premise && premise !== placeName) fullAddress.push(premise);
    if (streetNumber) fullAddress.push(streetNumber);
    if (route) fullAddress.push(route);
    if (neighborhood && neighborhood !== placeName) fullAddress.push(neighborhood);
    if (sublocality && sublocality !== placeName) fullAddress.push(sublocality);
    
    // Set field values
    document.getElementById('shipping_address').value = fullAddress.length > 0 ? fullAddress.join(', ') : place.formatted_address;
    document.getElementById('shipping_city').value = locality || adminArea2 || '';
    document.getElementById('shipping_state').value = adminArea1 || '';
    document.getElementById('shipping_pincode').value = postalCode || '';
    
    // Show location selected indicator
    document.getElementById('location_selected').classList.remove('hidden');
    
    // Show formatted address in search input
    document.getElementById('address_search').value = place.formatted_address;
    
    console.log('Location:', { lat, lng, address: place.formatted_address, name: placeName });
}

// Payment method selection styling and show/hide details
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', function() {
        // Remove highlight from all
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('border-orange-500', 'bg-orange-50');
            opt.classList.add('border-gray-200');
        });
        
        // Highlight selected
        this.classList.remove('border-gray-200');
        this.classList.add('border-orange-500', 'bg-orange-50');
        
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
