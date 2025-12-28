<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

        /* ==================== HEADER SECTION ==================== */
        .header {
            width: 100%;
            border-bottom: 3px solid #F97316;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
            table-layout: fixed;
        }

        .logo-cell {
            width: 65%;
            text-align: left;
            vertical-align: top;
            padding-left: 20;
        }

        .invoice-cell {
            width: 35%;
            text-align: right;
            vertical-align: top;
            padding-right: 20px;
        }

        .logo-img {
            max-height: 60px;
            max-width: 180px;
            display: block;
            margin-bottom: 8px;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #166534;
            margin-bottom: 8px;
        }

        .company-details {
            font-size: 10px;
            color: #555;
            line-height: 1.7;
            margin-top: 5px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #F97316;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }

        .invoice-meta {
            font-size: 12px;
            color: #333;
            line-height: 2;
            text-align: right;
        }

        .invoice-meta-row {
            margin-bottom: 3px;
        }

        .invoice-meta strong {
            color: #000;
        }

        /* ==================== INFO BOXES SECTION ==================== */
        .info-section {
            margin-bottom: 15px;
            width: 100%;
        }

        .info-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .info-box {
            width: 33.33%;
            padding: 10px 12px;
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
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #F97316;
        }

        .info-content {
            font-size: 11px;
            line-height: 1.7;
        }

        .info-content strong {
            font-size: 12px;
            color: #000;
            display: block;
            margin-bottom: 3px;
        }

        /* Payment Badge */
        .badge-paid {
            display: inline-block;
            background-color: #dcfce7;
            color: #166534;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-pending {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-cod {
            display: inline-block;
            background-color: #dbeafe;
            color: #1e40af;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        /* ==================== ITEMS TABLE SECTION ==================== */
        .items-section {
            width: 100%;
            margin-bottom: 12px;
        }

        .items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .items-table th {
            background-color: #F97316;
            color: #fff;
            padding: 10px 8px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
            border: 1px solid #e86a10;
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
            padding: 10px 8px;
            border: 1px solid #ddd;
            font-size: 11px;
            vertical-align: top;
        }

        .items-table td.text-center {
            text-align: center;
            vertical-align: middle;
        }

        .items-table td.text-right {
            text-align: right;
            vertical-align: middle;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #fafafa;
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
            margin-bottom: 3px;
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

        /* ==================== BOTTOM SECTION ==================== */
        .bottom-section {
            width: 100%;
            margin-top: 12px;
        }

        .bottom-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .bottom-left {
            width: 48%;
            vertical-align: top;
            padding-right: 15px;
        }

        .bottom-right {
            width: 52%;
            vertical-align: top;
        }

        /* Terms */
        .terms-box {
            border: 1px solid #ddd;
            padding: 10px 12px;
            font-size: 9px;
            color: #666;
            line-height: 1.7;
            background: #fafafa;
            margin-bottom: 10px;
        }

        .terms-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 6px;
            font-size: 10px;
        }

        /* Amount Words */
        .amount-words {
            font-size: 10px;
            color: #555;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            line-height: 1.6;
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
            padding: 8px 12px;
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
            background-color: #F97316 !important;
            color: #fff !important;
            font-size: 14px;
            font-weight: bold;
            padding: 12px;
        }

        /* Thank You */
        .thank-you {
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(135deg, #FFF7ED 0%, #FED7AA 100%);
            font-size: 14px;
            color: #C2410C;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <!-- ==================== HEADER ==================== -->
    <div class="header">
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                {{-- <td  class="logo-cell"></td> --}}
                <td class="logo-cell">
                    @if (!empty($company['logo']))
                        <img src="{{ $company['logo'] }}" alt="Logo" class="logo-img">
                    @else
                        <div class="company-name">{{ $company['name'] }}</div>
                    @endif
                    <div class="company-details">
                        @if ($company['address'])
                            @foreach (explode(',', $company['address']) as $line)
                                {{ trim($line) }}<br>
                            @endforeach
                        @endif
                        @if ($company['phone'])
                            Phone: {{ $company['phone'] }}
                        @endif
                        @if ($company['email'])
                            &nbsp;|&nbsp; Email: {{ $company['email'] }}
                        @endif
                        @if ($company['gst'])
                            <br>GSTIN: {{ $company['gst'] }}
                        @endif
                    </div>
                </td>

                <td class="invoice-cell">
                    <div class="invoice-title">TAX INVOICE</div>
                    <div class="invoice-meta">
                        <div class="invoice-meta-row">
                            <strong>Invoice No:</strong> {{ $order->invoice_number ?? $order->order_number }}
                        </div>
                        <div class="invoice-meta-row">
                            <strong>Date:</strong>
                            {{ $order->invoice_generated_at ? $order->invoice_generated_at->format('d/m/Y') : now()->format('d/m/Y') }}
                        </div>
                    </div>
                </td>
                {{-- <td  class="logo-cell"></td> --}}
            </tr>
        </table>
    </div>

    <!-- ==================== INFO SECTION ==================== -->
    <div class="info-section">
        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-box">
                    <div class="info-box-title">Bill To</div>
                    <div class="info-content">
                        <strong>{{ $order->customer_name }}</strong>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                        PIN: {{ $order->shipping_pincode }}<br>
                        Phone: {{ $order->customer_phone }}
                    </div>
                </td>
                <td class="info-box">
                    <div class="info-box-title">Ship To</div>
                    <div class="info-content">
                        <strong>{{ $order->customer_name }}</strong>
                        {{ $order->shipping_address }}<br>
                        {{ $order->shipping_city }}, {{ $order->shipping_state }}<br>
                        PIN: {{ $order->shipping_pincode }}<br>
                        Phone: {{ $order->customer_phone }}
                    </div>
                </td>
                <td class="info-box">
                    <div class="info-box-title">Order Details</div>
                    <div class="info-content">
                        <strong style="display: inline;">Order No:</strong> {{ $order->order_number }}<br>
                        <strong style="display: inline;">Order Date:</strong>
                        {{ $order->created_at->format('d/m/Y') }}<br>
                        <strong style="display: inline;">Order Type:</strong>
                        {{ ucfirst($order->order_type ?? 'Retail') }}<br>
                        <strong style="display: inline;">Payment:</strong>
                        <span
                            class="{{ $order->payment_status === 'paid' ? 'badge-paid' : ($order->payment_method === 'cod' ? 'badge-cod' : 'badge-pending') }}">
                            {{ $order->payment_method === 'cod' ? 'COD' : ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== ITEMS TABLE ==================== -->
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
                    <th class="text-right">Rate (&#8377;)</th>
                    <th class="text-right">Amount (&#8377;)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if ($item->variant_name)
                                <div class="item-variant">{{ $item->variant_name }}</div>
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

    <!-- ==================== BOTTOM SECTION ==================== -->
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
                        Indian Rupees
                        {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format(floor($order->total_amount))) }}
                        Only
                    </div>
                </td>
                <td class="bottom-right">
                    <!-- Totals -->
                    <table class="totals-table" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="label">Subtotal:</td>
                            <td class="value">{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if ($order->gst_amount > 0)
                            <tr>
                                <td class="label">CGST:</td>
                                <td class="value">{{ number_format($order->gst_amount / 2, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="label">SGST:</td>
                                <td class="value">{{ number_format($order->gst_amount / 2, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="label">Shipping Charges:</td>
                            <td class="value">
                                @if ($order->shipping_charge > 0)
                                    {{ number_format($order->shipping_charge, 2) }}
                                @else
                                    FREE
                                @endif
                            </td>
                        </tr>
                        @if ($order->discount_amount > 0)
                            <tr>
                                <td class="label">Discount:</td>
                                <td class="value" style="color: #dc2626;">
                                    -&#8377;{{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="grand-total">
                            <td class="label">Grand Total:</td>
                            <td class="value">&#8377;{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- ==================== THANK YOU ==================== -->
    {{-- <div class="thank-you">
        Thank You for Your Business!
    </div> --}}
</body>

</html>
