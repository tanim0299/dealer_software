<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Balance Sheet</title>

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

    <!-- Customer Info -->
    <div class="info-section">
        <div>
            <strong>Customer:</strong> {{ $customer->name ?? 'N/A' }}<br>
            <strong>Customer ID:</strong> {{ $customer->id ?? 'N/A' }}<br>
            <strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}<br>
            <strong>Area:</strong> {{ $customer->customerArea->name ?? 'N/A' }}
        </div>
    </div>

    <!-- Transaction Table -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Description</th>
                <th class="amount">Total Sale</th>
                <th class="amount">Paid</th>
                <th class="amount">Return</th>
                <th class="amount">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php 
            $balance = $previous_balance;
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
                        $saleAmount = 0;
                        $paidAmount = 0;
                        $returnAmount = 0;

                        if($item->type == 0) {
                            $saleAmount = ($item->sale->subtotal ?? 0) - ($item->sale->discount ?? 0);
                            $paidAmount = $item->amount;
                            $balance += ($saleAmount - $paidAmount);
                        } elseif($item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE) {
                            $saleAmount = $item->amount;
                            $balance += $saleAmount;
                        } elseif($item->type == 1) {
                            $paidAmount = $item->amount;
                            $balance -= $paidAmount;
                        } elseif($item->type == 2) {
                            if($item->amount < 0) {
                                // Cash paid back to customer: due increases
                                $paidAmount = $item->amount * -1;
                                $balance += $paidAmount;
                            } else {
                                // Adjusted with due: due decreases
                                $returnAmount = $item->returnLedger->subtotal ?? 0;
                                $balance -= $returnAmount;
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                        <td>
                            @if($item->type == 0)
                                Sale
                            @elseif($item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE)
                                Previous Due
                            @elseif($item->type == 1)
                                Payment
                            @elseif($item->type == 2)
                                Return
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($item->type == 0)
                            <a href="{{ route('sales.invoice', $item->ledger_id) }}" target="_blank">
                                {{ $item->sale->invoice_no ?? 'N/A' }}
                            </a>
                            @elseif($item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE)
                            Opening Due
                            @elseif($item->type == 2)
                            Sales Return #{{ $item->reference_id ?? 'N/A' }}
                            @else
                            {{ $item->note ?? 'N/A' }} 
                            @endif
                        </td>
                        <td>
                            @if($item->type == 0)
                                @if(!empty($item->sale) && count($item->sale->items) > 0)
                                    @foreach($item->sale->items as $entry)
                                        {{ $entry->product->name ?? 'N/A' }} - {{ $entry->final_quantity }} {{ $entry->subUnit->name ?? '' }} × {{ number_format($entry->sale_price, 2) }} - Dis: {{ number_format($entry->discount, 2) }} = {{ number_format(($entry->final_quantity * $entry->sale_price) - $entry->discount, 2) }}<br>
                                    @endforeach
                                @else
                                    Sale Transaction
                                @endif
                            @elseif($item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE)
                                Previous due brought forward
                            @elseif($item->type == 1)
                                Payment Received
                            @elseif($item->type == 2)
                                @if(!empty($item->returnLedger) && count($item->returnLedger->entries) > 0)
                                    @foreach($item->returnLedger->entries as $entry)
                                        {{ $entry->product->name ?? 'N/A' }} - Return Qty: {{ $entry->return_qty }} × {{ number_format($entry->sale_price, 2) }} = {{ number_format($entry->return_qty * $entry->sale_price, 2) }}<br>
                                    @endforeach
                                @else
                                    Sales Return
                                @endif
                            @endif
                        </td>
                        <td class="amount">
                            @if($item->type == 0 || $item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE)
                                {{ number_format($saleAmount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="amount">
                            @if($item->type == 0 || $item->type == 1)
                                {{ number_format($paidAmount, 2) }}
                            @elseif($item->type == 2 && $item->amount < 0)
                                ({{ number_format($paidAmount, 2) }})
                            @else
                                -
                            @endif
                        </td>
                        <td class="amount">
                            @if($item->type == 2 && $item->amount >= 0)
                                {{ number_format($returnAmount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="amount">
                            {{ number_format($balance, 2) }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" style="text-align: center;">No transactions found for the selected period.</td>
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
