@extends('emails.layouts.base')

@section('title', 'Order Status Update - #' . $order->order_number)

@section('content')
@php
    $statusInfo = [
        'pending' => ['icon' => '⏳', 'color' => '#f59e0b', 'message' => 'Your order is pending confirmation.'],
        'confirmed' => ['icon' => '✅', 'color' => '#10b981', 'message' => 'Great news! Your order has been confirmed and will be processed soon.'],
        'processing' => ['icon' => '⚙️', 'color' => '#3b82f6', 'message' => 'Your order is being prepared with care.'],
        'packed' => ['icon' => '📦', 'color' => '#8b5cf6', 'message' => 'Your order has been packed and is ready for shipping.'],
        'shipped' => ['icon' => '🚚', 'color' => '#06b6d4', 'message' => 'Exciting! Your order is on its way to you.'],
        'delivered' => ['icon' => '🎉', 'color' => '#22c55e', 'message' => 'Your order has been delivered. Enjoy your products!'],
        'cancelled' => ['icon' => '❌', 'color' => '#ef4444', 'message' => 'Your order has been cancelled.'],
        'returned' => ['icon' => '↩️', 'color' => '#6b7280', 'message' => 'Your order return has been processed.'],
    ];
    $status = $statusInfo[$newStatus] ?? ['icon' => '📋', 'color' => '#6b7280', 'message' => 'Your order status has been updated.'];
@endphp

<div style="text-align: center; margin-bottom: 30px;">
    <span style="font-size: 60px;">{{ $status['icon'] }}</span>
</div>

<h2 class="greeting" style="text-align: center;">Order Status Updated</h2>

<p class="message" style="text-align: center;">
    Hi {{ $order->customer_name }},<br><br>
    {{ $status['message'] }}
</p>

<!-- Status Change Visual -->
<div style="text-align: center; margin: 30px 0;">
    <div style="display: inline-block; padding: 10px 20px; background: #f3f4f6; border-radius: 20px; font-size: 14px;">
        <span style="color: #9ca3af; text-decoration: line-through;">{{ ucfirst($oldStatus) }}</span>
        <span style="margin: 0 10px;">→</span>
        <span style="color: {{ $status['color'] }}; font-weight: bold;">{{ ucfirst($newStatus) }}</span>
    </div>
</div>

<!-- Order Summary Box -->
<div class="order-box">
    <p class="order-number">
        Order Number
        <span>#{{ $order->order_number }}</span>
    </p>
    <div class="order-details">
        <div class="detail-row">
            <span class="detail-label">Order Date</span>
            <span class="detail-value">{{ $order->created_at->format('d M Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Total Amount</span>
            <span class="detail-value">₹{{ number_format($order->total_amount, 2) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Items</span>
            <span class="detail-value">{{ $order->items->count() }} products</span>
        </div>
    </div>
</div>

@if($newStatus === 'shipped' && $order->tracking_number)
<!-- Tracking Information -->
<div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
    <p style="font-size: 14px; color: #1e40af; margin-bottom: 10px;">🚚 Tracking Information</p>
    <p style="font-size: 12px; color: #3b82f6; margin-bottom: 5px;">Delivery Partner: {{ $order->delivery_partner }}</p>
    <p style="font-size: 18px; font-weight: bold; color: #1e40af; letter-spacing: 2px;">{{ $order->tracking_number }}</p>
    @if($order->expected_delivery_date)
        <p style="font-size: 14px; color: #3b82f6; margin-top: 10px;">
            Expected Delivery: {{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('d M Y') }}
        </p>
    @endif
</div>
@endif

@if($newStatus === 'delivered')
<div class="highlight-box" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border-color: #22c55e;">
    <p style="color: #166534;">
        <strong>Thank you for shopping with us!</strong><br>
        We hope you love your products. If you have any feedback or concerns, please don't hesitate to reach out to us.
    </p>
</div>
@endif

@if($newStatus === 'cancelled')
<div class="highlight-box" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-color: #ef4444;">
    <p style="color: #991b1b;">
        We're sorry your order was cancelled. If you have any questions or would like to place a new order, please contact us.
        @if($order->payment_status === 'paid')
            <br><br><strong>Refund:</strong> If payment was made, your refund will be processed within 5-7 business days.
        @endif
    </p>
</div>
@endif

<!-- Track Order Button -->
<div style="text-align: center;">
    <a href="{{ url('/tracking?order=' . $order->order_number) }}" class="btn">
        Track Your Order
    </a>
</div>

<!-- Order Items Summary -->
<h3 style="font-size: 14px; color: #666; margin: 30px 0 15px;">Order Items</h3>
<table style="width: 100%; font-size: 13px; border-collapse: collapse;">
    @foreach($order->items as $item)
    <tr style="border-bottom: 1px solid #eee;">
        <td style="padding: 10px 0;">
            {{ $item->product_name }}
            @if($item->variant_name)
                <span style="color: #ea580c;">({{ $item->variant_name }})</span>
            @endif
        </td>
        <td style="padding: 10px 0; text-align: right; color: #666;">x{{ $item->quantity }}</td>
    </tr>
    @endforeach
</table>

<p class="message" style="margin-top: 30px; font-size: 14px;">
    Questions? Contact us at 
    <a href="mailto:{{ \App\Models\Setting::get('business_email') }}" style="color: #ea580c;">{{ \App\Models\Setting::get('business_email') }}</a> 
    or call <a href="tel:{{ \App\Models\Setting::get('business_phone') }}" style="color: #ea580c;">{{ \App\Models\Setting::get('business_phone') }}</a>.
</p>
@endsection
