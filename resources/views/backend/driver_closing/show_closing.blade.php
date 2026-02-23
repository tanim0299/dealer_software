<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Driver Daily Closing Report</title>

<style>
body{
    margin:0;
    font-family: Arial, sans-serif;
    background:#fff;
    color:#000;
}

/* Letterhead */
.letterhead{
    padding:20px 40px;
    border-bottom:2px solid #000;
}

.letterhead h1{
    margin:0;
    font-size:24px;
}

.letterhead p{
    margin:3px 0;
    font-size:13px;
}

.contact{
    float:right;
    text-align:right;
    font-size:13px;
}

.clear{
    clear:both;
}

.container{
    width:90%;
    margin:30px auto;
}

/* Header Info */
.header-info{
    display:flex;
    justify-content:space-between;
    margin-bottom:20px;
    font-weight:bold;
    font-size:14px;
}

/* Section Title */
.section-title{
    margin-top:25px;
    font-weight:bold;
    font-size:15px;
    border-bottom:1px solid #000;
    padding-bottom:4px;
}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
    font-size:13px;
}

th, td{
    border:1px solid #000;
    padding:6px;
}

th{
    text-align:left;
}

td{
    text-align:left;
}

.total-row{
    font-weight:bold;
}

.cash-summary{
    margin-top:20px;
    font-size:14px;
    font-weight:bold;
}

.signature{
    margin-top:60px;
    display:flex;
    justify-content:space-between;
    font-size:13px;
}

.signature div{
    text-align:center;
}

button{
    margin-top:30px;
    padding:8px 25px;
    border:1px solid #000;
    background:#fff;
    cursor:pointer;
}

@media print{
    button{
        display:none;
    }
}
</style>
</head>

<body>

<div class="letterhead">
    <div class="contact">
        Sakhawat Hossen<br>
        Chief Executive Officer<br>
        +880 133-9700077<br>
        Central Jame Masjid, Feni<br>
        sakhawathossen5895@gmail.com
    </div>

    <h1>MEHEDI ENTERPRISE</h1>
    <p>Trust Begins With Quality</p>
    <div class="clear"></div>
</div>

<div class="container">
    <form method="post" action="{{ route('driver_closing.store') }}">
        @csrf
        <input type="hidden" name="driver_id" value="{{ $driver->id }}">
        <input type="hidden" name="date" id="date" value="{{ date('Y-m-d') }}">
    <div class="header-info">
        <div>Driver Name: {{ $driver->name }}</div>
        <div>Date: {{ date('d F Y') }}</div>
    </div>

    <!-- Sales -->
    <div class="section-title">1. Sales Details</div>
    <table>
        <tr>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Invoice Amount</th>
            <th>Paid</th>
        </tr>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->invoice_no ?? '-' }}</td>
            <td>{{ $sale->customer->name }}</td>
            <td>{{ number_format($sale->subtotal,2) }}</td>
            <td>{{ number_format($sale->paid,2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="2">Total</td>
            <td>{{ number_format($sales->sum('subtotal'),2) }}</td>
            <td>{{ number_format($sales->sum('paid'),2) }}</td>
            <input type="hidden" name="cash_sales" value="{{ $sales->sum('paid') }}">
        </tr>
    </table>

    <!-- Collection -->
    <div class="section-title">2. Collection Details</div>
    <table>
        <tr>
            <th>Customer</th>
            <th>Amount</th>
        </tr>
        @foreach($collections as $collection)
        <tr>
            <td>{{ $collection->customer->name }}</td>
            <td>{{ number_format($collection->amount,2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>Total</td>
            <td>{{ number_format($collections->sum('amount'),2) }}</td>
            <input type="hidden" name="total_collection" value="{{ $collections->sum('amount') }}">
        </tr>
    </table>

    <div class="section-title">3. Sales Return</div>
    <table>
        <tr>
            <th>Customer</th>
            <th>Amount</th>
        </tr>
        @foreach($returnpaids as $return)
        <tr>
            <td>{{ $return->customer->name }}</td>
            <td>{{ number_format($return->amount,2) * -1 }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>Total</td>
            <td>{{ number_format($returnpaids->sum('amount'),2) * -1 }}</td>
            <input type="hidden" name="total_return" value="{{ $returnpaids->sum('amount') }}">
        </tr>
    </table>

    <!-- Expense -->
    <div class="section-title">4. Expense Details</div>
    <table>
        <tr>
            <th>Expense Head</th>
            <th>Amount</th>
        </tr>
        @foreach($expenses as $expense)
        <tr>
            <td>{{ $expense->expense->title }}</td>
            <td>{{ number_format($expense->amount,2) }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td>Total</td>
            <td>{{ number_format($expenses->sum('amount'),2) }}</td>
            <input type="hidden" name="total_expense" value="{{ $expenses->sum('amount') }}">
        </tr>
    </table>

    @php
    $returnPaids = $returnpaids->sum('amount') * -1;
    $cashInHand = $sales->sum('paid') + $collections->sum('amount') - $expenses->sum('amount') - $returnPaids ;
    @endphp
    <input type="hidden" name="cash_in_hand" value="{{ $cashInHand }}">
    <div class="cash-summary">
        Cash In Hand = 
        (Total Paid {{ number_format($sales->sum('paid'),2) }} 
        + Collection {{ number_format($collections->sum('amount'),2) }} 
        - Expense {{ number_format($expenses->sum('amount'),2) }}
        - Returns {{ number_format($returnpaids->sum('amount'),2) }}) 
        = {{ number_format($cashInHand,2) }}
    </div>

    <!-- Product Stock -->
    <div class="section-title">4. Product Stock Summary</div>
    <table>
        <tr>
            <th>Product</th>
            <th>Stock Taken</th>
            <th>Sold</th>
            <th>Returned</th>
            <th>Remaining</th>
        </tr>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->product->name }}</td>
            <td>{{ $product->issue_qty }}</td>
            <td>{{ $product->sold_qty }}</td>
            <td>{{ $product->return_qty }}</td>
            <td>{{ $product->issue_qty - $product->sold_qty + $product->return_qty }}</td>
        </tr>
        @endforeach
    </table>

    <!-- Signature -->
    <div class="signature">
        <div>
            ___________________<br>
            Driver Signature
        </div>
        <div>
            ___________________<br>
            Manager Signature
        </div>
    </div>
    @if(empty($closingStatus))
    <button type="submit">Submit Closing</button>
    @else
    <button type="button" onclick="window.print()">Print Now</button>
    @endif

    </form>
</div>

</body>
</html>