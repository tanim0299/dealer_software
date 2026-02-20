@extends('driver.layouts.master')

@section('body')

<div class="container py-3">

    <h5 class="mb-3 text-center">Customer Payments</h5>

    {{-- 🔹 Filter Card --}}
    <div class="card mb-3">
        <div class="card-body">

            <form method="GET" action="{{ route('customer_payment.index') }}">

                <div class="mb-2">
                    <select name="customer_id"
                            class="form-control js-example-basic-single">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-2">
                    <input type="number" step="0.01"
                           name="amount"
                           value="{{ request('amount') }}"
                           placeholder="Filter by Amount"
                           class="form-control">
                </div>

                <div class="row">
                    <div class="col-6">
                        <input type="date"
                               name="from_date"
                               value="{{ request('from_date') }}"
                               class="form-control">
                    </div>
                    <div class="col-6">
                        <input type="date"
                               name="to_date"
                               value="{{ request('to_date') }}"
                               class="form-control">
                    </div>
                </div>

                <div class="d-grid mt-2">
                    <button class="btn btn-primary btn-sm">
                        Filter
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- 🔹 Payment List --}}
    @foreach($payments as $payment)

        <div class="card mb-2 shadow-sm">
            <div class="card-body p-2">

                <div class="d-flex justify-content-between">
                    <strong>{{ $payment->customer->name ?? '' }}</strong>
                    <span class="text-success">
                        {{ number_format($payment->amount,2) }}
                    </span>
                </div>

                <div class="small text-muted">
                    Date: {{ $payment->date }}
                </div>

                <div class="small">
                    {{ $payment->note }}
                </div>

                <div class="text-end mt-1">
                    <form method="POST"
                          action="{{ route('customer_payment.destroy',$payment->id) }}"
                          onsubmit="return confirm('Delete this payment?')">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            Delete
                        </button>
                    </form>
                </div>

            </div>
        </div>

    @endforeach

    {{ $payments->links() }}

</div>

@endsection
