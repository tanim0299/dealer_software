<!DOCTYPE html>
<html>

<head>
    <title>Driver Daily Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #000;
        }

        h2,
        h4,
        h5 {
            margin: 6px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 12px;
            vertical-align: top;
        }

        .table th {
            background: #f5f5f5;
            text-align: left;
        }

        .text-end {
            text-align: right;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 6px;
        }

        .summary-item {
            border: 1px solid #000;
            padding: 8px;
            font-size: 12px;
        }

        .strong {
            font-weight: 700;
            font-size: 14px;
        }

        .section-title {
            margin: 10px 0 5px;
            font-size: 14px;
            font-weight: 700;
        }

        .meta {
            margin: 10px 0;
            font-size: 13px;
        }

        @media print {
            body {
                margin: 8px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
@include('letterhead', ['report_title' => $report_title])

<div class="meta">
    <strong>Driver:</strong> {{ $report['driver']->name }} &nbsp; | &nbsp;
    <strong>Date:</strong> {{ \Carbon\Carbon::parse($report['date'])->format('d M, Y') }} &nbsp; | &nbsp;
    <strong>Status:</strong> {{ $report['closingStatus'] ? 'Closing Submitted' : 'Closing Pending' }}
</div>

<div class="section-title">Sales Details</div>
<table class="table">
    <thead>
    <tr>
        <th>Invoice</th>
        <th>Customer</th>
        <th class="text-end">Amount</th>
        <th class="text-end">Paid</th>
    </tr>
    </thead>
    <tbody>
    @forelse($report['sales'] as $sale)
        <tr>
            <td>{{ $sale->invoice_no }}</td>
            <td>{{ $sale->customer->name ?? '-' }}</td>
            <td class="text-end">{{ number_format((float) $sale->subtotal, 2) }}</td>
            <td class="text-end">{{ number_format((float) $sale->paid, 2) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4" style="text-align:center;">No sales found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="section-title">Collection Details</div>
<table class="table">
    <thead>
    <tr>
        <th>Customer</th>
        <th class="text-end">Amount</th>
    </tr>
    </thead>
    <tbody>
    @forelse($report['collections'] as $collection)
        <tr>
            <td>{{ $collection->customer->name ?? '-' }}</td>
            <td class="text-end">{{ number_format((float) $collection->amount, 2) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="2" style="text-align:center;">No collection found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="section-title">Sales Return Details</div>
<table class="table">
    <thead>
    <tr>
        <th>Return Invoice</th>
        <th>Customer</th>
        <th>Products</th>
        <th class="text-end">Amount</th>
        <th>Adjustment</th>
    </tr>
    </thead>
    <tbody>
    @forelse($report['salesReturns'] as $returnLedger)
        @php
            $productText = $returnLedger->entries->map(function($entry){
                return ($entry->product->name ?? 'Product') . ' x ' . rtrim(rtrim(number_format((float) $entry->return_qty, 4, '.', ''), '0'), '.');
            })->implode(', ');
            $payment = $returnLedger->payments->first();
            $adjustmentType = 'Due Adjust';
            if ($payment && (float) $payment->amount < 0) {
                $adjustmentType = 'Cash Paid';
            }
        @endphp
        <tr>
            <td>{{ $returnLedger->invoice_no ?? '-' }}</td>
            <td>{{ $returnLedger->customer->name ?? '-' }}</td>
            <td>{{ $productText ?: '-' }}</td>
            <td class="text-end">{{ number_format((float) $returnLedger->subtotal, 2) }}</td>
            <td>{{ $adjustmentType }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" style="text-align:center;">No sales return found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="section-title">Expense Details</div>
<table class="table">
    <thead>
    <tr>
        <th>Expense Head</th>
        <th class="text-end">Amount</th>
    </tr>
    </thead>
    <tbody>
    @forelse($report['expenses'] as $expense)
        <tr>
            <td>{{ $expense->expense->title ?? '-' }}</td>
            <td class="text-end">{{ number_format((float) $expense->amount, 2) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="2" style="text-align:center;">No expense found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="section-title">Product Summary</div>
<table class="table">
    <thead>
    <tr>
        <th>Product</th>
        <th class="text-end">Taken</th>
        <th class="text-end">Sold</th>
        <th class="text-end">Return</th>
        <th class="text-end">Remain</th>
    </tr>
    </thead>
    <tbody>
    @forelse($report['products'] as $product)
        <tr>
            <td>{{ $product->product->name ?? '-' }}</td>
            <td class="text-end">{{ number_format((float) $product->issue_qty, 2) }}</td>
            <td class="text-end">{{ number_format((float) $product->sold_qty, 2) }}</td>
            <td class="text-end">{{ number_format((float) $product->return_qty, 2) }}</td>
            <td class="text-end">{{ number_format((float) $product->issue_qty - (float) $product->sold_qty + (float) $product->return_qty, 2) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" style="text-align:center;">No product issue found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="section-title">Cash Summary</div>
<div class="summary-grid">
    <div class="summary-item">Sales Paid<br><span class="strong">Tk {{ number_format((float) $report['sales']->sum('paid'), 2) }}</span></div>
    <div class="summary-item">Due Collection<br><span class="strong">Tk {{ number_format((float) $report['collections']->sum('amount'), 2) }}</span></div>
    <div class="summary-item">Extra Cash Issued<br><span class="strong">Tk {{ number_format((float) $report['cashFromManager'], 2) }}</span></div>
    <div class="summary-item">Given Amount<br><span class="strong">Tk {{ number_format((float) $report['givenAmountTotal'], 2) }}</span></div>
    <div class="summary-item">Expense<br><span class="strong">Tk {{ number_format((float) $report['expenses']->sum('amount'), 2) }}</span></div>
    <div class="summary-item">Return Paid<br><span class="strong">Tk {{ number_format((float) $report['returnCashPaid'], 2) }}</span></div>
    <div class="summary-item" style="grid-column: span 3;">
        Cash In Hand (Computed)<br>
        <span class="strong">Tk {{ number_format((float) $report['cashInHand'], 2) }}</span>
    </div>
</div>

<div class="no-print" style="margin-top: 16px; text-align: right;">
    <button type="button" onclick="window.print()">Print Statement</button>
</div>
</body>

</html>


