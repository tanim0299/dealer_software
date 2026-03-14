<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Return Report</title>
@include('backend.reports.partials.print_style')
</head>
<body>
<div class="container">
    @include('letterhead', ['report_title' => 'Sales Return Report'])
    <div class="info"><strong>Period:</strong> {{ $period['label'] }}</div>

    <div class="summary-box">
        <div class="summary-item">Return Count<strong>{{ $summary['return_count'] }}</strong></div>
        <div class="summary-item">Return Subtotal<strong>{{ number_format($summary['subtotal'], 2) }}</strong></div>
        <div class="summary-item">Cash Paid<strong>{{ number_format($summary['cash_paid'], 2) }}</strong></div>
        <div class="summary-item">Due Adjusted<strong>{{ number_format($summary['due_adjusted'], 2) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th class="text-end">Subtotal</th>
                <th>Adjustment Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                <td>{{ $row->invoice_no ?? '-' }}</td>
                <td>{{ $row->customer->name ?? '-' }}</td>
                <td class="text-end">{{ number_format((float) $row->subtotal, 2) }}</td>
                <td>{{ (float) $row->subtotal > 0 ? 'Cash/Due Adjustment' : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;">No sales return found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">This is a system generated report.</div>
</div>
</body>
</html>
