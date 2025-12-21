<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->invoice_number ?? $order->order_number }}</title>
    <style>
        @page {
            margin: 8mm 10mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.1;
        }
        .invoice-container {
            padding: 0;
        }
        
        /* Header - Very Compact */
        .header {
            border-bottom: 2px solid #166534;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .header-table {
            width: 100%;
        }
        .logo-section {
            width: 70%;
            vertical-align: middle;
        }
        .logo-img {
            max-height: 40px;
            max-width: 100px;
            vertical-align: middle;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #166534;
            display: inline-block;
            vertical-align: middle;
            margin-left: 5px;
        }
        .company-details {
            font-size: 8px;
            color: #555;
            line-height: 1.2;
            margin-top: 2px;
        }
        .invoice-title-section {
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
        }
        .invoice-number {
            font-size: 10px;
            color: #333;
            font-weight: bold;
        }
        .invoice-date {
            font-size: 8px;
            color: #666;
        }
        
        /* Info Section - Compact */
        .info-section {
            margin-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border: 1px solid #ddd;
            border-collapse: collapse;
        }
        .info-box {
            width: 33.33%;
            vertical-align: top;
            padding: 4px 6px;
            border-right: 1px solid #ddd;
        }
        .info-box:last-child {
            border-right: none;
        }
        .info-box-title {
            font-size: 7px;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
            margin-bottom: 2px;
            border-bottom: 1px solid #d1fae5;
            padding-bottom: 1px;
        }
        .info-box-content {
            font-size: 8px;
            line-height: 1.3;
        }
        .info-box-content strong {
            color: #000;
        }
        
        /* Payment Badge */
        .payment-badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 8px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid {
            background-color: #dcfce7;
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
        
        /* Items Table - Super Compact for 15 items */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .items-table th {
            background-color: #166534;
            color: #fff;
            padding: 3px 3px;
            text-align: left;
            font-size: 7px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .items-table th:first-child {
            width: 3%;
            text-align: center;
        }
        .items-table th:last-child {
            text-align: right;
        }
        .items-table td {
            padding: 3px 3px;
            border-bottom: 1px solid #eee;
            font-size: 8px;
            vertical-align: middle;
        }
        .items-table .item-name {
            font-weight: 600;
            color: #000;
            font-size: 8px;
        }
        .items-table .item-variant {
            font-size: 7px;
            color: #166534;
        }
        .items-table .item-sku {
            font-size: 6px;
            color: #888;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        /* Bottom Section */
        .bottom-section {
            margin-top: 5px;
        }
        .bottom-table {
            width: 100%;
        }
        .terms-cell {
            width: 55%;
            vertical-align: top;
            padding-right: 10px;
        }
        .totals-cell {
            width: 45%;
            vertical-align: top;
        }
        
        /* Totals - Compact */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        .totals-table td {
            padding: 2px 5px;
            font-size: 8px;
            border-bottom: 1px solid #eee;
        }
        .totals-table .label {
            text-align: right;
            color: #555;
            width: 55%;
        }
        .totals-table .value {
            text-align: right;
            font-weight: 500;
            width: 45%;
        }
        .totals-table .grand-total td {
            background-color: #166534;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            padding: 4px 5px;
            border: none;
        }
        
        /* Amount Words */
        .amount-words {
            font-size: 7px;
            color: #555;
            margin-top: 3px;
            padding: 3px 5px;
            background: #f5f5f5;
            border-radius: 2px;
        }
        
        /* Terms */
        .terms-box {
            border: 1px solid #ddd;
            padding: 4px 6px;
            font-size: 7px;
            color: #666;
            line-height: 1.3;
        }
        .terms-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 2px;
            font-size: 7px;
        }
        
        /* Signature */
        .signature-box {
            text-align: right;
            margin-top: 8px;
            padding-top: 15px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 120px;
            margin-left: auto;
            padding-top: 3px;
            font-size: 7px;
            color: #555;
        }
        
        /* Thank You */
        .thank-you {
            text-align: center;
            margin-top: 8px;
            padding: 5px;
            background: #f0fdf4;
            border-radius: 3px;
            font-size: 9px;
            color: #166534;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header with Logo -->
        <div class="header">
            <table class="header-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="logo-section">
                        @if(!empty($company['logo']))
                            <img src="{{ $company['logo'] }}" alt="Logo" class="logo-img">
                        @endif
                        <span class="company-name">{{ $company['name'] }}</span>
                        <div class="company-details">
                            @if($company['address']){{ $company['address'] }} | @endif
                            @if($company['phone'])Ph: {{ $company['phone'] }} | @endif
                            @if($company['email']){{ $company['email'] }}@endif
                            @if($company['gst']) | GSTIN: {{ $company['gst'] }}@endif
                        </div>
                    </td>
                    <td class="invoice-title-section">
                        <div class="invoice-title">TAX INVOICE</div>
                        <div class="invoice-number">#{{ $order->invoice_number ?? $order->order_number }}</div>
                        <div class="invoice-date">Date: {{ $order->invoice_generated_at ? $order->invoice_generated_at->format('d/m/Y') : now()->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Billing, Shipping & Order Info -->
        <div class="info-section">
            <table class="info-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="info-box">
                        <div class="info-box-title">Bill To</div>
                        <div class="info-box-content">
                            <strong>{{ $order->customer_name }}</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
                            Ph: {{ $order->customer_phone }}
                        </div>
                    </td>
                    <td class="info-box">
                        <div class="info-box-title">Ship To</div>
                        <div class="info-box-content">
                            <strong>{{ $order->customer_name }}</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
                            Ph: {{ $order->customer_phone }}
                        </div>
                    </td>
                    <td class="info-box">
                        <div class="info-box-title">Order Details</div>
                        <div class="info-box-content">
                            <strong>Order:</strong> {{ $order->order_number }}<br>
                            <strong>Date:</strong> {{ $order->created_at->format('d/m/Y') }}<br>
                            <strong>Type:</strong> {{ ucfirst($order->order_type ?? 'Retail') }}<br>
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
                    <th class="text-center">#</th>
                    <th style="width: 40%;">Description</th>
                    <th style="width: 10%;">HSN</th>
                    <th class="text-center" style="width: 8%;">Qty</th>
                    <th class="text-right" style="width: 12%;">Rate</th>
                    <th class="text-right" style="width: 8%;">GST%</th>
                    <th class="text-right" style="width: 14%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="item-name">{{ $item->product_name }}</span>
                        @if($item->variant_name)
                            <span class="item-variant">({{ $item->variant_name }})</span>
                        @endif
                        @if($item->product_sku)
                            <span class="item-sku">SKU: {{ $item->product_sku }}</span>
                        @endif
                    </td>
                    <td>{{ $item->product->hsn_code ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ $item->product->gst_percent ?? 12 }}%</td>
                    <td class="text-right">{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bottom Section: Terms & Totals -->
        <div class="bottom-section">
            <table class="bottom-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="terms-cell">
                        <div class="terms-box">
                            <div class="terms-title">Terms & Conditions:</div>
                            1. Goods once sold will not be taken back or exchanged.<br>
                            2. All disputes are subject to local jurisdiction only.<br>
                            3. E. & O.E. - This is a computer generated invoice.
                        </div>
                        
                        <div class="amount-words">
                            <strong>Amount in Words:</strong> Indian Rupees {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format(floor($order->total_amount))) }} Only
                        </div>
                        
                        <div class="signature-box">
                            <div class="signature-line">
                                Authorized Signatory<br>
                                <strong>{{ $company['name'] }}</strong>
                            </div>
                        </div>
                    </td>
                    <td class="totals-cell">
                        <table class="totals-table" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="label">Subtotal:</td>
                                <td class="value">₹{{ number_format($order->subtotal, 2) }}</td>
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
                                <td class="label">Shipping:</td>
                                <td class="value">{{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 2) : 'FREE' }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <td class="label">Discount:</td>
                                <td class="value" style="color: #16a34a;">-₹{{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="grand-total">
                                <td class="label">Grand Total:</td>
                                <td class="value">₹{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Thank You -->
        <div class="thank-you">
            Thank You for Your Business! | {{ $company['phone'] ?? '' }} | {{ $company['email'] ?? '' }}
        </div>
    </div>
</body>
</html>
