<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
        table {
            border-collapse: collapse;
        }
        
        /* Rupee Symbol Fix */
        .rupee {
            font-family: 'DejaVu Sans', sans-serif;
        }
        
        /* Header */
        .header {
            width: 100%;
            border-bottom: 3px solid #F97316;
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
        .invoice-cell {
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
        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #F97316;
            text-transform: uppercase;
        }
        .invoice-meta {
            font-size: 12px;
            color: #333;
            line-height: 1.8;
            margin-top: 8px;
        }
        .invoice-meta strong {
            color: #000;
        }
        
        /* Info Boxes */
        .info-section {
            margin-bottom: 12px;
            width: 100%;
        }
        .info-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .info-box {
            width: 33.33%;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            vertical-align: top;
        }
        .info-box-title {
            font-size: 10px;
            font-weight: bold;
            color: #F97316;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 2px solid #F97316;
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
        .badge-paid {
            display: inline-block;
            background-color: #dcfce7;
            color: #166534;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-pending {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-cod {
            display: inline-block;
            background-color: #dbeafe;
            color: #1e40af;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        
        /* Items Table */
        .items-section {
            width: 100%;
            margin-bottom: 10px;
        }
        .items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .items-table th {
            background-color: #F97316;
            color: #fff;
            padding: 8px 6px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            border: 1px solid #F97316;
        }
        .items-table th.text-left {
            text-align: left;
        }
        .items-table th.text-center {
            text-align: center;
        }
        .items-table th.text-right {
            text-align: right;
        }
        .items-table td {
            padding: 8px 6px;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }
        .items-table td.text-center {
            text-align: center;
        }
        .items-table td.text-right {
            text-align: right;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Column widths */
        .col-sno {
            width: 6%;
        }
        .col-desc {
            width: 38%;
        }
        .col-hsn {
            width: 12%;
        }
        .col-qty {
            width: 10%;
        }
        .col-rate {
            width: 17%;
        }
        .col-amount {
            width: 17%;
        }
        
        .item-name {
            font-weight: 600;
            color: #000;
            margin-bottom: 2px;
        }
        .item-variant {
            font-size: 10px;
            color: #F97316;
            margin-bottom: 2px;
        }
        .item-sku {
            font-size: 9px;
            color: #888;
        }
        
        /* Bottom Section */
        .bottom-section {
            width: 100%;
            margin-top: 10px;
        }
        .bottom-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .bottom-left {
            width: 48%;
            vertical-align: top;
            padding-right: 10px;
        }
        .bottom-right {
            width: 52%;
            vertical-align: top;
        }
        
        /* Terms */
        .terms-box {
            border: 1px solid #ddd;
            padding: 8px 10px;
            font-size: 9px;
            color: #666;
            line-height: 1.6;
            background: #fafafa;
            margin-bottom: 10px;
        }
        .terms-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            font-size: 10px;
        }
        
        /* Amount Words */
        .amount-words {
            font-size: 10px;
            color: #555;
            padding: 8px;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
        }
        .amount-words strong {
            color: #333;
        }
        
        /* Totals */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        .totals-table tr {
            border-bottom: 1px solid #eee;
        }
        .totals-table tr:last-child {
            border-bottom: none;
        }
        .totals-table td {
            padding: 8px 10px;
            font-size: 11px;
        }
        .totals-table td.label {
            text-align: right;
            color: #555;
            width: 55%;
            background-color: #fafafa;
        }
        .totals-table td.value {
            text-align: right;
            font-weight: 600;
            width: 45%;
            color: #000;
            background-color: #fff;
        }
        .totals-table tr.grand-total td {
            background-color: #F97316;
            color: #fff;
            font-size: 13px;
            font-weight: bold;
            padding: 10px;
        }
        .totals-table tr.grand-total td.label {
            background-color: #F97316;
            color: #fff;
        }
        .totals-table tr.grand-total td.value {
            background-color: #F97316;
            color: #fff;
        }
        
        /* Thank You */
        .thank-you {
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(135deg, #FFF7ED 0%, #FED7AA 100%);
            font-size: 13px;
            color: #C2410C;
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
                <td class="invoice-cell">
                    <div class="invoice-title">TAX INVOICE</div>
                    <div class="invoice-meta">
                        <strong>Invoice No:</strong> {{ $order->invoice_number ?? $order->order_number }}<br>
                        <strong>Date:</strong> {{ $order->invoice_generated_at ? $order->invoice_generated_at->format('d/m/Y') : now()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
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
                        <span class="{{ $order->payment_status === 'paid' ? 'badge-paid' : ($order->payment_method === 'cod' ? 'badge-cod' : 'badge-pending') }}">
                            {{ $order->payment_method === 'cod' ? 'COD' : ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table -->
    <div class="items-section">
        <table class="items-table" cellpadding="0" cellspacing="0">
            <colgroup>
                <col class="col-sno">
                <col class="col-desc">
                <col class="col-hsn">
                <col class="col-qty">
                <col class="col-rate">
                <col class="col-amount">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-left">Description</th>
                    <th class="text-center">HSN</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Rate (<span class="rupee">&#8377;</span>)</th>
                    <th class="text-right">Amount (<span class="rupee">&#8377;</span>)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if($item->variant_name)
                            <div class="item-variant">{{ $item->variant_name }}</div>
                        @endif
                        @if($item->product_sku)
                            <div class="item-sku">SKU: {{ $item->product_sku }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->product->hsn_code ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Bottom Section -->
    <div class="bottom-section">
        <table class="bottom-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bottom-left">
                    <!-- Terms -->
                    <div class="terms-box">
                        <div class="terms-title">Terms &amp; Conditions:</div>
                        1. Goods once sold will not be taken back or exchanged.<br>
                        2. All disputes are subject to local jurisdiction only.<br>
                        3. E. &amp; O.E. - This is a computer generated invoice.
                    </div>
                    
                    <!-- Amount in Words -->
                    <div class="amount-words">
                        <strong>Amount in Words:</strong><br>
                        Indian Rupees {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format(floor($order->total_amount))) }} Only
                    </div>
                </td>
                <td class="bottom-right">
                    <!-- Totals -->
                    <table class="totals-table" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="label">Subtotal:</td>
                            <td class="value"><span class="rupee">&#8377;</span>{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->gst_amount > 0)
                        <tr>
                            <td class="label">CGST:</td>
                            <td class="value"><span class="rupee">&#8377;</span>{{ number_format($order->gst_amount / 2, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label">SGST:</td>
                            <td class="value"><span class="rupee">₹</span>{{ number_format($order->gst_amount / 2, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label">Shipping Charges:</td>
                            <td class="value">
                                @if($order->shipping_charge > 0)
                                    <span class="rupee">₹</span>{{ number_format($order->shipping_charge, 2) }}
                                @else
                                    FREE
                                @endif
                            </td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td class="label">Discount:</td>
                            <td class="value" style="color: #dc2626;">-<span class="rupee">&#8377;</span>{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total">
                            <td class="label">Grand Total:</td>
                            <td class="value"><span class="rupee">&#8377;</span>{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- Thank You -->
    <div class="thank-you">
        Thank You for Your Business!
    </div>
</body>
</html>