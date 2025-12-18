<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->invoice_number ?? $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .invoice-container {
            padding: 20px;
        }
        
        /* Rupee Symbol */
        .rupee {
            font-family: 'DejaVu Sans', sans-serif;
        }
        
        /* Header */
        .header {
            border-bottom: 3px solid #166534;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
        }
        .logo-section {
            width: 60%;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #166534;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.6;
        }
        .invoice-title-section {
            width: 40%;
            text-align: right;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-number {
            font-size: 14px;
            color: #166534;
            margin-top: 5px;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 25px;
        }
        .info-table {
            width: 100%;
        }
        .info-box {
            width: 48%;
            vertical-align: top;
        }
        .info-box-title {
            font-size: 11px;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #86efac;
        }
        .info-box-content {
            font-size: 12px;
            line-height: 1.8;
        }
        .info-box-content strong {
            color: #1a1a1a;
        }
        
        /* Order Details */
        .order-meta {
            background-color: #f0fdf4;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .order-meta-table {
            width: 100%;
        }
        .order-meta td {
            padding: 5px 15px 5px 0;
            font-size: 11px;
        }
        .order-meta .label {
            color: #78716c;
        }
        .order-meta .value {
            font-weight: bold;
            color: #1a1a1a;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #166534;
            color: #ffffff;
            padding: 12px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .items-table th:first-child {
            border-radius: 5px 0 0 0;
        }
        .items-table th:last-child {
            border-radius: 0 5px 0 0;
            text-align: right;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        .items-table .item-name {
            font-weight: 600;
            color: #1a1a1a;
        }
        .items-table .item-variant {
            font-size: 10px;
            color: #166534;
        }
        .items-table .item-sku {
            font-size: 10px;
            color: #999;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        /* Totals */
        .totals-section {
            margin-top: 20px;
        }
        .totals-table {
            width: 350px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 10px;
            font-size: 12px;
        }
        .totals-table .label {
            text-align: right;
            color: #666;
        }
        .totals-table .value {
            text-align: right;
            font-weight: 500;
            width: 120px;
        }
        .totals-table .grand-total td {
            border-top: 2px solid #166534;
            padding-top: 12px;
            font-size: 16px;
            font-weight: bold;
        }
        .totals-table .grand-total .value {
            color: #166534;
        }
        
        /* Amount in Words */
        .amount-words {
            background-color: #f8fafc;
            padding: 12px 15px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 11px;
        }
        .amount-words-label {
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .amount-words-value {
            font-weight: 600;
            color: #1a1a1a;
            margin-top: 3px;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .footer-content {
            width: 100%;
        }
        .terms-section {
            width: 60%;
            vertical-align: top;
        }
        .terms-title {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            margin-bottom: 8px;
        }
        .terms-list {
            font-size: 10px;
            color: #888;
            line-height: 1.8;
        }
        .signature-section {
            width: 40%;
            text-align: right;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 180px;
            margin-left: auto;
            padding-top: 8px;
            font-size: 11px;
            color: #666;
        }
        
        /* Thank You */
        .thank-you {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 5px;
        }
        .thank-you-text {
            font-size: 14px;
            color: #166534;
            font-weight: bold;
        }
        .thank-you-subtext {
            font-size: 11px;
            color: #15803d;
            margin-top: 5px;
        }
        
        /* Payment Badge */
        .payment-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
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
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-section">
                        <div class="company-name">{{ $company['name'] }}</div>
                        <div class="company-details">
                            @if($company['address']){{ $company['address'] }}<br>@endif
                            @if($company['phone'])Phone: {{ $company['phone'] }} @endif
                            @if($company['email'])| Email: {{ $company['email'] }}@endif
                            @if($company['gst'])<br>GSTIN: {{ $company['gst'] }}@endif
                        </div>
                    </td>
                    <td class="invoice-title-section">
                        <div class="invoice-title">Invoice</div>
                        <div class="invoice-number">#{{ $order->invoice_number ?? $order->order_number }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Billing & Shipping Info -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="info-box">
                        <div class="info-box-title">Bill To</div>
                        <div class="info-box-content">
                            <strong>{{ $order->customer_name }}</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                            PIN: {{ $order->shipping_pincode }}<br>
                            Phone: {{ $order->customer_phone }}
                            @if($order->customer_email)<br>Email: {{ $order->customer_email }}@endif
                        </div>
                    </td>
                    <td style="width: 4%;"></td>
                    <td class="info-box">
                        <div class="info-box-title">Ship To</div>
                        <div class="info-box-content">
                            <strong>{{ $order->customer_name }}</strong><br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                            PIN: {{ $order->shipping_pincode }}<br>
                            Phone: {{ $order->customer_phone }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Order Meta -->
        <div class="order-meta">
            <table class="order-meta-table">
                <tr>
                    <td class="label">Invoice Date:</td>
                    <td class="value">{{ $order->invoice_generated_at ? $order->invoice_generated_at->format('d M Y') : now()->format('d M Y') }}</td>
                    <td class="label">Order Date:</td>
                    <td class="value">{{ $order->created_at->format('d M Y') }}</td>
                    <td class="label">Order No:</td>
                    <td class="value">{{ $order->order_number }}</td>
                    <td class="label">Payment:</td>
                    <td class="value">
                        <span class="payment-badge {{ $order->payment_status === 'paid' ? 'badge-paid' : ($order->payment_method === 'cod' ? 'badge-cod' : 'badge-pending') }}">
                            {{ $order->payment_method === 'cod' ? 'COD' : ucfirst($order->payment_status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Item Description</th>
                    <th style="width: 12%;">HSN</th>
                    <th class="text-center" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 15%;">Rate</th>
                    <th class="text-right" style="width: 18%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item->product_name }}</div>
                        @if($item->variant_name)
                            <div class="item-variant">{{ $item->variant_name }}</div>
                        @endif
                        <div class="item-sku">SKU: {{ $item->product_sku }}</div>
                    </td>
                    <td>{{ $item->product->hsn_code ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">Rs. {{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">Rs. {{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->gst_amount > 0)
                <tr>
                    <td class="label">GST ({{ $order->gst_percent ?? 12 }}%):</td>
                    <td class="value">Rs. {{ number_format($order->gst_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Shipping:</td>
                    <td class="value">{{ $order->shipping_charge > 0 ? 'Rs. ' . number_format($order->shipping_charge, 2) : 'FREE' }}</td>
                </tr>
                @if($order->discount_amount > 0)
                <tr>
                    <td class="label">Discount:</td>
                    <td class="value" style="color: #16a34a;">- Rs. {{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td class="label">Grand Total:</td>
                    <td class="value">Rs. {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Amount in Words -->
        <div class="amount-words">
            <div class="amount-words-label">Amount in Words</div>
            <div class="amount-words-value">Indian Rupees {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format(floor($order->total_amount))) }} Only</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <table class="footer-content">
                <tr>
                    <td class="terms-section">
                        <div class="terms-title">Terms & Conditions</div>
                        <div class="terms-list">
                            1. Goods once sold will not be taken back.<br>
                            2. All disputes are subject to local jurisdiction.<br>
                            3. E. & O.E. (Errors and Omissions Excepted)<br>
                            4. This is a computer-generated invoice.
                        </div>
                    </td>
                    <td class="signature-section">
                        <div class="signature-line">
                            Authorized Signatory<br>
                            <strong>{{ $company['name'] }}</strong>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Thank You -->
        <div class="thank-you">
            <div class="thank-you-text">Thank You for Your Business!</div>
            <div class="thank-you-subtext">We appreciate your trust in {{ $company['name'] }}</div>
        </div>
    </div>
</body>
</html>
