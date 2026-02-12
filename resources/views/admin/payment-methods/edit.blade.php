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

    <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data">
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
                
                <!-- PhonePe Settings -->
                @if($paymentMethod->code === 'phonepe')
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-mobile-alt mr-2 text-purple-600"></i>PhonePe Configuration
                    </h3>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-purple-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Get your PhonePe PG credentials from <a href="https://developer.phonepe.com/" target="_blank" class="underline font-medium">PhonePe Developer Dashboard</a>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Client ID *</label>
                            <input type="text" name="phonepe_client_id"
                                   value="{{ old('phonepe_client_id', $paymentMethod->getSetting('client_id')) }}"
                                   placeholder="TEST-M22XXXXXXXXXXXXX"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Your PhonePe Client ID</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Client Secret *</label>
                            <input type="password" name="phonepe_client_secret"
                                   value="{{ old('phonepe_client_secret', $paymentMethod->getSetting('client_secret')) }}"
                                   placeholder="••••••••••••••••"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Your PhonePe Client Secret</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Client Version</label>
                            <input type="number" name="phonepe_client_version"
                                   value="{{ old('phonepe_client_version', $paymentMethod->getSetting('client_version', '1')) }}"
                                   placeholder="1"
                                   min="1"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Usually "1" (check your PhonePe dashboard)</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Environment</label>
                            <select name="phonepe_environment"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                                <option value="sandbox" {{ $paymentMethod->getSetting('environment', 'sandbox') === 'sandbox' ? 'selected' : '' }}>UAT / Sandbox (Testing)</option>
                                <option value="production" {{ $paymentMethod->getSetting('environment') === 'production' ? 'selected' : '' }}>Production (Live)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Use UAT/Sandbox for testing first</p>
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="text-sm font-semibold text-blue-700 mb-2">
                            <i class="fas fa-flask mr-1"></i> How to get credentials
                        </h4>
                        <ol class="text-xs text-blue-600 space-y-1 list-decimal list-inside">
                            <li>Go to <a href="https://developer.phonepe.com/" target="_blank" class="underline">developer.phonepe.com</a></li>
                            <li>Sign up / Login to your merchant account</li>
                            <li>Navigate to API Keys section</li>
                            <li>Copy your Client ID and Client Secret</li>
                        </ol>
                    </div>

                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="text-sm font-semibold text-yellow-700 mb-2">
                            <i class="fas fa-globe mr-1"></i> Webhook URL (S2S Callback)
                        </h4>
                        <code class="text-xs text-yellow-600 break-all">
                            {{ url('/phonepe/webhook') }}
                        </code>
                        <p class="text-xs text-yellow-600 mt-2">Add this URL in your PhonePe dashboard for receiving payment notifications.</p>
                    </div>
                </div>
                @endif

                <!-- UPI Settings -->
                @if($paymentMethod->code === 'upi')
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-mobile-alt mr-2 text-purple-600"></i>UPI Configuration
                    </h3>
                    
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-purple-700">
                            <i class="fas fa-info-circle mr-1"></i>
                            Configure your UPI details for receiving payments. For business accounts, you can get a merchant VPA from your bank or payment provider.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">UPI ID (VPA) *</label>
                            <input type="text" name="upi_id" 
                                   value="{{ old('upi_id', $paymentMethod->getSetting('upi_id')) }}" 
                                   placeholder="yourname@upi or merchant@bank"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Example: yourstore@okaxis, business@ybl</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payee/Merchant Name *</label>
                            <input type="text" name="upi_name" 
                                   value="{{ old('upi_name', $paymentMethod->getSetting('upi_name')) }}" 
                                   placeholder="Your Business Name"
                                   maxlength="20"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Max 20 characters, alphanumeric only</p>
                        </div>
                    </div>
                    
                    <!-- Merchant Configuration -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-store mr-1"></i> Merchant Configuration (Optional)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Merchant Code (MCC)</label>
                                <input type="text" name="merchant_code" 
                                       value="{{ old('merchant_code', $paymentMethod->getSetting('merchant_code', '0000')) }}" 
                                       placeholder="0000"
                                       maxlength="4"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                                <p class="text-xs text-gray-500 mt-1">4-digit code. Common: 5411 (Grocery), 5499 (Food Store), 0000 (Default)</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Transaction URL (Optional)</label>
                                <input type="url" name="transaction_url" 
                                       value="{{ old('transaction_url', $paymentMethod->getSetting('transaction_url')) }}" 
                                       placeholder="https://yourstore.com/order/"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-green-500 focus:border-green-500">
                                <p class="text-xs text-gray-500 mt-1">URL for transaction reference (used by some UPI apps)</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- QR Code Upload -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">QR Code Image</label>
                        
                        @php
                            $qrCode = $paymentMethod->getSetting('qr_code');
                        @endphp
                        
                        @if($qrCode)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg inline-block">
                                <img src="{{ asset('storage/' . $qrCode) }}" alt="UPI QR Code" class="max-w-[200px] h-auto rounded border">
                                <div class="mt-2 flex items-center gap-2">
                                    <label class="flex items-center text-sm text-red-600 cursor-pointer">
                                        <input type="checkbox" name="remove_qr_code" value="1" class="mr-1">
                                        Remove QR Code
                                    </label>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-qrcode text-3xl text-gray-400 mb-2"></i>
                                    <p class="mb-1 text-sm text-gray-500">
                                        <span class="font-semibold">Click to upload QR Code</span>
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG or GIF (Max 2MB)</p>
                                </div>
                                <input type="file" name="qr_code_image" accept="image/*" class="hidden" onchange="previewQR(this)">
                            </label>
                        </div>
                        
                        <!-- Preview -->
                        <div id="qr-preview" class="mt-4 hidden">
                            <p class="text-sm text-gray-600 mb-2">New QR Code Preview:</p>
                            <img id="qr-preview-img" src="" alt="QR Preview" class="max-w-[200px] h-auto rounded border">
                        </div>
                        
                        @error('qr_code_image')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- UPI Deep Link Preview -->
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="text-sm font-semibold text-blue-700 mb-2">
                            <i class="fas fa-link mr-1"></i> UPI Deep Link Format
                        </h4>
                        <code class="text-xs text-blue-600 break-all">
                            upi://pay?pa={{ $paymentMethod->getSetting('upi_id') ?: 'your-upi@bank' }}&pn={{ $paymentMethod->getSetting('upi_name') ?: 'MerchantName' }}&mc={{ $paymentMethod->getSetting('merchant_code', '0000') }}&tr={ORDER_REF}&tn=Payment&am={AMOUNT}&cu=INR
                        </code>
                        <p class="text-xs text-blue-600 mt-2">This is the format used when customers pay via UPI apps.</p>
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

<script>
function previewQR(input) {
    const preview = document.getElementById('qr-preview');
    const previewImg = document.getElementById('qr-preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
