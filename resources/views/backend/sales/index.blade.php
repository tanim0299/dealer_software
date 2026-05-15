@extends('backend.layouts.master')

@section('title', 'Sales')

@section('content')
    <style>
        .admin-sales-page .admin-sale-filter .card-header-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(103, 126, 234, .15) 0%, rgba(118, 75, 162, .12) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5e4caf;
        }

        .admin-sales-page .admin-sale-card {
            border-radius: 14px;
            overflow: hidden;
            transition: box-shadow .18s ease;
        }

        .admin-sales-page .admin-sale-card:hover {
            box-shadow: 0 .35rem 1.25rem rgba(34, 41, 47, .1) !important;
        }

        .admin-sales-page .admin-sale-card .admin-sale-accent {
            width: 5px;
            flex-shrink: 0;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 52%, #0d9488 100%);
            border-radius: 5px 0 0 5px;
            align-self: stretch;
            min-height: 100%;
        }

        .admin-sales-page .admin-sale-card .admin-sale-lead {
            flex: 1;
            min-width: 0;
            padding: 1rem 1.25rem;
        }

        .admin-sales-page .admin-sale-invoice {
            font-size: 1.05rem;
            font-weight: 700;
            color: #546de5;
            letter-spacing: -.02em;
        }

        .admin-sales-page .admin-sale-meta {
            font-size: .875rem;
            color: #6c757d;
        }

        .admin-sales-page .admin-sale-meta i {
            width: 1rem;
            margin-right: .35rem;
            opacity: .85;
        }

        .admin-sales-page .admin-sale-metrics .metric-pill {
            background: #f8f9fb;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: .5rem .65rem;
            text-align: center;
            height: 100%;
        }

        .admin-sales-page .admin-sale-metrics .metric-pill .label {
            display: block;
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #868e96;
            margin-bottom: .2rem;
        }

        .admin-sales-page .admin-sale-metrics .metric-pill .value {
            font-size: .9rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: #212529;
        }

        .admin-sales-page .admin-sale-metrics .metric-pill--balance .value {
            color: #546de5;
        }

        .admin-sales-page .admin-sale-actions .btn-icon-round {
            width: 42px;
            height: 42px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px !important;
            font-size: 1rem;
        }

        .admin-sales-page .admin-sale-actions .btn-icon-round.btn-success {
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
            border: none;
        }

        .admin-sales-page .admin-sale-actions .btn-icon-round.btn-danger {
            background: #fff;
            border: 2px solid rgba(220, 53, 69, .45);
            color: #dc3545 !important;
        }

        .admin-sales-page .admin-sale-actions .btn-icon-round.btn-danger:hover {
            background: #fff5f5;
        }

        @media (min-width: 992px) {
            .admin-sales-page .admin-sale-metrics {
                border-top: none !important;
                border-left: 1px solid #e9ecef !important;
            }

            .admin-sales-page .admin-sale-actions {
                border-top: none !important;
                align-self: center;
            }
        }

        @media (max-width: 991.98px) {
            .admin-sales-page .admin-sale-metrics {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .admin-sales-page .admin-sale-actions {
                justify-content: flex-start !important;
            }
        }

        .admin-sales-page .admin-sale-empty .empty-icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #f1f3f5;
            color: #868e96;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
    </style>

    <div class="container admin-sales-page">
        <div class="page-inner pb-5">

            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
                @include('backend.layouts.partials.breadcrumb', ['page_title' => 'Sales List'])
            </div>

            {{-- Filters --}}
            <div class="card admin-sale-filter border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center gap-2">
                    <div class="card-header-icon">
                        <i class="fas fa-filter"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Search &amp; filters</h6>
                        <small class="text-muted">Invoice, customer name, or date range</small>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('sales.index') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-semibold text-uppercase"
                                    style="font-size:.7rem;letter-spacing:.06em;">Invoice / Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="free_text" class="form-control border-start-0"
                                        placeholder="Type to search..."
                                        value="{{ $search['free_text'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small text-muted fw-semibold text-uppercase"
                                    style="font-size:.7rem;letter-spacing:.06em;">From date</label>
                                <input type="date" name="from_date" class="form-control"
                                    value="{{ data_get($search ?? [], 'from_date') }}">
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small text-muted fw-semibold text-uppercase"
                                    style="font-size:.7rem;letter-spacing:.06em;">To date</label>
                                <input type="date" name="to_date" class="form-control"
                                    value="{{ data_get($search ?? [], 'to_date') }}">
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary fw-semibold rounded-3"
                                    style="min-height: 42px;">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Sales List --}}
            <div class="row">
                @forelse ($sales as $ledger)
                    @php
                        $balance = (float) $ledger->subtotal - (float) $ledger->discount - (float) $ledger->paid;
                        $eps = 0.009;
                        $settled = abs($balance) < $eps;
                        $hasDue = $balance > $eps;
                    @endphp
                    <div class="col-12 mb-3">
                        <div class="card admin-sale-card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="d-flex flex-column flex-lg-row">
                                    <div class="d-flex flex-grow-1 min-w-0">
                                        <span class="admin-sale-accent" aria-hidden="true"></span>
                                        <div class="admin-sale-lead">
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                <span class="admin-sale-invoice">{{ $ledger->invoice_no }}</span>
                                                @if ($settled)
                                                    <span class="badge rounded-pill bg-light text-success border border-success fw-bold" style="font-size:.65rem;">Paid</span>
                                                @elseif ($hasDue)
                                                    <span class="badge rounded-pill bg-light text-warning border border-warning fw-bold" style="font-size:.65rem;">{{ number_format($balance, 2) }} due</span>
                                                @else
                                                    <span class="badge rounded-pill bg-light text-info border border-info fw-bold" style="font-size:.65rem;"
                                                        title="Overpaid">{{ number_format(abs($balance), 2) }} over</span>
                                                @endif
                                            </div>
                                            <div class="admin-sale-meta mb-1">
                                                <i class="fas fa-calendar-alt"></i>{{ $ledger->date }}
                                            </div>
                                            <div class="admin-sale-meta mb-1">
                                                <i class="fas fa-user"></i>{{ $ledger->customer->name ?? 'N/A' }}
                                            </div>
                                            @if ($ledger->driver)
                                                <div class="admin-sale-meta mb-0">
                                                    <i class="fas fa-truck"></i>{{ $ledger->driver->name ?? '' }}
                                                    <span class="text-muted fw-normal">&nbsp;·&nbsp;DSR</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="admin-sale-metrics flex-grow-1 border-top p-3">
                                        <div class="row g-2 px-lg-3">
                                            <div class="col-6 col-sm-3">
                                                <div class="metric-pill">
                                                    <span class="label">Subtotal</span>
                                                    <span class="value">{{ number_format($ledger->subtotal, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <div class="metric-pill">
                                                    <span class="label">Discount</span>
                                                    <span class="value">{{ number_format($ledger->discount, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <div class="metric-pill">
                                                    <span class="label">Paid</span>
                                                    <span class="value">{{ number_format($ledger->paid, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <div class="metric-pill metric-pill--balance">
                                                    <span class="label">Balance</span>
                                                    <span class="value">{{ number_format($balance, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="admin-sale-actions d-flex align-items-center justify-content-lg-center gap-2 px-3 pb-3 px-lg-3 border-top pt-3 flex-shrink-0"
                                        style="min-width: 120px;">
                                        <a href="{{ url('sales_invoice/' . $ledger->id) }}"
                                            class="btn btn-success btn-icon-round"
                                            title="View invoice"
                                            aria-label="View invoice">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <form action="{{ route('sales.destroy', $ledger->id) }}" method="POST" class="d-inline m-0"
                                            onsubmit="return confirm('Are you sure to delete this sale?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-danger btn-icon-round"
                                                title="Delete sale"
                                                aria-label="Delete sale">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm admin-sale-empty py-5 text-center">
                            <div class="empty-icon-circle">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <h6 class="fw-bold mb-1">No sales found</h6>
                            <p class="text-muted small mb-0">Adjust your filters or try a different date range.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
@endsection
