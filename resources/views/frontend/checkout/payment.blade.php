@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-bold">Complete Your Payment</h1>
                <span class="text-green-600 font-semibold text-xl">Rs. {{ number_format($order->total_amount, 2) }}</span>
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

        @if($paymentMethod->code === 'razorpay')
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
                <span x-show="!processing"><i class="fas fa-lock mr-2"></i>Pay Rs. {{ number_format($order->total_amount, 2) }}</span>
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
            </div>

            @if($paymentMethod->getSetting('upi_id'))
            <div class="bg-gray-50 rounded-lg p-4 text-center mb-4">
                <p class="text-sm text-gray-500 mb-1">Pay to UPI ID</p>
                <p class="font-mono text-lg font-bold text-purple-600">{{ $paymentMethod->getSetting('upi_id') }}</p>
            </div>
            @endif

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    After payment, share transaction ID on WhatsApp to confirm your order.
                </p>
            </div>
        </div>

        @elseif($paymentMethod->code === 'bank_transfer')
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-university text-indigo-600 text-2xl"></i>
                </div>
                <h2 class="text-lg font-semibold">Bank Transfer Details</h2>
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
            </div>

            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    Please share transaction reference number to confirm your order.
                </p>
            </div>
        </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('tracking.show', $order) }}" class="text-green-600 hover:text-green-700">
                <i class="fas fa-search mr-1"></i>Track your order
            </a>
        </div>
    </div>
</div>
@endsection

@if($paymentMethod->code === 'razorpay')
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
