<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $ledger->invoice_no }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f6f8;
        }

        .invoice-wrapper {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            background: #fff;
        }

        /* HEADER (UPDATED) */
        .header {
            background: #1f6fae;
            color: #fff;
            padding: 12px 25px;
            /* height কমানো */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .company h1 {
            margin: 0;
            font-size: 22px;
            /* ছোট করা */
            letter-spacing: .5px;
        }

        .company small {
            font-size: 11px;
            opacity: .9;
        }

        .contact {
            text-align: right;
            font-size: 12px;
            line-height: 1.4;
        }

        /* BODY */
        .body {
            padding: 25px;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            color: #1f6fae;
            margin-bottom: 15px;
        }

        .info-grid {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 25px;
        }

        .box {
            width: 50%;
            background: #f9fbfd;
            border: 1px solid #e3e8ee;
            padding: 14px;
            border-radius: 6px;
        }

        .box h4 {
            margin: 0 0 6px;
            color: #1f6fae;
            font-size: 14px;
        }

        .box p {
            margin: 3px 0;
            font-size: 12.5px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background: #1f6fae;
            color: #fff;
            padding: 9px;
            font-size: 12.5px;
            text-align: left;
        }

        td {
            padding: 9px;
            border-bottom: 1px solid #e6e6e6;
            font-size: 12.5px;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        /* TOTAL */
        .total-box {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }

        .total-box table td {
            padding: 8px 12px;
            font-size: 13px;
        }

        .grand {
            background: #1f6fae;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 11.5px;
            color: #777;
            border-top: 1px solid #eee;
            margin-top: 35px;
        }
    </style>
</head>

<body>
    <div class="invoice-wrapper">

        <!-- HEADER -->
        <div class="header">

            <div class="company">
                <h1>{{ $settings->title }}</h1>
                <small>Trust Begins With Quality</small>
            </div>

            <div class="contact">
                <strong>Sakhawat Hossen</strong><br>
                Chief Executive Officer<br>
                📞 {{ $settings->phone }}<br>
                {!! $settings->address !!}<br>
                ✉ sakhawathossen5895@gmail.com
            </div>

        </div>

        <!-- BODY -->
        <div class="body">

            <div class="title">INVOICE</div>

            <div class="info-grid">

                <div class="box">
                    <h4>Bill To</h4>
                    <p>{{ $ledger->customer->name ?? '' }}</p>
                    <p>{{ $ledger->customer->address ?? '' }}</p>
                </div>

                <div class="box">
                    <h4>Invoice Info</h4>
                    <p><b>Invoice No:</b> {{ $ledger->invoice_no }}</p>
                    <p><b>Date:</b> {{ $ledger->date }}</p>
                    <p><b>Time:</b> {{ $ledger->time }}</p>
                </div>

            </div>

            <!-- ITEMS -->
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ledger->items as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->product->name ?? '' }}</td>
                            <td>{{ $item->final_quantity }}</td>
                            <td>{{ $item->subUnit->name ?? '' }}</td>
                            <td>{{ number_format($item->sale_price, 2) }}</td>
                            <td>{{ number_format($item->discount, 2) }}</td>
                            <td>{{ number_format($item->final_quantity * $item->sale_price - $item->discount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- TOTAL -->
            <div class="total-box">
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
                    <tr class="grand">
                        <td>Balance</td>
                        <td>{{ number_format($ledger->subtotal - $ledger->discount - $ledger->paid, 2) }}</td>
                    </tr>
                </table>
            </div>

            @if ($ledger->note)
                <div style="margin-top:25px;">
                    <strong>Note:</strong>
                    <p>{{ $ledger->note }}</p>
                </div>
            @endif

        </div>

        <!-- FOOTER -->
        <div class="footer">
            Thank you for your business • This is a computer generated invoice
        </div>

    </div>
</body>

</html>
