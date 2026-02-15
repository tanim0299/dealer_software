@extends('backend.layouts.master')

@section('title', 'Sales List')

@section('content')
    <div class="container">
        <div class="page-inner">

            {{-- Header --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                @include('backend.layouts.partials.breadcrumb', ['page_title' => 'Sales List'])
            </div>

            {{-- Filters --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('sales.index') }}">
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

            {{-- Sales List --}}
            <div class="row">
                @forelse($sales as $ledger)
                    <div class="col-12 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body d-flex flex-column flex-md-row justify-content-between">

                                {{-- Left Info --}}
                                <div class="mb-3 mb-md-0">
                                    <h6 class="text-primary fw-bold mb-1">
                                        Invoice: {{ $ledger->invoice_no }}
                                    </h6>
                                    <p class="mb-0 text-muted">
                                        Date: {{ $ledger->date }}
                                    </p>
                                    <p class="mb-0 text-muted">
                                        Customer: {{ $ledger->customer->name ?? 'N/A' }}
                                    </p>
                                </div>

                                {{-- Amounts --}}
                                <div class="mb-3 mb-md-0 text-md-center">
                                    <div>Subtotal: <strong>{{ number_format($ledger->subtotal, 2) }}</strong></div>
                                    <div>Discount: <strong>{{ number_format($ledger->discount, 2) }}</strong></div>
                                    <div>Paid: <strong>{{ number_format($ledger->paid, 2) }}</strong></div>
                                    <div>
                                        Balance:
                                        <strong class="text-primary">
                                            {{ number_format($ledger->subtotal - $ledger->discount - $ledger->paid, 2) }}
                                        </strong>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ url('sales_invoice/' . $ledger->id) }}" class="btn btn-success btn-sm">
                                        View
                                    </a>

                                    <form action="{{ route('sales.destroy', $ledger->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure to delete this sale?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            No sales found.
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $sales->links() }}
            </div>

        </div>
    </div>
@endsection
