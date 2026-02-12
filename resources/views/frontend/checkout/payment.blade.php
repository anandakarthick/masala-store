@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-bold">Complete Your Payment</h1>
                <span class="text-green-600 font-semibold text-xl">₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Order Number</p>
                        <p class="font-medium">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Payment Method</p>
                        <p class="font-medium">{{ $paymentMethod->display_name }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($paymentMethod->code === 'phonepe')
        <div class="bg-white rounded-lg shadow-md p-6" x-data="phonePePayment()">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" fill="#5F259F"/>
                        <path d="M15.5 8H11c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h1v-3h2.5c1.38 0 2.5-1.12 2.5-2.5S16.88 8 15.5 8zm0 3.5H12V9.5h3.5c.55 0 1 .45 1 1s-.45 1-1 1z" fill="white"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold">Secure Payment via PhonePe</h2>
                <p class="text-gray-500 text-sm mt-1">Pay using UPI, Card, Net Banking or Wallet</p>
            </div>

            <div class="flex justify-center space-x-4 mb-6">
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-1">
                        <i class="fas fa-mobile-alt text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-gray-500">UPI</span>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-1">
                        <i class="fas fa-credit-card text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-gray-500">Card</span>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-1">
                        <i class="fas fa-university text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-gray-500">NetBanking</span>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mx-auto mb-1">
                        <i class="fas fa-wallet text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs text-gray-500">Wallet</span>
                </div>
            </div>

            <div x-show="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" x-text="error"></div>

            <button @click="initiatePayment()" :disabled="processing"
                    class="w-full bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-4 rounded-lg font-semibold transition-colors">
                <span x-show="!processing"><i class="fas fa-lock mr-2"></i>Pay ₹{{ number_format($amountToPay, 2) }}</span>
                <span x-show="processing"><i class="fas fa-spinner fa-spin mr-2"></i>Redirecting to PhonePe...</span>
            </button>

            <p class="text-xs text-gray-500 text-center mt-4">
                <i class="fas fa-shield-alt mr-1"></i>Your payment is secured by PhonePe
            </p>
        </div>

        @elseif($paymentMethod->code === 'razorpay')
        <div class="bg-white rounded-lg shadow-md p-6" x-data="razorpayPayment()">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-blue-600 text-2xl"></i>
                </div>
                <h2 class="text-lg font-semibold">Secure Payment via Razorpay</h2>
                <p class="text-gray-500 text-sm mt-1">Pay using Card, UPI, Net Banking or Wallet</p>
            </div>

            <div class="flex justify-center space-x-3 mb-6">
                <img src="https://cdn.razorpay.com/static/assets/logo/payment/visa.svg" class="h-7" alt="Visa">
                <img src="https://cdn.razorpay.com/static/assets/logo/payment/mastercard.svg" class="h-7" alt="Mastercard">
                <img src="https://cdn.razorpay.com/static/assets/logo/payment/upi.svg" class="h-7" alt="UPI">
            </div>

            <div x-show="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" x-text="error"></div>

            <button @click="initiatePayment()" :disabled="processing"
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white py-4 rounded-lg font-semibold">
                <span x-show="!processing"><i class="fas fa-lock mr-2"></i>Pay ₹{{ number_format($amountToPay, 2) }}</span>
                <span x-show="processing"><i class="fas fa-spinner fa-spin mr-2"></i>Processing...</span>
            </button>

            <p class="text-xs text-gray-500 text-center mt-4">
                <i class="fas fa-shield-alt mr-1"></i>Your payment is secured by Razorpay
            </p>
        </div>

        @elseif($paymentMethod->code === 'upi')
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-mobile-alt text-purple-600 text-2xl"></i>
                </div>
                <h2 class="text-lg font-semibold">Pay via UPI</h2>
                <p class="text-gray-500 text-sm mt-1">Scan QR code or pay using UPI ID</p>
            </div>

            {{-- QR Code Image --}}
            @php
                $qrCode = $paymentMethod->getSetting('qr_code');
            @endphp
            
            @if($qrCode)
            <div class="text-center mb-6">
                <div class="inline-block p-4 bg-white border-2 border-purple-200 rounded-xl shadow-sm">
                    <img src="{{ asset('storage/' . $qrCode) }}" alt="UPI QR Code" class="w-48 h-48 object-contain mx-auto">
                </div>
                <p class="text-sm text-gray-500 mt-3">
                    <i class="fas fa-camera mr-1"></i> Scan with any UPI app
                </p>
            </div>
            @endif

            {{-- Amount to Pay --}}
            <div class="bg-purple-50 rounded-lg p-4 text-center mb-4">
                <p class="text-sm text-purple-600 mb-1">Amount to Pay</p>
                <p class="text-3xl font-bold text-purple-700">₹{{ number_format($order->total_amount, 2) }}</p>
            </div>

            {{-- UPI ID --}}
            @if($paymentMethod->getSetting('upi_id'))
            <div class="bg-gray-50 rounded-lg p-4 text-center mb-4">
                <p class="text-sm text-gray-500 mb-1">Or pay using UPI ID</p>
                <div class="flex items-center justify-center gap-2">
                    <p class="font-mono text-lg font-bold text-purple-600" id="upi-id">{{ $paymentMethod->getSetting('upi_id') }}</p>
                    <button onclick="copyUpiId()" class="text-gray-500 hover:text-purple-600" title="Copy UPI ID">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                @if($paymentMethod->getSetting('upi_name'))
                    <p class="text-xs text-gray-500 mt-1">Payee: {{ $paymentMethod->getSetting('upi_name') }}</p>
                @endif
            </div>
            @endif

            {{-- Payment Instructions --}}
            @if($paymentMethod->instructions)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ $paymentMethod->instructions }}
                </p>
            </div>
            @endif

            {{-- After Payment Info --}}
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800 font-medium mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    After making payment:
                </p>
                <ol class="text-sm text-yellow-700 list-decimal list-inside space-y-1">
                    <li>Take a screenshot of the payment confirmation</li>
                    <li>Share the transaction ID/UTR number on WhatsApp</li>
                    <li>Your order will be confirmed within 30 minutes</li>
                </ol>
            </div>

            {{-- WhatsApp Button --}}
            @php
                $whatsappNumber = \App\Models\Setting::get('whatsapp_number', '');
                $whatsappMessage = "Hi! I've made a UPI payment for Order #{$order->order_number}. Amount: ₹" . number_format($order->total_amount, 2) . ". Transaction ID: ";
            @endphp
            @if($whatsappNumber)
            <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
               target="_blank" rel="noopener"
               class="mt-4 w-full flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg font-semibold">
                <i class="fab fa-whatsapp text-xl"></i>
                Share Payment Details on WhatsApp
            </a>
            @endif
        </div>

        @elseif($paymentMethod->code === 'bank_transfer')
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-university text-indigo-600 text-2xl"></i>
                </div>
                <h2 class="text-lg font-semibold">Bank Transfer Details</h2>
            </div>

            {{-- Amount to Pay --}}
            <div class="bg-indigo-50 rounded-lg p-4 text-center mb-4">
                <p class="text-sm text-indigo-600 mb-1">Amount to Pay</p>
                <p class="text-3xl font-bold text-indigo-700">₹{{ number_format($order->total_amount, 2) }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                @if($paymentMethod->getSetting('account_name'))
                <div class="flex justify-between">
                    <span class="text-gray-500">Account Name</span>
                    <span class="font-medium">{{ $paymentMethod->getSetting('account_name') }}</span>
                </div>
                @endif
                @if($paymentMethod->getSetting('account_number'))
                <div class="flex justify-between">
                    <span class="text-gray-500">Account Number</span>
                    <span class="font-medium font-mono">{{ $paymentMethod->getSetting('account_number') }}</span>
                </div>
                @endif
                @if($paymentMethod->getSetting('bank_name'))
                <div class="flex justify-between">
                    <span class="text-gray-500">Bank Name</span>
                    <span class="font-medium">{{ $paymentMethod->getSetting('bank_name') }}</span>
                </div>
                @endif
                @if($paymentMethod->getSetting('ifsc_code'))
                <div class="flex justify-between">
                    <span class="text-gray-500">IFSC Code</span>
                    <span class="font-medium font-mono">{{ $paymentMethod->getSetting('ifsc_code') }}</span>
                </div>
                @endif
                @if($paymentMethod->getSetting('branch'))
                <div class="flex justify-between">
                    <span class="text-gray-500">Branch</span>
                    <span class="font-medium">{{ $paymentMethod->getSetting('branch') }}</span>
                </div>
                @endif
            </div>

            @if($paymentMethod->instructions)
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ $paymentMethod->instructions }}
                </p>
            </div>
            @endif

            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800 font-medium mb-2">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    After making payment:
                </p>
                <ol class="text-sm text-yellow-700 list-decimal list-inside space-y-1">
                    <li>Note down the transaction reference number</li>
                    <li>Share the details on WhatsApp to confirm your order</li>
                </ol>
            </div>

            {{-- WhatsApp Button --}}
            @php
                $whatsappNumber = \App\Models\Setting::get('whatsapp_number', '');
                $whatsappMessage = "Hi! I've made a bank transfer for Order #{$order->order_number}. Amount: ₹" . number_format($order->total_amount, 2) . ". Transaction Ref: ";
            @endphp
            @if($whatsappNumber)
            <a href="https://wa.me/91{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" 
               target="_blank" rel="noopener"
               class="mt-4 w-full flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg font-semibold">
                <i class="fab fa-whatsapp text-xl"></i>
                Share Payment Details on WhatsApp
            </a>
            @endif
        </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('tracking.show', $order) }}" class="text-green-600 hover:text-green-700">
                <i class="fas fa-search mr-1"></i>Track your order
            </a>
        </div>
    </div>
</div>

<script>
function copyUpiId() {
    const upiId = document.getElementById('upi-id').textContent;
    navigator.clipboard.writeText(upiId).then(() => {
        alert('UPI ID copied!');
    });
}
</script>
@endsection

@if($paymentMethod->code === 'phonepe')
@push('scripts')
<script>
function phonePePayment() {
    return {
        processing: false,
        error: null,

        async initiatePayment() {
            this.processing = true;
            this.error = null;

            try {
                const response = await fetch('{{ route("phonepe.create-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: {{ $order->id }}
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    this.error = data.message || 'Failed to create payment order';
                    this.processing = false;
                    return;
                }

                // Redirect to PhonePe payment page
                window.location.href = data.redirect_url;

            } catch (err) {
                console.error('Payment error:', err);
                this.error = 'Something went wrong. Please try again.';
                this.processing = false;
            }
        }
    }
}
</script>
@endpush
@elseif($paymentMethod->code === 'razorpay')
@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function razorpayPayment() {
    return {
        processing: false,
        error: null,
        
        async initiatePayment() {
            this.processing = true;
            this.error = null;
            
            try {
                const response = await fetch('{{ route("razorpay.create-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_id: {{ $order->id }} })
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    this.error = data.message || 'Failed to create payment order';
                    this.processing = false;
                    return;
                }
                
                const options = {
                    key: data.razorpay_key_id,
                    amount: data.amount * 100,
                    currency: data.currency,
                    name: data.name,
                    description: data.description,
                    order_id: data.razorpay_order_id,
                    prefill: data.prefill,
                    theme: { color: '#16a34a' },
                    handler: (response) => this.verifyPayment(response),
                    modal: {
                        ondismiss: () => {
                            this.processing = false;
                            this.error = 'Payment cancelled';
                        }
                    }
                };
                
                const rzp = new Razorpay(options);
                rzp.open();
                
            } catch (err) {
                console.error('Payment error:', err);
                this.error = 'Something went wrong. Please try again.';
                this.processing = false;
            }
        },
        
        async verifyPayment(response) {
            try {
                const verifyResponse = await fetch('{{ route("razorpay.verify-payment") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature: response.razorpay_signature,
                        order_id: {{ $order->id }}
                    })
                });
                
                const data = await verifyResponse.json();
                
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    this.error = data.message || 'Payment verification failed';
                    this.processing = false;
                }
            } catch (err) {
                console.error('Verification error:', err);
                this.error = 'Payment verification failed. Please contact support.';
                this.processing = false;
            }
        }
    }
}
</script>
@endpush
@endif
