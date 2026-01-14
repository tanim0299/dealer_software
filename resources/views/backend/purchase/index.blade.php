@extends('backend.layouts.master')
@section('title','Purchase List')

@section('content')
<div class="container">
    <div class="page-inner">

        <!-- PAGE HEADER -->
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Purchase List'])
            <div class="ms-md-auto py-2 py-md-0">
                @can('Purchase create')
                    <a href="{{ route('purchase.create') }}" class="btn btn-primary btn-round">
                        <i class="fa fa-plus"></i> Create Purchase
                    </a>
                @endcan
            </div>
        </div>

        <!-- FILTER -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('purchase.index') }}">
                    <div class="row g-2">

                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="free_text" class="form-control"
                                   placeholder="Invoice / Supplier"
                                   value="{{ request('free_text') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control"
                                   value="{{ request('from_date') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control"
                                   value="{{ request('to_date') }}">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary me-2" type="submit">
                                <i class="fa fa-search"></i> Filter
                            </button>
                            <a href="{{ route('purchase.index') }}" class="btn btn-secondary">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- PURCHASE TABLE -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Invoice No</th>
                                <th>Supplier</th>
                                <th>Purchase Date</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Due</th>
                                <th>Status</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($purchases as $key => $purchase)
                                <tr>
                                    <td>{{ $purchases->firstItem() + $key }}</td>
                                    <td>{{ $purchase->invoice_no }}</td>
                                    <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                    <td>{{ $purchase->purchase_date }}</td>
                                    <td class="text-end">{{ number_format($purchase->total_amount, 2) }}</td>
                                    <td class="text-end">{{ number_format($purchase->paid, 2) }}</td>
                                    <td class="text-end">
                                        {{ number_format(($purchase->total_amount - $purchase->discount) - $purchase->paid, 2) }}
                                    </td>
                                    <td>
                                        @if(($purchase->total_amount - $purchase->discount) - $purchase->paid == 0)
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Due</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('purchase.invoice', $purchase->id) }}"
                                           class="btn btn-sm btn-info" target="_blank">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        @can('Purchase edit')
                                        <a href="{{ route('purchase.edit', $purchase->id) }}"
                                           class="btn btn-sm btn-warning" target="">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        @endcan

                                        @can('Purchase delete')
                                            <a href="{{ route('purchase.destroy', $purchase->id) }}"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        No purchase records found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

                <!-- PAGINATION -->
                <div class="mt-3">
                    {{ $purchases->withQueryString()->links() }}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
