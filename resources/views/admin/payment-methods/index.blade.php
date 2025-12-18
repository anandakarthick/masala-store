@extends('layouts.admin')

@section('title', 'Payment Methods')
@section('page_title', 'Payment Methods')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <h2 class="text-lg font-semibold">Manage Payment Methods</h2>
        <p class="text-gray-600 text-sm mt-1">Configure payment options for your store</p>
    </div>
    
    <div class="divide-y">
        @foreach($paymentMethods as $method)
        <div class="p-6 flex items-center justify-between hover:bg-gray-50">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $method->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <i class="fas {{ $method->icon ?? 'fa-credit-card' }} text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $method->display_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $method->description }}</p>
                    <div class="flex items-center space-x-3 mt-1">
                        <span class="text-xs {{ $method->is_active ? 'text-green-600' : 'text-gray-400' }}">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            {{ $method->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($method->is_online)
                            <span class="text-xs text-blue-600">
                                <i class="fas fa-globe mr-1"></i>Online Payment
                            </span>
                        @endif
                        @if($method->extra_charge > 0)
                            <span class="text-xs text-orange-600">
                                <i class="fas fa-plus-circle mr-1"></i>
                                +{{ $method->extra_charge_type === 'percentage' ? $method->extra_charge . '%' : 'Rs. ' . number_format($method->extra_charge, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Toggle Switch -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           class="sr-only peer" 
                           {{ $method->is_active ? 'checked' : '' }}
                           onchange="togglePaymentMethod({{ $method->id }}, this)">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
                
                <!-- Edit Button -->
                <a href="{{ route('admin.payment-methods.edit', $method) }}" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                    <i class="fas fa-cog mr-1"></i> Configure
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Payment Method Info Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <!-- Razorpay Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-credit-card text-white"></i>
            </div>
            <h3 class="font-semibold text-blue-800">Razorpay Integration</h3>
        </div>
        <p class="text-sm text-blue-700 mb-3">
            Accept payments via Credit/Debit Cards, UPI, Net Banking, Wallets, and more.
        </p>
        <ul class="text-sm text-blue-600 space-y-1">
            <li><i class="fas fa-check mr-2"></i>No setup fees</li>
            <li><i class="fas fa-check mr-2"></i>2% transaction fee</li>
            <li><i class="fas fa-check mr-2"></i>Instant settlement available</li>
        </ul>
        <a href="https://razorpay.com" target="_blank" class="inline-block mt-4 text-blue-600 hover:text-blue-800 text-sm font-medium">
            <i class="fas fa-external-link-alt mr-1"></i> Learn more about Razorpay
        </a>
    </div>
    
    <!-- COD Info -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <h3 class="font-semibold text-green-800">Cash on Delivery</h3>
        </div>
        <p class="text-sm text-green-700 mb-3">
            Customers pay when they receive their order. Most popular payment method in India.
        </p>
        <ul class="text-sm text-green-600 space-y-1">
            <li><i class="fas fa-check mr-2"></i>No payment gateway fees</li>
            <li><i class="fas fa-check mr-2"></i>Higher customer trust</li>
            <li><i class="fas fa-check mr-2"></i>Set minimum/maximum order limits</li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePaymentMethod(id, checkbox) {
    fetch(`{{ url('admin/payment-methods') }}/${id}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const container = checkbox.closest('.p-6');
            const icon = container.querySelector('.w-12');
            const status = container.querySelector('.text-xs');
            
            if (data.is_active) {
                icon.classList.remove('bg-gray-100', 'text-gray-400');
                icon.classList.add('bg-green-100', 'text-green-600');
                status.classList.remove('text-gray-400');
                status.classList.add('text-green-600');
                status.innerHTML = '<i class="fas fa-circle text-xs mr-1"></i>Active';
            } else {
                icon.classList.remove('bg-green-100', 'text-green-600');
                icon.classList.add('bg-gray-100', 'text-gray-400');
                status.classList.remove('text-green-600');
                status.classList.add('text-gray-400');
                status.innerHTML = '<i class="fas fa-circle text-xs mr-1"></i>Inactive';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        checkbox.checked = !checkbox.checked;
    });
}
</script>
@endpush
