<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #166534 0%, #15803d 100%);
            padding: 30px;
            text-align: center;
        }
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .company-name {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .tagline {
            color: rgba(255,255,255,0.9);
            font-size: 14px;
            margin-top: 5px;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #4a4a4a;
            margin-bottom: 25px;
        }
        .order-box {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .order-number {
            font-size: 14px;
            color: #166534;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .order-number span {
            font-size: 20px;
            color: #15803d;
            display: block;
            margin-top: 5px;
        }
        .order-details {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #86efac;
        }
        .detail-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            font-size: 14px;
        }
        .detail-label {
            display: table-cell;
            color: #78716c;
            width: 40%;
        }
        .detail-value {
            display: table-cell;
            color: #1a1a1a;
            font-weight: 500;
            text-align: right;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .items-table th {
            background-color: #f8f8f8;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #eee;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        .items-table .item-name {
            font-weight: 500;
            color: #1a1a1a;
        }
        .items-table .item-variant {
            font-size: 12px;
            color: #15803d;
        }
        .total-section {
            background-color: #fafafa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .total-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            font-size: 14px;
        }
        .total-row .label {
            display: table-cell;
            width: 70%;
        }
        .total-row .value {
            display: table-cell;
            text-align: right;
            font-weight: 500;
        }
        .total-row.grand-total {
            border-top: 2px solid #eee;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #166534;
        }
        .address-section {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .address-title {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .address-text {
            font-size: 14px;
            color: #4a4a4a;
            line-height: 1.8;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-confirmed { background-color: #dcfce7; color: #166534; }
        .status-processing { background-color: #fef3c7; color: #92400e; }
        .status-shipped { background-color: #dbeafe; color: #1e40af; }
        .status-delivered { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        .btn {
            display: inline-block;
            padding: 14px 30px;
            background: linear-gradient(135deg, #166534 0%, #15803d 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            margin: 20px 0;
        }
        .email-footer {
            background-color: #1a1a1a;
            padding: 30px;
            text-align: center;
        }
        .footer-logo {
            color: #22c55e;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .footer-text {
            color: #9ca3af;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .footer-links a {
            color: #9ca3af;
            text-decoration: none;
            margin: 0 10px;
            font-size: 13px;
        }
        .copyright {
            color: #6b7280;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }
        .highlight-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .highlight-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            @php
                $logo = \App\Models\Setting::logo();
                $companyName = \App\Models\Setting::get('business_name', 'SV Masala & Herbal Products');
                $tagline = \App\Models\Setting::get('business_tagline', 'Premium Masala, Oils & Herbal Products');
            @endphp
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $companyName }}" class="logo">
            @endif
            <h1 class="company-name">🌿 {{ $companyName }}</h1>
            <p class="tagline">{{ $tagline }}</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            @yield('content')
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-logo">🌿 {{ $companyName }}</div>
            <p class="footer-text">
                {{ \App\Models\Setting::get('business_address', '') }}
            </p>
            <div class="footer-links">
                <a href="{{ url('/') }}">Shop</a> |
                <a href="{{ url('/about') }}">About Us</a> |
                <a href="{{ url('/contact') }}">Contact</a> |
                <a href="{{ url('/tracking') }}">Track Order</a>
            </div>
            <p class="footer-text">
                📞 {{ \App\Models\Setting::get('business_phone', '') }} | 
                ✉️ {{ \App\Models\Setting::get('business_email', '') }}
            </p>
            <p class="copyright">
                © {{ date('Y') }} {{ $companyName }}. All rights reserved.<br>
                This email was sent because you placed an order with us.
            </p>
        </div>
    </div>
</body>
</html>
