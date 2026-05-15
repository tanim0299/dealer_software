@extends('backend.layouts.master')
@section('title', 'Supplier Due List')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb', ['page_title' => 'Supplier Due List'])
            <div class="ms-md-auto py-2 py-md-0 d-flex flex-wrap gap-2">
                @if(auth()->user()->can('Supplier Payment create'))
                    <a href="{{ route('supplier_payment.create') }}" class="btn btn-primary btn-round">
                        <i class="fa fa-plus"></i> New payment
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h4 class="card-title mb-0">Suppliers with outstanding due</h4>
                        <span class="badge badge-warning">
                            {{ $suppliers->count() }} supplier(s)
                        </span>
                    </div>

                    <div class="card-body">
                        <form method="GET" action="{{ route('supplier_due_list.index') }}" class="mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Search</label>
                                    <input type="text"
                                           name="search"
                                           class="form-control"
                                           placeholder="Name, phone, email, or supplier ID"
                                           value="{{ data_get($search ?? [], 'search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">From date</label>
                                    <input type="date"
                                           name="from_date"
                                           class="form-control"
                                           value="{{ data_get($search ?? [], 'from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">To date</label>
                                    <input type="date"
                                           name="to_date"
                                           class="form-control"
                                           value="{{ data_get($search ?? [], 'to_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check mt-4 pt-1">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="include_zero"
                                               id="include_zero"
                                               value="1"
                                               {{ data_get($search ?? [], 'include_zero') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="include_zero">
                                            Show zero due
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex flex-wrap gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="fa fa-filter"></i> Apply
                                    </button>
                                    <a href="{{ route('supplier_due_list.index') }}" class="btn btn-outline-secondary" title="Reset filters">
                                        <i class="fa fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>

                        @if(!empty($use_date_range))
                            <div class="alert alert-info small mb-4" role="alert">
                                <strong>Date range active:</strong> Due amounts use only purchases, payments, returns, and opening entries dated between the selected dates (not lifetime closing balance).
                            </div>
                        @endif

                        @if(request()->filled('search'))
                            <div class="alert alert-light border mb-4 py-2 small">
                                Search: <strong>{{ request('search') }}</strong>
                            </div>
                        @endif

                        @php $total_due = $suppliers->sum('due'); @endphp

                        @if($suppliers->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="fa fa-check-circle text-success" style="font-size: 2.5rem;"></i>
                                <p class="mt-3 mb-1"><strong>No matching suppliers with due.</strong></p>
                                <p class="small mb-0">Try clearing search, widening the date range, or enable “Show zero due”.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:3rem;">SL</th>
                                            <th>Supplier</th>
                                            <th>Code</th>
                                            <th>Contact</th>
                                            <th class="text-end">Due (Tk)</th>
                                            <th style="width:12rem;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($suppliers as $key => $supplier)
                                            <tr>
                                                <td><strong>{{ $key + 1 }}</strong></td>
                                                <td><strong>{{ $supplier['name'] }}</strong></td>
                                                <td>
                                                    @if(!empty($supplier['supplier_id']))
                                                        <span class="badge badge-secondary">{{ $supplier['supplier_id'] }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted d-block">{{ $supplier['phone'] ?? '—' }}</small>
                                                    @if(!empty($supplier['email']))
                                                        <small class="text-muted">{{ $supplier['email'] }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge badge-danger badge-lg">
                                                        {{ number_format($supplier['due'], 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if(auth()->user()->can('Supplier Payment create'))
                                                            <a href="{{ route('supplier_payment.create', ['supplier_id' => $supplier['id']]) }}"
                                                               class="btn btn-success"
                                                               title="Pay">
                                                                <i class="fa fa-money"></i> Pay
                                                            </a>
                                                        @endif
                                                        @if(auth()->user()->can('Supplier Payment view'))
                                                            <a href="{{ route('supplier_payment.index', ['supplier_id' => $supplier['id']]) }}"
                                                               class="btn btn-outline-primary"
                                                               title="Payments">
                                                                <i class="fa fa-list"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="card bg-light border-0 mt-4">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="mb-0 text-muted">Total outstanding (this list)</h5>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <h3 class="text-danger mb-0">
                                                <strong>{{ number_format($total_due, 2) }}</strong>
                                                <small class="text-muted fs-6">Tk</small>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
