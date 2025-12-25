<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cancelled</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #EF4444, #DC2626); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header .icon { font-size: 48px; margin-bottom: 10px; }
        .content { padding: 30px; }
        .order-card { background: #FEF2F2; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #EF4444; }
        .order-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #FECACA; }
        .order-item:last-child { border-bottom: none; }
        .order-label { color: #6b7280; }
        .order-value { font-weight: 600; color: #1f2937; }
        .reason-box { background: #FEF3C7; border-radius: 8px; padding: 15px; margin: 20px 0; }
        .reason-box h3 { margin: 0 0 10px 0; color: #92400E; font-size: 16px; }
        .refund-box { background: #D1FAE5; border-radius: 8px; padding: 15px; margin: 20px 0; }
        .refund-box h3 { margin: 0 0 10px 0; color: #065F46; font-size: 16px; }
        .shop-button { display: inline-block; background: #F97316; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 20px 0; }
        .shop-button:hover { background: #EA580C; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">❌</div>
            <h1>Order Cancelled</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Order #{{ $order->order_number }}</p>
        </div>
        
        <div class="content">
            <p>Hi {{ $order->customer_name }},</p>
            
            @if($cancelledBy === 'customer')
            <p>Your order has been cancelled as per your request. We're sorry to see you cancel, but we understand that sometimes plans change.</p>
            @else
            <p>We regret to inform you that your order has been cancelled. We apologize for any inconvenience this may cause.</p>
            @endif
            
            <div class="order-card">
                <div class="order-item">
                    <span class="order-label">Order Number</span>
                    <span class="order-value">#{{ $order->order_number }}</span>
                </div>
                <div class="order-item">
                    <span class="order-label">Order Date</span>
                    <span class="order-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="order-item">
                    <span class="order-label">Total Amount</span>
                    <span class="order-value">₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="order-item">
                    <span class="order-label">Cancelled On</span>
                    <span class="order-value">{{ $order->cancelled_at ? $order->cancelled_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</span>
                </div>
            </div>
            
            @if($reason)
            <div class="reason-box">
                <h3>📝 Cancellation Reason</h3>
                <p style="margin: 0; color: #92400E;">{{ $reason }}</p>
            </div>
            @endif
            
            @if($order->wallet_amount_used > 0)
            <div class="refund-box">
                <h3>💰 Wallet Refund</h3>
                <p style="margin: 0; color: #065F46;">
                    ₹{{ number_format($order->wallet_amount_used, 2) }} has been refunded to your wallet. 
                    You can use this balance for your next purchase.
                </p>
            </div>
            @endif
            
            @if($order->payment_status === 'paid' && $order->payment_method !== 'cod')
            <div class="refund-box">
                <h3>💳 Payment Refund</h3>
                <p style="margin: 0; color: #065F46;">
                    Your payment of ₹{{ number_format($order->total_amount - $order->wallet_amount_used, 2) }} will be refunded to your original payment method within 5-7 business days.
                </p>
            </div>
            @endif
            
            <p>We hope to serve you again soon. Feel free to explore our latest products and offers!</p>
            
            <center>
                <a href="{{ config('app.url') }}" class="shop-button">
                    Continue Shopping
                </a>
            </center>
            
            <p style="margin-top: 30px;">If you have any questions or concerns, please don't hesitate to contact our support team.</p>
        </div>
        
        <div class="footer">
            <p>{{ config('app.name') }}</p>
            <p style="font-size: 12px;">This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
