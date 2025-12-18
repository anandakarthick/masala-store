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

    <div class="mt-6">
        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg font-semibold">
            Save Settings
        </button>
    </div>
</form>
@endsection
