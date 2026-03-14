<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Report</title>
@include('backend.reports.partials.print_style')
</head>
<body>
<div class="container">
    @include('letterhead', ['report_title' => 'Sales Report'])
    <div class="info"><strong>Period:</strong> {{ $period['label'] }}</div>

    <div class="summary-box">
        <div class="summary-item">Invoice Count<strong>{{ $summary['invoice_count'] }}</strong></div>
        <div class="summary-item">Net Sales<strong>{{ number_format($summary['net_sales'], 2) }}</strong></div>
        <div class="summary-item">Total Paid<strong>{{ number_format($summary['paid'], 2) }}</strong></div>
        <div class="summary-item">Subtotal<strong>{{ number_format($summary['subtotal'], 2) }}</strong></div>
        <div class="summary-item">Discount<strong>{{ number_format($summary['discount'], 2) }}</strong></div>
        <div class="summary-item">Total Due<strong>{{ number_format($summary['due'], 2) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Driver</th>
                <th class="text-end">Net</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Due</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            @php $net = ((float)$row->subtotal - (float)$row->discount); @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                <td>{{ $row->invoice_no }}</td>
                <td>{{ $row->customer->name ?? '-' }}</td>
                <td>{{ $row->driver->name ?? '-' }}</td>
                <td class="text-end">{{ number_format($net, 2) }}</td>
                <td class="text-end">{{ number_format((float)$row->paid, 2) }}</td>
                <td class="text-end">{{ number_format($net - (float)$row->paid, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-end" style="text-align:center;">No sales found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">This is a system generated report.</div>
</div>
</body>
</html>
