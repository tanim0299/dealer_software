<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Return Report</title>
@include('backend.reports.partials.print_style')
</head>
<body>
<div class="container">
    @include('letterhead', ['report_title' => 'Purchase Return Report'])
    <div class="info"><strong>Period:</strong> {{ $period['label'] }}</div>

    <div class="summary-box">
        <div class="summary-item">Return Count<strong>{{ $summary['return_count'] }}</strong></div>
        <div class="summary-item">Return Subtotal<strong>{{ number_format($summary['subtotal'], 2) }}</strong></div>
        <div class="summary-item">Cash Return<strong>{{ number_format($summary['cash_return'], 2) }}</strong></div>
        <div class="summary-item">Due Adjusted<strong>{{ number_format($summary['due_adjusted'], 2) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Supplier</th>
                <th>Type</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                <td>{{ $row->supplier->name ?? '-' }}</td>
                <td>{{ (int)$row->return_type === 1 ? 'Cash Return' : 'Minus From Due' }}</td>
                <td class="text-end">{{ number_format((float) $row->subtotal, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;">No purchase return found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">This is a system generated report.</div>
</div>
</body>
</html>
