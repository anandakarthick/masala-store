@extends('layouts.admin')

@section('title', 'Edit Payment Method')
@section('page_title', 'Edit Payment Method')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.payment-methods.index') }}" class="text-green-600 hover:text-green-700">
            <i class="fas fa-arrow-left mr-2"></i>Back to Payment Methods
        </a>
    </div>

    <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="p-6 border-b flex items-center space-x-4">
                <div class="w-14 h-14 rounded-lg flex items-center justify-center {{ $paymentMethod->is_active ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                    <i class="fas {{ $paymentMethod->icon ?? 'fa-credit-card' }} text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">{{ $paymentMethod->name }}</h2>
                    <p class="text-gray-500 text-sm">Code: {{ $paymentMethod->code }}</p>
                </div>
                <!-- Active Toggle - Moved to header -->
                <div class="ml-auto">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ $paymentMethod->is_active ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Basic Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Name *</label>
                        <input type="text" name="display_name" value="{{ old('display_name', $paymentMethod->display_name) }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500" required>
                        @error('display_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-500">
                                <i class="fas {{ $paymentMethod->icon ?? 'fa-credit-card' }}"></i>
                            </span>
                            <input type="text" name="icon" value="{{ old('icon', $paymentMethod->icon) }}" 
                                   placeholder="fa-credit-card"
                                   class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Example: fa-credit-card, fa-money-bill-wave, fa-mobile-alt</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description', $paymentMethod->description) }}" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Instructions</label>
                    <textarea name="instructions" rows="2" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">{{ old('instructions', $paymentMethod->instructions) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Instructions shown to customer during checkout</p>
                </div>
                
                <!-- Order Amount Limits & Extra Charges in one row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Order (₹)</label>
                        <input type="number" name="min_order_amount" step="0.01" min="0"
                               value="{{ old('min_order_amount', $paymentMethod->min_order_amount) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Order (₹)</label>
                        <input type="number" name="max_order_amount" step="0.01" min="0"
                               value="{{ old('max_order_amount', $paymentMethod->max_order_amount) }}" 
                               placeholder="No limit"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Extra Charge</label>
                        <input type="number" name="extra_charge" step="0.01" min="0"
                               value="{{ old('extra_charge', $paymentMethod->extra_charge) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Charge Type</label>
                        <select name="extra_charge_type" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <option value="fixed" {{ $paymentMethod->extra_charge_type === 'fixed' ? 'selected' : '' }}>Fixed (₹)</option>
                            <option value="percentage" {{ $paymentMethod->extra_charge_type === 'percentage' ? 'selected' : '' }}>Percent (%)</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" min="0"
                           value="{{ old('sort_order', $paymentMethod->sort_order) }}" 
                           class="w-24 border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                </div>
                
                <!-- Razorpay Settings -->
                @if($paymentMethod->code === 'razorpay')
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-cog mr-2 text-blue-600"></i>Razorpay Configuration
                    </h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Get your API keys from <a href="https://dashboard.razorpay.com/app/keys" target="_blank" class="underline font-medium">Razorpay Dashboard</a>
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Key ID *</label>
                            <input type="text" name="razorpay_key_id" 
                                   value="{{ old('razorpay_key_id', $paymentMethod->getSetting('key_id')) }}" 
                                   placeholder="rzp_live_xxxxxxxxxxxxx"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Key Secret *</label>
                            <input type="password" name="razorpay_key_secret" 
                                   value="{{ old('razorpay_key_secret', $paymentMethod->getSetting('key_secret')) }}" 
                                   placeholder="••••••••••••••••"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret (Optional)</label>
                        <input type="text" name="razorpay_webhook_secret" 
                               value="{{ old('razorpay_webhook_secret', $paymentMethod->getSetting('webhook_secret')) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                @endif
                
                <!-- UPI Settings -->
                @if($paymentMethod->code === 'upi')
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-mobile-alt mr-2 text-purple-600"></i>UPI Configuration
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">UPI ID</label>
                            <input type="text" name="upi_id" 
                                   value="{{ old('upi_id', $paymentMethod->getSetting('upi_id')) }}" 
                                   placeholder="yourname@upi"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payee Name</label>
                            <input type="text" name="upi_name" 
                                   value="{{ old('upi_name', $paymentMethod->getSetting('upi_name')) }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">QR Code Image URL</label>
                        <input type="text" name="qr_code" 
                               value="{{ old('qr_code', $paymentMethod->getSetting('qr_code')) }}" 
                               placeholder="https://example.com/upi-qr.png"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                @endif
                
                <!-- Bank Transfer Settings -->
                @if($paymentMethod->code === 'bank_transfer')
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-university mr-2 text-indigo-600"></i>Bank Account Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account Holder Name</label>
                            <input type="text" name="account_name" 
                                   value="{{ old('account_name', $paymentMethod->getSetting('account_name')) }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                            <input type="text" name="account_number" 
                                   value="{{ old('account_number', $paymentMethod->getSetting('account_number')) }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                            <input type="text" name="bank_name" 
                                   value="{{ old('bank_name', $paymentMethod->getSetting('bank_name')) }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">IFSC Code</label>
                            <input type="text" name="ifsc_code" 
                                   value="{{ old('ifsc_code', $paymentMethod->getSetting('ifsc_code')) }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                            <input type="text" name="branch" 
                                   value="{{ old('branch', $paymentMethod->getSetting('branch')) }}" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg flex justify-end space-x-3">
                <a href="{{ route('admin.payment-methods.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
