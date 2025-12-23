@extends('emails.layouts.base')

@section('title', 'Share Your Experience - Order #' . $order->order_number)

@section('content')
<div style="text-align: center; margin-bottom: 30px;">
    <span style="font-size: 60px;">⭐</span>
</div>

<h2 class="greeting" style="text-align: center;">How was your experience?</h2>

<p class="message" style="text-align: center;">
    Hi {{ $order->customer_name }},<br><br>
    Your order <strong>#{{ $order->order_number }}</strong> has been delivered! 
    We hope you're enjoying your products. We'd love to hear your feedback!
</p>

<!-- Products to Review -->
<div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 20px; border-radius: 8px; margin: 25px 0;">
    <p style="font-size: 14px; color: #92400e; margin-bottom: 15px; font-weight: 600;">
        📦 Products from your order:
    </p>
    @foreach($order->items as $item)
        <div style="display: flex; align-items: center; padding: 10px 0; border-bottom: 1px dashed rgba(146, 64, 14, 0.3);">
            <div style="flex: 1;">
                <p style="margin: 0; color: #1a1a1a; font-weight: 500;">{{ $item->product_name }}</p>
                @if($item->variant_name)
                    <p style="margin: 5px 0 0; font-size: 12px; color: #666;">{{ $item->variant_name }}</p>
                @endif
            </div>
            <div style="color: #92400e; font-size: 20px;">
                ☆☆☆☆☆
            </div>
        </div>
    @endforeach
</div>

<!-- Why Review Matters -->
<div class="highlight-box" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border-color: #22c55e;">
    <p style="color: #166534; margin: 0;">
        <strong>Why your review matters:</strong><br>
        ✓ Help other customers make informed decisions<br>
        ✓ Share your honest experience with our products<br>
        ✓ Help us improve our products and services
    </p>
</div>

<!-- CTA Button -->
<div style="text-align: center; margin: 30px 0;">
    @if($order->user_id)
        <a href="{{ url('/account/orders/' . $order->id . '/review') }}" class="btn" style="font-size: 16px; padding: 16px 40px;">
            ⭐ Write Your Review
        </a>
    @else
        <a href="{{ url('/review/' . $order->review_token) }}" class="btn" style="font-size: 16px; padding: 16px 40px;">
            ⭐ Write Your Review
        </a>
    @endif
</div>

<!-- Order Summary -->
<div class="order-box">
    <p class="order-number">
        Order Summary
        <span>#{{ $order->order_number }}</span>
    </p>
    <div class="order-details">
        <div class="detail-row">
            <span class="detail-label">Order Date</span>
            <span class="detail-value">{{ $order->created_at->format('d M Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Delivered On</span>
            <span class="detail-value">{{ $order->delivered_at ? \Carbon\Carbon::parse($order->delivered_at)->format('d M Y') : 'Recently' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Total Items</span>
            <span class="detail-value">{{ $order->items->count() }} products</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Order Total</span>
            <span class="detail-value">₹{{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>
</div>

<!-- Alternative Link -->
<p style="text-align: center; font-size: 13px; color: #666; margin-top: 25px;">
    Having trouble with the button? Copy and paste this link in your browser:<br>
    @if($order->user_id)
        <a href="{{ url('/account/orders/' . $order->id . '/review') }}" style="color: #ea580c; word-break: break-all;">
            {{ url('/account/orders/' . $order->id . '/review') }}
        </a>
    @else
        <a href="{{ url('/review/' . $order->review_token) }}" style="color: #ea580c; word-break: break-all;">
            {{ url('/review/' . $order->review_token) }}
        </a>
    @endif
</p>

<p class="message" style="margin-top: 30px; font-size: 14px; text-align: center;">
    Thank you for shopping with us! 🙏<br>
    Your feedback helps us serve you better.
</p>
@endsection
