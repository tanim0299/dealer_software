@extends('backend.layouts.master')
@section('title', 'List Of Customer Payment')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- Header --}}
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb', ['page_title' => 'List Of Customer Payment'])

            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{ route('customer_payment.create') }}" class="btn btn-primary btn-round">
                    <i class="fa fa-plus"></i> Create Customer Payment
                </a>
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">

                    {{-- 🔹 Filter (driver page logic) --}}
                    <form method="GET" action="{{ route('customer_payment.index') }}">
                        <div class="row mb-3">

                            <div class="col-lg-3 col-md-4">
                                <select name="customer_id" class="form-control js-example-basic-single">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <input type="number" step="0.01"
                                       name="amount"
                                       value="{{ request('amount') }}"
                                       placeholder="Amount"
                                       class="form-control">
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <input type="date"
                                       name="from_date"
                                       value="{{ request('from_date') }}"
                                       class="form-control">
                            </div>

                            <div class="col-lg-2 col-md-4">
                                <input type="date"
                                       name="to_date"
                                       value="{{ request('to_date') }}"
                                       class="form-control">
                            </div>

                            <div class="col-lg-3 col-md-4">
                                <button class="btn btn-primary w-100">
                                    <i class="fa fa-filter"></i> Filter
                                </button>
                            </div>

                        </div>
                    </form>

                    {{-- 🔹 Table (UI unchanged) --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">SL</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Note</th>
                                    <th>Amount</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $key => $payment)
                                    <tr>
                                        <td>{{ $payments->firstItem() + $key }}</td>
                                        <td>{{ $payment->date }}</td>
                                        <td>{{ $payment->customer->name ?? 'N/A' }}</td>
                                        <td>{{ $payment->note }}</td>
                                        <td>{{ number_format($payment->amount,2) }}</td>
                                        <td>
                                            <form method="POST"
                                                  action="{{ route('customer_payment.destroy', $payment->id) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this payment?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            No Customer Payment Found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{ $payments->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
