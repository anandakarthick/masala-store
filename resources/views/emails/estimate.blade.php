<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate from {{ $company['name'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #166534 0%, #15803d 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .estimate-box {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .estimate-number {
            font-size: 18px;
            font-weight: bold;
            color: #166534;
        }
        .estimate-details {
            margin-top: 15px;
        }
        .estimate-details table {
            width: 100%;
        }
        .estimate-details td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .estimate-details .label {
            color: #666;
            width: 40%;
        }
        .estimate-details .value {
            font-weight: 600;
            text-align: right;
        }
        .total-row td {
            border-top: 2px solid #166534;
            font-size: 18px;
            padding-top: 15px;
        }
        .total-row .value {
            color: #166534;
        }
        .message-box {
            background: #f0fdf4;
            border-left: 4px solid #166534;
            padding: 15px;
            margin: 20px 0;
        }
        .cta-button {
            display: inline-block;
            background: #166534;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 10px 10px;
        }
        .valid-banner {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $company['name'] }}</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Estimate for Your Review</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $estimate->customer_name }}</strong>,</p>

        <p>Thank you for your interest in our products. Please find below the estimate as per your requirements.</p>

        @if($customMessage)
            <div class="message-box">
                <strong>Message from us:</strong><br>
                {{ $customMessage }}
            </div>
        @endif

        @if($estimate->valid_until)
            <div class="valid-banner">
                ⏰ <strong>This estimate is valid until {{ $estimate->valid_until->format('d M Y') }}</strong>
            </div>
        @endif

        <div class="estimate-box">
            <div class="estimate-number">Estimate #{{ $estimate->estimate_number }}</div>
            <p style="color: #666; margin: 5px 0;">Date: {{ $estimate->estimate_date->format('d M Y') }}</p>

            @if($estimate->subject)
                <p style="margin-top: 15px;"><strong>Subject:</strong> {{ $estimate->subject }}</p>
            @endif

            <div class="estimate-details">
                <table>
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="value">₹{{ number_format($estimate->subtotal, 2) }}</td>
                    </tr>
                    @if($estimate->discount_amount > 0)
                    <tr>
                        <td class="label">Discount:</td>
                        <td class="value" style="color: #dc2626;">-₹{{ number_format($estimate->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($estimate->gst_amount > 0)
                    <tr>
                        <td class="label">GST:</td>
                        <td class="value">₹{{ number_format($estimate->gst_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($estimate->shipping_charge > 0)
                    <tr>
                        <td class="label">Shipping:</td>
                        <td class="value">₹{{ number_format($estimate->shipping_charge, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="label"><strong>Total Amount:</strong></td>
                        <td class="value"><strong>₹{{ number_format($estimate->total_amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <p>The detailed estimate is attached as a PDF document for your reference.</p>

        @if($estimate->notes)
            <p><strong>Notes:</strong><br>{{ $estimate->notes }}</p>
        @endif

        <p>If you have any questions or would like to proceed with this estimate, please don't hesitate to contact us.</p>

        <p>
            Best regards,<br>
            <strong>{{ $company['name'] }}</strong>
        </p>
    </div>

    <div class="footer">
        <p>
            {{ $company['address'] }}<br>
            @if($company['phone'])Phone: {{ $company['phone'] }} | @endif
            @if($company['email'])Email: {{ $company['email'] }}@endif
        </p>
        <p style="margin-top: 10px;">
            This email was sent from {{ $company['name'] }}
        </p>
    </div>
</body>
</html>
