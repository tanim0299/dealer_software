@extends('driver.layouts.master')

@section('page_title', 'Returns')

@push('styles')
    <style>
        .return-list-page .return-filter-card .return-filter-heading {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-weight: 600;
            font-size: .95rem;
            color: var(--drv-on-surface);
            margin-bottom: .75rem;
        }

        .return-list-page .return-filter-card .return-filter-heading i {
            font-size: 1.15rem;
            color: var(--drv-primary);
        }

        .return-list-page .return-filter-card .input-icon-wrap {
            position: relative;
        }

        .return-list-page .return-filter-card .input-icon-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            color: var(--drv-on-surface-variant);
            pointer-events: none;
        }

        .return-list-page .return-filter-card .input-icon-wrap .form-control {
            padding-left: 2.5rem;
        }

        .return-list-page .return-filter-card label.small-muted {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--drv-on-surface-variant);
            margin-bottom: .35rem;
        }

        .return-list-page .btn-search-wave {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            font-weight: 600;
        }

        .return-list-page .return-card {
            position: relative;
            overflow: hidden;
            border-radius: var(--drv-radius-lg);
            border: 1px solid var(--drv-outline);
            background: var(--drv-surface);
            box-shadow: var(--drv-elev-card);
            padding: 0;
            margin-bottom: 12px;
        }

        .return-list-page .return-card__accent {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #c62828 0%, #b71c1c 55%, #8e2922 100%);
            border-radius: var(--drv-radius-lg) 0 0 var(--drv-radius-lg);
        }

        .return-list-page .return-card__inner {
            padding: 14px 16px 12px;
            padding-left: 18px;
        }

        .return-list-page .return-card__top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }

        .return-list-page .return-card__lead {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 0;
        }

        .return-list-page .return-card__icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(198, 40, 40, .12) 0%, rgba(27, 110, 243, .1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .return-list-page .return-card__icon i {
            font-size: 1.25rem;
            color: #b71c1c;
        }

        .return-list-page .return-card__invoice-no {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--drv-primary);
            letter-spacing: -.01em;
            line-height: 1.25;
        }

        .return-list-page .return-card__date {
            font-size: .8rem;
            color: var(--drv-on-surface-variant);
            display: flex;
            align-items: center;
            gap: .35rem;
            margin-top: 3px;
        }

        .return-list-page .return-card__badge {
            flex-shrink: 0;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: .45rem .7rem;
            border-radius: 999px;
        }

        .return-list-page .return-card__badge--cash {
            background: rgba(198, 40, 40, .12);
            color: #b71c1c;
            border: 1px solid rgba(198, 40, 40, .28);
        }

        .return-list-page .return-card__badge--due {
            background: rgba(249, 171, 0, .14);
            color: #c26400;
            border: 1px solid rgba(249, 171, 0, .35);
        }

        .return-list-page .return-card__badge--split {
            background: rgba(13, 148, 136, .14);
            color: #0d7377;
            border: 1px solid rgba(13, 148, 136, .3);
        }

        .return-list-page .return-card__customer {
            font-size: .9375rem;
            font-weight: 600;
            color: var(--drv-on-surface);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: .4rem;
            flex-wrap: wrap;
        }

        .return-list-page .return-card__customer i {
            color: var(--drv-on-surface-variant);
            font-size: 1rem;
        }

        .return-list-page .return-card__mode-pill {
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: .2rem .5rem;
            border-radius: 999px;
            background: var(--drv-surface-variant);
            border: 1px solid var(--drv-outline);
            color: var(--drv-on-surface-variant);
        }

        .return-list-page .return-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }

        .return-list-page .return-metric {
            background: var(--drv-surface-variant);
            border-radius: 12px;
            padding: 8px 6px;
            text-align: center;
            border: 1px solid var(--drv-outline);
        }

        .return-list-page .return-metric span:first-child {
            display: block;
            font-size: .65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--drv-on-surface-variant);
            margin-bottom: 2px;
        }

        .return-list-page .return-metric span:last-child {
            font-size: .8125rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--drv-on-surface);
        }

        .return-list-page .return-card__actions {
            display: flex;
            gap: 10px;
            padding-top: 4px;
        }

        .return-list-page .return-card__actions form {
            flex: 1;
            margin: 0;
        }

        .return-list-page .return-action-btn {
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

        .return-list-page .return-action-btn:active {
            transform: scale(.97);
        }

        .return-list-page .return-action-btn--view {
            background: linear-gradient(135deg, #1e8e3e 0%, #148f5c 100%);
            color: #fff !important;
        }

        .return-list-page .return-action-btn--view:focus-visible {
            outline: 2px solid var(--drv-primary);
            outline-offset: 2px;
        }

        .return-list-page .return-action-btn--delete {
            background: var(--drv-surface);
            color: var(--drv-error) !important;
            border: 2px solid rgba(217, 48, 37, .35) !important;
            box-shadow: none;
        }

        .return-list-page .return-empty {
            text-align: center;
            padding: 2.25rem 1.25rem;
        }

        .return-list-page .return-empty .return-empty-icon {
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
    @php
        $search = $search ?? ['from_date' => '', 'to_date' => '', 'free_text' => ''];
    @endphp
    <div class="return-list-page">
        <div class="driver-banner mb-3" role="status">
            <strong>Today</strong> — by default only <strong>today&rsquo;s</strong> returns are listed (newest first). Use <strong>From / To</strong> and press <strong>Search</strong> for another range.
        </div>

        <div class="page-card return-filter-card p-3 mb-3">
            <form method="GET" action="{{ route('sales_return.index') }}">
                <div class="return-filter-heading">
                    <i class="bi bi-funnel" aria-hidden="true"></i>
                    Filter returns
                </div>

                <label class="form-label visually-hidden" for="return-free-text">Invoice or customer</label>
                <div class="input-icon-wrap mb-2">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    <input id="return-free-text" type="text" name="free_text" placeholder="Invoice / customer"
                           value="{{ $search['free_text'] ?? '' }}" class="form-control" autocomplete="off">
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="small-muted d-block" for="return-from-date">From</label>
                        <input id="return-from-date" type="date" name="from_date"
                               value="{{ $search['from_date'] ?? '' }}" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="small-muted d-block" for="return-to-date">To</label>
                        <input id="return-to-date" type="date" name="to_date"
                               value="{{ $search['to_date'] ?? '' }}" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-search-wave">
                    <i class="bi bi-search" aria-hidden="true"></i>
                    Search
                </button>
            </form>
        </div>

        @forelse($returns as $return)
            @php
                $payment = $return->payments->first();
                $cashPaid = ($payment && (float) $payment->amount < -0.001) ? abs((float) $payment->amount) : 0.0;
                $dueApplied = max(0.0, (float) $return->subtotal - $cashPaid);
                $eps = 0.009;
                $lineDisc = (float) ($return->discount ?? 0);
                $linesSub = (float) ($return->lines_subtotal ?? 0);
                if ($linesSub < $eps) {
                    $linesSub = (float) $return->subtotal + $lineDisc;
                }
            @endphp
            <article class="return-card">
                <span class="return-card__accent" aria-hidden="true"></span>
                <div class="return-card__inner">
                    <div class="return-card__top">
                        <div class="return-card__lead">
                            <div class="return-card__icon" aria-hidden="true">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="return-card__invoice-no text-truncate">{{ $return->invoice_no }}</div>
                                <div class="return-card__date">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                                    {{ $return->date }}
                                </div>
                            </div>
                        </div>
                        @if ($cashPaid > $eps && $dueApplied > $eps)
                            <span class="return-card__badge return-card__badge--split">Due + cash</span>
                        @elseif ($cashPaid > $eps)
                            <span class="return-card__badge return-card__badge--cash">Cash paid</span>
                        @elseif ((float) $return->subtotal > $eps)
                            <span class="return-card__badge return-card__badge--due">Due only</span>
                        @endif
                    </div>

                    <div class="return-card__customer">
                        <i class="bi bi-person-circle" aria-hidden="true"></i>
                        {{ $return->customer->name ?? 'N/A' }}
                        @if (($return->return_mode ?? '') === 'without_invoice')
                            <span class="return-card__mode-pill">No invoice</span>
                        @endif
                    </div>

                    <div class="return-metrics">
                        <div class="return-metric">
                            <span>Lines</span>
                            <span>{{ number_format($linesSub, 2) }}</span>
                        </div>
                        <div class="return-metric">
                            <span>Disc</span>
                            <span>{{ number_format($lineDisc, 2) }}</span>
                        </div>
                        <div class="return-metric">
                            <span>Total</span>
                            <span>{{ number_format($return->subtotal, 2) }}</span>
                        </div>
                    </div>

                    <div class="return-card__actions">
                        <a href="{{ route('sales_return.show', $return->id) }}"
                           class="return-action-btn return-action-btn--view flex-grow-1"
                           title="View return"
                           aria-label="View return">
                            <i class="bi bi-receipt-cutoff" aria-hidden="true"></i>
                            <span class="visually-hidden">View return</span>
                        </a>
                        <form action="{{ route('sales_return.destroy', $return->id) }}" method="POST" class="flex-grow-1"
                              onsubmit="return confirm('Delete this return?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="return-action-btn return-action-btn--delete w-100"
                                    title="Delete return"
                                    aria-label="Delete return">
                                <i class="bi bi-trash3" aria-hidden="true"></i>
                                <span class="visually-hidden">Delete return</span>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @empty
            <div class="driver-empty page-card return-empty">
                <div class="return-empty-icon" aria-hidden="true">
                    <i class="bi bi-inbox"></i>
                </div>
                <div class="fw-semibold mb-1">No returns in this range</div>
                <div class="small text-muted">Try another date range or search by invoice / customer.</div>
            </div>
        @endforelse

        <div class="mt-3 d-flex justify-content-center">
            {{ $returns->links() }}
        </div>
    </div>
@endsection
