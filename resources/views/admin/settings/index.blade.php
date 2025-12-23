@extends('layouts.admin')
@section('title', 'Settings')
@section('page_title', 'General Settings')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Business Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Business Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                    <input type="text" name="business_name" value="{{ \App\Models\Setting::get('business_name', 'Masala Store') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Email</label>
                    <input type="email" name="business_email" value="{{ \App\Models\Setting::get('business_email') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Phone</label>
                    <input type="text" name="business_phone" value="{{ \App\Models\Setting::get('business_phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Address</label>
                    <textarea name="business_address" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">{{ \App\Models\Setting::get('business_address') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Number</label>
                    <input type="text" name="gst_number" value="{{ \App\Models\Setting::get('gst_number') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    @if(\App\Models\Setting::logo())
                        <img src="{{ \App\Models\Setting::logo() }}" alt="Logo" class="h-16 mb-2">
                    @endif
                    <input type="file" name="logo" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>
        </div>

        <!-- Order Settings -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Order Settings</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                    <input type="text" name="currency" value="{{ \App\Models\Setting::get('currency', '₹') }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Order Amount (₹)</label>
                    <input type="number" name="min_order_amount" value="{{ \App\Models\Setting::get('min_order_amount', 0) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Free Shipping Amount (₹)</label>
                    <input type="number" name="free_shipping_amount" value="{{ \App\Models\Setting::get('free_shipping_amount', 500) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <p class="text-sm text-gray-500 mt-1">Orders above this amount get free shipping</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Shipping Charge (₹)</label>
                    <input type="number" name="default_shipping_charge" value="{{ \App\Models\Setting::get('default_shipping_charge', 50) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>
        </div>
    </div>

    <!-- First Time Customer Discount Section -->
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-gift text-green-600 mr-2"></i>First-Time Customer Discount
                </h3>
                @php
                    $usedCount = \App\Models\Order::whereNotNull('first_time_discount_applied')
                        ->where('first_time_discount_applied', '>', 0)
                        ->distinct('user_id')
                        ->count('user_id');
                    $maxCustomers = (int) \App\Models\Setting::get('first_time_discount_max_customers', 0);
                @endphp
                @if($maxCustomers > 0)
                    <span class="px-3 py-1 text-sm rounded-full {{ $usedCount >= $maxCustomers ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $usedCount }}/{{ $maxCustomers }} used
                    </span>
                @endif
            </div>
            
            <p class="text-sm text-gray-600 mb-4">
                Offer a special discount to first-time customers. This discount will be automatically applied at checkout for eligible customers.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Enable/Disable -->
                <div class="col-span-full">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="first_time_discount_enabled" value="1" 
                               {{ \App\Models\Setting::get('first_time_discount_enabled') ? 'checked' : '' }}
                               class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable First-Time Customer Discount</span>
                    </label>
                </div>

                <!-- Max Customers -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Number of Customers <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="first_time_discount_max_customers" 
                           value="{{ \App\Models\Setting::get('first_time_discount_max_customers', 10) }}"
                           min="1" max="10000"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">First X unique customers will get this offer</p>
                </div>

                <!-- Discount Percentage -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Discount Percentage (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="first_time_discount_percentage" 
                           value="{{ \App\Models\Setting::get('first_time_discount_percentage', 20) }}"
                           min="1" max="100" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Percentage discount to apply</p>
                </div>

                <!-- Minimum Order Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Minimum Order Amount (₹)
                    </label>
                    <input type="number" name="first_time_discount_min_order" 
                           value="{{ \App\Models\Setting::get('first_time_discount_min_order', 0) }}"
                           min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Leave 0 for no minimum</p>
                </div>

                <!-- Maximum Discount Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Maximum Discount Amount (₹)
                    </label>
                    <input type="number" name="first_time_discount_max_amount" 
                           value="{{ \App\Models\Setting::get('first_time_discount_max_amount', 0) }}"
                           min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
                    <p class="text-xs text-gray-500 mt-1">Cap on discount amount. Leave 0 for no cap.</p>
                </div>
            </div>

            <!-- Preview Box -->
            <div class="mt-4 p-4 bg-gradient-to-r from-green-50 to-yellow-50 border border-green-200 rounded-lg">
                <h4 class="font-semibold text-green-800 mb-2">📢 Offer Preview:</h4>
                <p class="text-green-700" id="offerPreview">
                    @php
                        $percentage = \App\Models\Setting::get('first_time_discount_percentage', 20);
                        $maxCust = \App\Models\Setting::get('first_time_discount_max_customers', 10);
                        $remaining = max(0, $maxCust - $usedCount);
                        $minOrder = \App\Models\Setting::get('first_time_discount_min_order', 0);
                        $maxDiscount = \App\Models\Setting::get('first_time_discount_max_amount', 0);
                    @endphp
                    🎉 First {{ $remaining }} customers get {{ $percentage }}% OFF!
                    @if($minOrder > 0) (Min. order ₹{{ number_format($minOrder, 0) }}) @endif
                    @if($maxDiscount > 0) (Max ₹{{ number_format($maxDiscount, 0) }} off) @endif
                </p>
            </div>
        </div>

    <div class="mt-6">
        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold">
            Save Settings
        </button>
    </div>
</form>
@endsection
