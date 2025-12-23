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
    </div>

    <!-- Referral Program Section -->
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">
                    <i class="fas fa-users text-blue-600 mr-2"></i>Referral Program
                </h3>
                @php
                    $totalReferrals = \App\Models\Referral::count();
                    $completedReferrals = \App\Models\Referral::completed()->count();
                    $totalRewards = \App\Models\WalletTransaction::where('source', 'referral')->sum('amount');
                @endphp
                <div class="flex gap-2">
                    <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-700">
                        {{ $completedReferrals }}/{{ $totalReferrals }} completed
                    </span>
                    <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">
                        ₹{{ number_format($totalRewards, 0) }} rewarded
                    </span>
                </div>
            </div>
            
            <p class="text-sm text-gray-600 mb-4">
                Allow customers to refer friends and earn wallet rewards when their referrals place orders.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Enable/Disable -->
                <div class="col-span-full">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="referral_enabled" value="1" 
                               {{ \App\Models\Setting::get('referral_enabled') ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm font-medium text-gray-700">Enable Referral Program</span>
                    </label>
                </div>

                <!-- Reward Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Reward Type <span class="text-red-500">*</span>
                    </label>
                    <select name="referral_reward_type" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="fixed" {{ \App\Models\Setting::get('referral_reward_type', 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage" {{ \App\Models\Setting::get('referral_reward_type') === 'percentage' ? 'selected' : '' }}>Percentage of Order</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">How to calculate the reward</p>
                </div>

                <!-- Reward Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Reward Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="referral_reward_amount" 
                               value="{{ \App\Models\Setting::get('referral_reward_amount', 50) }}"
                               min="1" step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">₹ or % based on reward type</p>
                </div>

                <!-- Max Reward Amount (for percentage type) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Maximum Reward (₹)
                    </label>
                    <input type="number" name="referral_max_reward_amount" 
                           value="{{ \App\Models\Setting::get('referral_max_reward_amount', 500) }}"
                           min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Cap on reward (for % type). 0 = no cap</p>
                </div>

                <!-- Minimum Order Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Min. Order Amount (₹)
                    </label>
                    <input type="number" name="referral_min_order_amount" 
                           value="{{ \App\Models\Setting::get('referral_min_order_amount', 0) }}"
                           min="0" step="0.01"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Min order for referral to count. 0 = no min</p>
                </div>

                <!-- First Order Only -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Reward Trigger
                    </label>
                    <select name="referral_first_order_only" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ \App\Models\Setting::get('referral_first_order_only', '1') == '1' ? 'selected' : '' }}>First Order Only</option>
                        <option value="0" {{ \App\Models\Setting::get('referral_first_order_only', '1') == '0' ? 'selected' : '' }}>Every Order</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">When to give rewards</p>
                </div>

                <!-- Max Rewards Per Referral (for every order) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Max Rewards Per Referral
                    </label>
                    <input type="number" name="referral_max_rewards_per_referral" 
                           value="{{ \App\Models\Setting::get('referral_max_rewards_per_referral', 1) }}"
                           min="0" max="100"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">0 = unlimited (use for "every order")</p>
                </div>
            </div>

            <!-- Preview Box -->
            <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg">
                <h4 class="font-semibold text-blue-800 mb-2">📢 Program Preview:</h4>
                @php
                    $refRewardType = \App\Models\Setting::get('referral_reward_type', 'fixed');
                    $refRewardAmount = \App\Models\Setting::get('referral_reward_amount', 50);
                    $refFirstOnly = \App\Models\Setting::get('referral_first_order_only', '1') == '1';
                    $refMinOrder = \App\Models\Setting::get('referral_min_order_amount', 0);
                @endphp
                <p class="text-blue-700">
                    🎁 Refer a friend and earn 
                    <strong>{{ $refRewardType === 'percentage' ? $refRewardAmount . '%' : '₹' . number_format($refRewardAmount, 0) }}</strong>
                    when they place {{ $refFirstOnly ? 'their first order' : 'an order' }}!
                    @if($refMinOrder > 0) (Min. order ₹{{ number_format($refMinOrder, 0) }}) @endif
                </p>
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
