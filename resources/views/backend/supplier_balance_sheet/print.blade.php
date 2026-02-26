<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Supplier Balance Sheet</title>

<style>
body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #fff;
    color: #000;
}

.container {
    width: 100%;
    padding: 20px 50px;
    box-sizing: border-box;
}

/* Letterhead */
.letterhead {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    border-bottom: 1px solid #000;
    padding-bottom: 8px;
}

.logo {
    width: 100px;
}

.logo img {
    max-width: 100%;
}

.company-section {
    flex: 1;
    text-align: center;
}

.company-section h2 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
}

.company-section p {
    margin: 2px 0;
    font-size: 12px;
}

/* Report title inside letterhead */
.report-title {
    margin-top: 6px;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
}

.print-btn button {
    font-size: 12px;
    padding: 4px 8px;
    cursor: pointer;
}

/* Hide print button while printing */
@media print {
    .print-btn {
        display: none;
    }
}

/* Info Section */
.info-section {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
    font-size: 12px;
}

/* Summary */
.summary {
    margin-top: 10px;
    font-size: 12px;
}

.summary p {
    margin: 2px 0;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 12px;
}

th, td {
    border: 1px solid #000;
    padding: 6px;
}

th {
    text-align: left;
    font-weight: bold;
}

.amount {
    text-align: right;
}

.footer {
    margin-top: 25px;
    font-size: 11px;
}
</style>
</head>

<body>

<div class="container">

    <!-- Letterhead -->
    @include('letterhead', ['report_title' => $report_title])                                                             

    <!-- Supplier Info -->
    <div class="info-section">
        <div>
            <strong>Supplier:</strong> {{ $supplier->name ?? 'N/A' }}<br>
            <strong>Supplier ID:</strong> {{ $supplier->supplier_id ?? 'N/A' }}
        </div>
    </div>

    <!-- Summary -->
    <!-- <div class="summary">
        <p><strong>Total Purchases:</strong> 15,000</p>
        <p><strong>Total Payments:</strong> 10,000</p>
        <p><strong>Outstanding Balance:</strong> 5,000</p>
    </div> -->

    <!-- Transaction Table -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Description</th>
                <th class="amount">Total</th>
                <th class="amount">Paid</th>
                <th class="amount">Return</th>
                <th class="amount">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php 
            $balance = 0;
            @endphp
            <tr>
                <td colspan="7"> 
                    Previous Balance as of {{ \Carbon\Carbon::parse($previous_date)->format('d M Y') }}: 
                    
                </td>
                <td>
                    <strong>{{ number_format($previous_balance, 2) }}</strong>
                </td>
            </tr>
            @if(count($items) > 0)
                @foreach($items as $item)
                    @php
                        if($item->type == 1) {
                            // Purchase
                            $balance += ($item->purchase->total_amount - $item->purchase->discount) - $item->amount;
                        } elseif($item->type == 2) {
                            // Payment
                            $balance -= $item->amount;
                        } elseif($item->type == 3) {
                            // Purchase Return
                            if($item->amount < 0) {
                                // Cash return
                                $balance -= $item->amount; // Subtract the negative amount
                            } else {
                                // Credit return
                                $balance -= $item->return->subtotal; // Subtract the return subtotal
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->payment_date)->format('d M Y') }}</td>
                        <td>
                            @if($item->type == 1)
                                Purchase
                            @elseif($item->type == 2)
                                Payment
                            @elseif($item->type == 3)
                                Purchase Return
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($item->type == 1)
                            <a href="{{ route('purchase.invoice', $item->purchase->id) }}" target="_blank">
                                {{ $item->purchase ? $item->purchase->invoice_no : 'N/A' }}
                            </a>
                            @elseif($item->type == 2)
                            <a href="{{ route('supplier_payment.show', $item->id) }}" target="_blank">
                                {{ $item->note ?? 'N/A' }} 
                            </a>
                            @elseif($item->type == 3)
                            Purchase Return 

                            @if($item->amount < 0)
                            (Cash Paid)
                            @else
                            (Credit Return)
                            @endif
                            
                            @endif
                        </td>
                        <td>
                            @if($item->type == 1)
                                @forelse($item->purchase->entries as $entry)
                                    {{ $entry->product ? $entry->product->name : 'N/A' }} - {{ $entry->final_quantity }} {{ $entry->sub_unit->name ?? 'N/A' }} * {{ $entry->unit_price }} - Dis : {{ $entry->discount }} = {{ ($entry->final_quantity * $entry->unit_price) - $entry->discount }}<br>
                                 @empty
                                    N/A
                                @endforelse
                            @endif
                        </td>
                        <td>
                            @if($item->type == 1)
                                {{ number_format($item->purchase->total_amount - $item->purchase->discount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->amount > 0)
                                {{ number_format($item->amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->type == 3)
                                @if($item->amount < 0)
                                {{ number_format($item->amount * -1, 2) }}
                                @else
                                ( {{ number_format($item->return->subtotal, 2) }} )
                                @endif
                            @endif
                        </td>
                        <td>
                            {{ number_format($balance, 2) }}
                        </td>
                @endforeach
            @else
                <tr>
                    <td colspan="7" style="text-align: center;">No transactions found for the selected period.</td>
                </tr>
            @endif
                    
        </tbody>
    </table>

    <div class="footer">
        This is a system generated balance sheet.
    </div>

</div>

</body>
</html>