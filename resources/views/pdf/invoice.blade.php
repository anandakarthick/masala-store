<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->invoice_number ?? $order->order_number }}</title>
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
        
        /* Header */
        .header {
            width: 100%;
            border-bottom: 3px solid #166534;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header-left {
            float: left;
            width: 55%;
        }
        .header-right {
            float: right;
            width: 42%;
            text-align: right;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .logo-img {
            max-height: 55px;
            max-width: 160px;
            display: block;
            margin-bottom: 8px;
        }
        .company-details {
            font-size: 10px;
            color: #555;
            line-height: 1.6;
        }
        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .invoice-meta {
            font-size: 12px;
            color: #333;
            line-height: 1.8;
        }
        .invoice-meta strong {
            color: #000;
        }
        
        /* Info Boxes */
        .info-section {
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-box {
            width: 33.33%;
            vertical-align: top;
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
        
        /* Payment Badge */
        .payment-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid {
            background-color: #d1fae5;
            color: #166534;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-cod {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
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
        .bottom-left {
            float: left;
            width: 48%;
        }
        .bottom-right {
            float: right;
            width: 48%;
        }
        
        /* Totals */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
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
        
        /* Amount Words */
        .amount-words {
            font-size: 10px;
            color: #555;
            margin-top: 10px;
            padding: 8px;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
        }
        .amount-words strong {
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
        
        /* Thank You */
        .thank-you {
            clear: both;
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 5px;
            font-size: 12px;
            color: #166534;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header clearfix">
        <div class="header-left">
            @if(!empty($company['logo']))
                <img src="{{ $company['logo'] }}" alt="Logo" class="logo-img">
            @else
                <div style="font-size: 20px; font-weight: bold; color: #166534; margin-bottom: 8px;">{{ $company['name'] }}</div>
            @endif
            <div class="company-details">
                @if($company['address']){{ $company['address'] }}<br>@endif
                @if($company['phone'])Phone: {{ $company['phone'] }}@endif
                @if($company['email']) | Email: {{ $company['email'] }}@endif
                @if($company['gst'])<br>GSTIN: {{ $company['gst'] }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">TAX INVOICE</div>
            <div class="invoice-meta">
                <strong>Invoice No:</strong> {{ $order->invoice_number ?? $order->order_number }}<br>
                <strong>Date:</strong> {{ $order->invoice_generated_at ? $order->invoice_generated_at->format('d/m/Y') : now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-box">
                    <div class="info-box-title">Bill To</div>
                    <div class="info-content">
                        <strong>{{ $order->customer_name }}</strong><br>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                        PIN: {{ $order->shipping_pincode }}<br>
                        Phone: {{ $order->customer_phone }}
                    </div>
                </td>
                <td class="info-box">
                    <div class="info-box-title">Ship To</div>
                    <div class="info-content">
                        <strong>{{ $order->customer_name }}</strong><br>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                        PIN: {{ $order->shipping_pincode }}<br>
                        Phone: {{ $order->customer_phone }}
                    </div>
                </td>
                <td class="info-box">
                    <div class="info-box-title">Order Details</div>
                    <div class="info-content">
                        <strong>Order No:</strong> {{ $order->order_number }}<br>
                        <strong>Order Date:</strong> {{ $order->created_at->format('d/m/Y') }}<br>
                        <strong>Order Type:</strong> {{ ucfirst($order->order_type ?? 'Retail') }}<br>
                        <strong>Payment:</strong> 
                        <span class="payment-badge {{ $order->payment_status === 'paid' ? 'badge-paid' : ($order->payment_method === 'cod' ? 'badge-cod' : 'badge-pending') }}">
                            {{ $order->payment_method === 'cod' ? 'COD' : ucfirst($order->payment_status) }}
                        </span>
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
                <th style="width: 12%;">HSN</th>
                <th style="width: 10%;" class="center">Qty</th>
                <th style="width: 15%;" class="right">Rate (₹)</th>
                <th style="width: 18%;" class="right">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
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
                </td>
                <td>{{ $item->product->hsn_code ?? '-' }}</td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="right">{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bottom Section -->
    <div class="bottom-section clearfix">
        <div class="bottom-left">
            <!-- Terms -->
            <div class="terms-box">
                <div class="terms-title">Terms & Conditions:</div>
                1. Goods once sold will not be taken back or exchanged.<br>
                2. All disputes are subject to local jurisdiction only.<br>
                3. E. & O.E. - This is a computer generated invoice.
            </div>
            
            <!-- Amount in Words -->
            <div class="amount-words">
                <strong>Amount in Words:</strong><br>
                Indian Rupees {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format(floor($order->total_amount))) }} Only
            </div>
        </div>
        <div class="bottom-right">
            <!-- Totals -->
            <table class="totals-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->gst_amount > 0)
                <tr>
                    <td class="label">CGST:</td>
                    <td class="value">₹{{ number_format($order->gst_amount / 2, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">SGST:</td>
                    <td class="value">₹{{ number_format($order->gst_amount / 2, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Shipping Charges:</td>
                    <td class="value">{{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 2) : 'FREE' }}</td>
                </tr>
                @if($order->discount_amount > 0)
                <tr>
                    <td class="label">Discount:</td>
                    <td class="value" style="color: #166534;">-₹{{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td class="label">Grand Total:</td>
                    <td class="value">₹{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
            
            <!-- Signature -->
            <div class="signature-box">
                <div class="signature-line">
                    Authorized Signatory
                </div>
            </div>
        </div>
    </div>

    <!-- Thank You -->
    <div class="thank-you">
        Thank You for Your Business!
    </div>
</body>
</html>
