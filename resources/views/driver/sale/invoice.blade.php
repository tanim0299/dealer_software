<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice #{{ $ledger->invoice_no }}</title>
<style>
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #444;
        margin: 0;
        padding: 0;
        font-size: 14px;
    }

    .invoice-box {
        max-width: 800px;
        margin: 20px auto;
        padding: 30px;
        border: 1px solid #eee;
        /* box-shadow: 0 2px 8px rgba(0,0,0,0.05); */
    }

    /* Header */
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .invoice-header h1 {
        color: #2E86C1;
        font-size: 32px;
        margin: 0;
    }

    .company-details {
        text-align: right;
    }

    .company-details p {
        margin: 2px 0;
        line-height: 1.3;
    }

    /* Customer Info */
    .customer-info {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 25px;
    }

    .customer-info h3 {
        margin: 0 0 5px 0;
        color: #2E86C1;
        font-size: 16px;
    }

    .customer-info p {
        margin: 2px 0;
    }

    /* Table */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
    }

    table th {
        background: #2E86C1;
        color: #fff;
        font-weight: bold;
        padding: 10px;
        text-align: left;
    }

    table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    table tbody tr:nth-child(even) {
        background: #f6f6f6;
    }

    /* Totals */
    .totals {
        width: 300px;
        float: right;
        margin-top: 15px;
    }

    .totals table {
        border: 1px solid #eee;
    }

    .totals table td {
        padding: 8px 12px;
        border-bottom: 1px solid #eee;
    }

    .totals table tr:last-child td {
        font-size: 18px;
        font-weight: bold;
        background: #2E86C1;
        color: #fff;
    }

    /* Notes */
    .notes {
        margin-top: 30px;
        padding: 15px;
        border-left: 4px solid #2E86C1;
        background: #f9f9f9;
    }

    /* Footer */
    .footer {
        text-align: center;
        font-size: 12px;
        color: #888;
        margin-top: 50px;
    }
</style>
</head>
<body>
<div class="invoice-box">
    <!-- Header -->
    <div class="invoice-header">
        <h1>INVOICE</h1>
        <div class="company-details">
            <p><strong>Dealer Name</strong></p>
            <p>Address Line 1</p>
            <p>City, Zip</p>
            <p>Phone: +123456789</p>
            <p>Email: dealer@example.com</p>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="customer-info">
        <h3>Bill To:</h3>
        <p>{{ $ledger->customer->name ?? 'Customer Name' }}</p>
        <p>{{ $ledger->customer->address ?? 'Customer Address' }}</p>
        <p><strong>Invoice No:</strong> {{ $ledger->invoice_no }}</p>
        <p><strong>Date:</strong> {{ $ledger->date }}</p>
        <p><strong>Time:</strong> {{ $ledger->time }}</p>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ledger->items as $key => $entry)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $entry->product->name ?? 'Product' }}</td>
                <td>{{ $entry->final_quantity }}</td>
                <td>{{ $entry->subUnit->name ?? '' }}</td>
                <td>{{ number_format($entry->sale_price, 2) }}</td>
                <td>{{ number_format($entry->discount, 2) }}</td>
                <td>{{ number_format(($entry->final_quantity * $entry->sale_price) - $entry->discount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td>{{ number_format($ledger->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td>{{ number_format($ledger->discount, 2) }}</td>
            </tr>
            <tr>
                <td>Paid</td>
                <td>{{ number_format($ledger->paid, 2) }}</td>
            </tr>
            <tr>
                <td>Balance</td>
                <td>{{ number_format($ledger->subtotal - $ledger->discount - $ledger->paid, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Notes -->
    @if($ledger->note)
    <div class="notes">
        <strong>Note:</strong>
        <p>{{ $ledger->note }}</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Thank you for your business! This is a computer-generated invoice.
    </div>
</div>
</body>
</html>
