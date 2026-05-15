@extends('driver.layouts.master')

@section('page_title', 'Sales')

@push('styles')
    <style>
        .sale-list-page .sale-filter-card .sale-filter-heading {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-weight: 600;
            font-size: .95rem;
            color: var(--drv-on-surface);
            margin-bottom: .75rem;
        }

        .sale-list-page .sale-filter-card .sale-filter-heading i {
            font-size: 1.15rem;
            color: var(--drv-primary);
        }

        .sale-list-page .sale-filter-card .input-icon-wrap {
            position: relative;
        }

        .sale-list-page .sale-filter-card .input-icon-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            color: var(--drv-on-surface-variant);
            pointer-events: none;
        }

        .sale-list-page .sale-filter-card .input-icon-wrap .form-control {
            padding-left: 2.5rem;
        }

        .sale-list-page .sale-filter-card label.small-muted {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--drv-on-surface-variant);
            margin-bottom: .35rem;
        }

        .sale-list-page .btn-search-wave {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            font-weight: 600;
        }

        /* Sale row card */
        .sale-list-page .sale-card {
            position: relative;
            overflow: hidden;
            border-radius: var(--drv-radius-lg);
            border: 1px solid var(--drv-outline);
            background: var(--drv-surface);
            box-shadow: var(--drv-elev-card);
            padding: 0;
            margin-bottom: 12px;
        }

        .sale-list-page .sale-card__accent {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, var(--drv-primary) 0%, #0d9488 100%);
            border-radius: var(--drv-radius-lg) 0 0 var(--drv-radius-lg);
        }

        .sale-list-page .sale-card__inner {
            padding: 14px 16px 12px;
            padding-left: 18px;
        }

        .sale-list-page .sale-card__top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }

        .sale-list-page .sale-card__lead {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 0;
        }

        .sale-list-page .sale-card__icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(27, 110, 243, .12) 0%, rgba(13, 148, 136, .14) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sale-list-page .sale-card__icon i {
            font-size: 1.25rem;
            color: var(--drv-primary-dark);
        }

        .sale-list-page .sale-card__invoice-no {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--drv-primary);
            letter-spacing: -.01em;
            line-height: 1.25;
        }

        .sale-list-page .sale-card__date {
            font-size: .8rem;
            color: var(--drv-on-surface-variant);
            display: flex;
            align-items: center;
            gap: .35rem;
            margin-top: 3px;
        }

        .sale-list-page .sale-card__badge {
            flex-shrink: 0;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: .45rem .7rem;
            border-radius: 999px;
        }

        .sale-list-page .sale-card__badge--settled {
            background: rgba(30, 142, 62, .14);
            color: var(--drv-success);
            border: 1px solid rgba(30, 142, 62, .25);
        }

        .sale-list-page .sale-card__badge--due {
            background: rgba(249, 171, 0, .14);
            color: #c26400;
            border: 1px solid rgba(249, 171, 0, .35);
        }

        .sale-list-page .sale-card__customer {
            font-size: .9375rem;
            font-weight: 600;
            color: var(--drv-on-surface);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .sale-list-page .sale-card__customer i {
            color: var(--drv-on-surface-variant);
            font-size: 1rem;
        }

        .sale-list-page .sale-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }

        .sale-list-page .sale-metric {
            background: var(--drv-surface-variant);
            border-radius: 12px;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid var(--drv-outline);
        }

        .sale-list-page .sale-metric span:first-child {
            display: block;
            font-size: .65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--drv-on-surface-variant);
            margin-bottom: 2px;
        }

        .sale-list-page .sale-metric span:last-child {
            font-size: .8125rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--drv-on-surface);
        }

        .sale-list-page .sale-card__actions {
            display: flex;
            gap: 10px;
            padding-top: 4px;
        }

        .sale-list-page .sale-card__actions form {
            flex: 1;
            margin: 0;
        }

        .sale-list-page .sale-action-btn {
            width: 100%;
            min-height: 46px;
            border-radius: 14px !important;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            font-size: 1.25rem;
            transition: transform .1s ease, box-shadow .1s ease, opacity .1s ease;
            box-shadow: 0 1px 3px rgba(60, 64, 67, .2);
        }

        .sale-list-page .sale-action-btn:active {
            transform: scale(.97);
        }

        .sale-list-page .sale-action-btn--invoice {
            background: linear-gradient(135deg, #1e8e3e 0%, #148f5c 100%);
            color: #fff !important;
        }

        .sale-list-page .sale-action-btn--invoice:focus-visible {
            outline: 2px solid var(--drv-primary);
            outline-offset: 2px;
        }

        .sale-list-page .sale-action-btn--delete {
            background: var(--drv-surface);
            color: var(--drv-error) !important;
            border: 2px solid rgba(217, 48, 37, .35) !important;
            box-shadow: none;
        }

        .sale-list-page .sale-empty {
            text-align: center;
            padding: 2.25rem 1.25rem;
        }

        .sale-list-page .sale-empty .sale-empty-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: var(--drv-surface-variant);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--drv-on-surface-variant);
            font-size: 1.75rem;
        }
    </style>
@endpush

@section('body')
    <div class="sale-list-page">
        <div class="driver-banner mb-3" role="status">
            <strong>Today</strong> — by default only <strong>today&rsquo;s</strong> sales are listed (newest first). Pick other days with <strong>From / To</strong> and press <strong>Search</strong>.
        </div>

        <div class="page-card sale-filter-card p-3 mb-3">
            <form method="GET" action="{{ route('sales.index') }}">
                <div class="sale-filter-heading">
                    <i class="bi bi-funnel" aria-hidden="true"></i>
                    Filter sales
                </div>

                <label class="form-label visually-hidden" for="sale-free-text">Invoice or customer</label>
                <div class="input-icon-wrap mb-2">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input id="sale-free-text" type="text" name="free_text" placeholder="Invoice / customer"
                           value="{{ $search['free_text'] ?? '' }}" class="form-control" autocomplete="off">
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="small-muted d-block" for="sale-from-date">From</label>
                        <input id="sale-from-date" type="date" name="from_date"
                               value="{{ $search['from_date'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="small-muted d-block" for="sale-to-date">To</label>
                        <input id="sale-to-date" type="date" name="to_date"
                               value="{{ $search['to_date'] ?? '' }}" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-search-wave">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    Search
                </button>
            </form>
        </div>

        @forelse($sales as $ledger)
            @php
                $balance = (float) $ledger->subtotal - (float) $ledger->discount - (float) $ledger->paid;
                $eps = 0.009;
                $settled = abs($balance) < $eps;
                $hasDue = $balance > $eps;
            @endphp
            <article class="sale-card">
                <span class="sale-card__accent" aria-hidden="true"></span>
                <div class="sale-card__inner">
                    <div class="sale-card__top">
                        <div class="sale-card__lead">
                            <div class="sale-card__icon" aria-hidden="true">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="sale-card__invoice-no text-truncate">{{ $ledger->invoice_no }}</div>
                                <div class="sale-card__date">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                                    {{ $ledger->date }}
                                </div>
                            </div>
                        </div>
                        @if ($settled)
                            <span class="sale-card__badge sale-card__badge--settled">Paid</span>
                        @elseif ($hasDue)
                            <span class="sale-card__badge sale-card__badge--due">{{ number_format($balance, 2) }} due</span>
                        @else
                            <span class="sale-card__badge sale-card__badge--settled" title="Overpaid">{{ number_format(abs($balance), 2) }} over</span>
                        @endif
                    </div>

                    <div class="sale-card__customer">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        {{ $ledger->customer->name ?? 'N/A' }}
                    </div>

                    <div class="sale-metrics">
                        <div class="sale-metric">
                            <span>Sub</span>
                            <span>{{ number_format($ledger->subtotal, 2) }}</span>
                        </div>
                        <div class="sale-metric">
                            <span>Disc</span>
                            <span>{{ number_format($ledger->discount, 2) }}</span>
                        </div>
                        <div class="sale-metric">
                            <span>Paid</span>
                            <span>{{ number_format($ledger->paid, 2) }}</span>
                        </div>
                    </div>

                    <div class="sale-card__actions">
                        <a href="{{ url('sales_invoice/' . $ledger->id) }}"
                           class="sale-action-btn sale-action-btn--invoice flex-grow-1"
                           title="View invoice"
                           aria-label="View invoice">
                            <i class="bi bi-receipt-cutoff" aria-hidden="true"></i>
                            <span class="visually-hidden">View invoice</span>
                        </a>
                        <form action="{{ route('sales.destroy', $ledger->id) }}" method="POST" class="flex-grow-1"
                              onsubmit="return confirm('Delete this sale?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="sale-action-btn sale-action-btn--delete w-100"
                                    title="Delete sale"
                                    aria-label="Delete sale">
                                <i class="bi bi-trash3" aria-hidden="true"></i>
                                <span class="visually-hidden">Delete sale</span>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="driver-empty page-card sale-empty">
                <div class="sale-empty-icon" aria-hidden="true">
                    <i class="bi bi-inbox"></i>
                </div>
                <div class="fw-semibold mb-1">No sales yet</div>
                <div class="small text-muted">Try another date range or search.</div>
            </div>
        @endforelse

        <div class="mt-3 d-flex justify-content-center">
            {{ $sales->links() }}
        </div>
    </div>
@endsection
