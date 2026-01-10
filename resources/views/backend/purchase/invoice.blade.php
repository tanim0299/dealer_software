<!DOCTYPE html>
<html>
<head>
    <title>Purchase Invoice</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            color: #333;
        }

        .invoice-wrapper {
            max-width: 850px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e5e5e5;
        }

        /* ---------- HEADER ---------- */
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #222;
            padding-bottom: 15px;
        }

        .company-details img {
            max-height: 60px;
            margin-bottom: 5px;
        }

        .company-details h2 {
            margin: 0;
            font-size: 22px;
        }

        .company-details p {
            margin: 2px 0;
            font-size: 12px;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-info h3 {
            margin: 0;
            font-size: 20px;
        }

        /* ---------- SUPPLIER ---------- */
        .supplier-box {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        /* ---------- TABLE ---------- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table thead {
            background: #f4f6f8;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 13px;
        }

        table th {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        /* ---------- TOTALS ---------- */
        .totals {
            width: 40%;
            margin-left: auto;
            margin-top: 20px;
        }

        .totals td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .totals tr:last-child td {
            font-weight: bold;
            background: #f4f6f8;
        }

        /* ---------- FOOTER ---------- */
        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>

<div class="invoice-wrapper">

    <!-- HEADER -->
    <div class="header">
        <div class="company-details">
            <img src="{{ asset('logo.png') }}" alt="Company Logo">
            <h2>Your Company Name</h2>
            <p>123 Business Street, City, Country</p>
            <p>Phone: +880 1234 567890</p>
            <p>Email: info@company.com</p>
        </div>

        <div class="invoice-info">
            <h3>Purchase Invoice</h3>
            <p><strong>Invoice #:</strong> {{ $purchase->invoice_no }}</p>
            <p><strong>Date:</strong> {{ $purchase->purchase_date }}</p>
        </div>
    </div>

    <!-- SUPPLIER -->
    <div class="supplier-box">
        <div>
            <strong>Supplier:</strong><br>
            {{ $purchase->supplier->name }}<br>
            Phone: {{ $purchase->supplier->phone ?? '' }}
        </div>
    </div>

    <!-- ITEMS -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Unit</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Discount Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->entries as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->sub_unit->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->discount, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TOTALS -->
    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">{{ number_format($purchase->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td class="text-right">{{ number_format($purchase->discount, 2) }}</td>
        </tr>
        <tr>
            <td>Paid</td>
            <td class="text-right">{{ number_format($purchase->paid, 2) }}</td>
        </tr>
        <tr>
            <td>Due Amount</td>
            <td class="text-right">
                {{ number_format(($purchase->total_amount - $purchase->discount) - $purchase->paid, 2) }}
            </td>
        </tr>
    </table>

    <!-- NOTE -->
    @if($purchase->note)
        <p><strong>Note:</strong> {{ $purchase->note }}</p>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        Thank you for your business<br>
        This is a system-generated invoice.
    </div>

</div>

</body>
</html>
