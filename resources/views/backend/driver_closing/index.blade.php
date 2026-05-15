@extends('backend.layouts.master')
@section('title', 'Driver Closing')

@push('styles')
<style>
    .closing-workbench .closing-filter-card {
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, .06);
        box-shadow: 0 4px 24px rgba(0, 0, 0, .06);
    }
    .closing-summary-gradient {
        background: linear-gradient(135deg, #1572e8 0%, #0d6efd 45%, #0aa39f 100%);
    }
    .closing-cash-in-hand {
        background: linear-gradient(135deg, #1e3a5f 0%, #1572e8 55%, #0d6efd 100%);
        border-radius: 0;
    }
    .closing-section__num {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: linear-gradient(135deg, #1572e8, #0aa39f);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .closing-section__num--sm {
        font-size: .65rem;
        width: auto;
        min-width: 28px;
        padding: 0 6px;
    }
    .driver-closing-report .table thead th {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6c757d;
        white-space: nowrap;
    }
    .closing-loaded-period-banner {
        border-radius: 12px;
        border: 1px solid rgba(13, 110, 253, 0.22);
        background: linear-gradient(135deg, rgba(21, 114, 232, 0.09), rgba(10, 163, 159, 0.08));
    }
    .closing-loaded-period-banner .period-range {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0d47a1;
        letter-spacing: .01em;
    }
    /* BS4 build: avoid bg-success/bg-danger + text-success/text-danger (same hue = invisible text) */
    .driver-closing-report .closing-total-strip--income {
        background: #d1fae5 !important;
        border-top: 1px solid rgba(5, 150, 105, 0.35) !important;
    }
    .driver-closing-report .closing-total-strip--income .closing-total-strip__label,
    .driver-closing-report .closing-total-strip--income .closing-total-strip__value {
        color: #065f46 !important;
    }
    .driver-closing-report .closing-total-strip--out {
        background: #fee2e2 !important;
        border-top: 1px solid rgba(220, 38, 38, 0.35) !important;
    }
    .driver-closing-report .closing-total-strip--out .closing-total-strip__label,
    .driver-closing-report .closing-total-strip--out .closing-total-strip__value,
    .driver-closing-report .closing-total-strip--out .closing-total-strip__meta {
        color: #991b1b !important;
    }
    .driver-closing-report .closing-total-strip__divider {
        border-top: 1px solid rgba(185, 28, 28, 0.35) !important;
    }
    @media print {
        .no-print { display: none !important; }
        .closing-workbench .card {
            box-shadow: none !important;
            break-inside: avoid;
        }
        .wrapper .sidebar,
        .wrapper .main-header,
        .wrapper .page-inner > .d-flex:first-child { display: none !important; }
        .main-panel { width: 100% !important; }
    }
</style>
@endpush

@section('content')
<div class="container closing-workbench pb-5">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb', ['page_title' => 'Driver closing'])
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card closing-filter-card mb-4 no-print">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <h4 class="card-title mb-1">Closing workbench</h4>
                        <p class="text-muted small mb-0">
                            Choose a driver to load the open period: <strong>last closing + 1 day</strong> through <strong>today</strong>.
                        </p>
                    </div>
                </div>
                <form method="get" action="{{ route('driver_closing.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-8 col-lg-6">
                        <label class="form-label fw-semibold">Driver <span class="text-danger">*</span></label>
                        <select name="driver_id" class="form-select" id="closing_driver_id" required data-select2-skip>
                            <option value="">Choose one</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}" @selected((string) request('driver_id') === (string) $d->id)>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-line-chart me-1"></i> Load report
                        </button>
                        @if(request()->filled('driver_id'))
                            <a href="{{ route('driver_closing.index') }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        @if(!empty($showReport) && isset($period, $driver))
            <div class="closing-loaded-period-banner px-4 py-3 mb-4 d-flex flex-column flex-md-row flex-wrap align-items-start align-items-md-center gap-2 gap-md-3 no-print">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill px-3 py-2">Loaded report</span>
                    <span class="text-muted small text-uppercase fw-semibold">DSR</span>
                    <span class="fw-semibold text-dark">{{ $driver->name }}</span>
                </div>
                <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-1 gap-sm-3">
                    <span class="text-muted small text-uppercase fw-semibold">Open period (data in this closing)</span>
                    <span class="period-range font-monospace">
                        {{ date('d M Y', strtotime($period['start_date'])) }}
                        <span class="text-primary mx-1">→</span>
                        {{ date('d M Y', strtotime($period['end_date'])) }}
                    </span>
                </div>
                @if(!empty($closingDate))
                    <div class="ms-md-auto small text-muted">
                        Closing date: <span class="fw-semibold text-dark">{{ date('d M Y', strtotime($closingDate)) }}</span>
                    </div>
                @endif
            </div>
        @endif

        @if(!empty($showReport))
            @include('backend.driver_closing.partials.closing_report')
        @else
            <div class="card border-0 shadow-sm text-center py-5 no-print">
                <div class="card-body">
                    <i class="fa fa-hand-o-up fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-muted mb-0">Select a driver and click &ldquo;Load report&rdquo; to see closing details here.</h5>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
