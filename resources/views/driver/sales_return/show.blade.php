@extends('driver.layouts.master')

@section('body')

<div class="container">

    <div class="card">
        <div class="card-body">

            <div class="text-center mb-3">
                <h4>Sales Return Invoice</h4>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Return Date:</strong> {{ $return->date }} <br>
                    <strong>Original Invoice:</strong> {{ $return->invoice_no }} <br>
                </div>

                <div class="col-md-6 text-end">
                    <strong>Customer:</strong> {{ $return->customer->name ?? '' }} <br>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Return Qty</th>
                        <th>Sale Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>

                @foreach($return->entries as $entry)

                    <tr>
                        <td>{{ $entry->product->name ?? '' }}</td>
                        <td>{{ $entry->return_qty }}</td>
                        <td>{{ number_format($entry->sale_price, 2) }}</td>
                        <td>
                            {{ number_format($entry->return_qty * $entry->sale_price, 2) }}
                        </td>
                    </tr>

                @endforeach

                </tbody>
            </table>

            <div class="row mt-3">
                <div class="col-md-6">
                    @php
                        $payment = $return->payments->first();
                    @endphp

                    <strong>Adjustment Type:</strong>

                    @if($payment && $payment->amount < 0)
                        Cash Paid
                    @else
                        Adjusted With Due
                    @endif
                </div>

                <div class="col-md-6 text-end">
                    <h5>
                        Total Return Amount:
                        {{ number_format($return->subtotal, 2) }}
                    </h5>
                </div>
            </div>

            <div class="text-center mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    Print Invoice
                </button>
            </div>

        </div>
    </div>

</div>

@endsection
