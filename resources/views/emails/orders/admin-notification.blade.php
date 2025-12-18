@extends('emails.layouts.base')

@section('title', 'New Order - #' . $order->order_number)

@section('content')
<h2 class="greeting">🛒 New Order Received!</h2>

<p class="message">
    A new order has been placed on your store. Please review the details below.
</p>

<!-- Order Summary Box -->
<div class="order-box" style="background-color: #ecfdf5; border-color: #6ee7b7;">
    <p class="order-number" style="color: #065f46;">
        Order Number
        <span style="color: #059669;">#{{ $order->order_number }}</span>
    </p>
    <div class="order-details" style="border-color: #6ee7b7;">
        <div class="detail-row">
            <span class="detail-label">Order Date</span>
            <span class="detail-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Order Type</span>
            <span class="detail-value">{{ ucfirst($order->order_type) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Payment Method</span>
            <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Payment Status</span>
            <span class="detail-value">
                <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                    {{ $order->payment_status === 'paid' ? 'background: #dcfce7; color: #166534;' : 'background: #fef3c7; color: #92400e;' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </span>
        </div>
    </div>
</div>

<!-- Customer Details -->
<div style="background-color: #f0f9ff; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #0284c7;">
    <h3 style="font-size: 14px; color: #0369a1; margin-bottom: 15px;">👤 Customer Details</h3>
    <table style="width: 100%; font-size: 14px;">
        <tr>
            <td style="padding: 5px 0; color: #666; width: 30%;">Name:</td>
            <td style="padding: 5px 0; font-weight: 600;">{{ $order->customer_name }}</td>
        </tr>
        <tr>
            <td style="padding: 5px 0; color: #666;">Phone:</td>
            <td style="padding: 5px 0;"><a href="tel:{{ $order->customer_phone }}" style="color: #0369a1;">{{ $order->customer_phone }}</a></td>
        </tr>
        @if($order->customer_email)
        <tr>
            <td style="padding: 5px 0; color: #666;">Email:</td>
            <td style="padding: 5px 0;"><a href="mailto:{{ $order->customer_email }}" style="color: #0369a1;">{{ $order->customer_email }}</a></td>
        </tr>
        @endif
    </table>
</div>

<!-- Shipping Address -->
<div class="address-section">
    <p class="address-title">📦 Shipping Address</p>
    <p class="address-text">
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}
    </p>
</div>

<!-- Order Items -->
<h3 style="font-size: 16px; color: #1a1a1a; margin-bottom: 15px;">📋 Order Items ({{ $order->items->count() }})</h3>
<table class="items-table">
    <thead>
        <tr>
            <th style="width: 40%;">Product</th>
            <th>SKU</th>
            <th style="text-align: center;">Qty</th>
            <th style="text-align: right;">Price</th>
            <th style="text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order->items as $item)
        <tr>
            <td>
                <span class="item-name">{{ $item->product_name }}</span>
                @if($item->variant_name)
                    <br><span class="item-variant">{{ $item->variant_name }}</span>
                @endif
            </td>
            <td style="font-size: 12px; color: #666;">{{ $item->product_sku }}</td>
            <td style="text-align: center; font-weight: 600;">{{ $item->quantity }}</td>
            <td style="text-align: right;">₹{{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align: right; font-weight: 600;">₹{{ number_format($item->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Order Total -->
<div class="total-section" style="background-color: #fef3c7;">
    <div class="total-row">
        <span class="label">Subtotal</span>
        <span class="value">₹{{ number_format($order->subtotal, 2) }}</span>
    </div>
    <div class="total-row">
        <span class="label">GST</span>
        <span class="value">₹{{ number_format($order->gst_amount, 2) }}</span>
    </div>
    <div class="total-row">
        <span class="label">Shipping</span>
        <span class="value">{{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 2) : 'FREE' }}</span>
    </div>
    @if($order->discount_amount > 0)
    <div class="total-row" style="color: #16a34a;">
        <span class="label">Discount</span>
        <span class="value">-₹{{ number_format($order->discount_amount, 2) }}</span>
    </div>
    @endif
    <div class="total-row grand-total" style="color: #b45309;">
        <span class="label">💰 Total Amount</span>
        <span class="value">₹{{ number_format($order->total_amount, 2) }}</span>
    </div>
</div>

@if($order->customer_notes)
<div class="highlight-box">
    <p><strong>📝 Customer Notes:</strong><br>{{ $order->customer_notes }}</p>
</div>
@endif

<!-- Action Button -->
<div style="text-align: center;">
    <a href="{{ url('/admin/orders/' . $order->id) }}" class="btn" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
        View Order in Admin Panel
    </a>
</div>

<p style="font-size: 12px; color: #666; margin-top: 30px; text-align: center;">
    This is an automated notification. Invoice PDF is attached.
</p>
@endsection
