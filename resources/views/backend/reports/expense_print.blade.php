<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Expense Report</title>
@include('backend.reports.partials.print_style')
</head>
<body>
<div class="container">
    @include('letterhead', ['report_title' => 'Expense Report'])
    <div class="info"><strong>Period:</strong> {{ $period['label'] }}</div>

    <div class="summary-box">
        <div class="summary-item">Entry Count<strong>{{ $summary['entry_count'] }}</strong></div>
        <div class="summary-item">Total Expense<strong>{{ number_format($summary['total'], 2) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Expense Head</th>
                <th>Note</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                <td>{{ $row->expense->title ?? '-' }}</td>
                <td>{{ $row->note ?? '-' }}</td>
                <td class="text-end">{{ number_format((float) $row->amount, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;">No expense entry found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">This is a system generated report.</div>
</div>
</body>
</html>
