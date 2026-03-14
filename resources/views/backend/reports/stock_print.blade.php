<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Stock Movement Report</title>
@include('backend.reports.partials.print_style')
</head>
<body>
<div class="container">
    @include('letterhead', ['report_title' => 'Stock Movement Report'])
    <div class="info"><strong>Period:</strong> {{ $period['label'] }}</div>

    <div class="summary-box">
        <div class="summary-item">Opening<strong>{{ number_format($summary['opening'], 2) }}</strong></div>
        <div class="summary-item">Purchase In<strong>{{ number_format($summary['purchase_in'], 2) }}</strong></div>
        <div class="summary-item">Sales Out<strong>{{ number_format($summary['sales_out'], 2) }}</strong></div>
        <div class="summary-item">Sales Return In<strong>{{ number_format($summary['sales_return_in'], 2) }}</strong></div>
        <div class="summary-item">Purchase Return Out<strong>{{ number_format($summary['purchase_return_out'], 2) }}</strong></div>
        <div class="summary-item">Closing<strong>{{ number_format($summary['closing'], 2) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-end">Opening</th>
                <th class="text-end">Purchase In</th>
                <th class="text-end">Sales Out</th>
                <th class="text-end">Sales Return In</th>
                <th class="text-end">Purchase Return Out</th>
                <th class="text-end">Closing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row['product_name'] }}</td>
                <td class="text-end">{{ number_format($row['opening'], 2) }}</td>
                <td class="text-end">{{ number_format($row['purchase_in'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sales_out'], 2) }}</td>
                <td class="text-end">{{ number_format($row['sales_return_in'], 2) }}</td>
                <td class="text-end">{{ number_format($row['purchase_return_out'], 2) }}</td>
                <td class="text-end">{{ number_format($row['closing'], 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;">No stock movement found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">This is a system generated report.</div>
</div>
</body>
</html>
