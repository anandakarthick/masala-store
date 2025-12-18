@extends('emails.layouts.base')

@section('title', 'Order Confirmation - #' . $order->order_number)

@section('content')
<h2 class="greeting">Thank You for Your Order! 🎉</h2>

<p class="message">
    Hi {{ $order->customer_name }},<br><br>
    Great news! We've received your order and it's being processed. 
    You'll receive another email once your order is shipped.
</p>

<!-- Order Summary Box -->
<div class="order-box">
    <p class="order-number">
        Order Number
        <span>#{{ $order->order_number }}</span>
    </p>
    <div class="order-details">
        <div class="detail-row">
            <span class="detail-label">Order Date</span>
            <span class="detail-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Payment Method</span>
            <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Order Status</span>
            <span class="detail-value">
                <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </span>
        </div>
    </div>
</div>

<!-- Order Items -->
<h3 style="font-size: 16px; color: #1a1a1a; margin-bottom: 15px;">Order Items</h3>
<table class="items-table">
    <thead>
        <tr>
            <th style="width: 50%;">Product</th>
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
            <td style="text-align: center;">{{ $item->quantity }}</td>
            <td style="text-align: right;">₹{{ number_format($item->unit_price, 2) }}</td>
            <td style="text-align: right;">₹{{ number_format($item->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Order Total -->
<div class="total-section">
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
    <div class="total-row grand-total">
        <span class="label">Total Amount</span>
        <span class="value">₹{{ number_format($order->total_amount, 2) }}</span>
    </div>
</div>

<!-- Shipping Address -->
<div class="address-section">
    <p class="address-title">📦 Shipping Address</p>
    <p class="address-text">
        {{ $order->customer_name }}<br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
        📞 {{ $order->customer_phone }}
        @if($order->customer_email)
            <br>✉️ {{ $order->customer_email }}
        @endif
    </p>
</div>

@if($order->payment_method === 'cod')
<div class="highlight-box">
    <p><strong>💰 Cash on Delivery:</strong> Please keep ₹{{ number_format($order->total_amount, 2) }} ready at the time of delivery.</p>
</div>
@endif

<!-- Track Order Button -->
<div style="text-align: center;">
    <a href="{{ url('/tracking?order=' . $order->order_number) }}" class="btn">
        Track Your Order
    </a>
</div>

<p class="message" style="margin-top: 30px; font-size: 14px;">
    If you have any questions about your order, please don't hesitate to contact us at 
    <a href="mailto:{{ \App\Models\Setting::get('business_email') }}" style="color: #166534;">{{ \App\Models\Setting::get('business_email') }}</a> 
    or call us at <a href="tel:{{ \App\Models\Setting::get('business_phone') }}" style="color: #166534;">{{ \App\Models\Setting::get('business_phone') }}</a>.
</p>

<p style="font-size: 14px; color: #666; margin-top: 20px;">
    Thank you for shopping with us! 🙏
</p>
@endsection
