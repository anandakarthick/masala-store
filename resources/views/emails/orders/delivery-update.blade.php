<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Update</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .header { background: linear-gradient(135deg, #F97316, #EA580C); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .header .icon { font-size: 48px; margin-bottom: 10px; }
        .content { padding: 30px; }
        .delivery-card { background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #F97316; }
        .delivery-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
        .delivery-item:last-child { border-bottom: none; }
        .delivery-label { color: #6b7280; }
        .delivery-value { font-weight: 600; color: #1f2937; }
        .attachments { background: #FEF3C7; border-radius: 8px; padding: 15px; margin: 20px 0; }
        .attachments h3 { margin: 0 0 10px 0; color: #92400E; font-size: 16px; }
        .attachment-link { display: inline-block; background: #F97316; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; margin: 5px 5px 5px 0; font-size: 14px; }
        .attachment-link:hover { background: #EA580C; }
        .notes { background: #EFF6FF; border-radius: 8px; padding: 15px; margin: 20px 0; }
        .notes h3 { margin: 0 0 10px 0; color: #1E40AF; font-size: 16px; }
        .track-button { display: inline-block; background: #F97316; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 20px 0; }
        .track-button:hover { background: #EA580C; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🚚</div>
            <h1>Delivery Update</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Order #{{ $order->order_number }}</p>
        </div>
        
        <div class="content">
            <p>Hi {{ $order->customer_name }},</p>
            
            <p>Great news! Your order delivery details have been updated. Here's the latest information:</p>
            
            <div class="delivery-card">
                @if($order->delivery_partner)
                <div class="delivery-item">
                    <span class="delivery-label">Delivery Partner</span>
                    <span class="delivery-value">{{ $order->delivery_partner }}</span>
                </div>
                @endif
                
                @if($order->tracking_number)
                <div class="delivery-item">
                    <span class="delivery-label">Tracking Number</span>
                    <span class="delivery-value">{{ $order->tracking_number }}</span>
                </div>
                @endif
                
                @if($order->expected_delivery_date)
                <div class="delivery-item">
                    <span class="delivery-label">Expected Delivery</span>
                    <span class="delivery-value">{{ $order->expected_delivery_date->format('d M Y') }}</span>
                </div>
                @endif
                
                <div class="delivery-item">
                    <span class="delivery-label">Shipping To</span>
                    <span class="delivery-value">{{ $order->shipping_city }}, {{ $order->shipping_state }}</span>
                </div>
            </div>
            
            @if($order->hasDeliveryAttachments())
            <div class="attachments">
                <h3>📎 Attachments</h3>
                <p style="margin: 0 0 10px 0; color: #92400E; font-size: 14px;">The following documents have been attached to your order:</p>
                @foreach($order->getDeliveryAttachmentUrls() as $index => $url)
                    <a href="{{ $url }}" target="_blank" class="attachment-link">
                        📄 Attachment {{ $index + 1 }}
                    </a>
                @endforeach
            </div>
            @endif
            
            @if($order->delivery_notes)
            <div class="notes">
                <h3>📝 Delivery Notes</h3>
                <p style="margin: 0; color: #1E40AF;">{{ $order->delivery_notes }}</p>
            </div>
            @endif
            
            <center>
                <a href="{{ config('app.url') }}/track-order?order={{ $order->order_number }}&phone={{ $order->customer_phone }}" class="track-button">
                    Track Your Order
                </a>
            </center>
            
            <p style="margin-top: 30px;">If you have any questions, feel free to contact our support team.</p>
            
            <p>Thank you for shopping with us!</p>
        </div>
        
        <div class="footer">
            <p>{{ config('app.name') }}</p>
            <p style="font-size: 12px;">This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
