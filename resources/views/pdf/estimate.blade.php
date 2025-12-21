<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estimate #{{ $estimate->estimate_number }}</title>
    <style>
        @page {
            margin: 10mm 12mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.3;
        }
        table {
            border-collapse: collapse;
        }
        
        /* Header */
        .header {
            width: 100%;
            border-bottom: 3px solid #166534;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header-table {
            width: 100%;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo-cell {
            width: 55%;
        }
        .estimate-cell {
            width: 45%;
            text-align: right;
        }
        .logo-img {
            max-height: 55px;
            max-width: 160px;
        }
        .company-details {
            font-size: 10px;
            color: #555;
            line-height: 1.6;
            margin-top: 8px;
        }
        .estimate-title {
            font-size: 26px;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
        }
        .estimate-meta {
            font-size: 12px;
            color: #333;
            line-height: 1.8;
            margin-top: 8px;
        }
        .estimate-meta strong {
            color: #000;
        }
        
        /* Info Boxes */
        .info-section {
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            vertical-align: top;
        }
        .info-box {
            width: 50%;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
        }
        .info-box-title {
            font-size: 10px;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 2px solid #166534;
        }
        .info-content {
            font-size: 11px;
            line-height: 1.6;
        }
        .info-content strong {
            font-size: 12px;
            color: #000;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            margin-bottom: 10px;
        }
        .items-table th {
            background-color: #166534;
            color: #fff;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .items-table th.center {
            text-align: center;
        }
        .items-table th.right {
            text-align: right;
        }
        .items-table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
            vertical-align: middle;
        }
        .items-table td.center {
            text-align: center;
        }
        .items-table td.right {
            text-align: right;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .item-name {
            font-weight: 600;
            color: #000;
        }
        .item-variant {
            font-size: 10px;
            color: #166534;
        }
        .item-sku {
            font-size: 9px;
            color: #888;
        }
        
        /* Bottom Section */
        .bottom-section {
            margin-top: 10px;
        }
        .bottom-table {
            width: 100%;
        }
        .bottom-table td {
            vertical-align: top;
        }
        .bottom-left {
            width: 50%;
            padding-right: 15px;
        }
        .bottom-right {
            width: 50%;
        }
        
        /* Totals */
        .totals-table {
            width: 100%;
            border: 1px solid #ddd;
        }
        .totals-table td {
            padding: 6px 10px;
            font-size: 11px;
            border-bottom: 1px solid #eee;
        }
        .totals-table .label {
            text-align: right;
            color: #555;
            width: 60%;
        }
        .totals-table .value {
            text-align: right;
            font-weight: 600;
            width: 40%;
            color: #000;
        }
        .totals-table .grand-total td {
            background-color: #166534;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 10px;
            border: none;
        }
        
        /* Notes */
        .notes-box {
            font-size: 10px;
            color: #555;
            margin-bottom: 10px;
            padding: 8px;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
        }
        .notes-box strong {
            color: #333;
        }
        
        /* Terms */
        .terms-box {
            border: 1px solid #ddd;
            padding: 8px 10px;
            font-size: 9px;
            color: #666;
            line-height: 1.6;
            background: #fafafa;
        }
        .terms-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            font-size: 10px;
        }
        
        /* Signature */
        .signature-box {
            text-align: right;
            margin-top: 25px;
            padding-top: 20px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 150px;
            margin-left: auto;
            padding-top: 5px;
            font-size: 10px;
            color: #555;
            text-align: center;
        }
        
        /* Valid Until Banner */
        .valid-banner {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 8px 12px;
            margin-bottom: 12px;
            font-size: 11px;
            text-align: center;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            font-size: 12px;
            color: #166534;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="logo-cell">
                    @if(!empty($company['logo']))
                        <img src="{{ $company['logo'] }}" alt="Logo" class="logo-img">
                    @else
                        <div style="font-size: 20px; font-weight: bold; color: #166534;">{{ $company['name'] }}</div>
                    @endif
                    <div class="company-details">
                        @if($company['address']){{ $company['address'] }}<br>@endif
                        @if($company['phone'])Phone: {{ $company['phone'] }}@endif
                        @if($company['email']) | Email: {{ $company['email'] }}@endif
                        @if($company['gst'])<br>GSTIN: {{ $company['gst'] }}@endif
                    </div>
                </td>
                <td class="estimate-cell">
                    <div class="estimate-title">ESTIMATE</div>
                    <div class="estimate-meta">
                        <strong>Estimate No:</strong> {{ $estimate->estimate_number }}<br>
                        <strong>Date:</strong> {{ $estimate->estimate_date->format('d/m/Y') }}
                        @if($estimate->valid_until)
                            <br><strong>Valid Until:</strong> {{ $estimate->valid_until->format('d/m/Y') }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Valid Until Banner -->
    @if($estimate->valid_until)
        <div class="valid-banner">
            <strong>⏰ This estimate is valid until {{ $estimate->valid_until->format('d M Y') }}</strong>
        </div>
    @endif

    <!-- Customer Info -->
    <div class="info-section">
        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-box">
                    <div class="info-box-title">Estimate For</div>
                    <div class="info-content">
                        <strong>{{ $estimate->customer_name }}</strong><br>
                        @if($estimate->customer_address)
                            {{ $estimate->customer_address }}<br>
                        @endif
                        @if($estimate->customer_city)
                            {{ $estimate->customer_city }}, {{ $estimate->customer_state }}
                            @if($estimate->customer_pincode) - {{ $estimate->customer_pincode }}@endif<br>
                        @endif
                        Phone: {{ $estimate->customer_phone }}
                        @if($estimate->customer_email)<br>Email: {{ $estimate->customer_email }}@endif
                    </div>
                </td>
                <td class="info-box">
                    <div class="info-box-title">Estimate Details</div>
                    <div class="info-content">
                        @if($estimate->subject)
                            <strong>Subject:</strong> {{ $estimate->subject }}<br>
                        @endif
                        <strong>Estimate No:</strong> {{ $estimate->estimate_number }}<br>
                        <strong>Date:</strong> {{ $estimate->estimate_date->format('d/m/Y') }}<br>
                        <strong>Status:</strong> {{ ucfirst($estimate->status) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%;" class="center">#</th>
                <th style="width: 40%;">Description</th>
                <th style="width: 10%;" class="center">Qty</th>
                <th style="width: 15%;" class="right">Rate (₹)</th>
                <th style="width: 12%;" class="right">GST</th>
                <th style="width: 18%;" class="right">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estimate->items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>
                    <span class="item-name">{{ $item->product_name }}</span>
                    @if($item->variant_name)
                        <br><span class="item-variant">{{ $item->variant_name }}</span>
                    @endif
                    @if($item->product_sku)
                        <br><span class="item-sku">SKU: {{ $item->product_sku }}</span>
                    @endif
                    @if($item->description)
                        <br><span style="font-size: 9px; color: #666;">{{ $item->description }}</span>
                    @endif
                </td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="right">
                    @if($item->gst_percent > 0)
                        {{ $item->gst_percent }}%
                    @else
                        -
                    @endif
                </td>
                <td class="right">{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bottom Section -->
    <div class="bottom-section">
        <table class="bottom-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bottom-left">
                    @if($estimate->notes)
                        <div class="notes-box">
                            <strong>Notes:</strong><br>
                            {!! nl2br(e($estimate->notes)) !!}
                        </div>
                    @endif
                    
                    @if($estimate->terms)
                        <div class="terms-box">
                            <div class="terms-title">Terms & Conditions:</div>
                            {!! nl2br(e($estimate->terms)) !!}
                        </div>
                    @endif
                </td>
                <td class="bottom-right">
                    <!-- Totals -->
                    <table class="totals-table" cellpadding="0" cellspacing="0">
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
                        <tr class="grand-total">
                            <td class="label">Total Amount:</td>
                            <td class="value">₹{{ number_format($estimate->total_amount, 2) }}</td>
                        </tr>
                    </table>
                    
                    <!-- Signature -->
                    <div class="signature-box">
                        <div class="signature-line">
                            Authorized Signatory
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        Thank You for Your Interest! | {{ $company['phone'] ?? '' }} | {{ $company['email'] ?? '' }}
    </div>
</body>
</html>
