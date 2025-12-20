@extends('layouts.admin')
@section('title', 'Create Page')
@section('page_title', 'Create New Page')

@php
    $templates = [
        'privacy' => [
            'title' => 'Privacy Policy',
            'content' => '<h2>Privacy Policy</h2>
<p>Last updated: ' . date('F d, Y') . '</p>

<h3>1. Information We Collect</h3>
<p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support. This information may include:</p>
<ul>
<li>Name and contact information</li>
<li>Billing and shipping address</li>
<li>Payment information</li>
<li>Order history</li>
</ul>

<h3>2. How We Use Your Information</h3>
<p>We use the information we collect to:</p>
<ul>
<li>Process and fulfill your orders</li>
<li>Send you order confirmations and updates</li>
<li>Respond to your comments and questions</li>
<li>Send promotional communications (with your consent)</li>
</ul>

<h3>3. Information Sharing</h3>
<p>We do not sell, trade, or otherwise transfer your personal information to third parties except as described in this policy.</p>

<h3>4. Data Security</h3>
<p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

<h3>5. Contact Us</h3>
<p>If you have any questions about this Privacy Policy, please contact us.</p>'
        ],
        'terms' => [
            'title' => 'Terms & Conditions',
            'content' => '<h2>Terms & Conditions</h2>
<p>Last updated: ' . date('F d, Y') . '</p>

<h3>1. Agreement to Terms</h3>
<p>By accessing and using this website, you accept and agree to be bound by the terms and provisions of this agreement.</p>

<h3>2. Products and Services</h3>
<p>All products displayed on our website are subject to availability. We reserve the right to limit quantities and discontinue products at any time.</p>

<h3>3. Pricing and Payment</h3>
<p>All prices are listed in Indian Rupees (INR) and are inclusive of applicable taxes unless otherwise stated. We accept various payment methods as displayed during checkout.</p>

<h3>4. Shipping and Delivery</h3>
<p>We aim to process and ship orders within 2-3 business days. Delivery times may vary based on your location.</p>

<h3>5. Returns and Refunds</h3>
<p>Please refer to our Return & Refund Policy for detailed information about returns and refunds.</p>

<h3>6. Limitation of Liability</h3>
<p>We shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of our services.</p>

<h3>7. Changes to Terms</h3>
<p>We reserve the right to modify these terms at any time. Changes will be effective immediately upon posting on the website.</p>'
        ],
        'refund' => [
            'title' => 'Return & Refund Policy',
            'content' => '<h2>Return & Refund Policy</h2>
<p>Last updated: ' . date('F d, Y') . '</p>

<h3>1. Return Eligibility</h3>
<p>You may return products within 7 days of delivery if:</p>
<ul>
<li>The product is damaged during transit</li>
<li>The product is defective</li>
<li>You received a wrong product</li>
</ul>

<h3>2. Non-Returnable Items</h3>
<p>The following items cannot be returned:</p>
<ul>
<li>Opened or used products</li>
<li>Products without original packaging</li>
<li>Perishable goods</li>
</ul>

<h3>3. Return Process</h3>
<p>To initiate a return:</p>
<ol>
<li>Contact our customer service within 7 days of delivery</li>
<li>Provide your order number and reason for return</li>
<li>Pack the product in original packaging</li>
<li>Ship the product back to us</li>
</ol>

<h3>4. Refund Processing</h3>
<p>Once we receive and inspect the returned item, we will process your refund within 5-7 business days. The refund will be credited to your original payment method.</p>

<h3>5. Contact Us</h3>
<p>For any questions regarding returns or refunds, please contact our customer service team.</p>'
        ],
        'shipping' => [
            'title' => 'Shipping Policy',
            'content' => '<h2>Shipping Policy</h2>
<p>Last updated: ' . date('F d, Y') . '</p>

<h3>1. Processing Time</h3>
<p>Orders are processed within 1-2 business days. Orders placed on weekends or holidays will be processed on the next business day.</p>

<h3>2. Shipping Methods</h3>
<p>We offer the following shipping options:</p>
<ul>
<li><strong>Standard Delivery:</strong> 5-7 business days</li>
<li><strong>Express Delivery:</strong> 2-3 business days</li>
</ul>

<h3>3. Shipping Charges</h3>
<p>Free shipping on orders above ₹500. For orders below ₹500, a flat shipping charge of ₹50 applies.</p>

<h3>4. Delivery Areas</h3>
<p>We currently deliver across India. Some remote areas may have extended delivery times.</p>

<h3>5. Order Tracking</h3>
<p>Once your order is shipped, you will receive a tracking number via email/SMS to track your delivery.</p>

<h3>6. Delivery Issues</h3>
<p>If you face any delivery issues, please contact our customer service team immediately.</p>'
        ],
    ];
    
    $template = request('template');
    $templateData = $templates[$template] ?? null;
@endphp

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-plus-circle text-green-500 mr-2"></i>
                    Create New Page
                </h2>
                <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Pages
                </a>
            </div>
        </div>

        <form action="{{ route('admin.pages.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Page Title *</label>
                        <input type="text" name="title" value="{{ old('title', $templateData['title'] ?? '') }}" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="e.g., Privacy Policy">
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Slug</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                /page/
                            </span>
                            <input type="text" name="slug" value="{{ old('slug') }}"
                                   class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="privacy-policy (auto-generated if empty)">
                        </div>
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Page Content *</label>
                    <textarea name="content" rows="15" required
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500 font-mono text-sm"
                              placeholder="Enter page content (HTML supported)">{{ old('content', $templateData['content'] ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">HTML tags are supported for formatting</p>
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-4">SEO Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="SEO title (uses page title if empty)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <input type="text" name="meta_description" value="{{ old('meta_description') }}"
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500"
                                   placeholder="Brief description for search engines">
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Display Settings</h3>
                    <div class="flex flex-wrap gap-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="w-5 h-5 text-green-600 focus:ring-green-500 rounded">
                            <span class="ml-2">
                                <span class="font-medium">Active</span>
                                <span class="text-sm text-gray-500 block">Page is visible on website</span>
                            </span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="show_in_footer" value="1" checked
                                   class="w-5 h-5 text-green-600 focus:ring-green-500 rounded">
                            <span class="ml-2">
                                <span class="font-medium">Show in Footer</span>
                                <span class="text-sm text-gray-500 block">Display link in website footer</span>
                            </span>
                        </label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                                   class="w-20 border border-gray-300 rounded-lg px-3 py-2">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.pages.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg">
                    Cancel
                </a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-save"></i> Create Page
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
