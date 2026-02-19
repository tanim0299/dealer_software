@extends('driver.layouts.master')

@section('body')

<div class="container">
    <h4>Sales Return</h4>

    {{-- Invoice Selection --}}
    <form method="GET" action="{{ route('sales_return.create') }}">
        <div class="row mb-3">
            <div class="col-md-4">
                <label>Invoice No</label>
                <select name="invoice_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Select Invoice</option>
                    @foreach($sales_ledgers as $ledger)
                        <option value="{{ $ledger->id }}"
                            {{ request('invoice_id') == $ledger->id ? 'selected' : '' }}>
                            {{ $ledger->invoice_no }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    @if(isset($selectedLedger))

    <form method="POST" action="{{ route('sales_return.store') }}">
        @csrf

        <input type="hidden" name="sales_ledger_id" value="{{ $selectedLedger->id }}">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Sold Qty</th>
                    <th>Already Returned</th>
                    <th>Return Qty</th>
                </tr>
            </thead>
            <tbody>

            @foreach($selectedLedger->items as $entry)

                @php
                    $returnedQty = $entry->return_entries_sum_return_qty ?? 0;
                    $remainingQty = $entry->quantity - $returnedQty;
                @endphp

                <tr>
                    <td>{{ $entry->product->name }}</td>
                    <td>{{ $entry->quantity }}</td>
                    <td>{{ $returnedQty }}</td>
                    <td>
                        <input type="number"
                               name="items[{{ $entry->id }}]"
                               max="{{ $remainingQty }}"
                               min="0"
                               step="0.01"
                               class="form-control">
                    </td>
                </tr>

            @endforeach

            </tbody>
        </table>

        <div class="row mt-3">
            <div class="col-md-4">
                <label>Return Adjustment Type</label>
                <select name="adjustment_type" class="form-control" required>
                    <option value="">Select Option</option>
                    <option value="due">Minus From Due</option>
                    <option value="cash">Cash Paid</option>
                </select>
            </div>
        </div>


        <button type="submit" class="btn btn-danger">
            Submit Return
        </button>
    </form>

    @endif
</div>

@endsection
