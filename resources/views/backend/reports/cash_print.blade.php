<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cash Report</title>
@include('backend.reports.partials.print_style')
</head>
<body>
<div class="container">
    @include('letterhead', ['report_title' => 'Cash Report'])
    <div class="info"><strong>Period:</strong> {{ $period['label'] }}</div>

    <div class="summary-box">
        <div class="summary-item">Total Cash In<strong>{{ number_format($summary['total_in'], 2) }}</strong></div>
        <div class="summary-item">Total Cash Out<strong>{{ number_format($summary['total_out'], 2) }}</strong></div>
        <div class="summary-item">Net Cash<strong>{{ number_format($summary['net_cash'], 2) }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="2">Cash In</th>
                <th colspan="2">Cash Out</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sales Cash</td>
                <td class="text-end">{{ number_format($cashIn['sales_cash'], 2) }}</td>
                <td>Purchase Paid</td>
                <td class="text-end">{{ number_format($cashOut['purchase_paid'], 2) }}</td>
            </tr>
            <tr>
                <td>Due Collection</td>
                <td class="text-end">{{ number_format($cashIn['due_collection'], 2) }}</td>
                <td>Supplier Due Paid</td>
                <td class="text-end">{{ number_format($cashOut['supplier_due_paid'], 2) }}</td>
            </tr>
            <tr>
                <td>Other Income</td>
                <td class="text-end">{{ number_format($cashIn['other_income'], 2) }}</td>
                <td>Expense</td>
                <td class="text-end">{{ number_format($cashOut['expense'], 2) }}</td>
            </tr>
            <tr>
                <td>Purchase Return Cash</td>
                <td class="text-end">{{ number_format($cashIn['purchase_return_cash'], 2) }}</td>
                <td>Sales Return Cash</td>
                <td class="text-end">{{ number_format($cashOut['sales_return_cash'], 2) }}</td>
            </tr>
            <tr>
                <td>Bank Withdraw</td>
                <td class="text-end">{{ number_format($cashIn['bank_withdraw'], 2) }}</td>
                <td>Salary Withdraw</td>
                <td class="text-end">{{ number_format($cashOut['salary_withdraw'], 2) }}</td>
            </tr>
            <tr>
                <td>-</td>
                <td class="text-end">-</td>
                <td>Bank Deposit</td>
                <td class="text-end">{{ number_format($cashOut['bank_deposit'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">This is a system generated report.</div>
</div>
</body>
</html>
