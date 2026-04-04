<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Report</title>
@include('backend.reports.partials.print_style')
</head>
<body>
<div class="container">
    @include('letterhead', ['report_title' => 'Purchase Report'])
    <div class="info"><strong>Period:</strong> {{ $period['label'] }}</div>

    <div class="summary-box">
        <div class="summary-item">Invoice Count<strong>{{ $summary['invoice_count'] }}</strong></div>
        <div class="summary-item">Net Purchase<strong>{{ number_format($summary['net_purchase'], 2) }}</strong></div>
        <div class="summary-item">Total Paid<strong>{{ number_format($summary['paid'], 2) }}</strong></div>
        <div class="summary-item">Total Amount<strong>{{ number_format($summary['total_amount'], 2) }}</strong></div>
        <div class="summary-item">Discount<strong>{{ number_format($summary['discount'], 2) }}</strong></div>
        <div class="summary-item">Total Due<strong>{{ number_format($summary['due'], 2) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Supplier</th>
                <th class="text-end">Net</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Due</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            @php $net = ((float)$row->total_amount - (float)$row->discount); @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->purchase_date)->format('d M Y') }}</td>
                <td>{{ $row->invoice_no }}</td>
                <td>{{ $row->supplier->name ?? '-' }}</td>
                <td class="text-end">{{ number_format($net, 2) }}</td>
                <td class="text-end">{{ number_format((float)$row->paid_amount, 2) }}</td>
                <td class="text-end">{{ number_format($net - (float)$row->paid_amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;">No purchases found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">This is a system generated report.</div>
</div>
</body>
</html>

