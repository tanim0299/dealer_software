@extends('backend.layouts.master')

@section('title', 'Sales Return List')

@section('content')
    <div class="container">
        <div class="page-inner">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                @include('backend.layouts.partials.breadcrumb', ['page_title' => 'Sales Return List'])
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('sales_return.index') }}">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="free_text" class="form-control" placeholder="Invoice / Customer"
                                    value="{{ request('free_text') }}">
                            </div>

                            <div class="col-md-3">
                                <input type="date" name="from_date" class="form-control"
                                    value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-3">
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            <div class="col-md-2 d-grid">
                                <button class="btn btn-secondary">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Return Table --}}
            <div class="card mb-4">
                <div class="card-body">
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
                            @forelse($returns as $ledger)
                                @php
                                    $payment = \App\Models\SalesPayment::where('reference_type', 'return')
                                        ->where('reference_id', $ledger->id)
                                        ->first();
                                @endphp

                                <tr>
                                    <td>{{ $ledger->date }}</td>
                                    <td>{{ $ledger->invoice_no }}</td>
                                    <td>{{ $ledger->customer->name ?? '' }}</td>
                                    <td>{{ number_format($ledger->subtotal, 2) }}</td>

                                    <td>
                                        @if ($payment && $payment->amount < 0)
                                            <span class="badge bg-danger">Cash Paid</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Adjusted With Due</span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('sales_return.show', $ledger->id) }}" class="btn btn-sm btn-info">
                                            View
                                        </a>
                                        <form method="POST" action="{{ route('sales_return.destroy', $ledger->id) }}"
                                            onsubmit="return confirm('Are you sure to delete this return?')"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Delete Return</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No Sales Return Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $returns->links() }}
                </div>
            </div>

        </div>
    </div>
@endsection
