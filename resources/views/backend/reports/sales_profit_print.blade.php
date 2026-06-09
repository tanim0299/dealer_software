<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Profit Report</title>
@include('backend.reports.partials.print_style')
</head>
<body>
<div class="container">
    @include('letterhead', ['report_title' => 'Sales Profit Report'])
    <div class="info"><strong>Period:</strong> {{ $period['label'] }}</div>

    <div class="summary-box">
        <div class="summary-item">Line Count<strong>{{ $summary['line_count'] }}</strong></div>
        <div class="summary-item">Total Sales<strong>{{ number_format($summary['sales_amount'], 2) }}</strong></div>
        <div class="summary-item">Total Profit<strong>{{ number_format($summary['profit'], 2) }}</strong></div>
        <div class="summary-item">Sale Qty<strong>{{ number_format($summary['quantity'], 2) }}</strong></div>
        <div class="summary-item">Base Qty<strong>{{ number_format($summary['final_quantity'], 2) }}</strong></div>
        <div class="summary-item">Total Purchase Cost<strong>{{ number_format($summary['purchase_amount'], 2) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Driver</th>
                <th>Product</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Base Qty</th>
                <th class="text-end">Purchase Price</th>
                <th class="text-end">Sales Price</th>
                <th class="text-end">Discount</th>
                <th class="text-end">Purchase Cost</th>
                <th class="text-end">Sales Amount</th>
                <th class="text-end">Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                <td>{{ $row->invoice_no }}</td>
                <td>{{ $row->customer_name ?? '-' }}</td>
                <td>{{ $row->driver_name ?? '-' }}</td>
                <td>{{ $row->product_name }}</td>
                <td class="text-end">{{ number_format((float) $row->quantity, 2) }}</td>
                <td class="text-end">{{ number_format((float) $row->final_quantity, 2) }}</td>
                <td class="text-end">{{ number_format((float) $row->purchase_price, 2) }}</td>
                <td class="text-end">{{ number_format((float) $row->sale_price, 2) }}</td>
                <td class="text-end">{{ number_format((float) $row->discount, 2) }}</td>
                <td class="text-end">{{ number_format((float) $row->purchase_amount, 2) }}</td>
                <td class="text-end">{{ number_format((float) $row->sales_amount, 2) }}</td>
                <td class="text-end">{{ number_format((float) $row->profit, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="13" style="text-align:center;">No sales profit data found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">This is a system generated report.</div>
</div>
</body>
</html>
