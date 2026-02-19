@extends('driver.layouts.master')

@section('body')

<div class="container">

    <h4 class="mb-3">Sales Return List</h4>

    {{-- 🔹 Date Filter --}}
    <form method="GET" action="{{ route('sales_return.index') }}">
        <div class="row mb-3">

            <div class="col-md-3">
                <label>From Date</label>
                <input type="date" name="from_date"
                       value="{{ request('from_date') }}"
                       class="form-control">
            </div>

            <div class="col-md-3">
                <label>To Date</label>
                <input type="date" name="to_date"
                       value="{{ request('to_date') }}"
                       class="form-control">
            </div>

            <div class="col-md-3 mt-4">
                <button class="btn btn-primary">
                    Search
                </button>

                <a href="{{ route('sales_return.index') }}"
                   class="btn btn-secondary">
                    Reset
                </a>
            </div>

        </div>
    </form>

    {{-- 🔹 Return Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Adjustment Type</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        @forelse($returns as $return)

            @php
                $payment = \App\Models\SalesPayment::where('reference_type', 'return')
                    ->where('reference_id', $return->id)
                    ->first();
            @endphp

            <tr>
                <td>{{ $return->date }}</td>
                <td>{{ $return->invoice_no }}</td>
                <td>{{ $return->customer->name ?? '' }}</td>
                <td>{{ number_format($return->subtotal, 2) }}</td>

                <td>
                    @if($payment && $payment->amount < 0)
                        <span class="badge bg-danger">
                            Cash Paid
                        </span>
                    @else
                        <span class="badge bg-warning text-dark">
                            Adjusted With Due
                        </span>
                    @endif
                </td>

                <td>
                    <a href="{{ route('sales_return.show', $return->id) }}"
                       class="btn btn-sm btn-info">
                        View
                    </a>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="6" class="text-center">
                    No Sales Return Found
                </td>
            </tr>
        @endforelse

        </tbody>
    </table>

    {{ $returns->links() }}

</div>

@endsection
