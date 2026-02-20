@extends('backend.layouts.master')
@section('title','Supplier Payment List')

@section('content')
<div class="container">
    <div class="page-inner">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Supplier Payment List</h4>

            <a href="{{ route('supplier_payment.create') }}"
            class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Create New
            </a>
        </div>

      <br>

        {{-- FILTER SECTION --}}
        <form method="GET" class="card p-3 mb-3">
            <div class="row g-2">

                <div class="col-md-2">
                    <input type="date" name="from_date"
                        value="{{ request('from_date') }}"
                        class="form-control">
                </div>

                <div class="col-md-2">
                    <input type="date" name="to_date"
                        value="{{ request('to_date') }}"
                        class="form-control">
                </div>

                <div class="col-md-3">
                    <select name="supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="number" step="0.01" name="min_amount"
                        value="{{ request('min_amount') }}"
                        class="form-control"
                        placeholder="Min Amount">
                </div>

                <div class="col-md-2">
                    <input type="number" step="0.01" name="max_amount"
                        value="{{ request('max_amount') }}"
                        class="form-control"
                        placeholder="Max Amount">
                </div>

                <div class="col-md-1">
                    <button class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>

            </div>
        </form>

        {{-- TABLE --}}
        <div class="card">
            <div class="card-body table-responsive">

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->payment_date }}</td>
                                <td>{{ $payment->supplier->name }}</td>
                                <td>{{ number_format($payment->amount,2) }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ $payment->note }}</td>
                                <td>
                                    <form action="{{ route('supplier_payment.destroy', $payment->id) }}"
                                        method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Are you sure to delete this payment?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    No payments found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $payments->links() }}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection