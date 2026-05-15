@extends('driver.layouts.master')

@section('page_title', 'Return detail')

@section('body')
    <div class="page-card p-3 mb-3 text-center">
        <div class="text-muted small">Sales return</div>
        <h2 class="h5 fw-bold mt-1 mb-0">{{ $return->invoice_no }}</h2>
        <div class="small text-muted mt-1">{{ $return->date }}</div>
    </div>

    <div class="driver-tile mb-3">
        <div class="small text-muted">Customer</div>
        <div class="fw-medium">{{ $return->customer->name ?? '' }}</div>
        @if($return->return_mode)
            <div class="small text-muted mt-2">Return type</div>
            <div class="small">
                @if($return->return_mode === 'with_invoice')
                    With invoice {{ $return->sales_ledger_id ? '(#'.$return->invoice_no.')' : '' }}
                @else
                    Without invoice
                @endif
            </div>
        @endif
    </div>

    <div class="driver-table-wrap mb-3">
        <table class="table table-sm mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Unit (net)</th>
                    <th class="text-end">Line</th>
                </tr>
            </thead>
            <tbody>
                @foreach($return->entries as $entry)
                    <tr>
                        <td>{{ $entry->product->name ?? '' }}</td>
                        <td class="text-end">{{ $entry->return_qty }}</td>
                        <td class="text-end">{{ number_format($entry->sale_price, 2) }}</td>
                        <td class="text-end fw-medium">{{ number_format($entry->return_qty * $entry->sale_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @php
        $linesSub = (float) ($return->lines_subtotal ?? $return->entries->sum(fn ($e) => (float) $e->return_qty * (float) $e->sale_price));
        $hdrDisc = (float) ($return->discount ?? 0);
        $payment = $return->payments->first();
        $cashPaid = ($payment && (float) $payment->amount < -0.001) ? abs((float) $payment->amount) : 0.0;
        $dueApplied = max(0.0, (float) $return->subtotal - $cashPaid);
    @endphp
    <div class="driver-tile mb-3">
        <div class="d-flex justify-content-between align-items-center small">
            <span class="text-muted">Lines subtotal</span>
            <span class="fw-medium">Tk {{ number_format($linesSub, 2) }}</span>
        </div>
        @if($hdrDisc > 0.001)
            <div class="d-flex justify-content-between align-items-center small mt-1">
                <span class="text-muted">Extra discount</span>
                <span class="fw-medium text-danger">− Tk {{ number_format($hdrDisc, 2) }}</span>
            </div>
        @endif
        <hr class="my-2">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Grand total</span>
            <span class="fs-5 fw-bold text-primary">Tk {{ number_format($return->subtotal, 2) }}</span>
        </div>
    </div>

    <div class="driver-tile mb-3">
        <div class="small text-muted mb-2">Settlement</div>
        @if($cashPaid > 0.001 && $dueApplied > 0.001)
            <div class="d-flex justify-content-between small"><span>Applied to due</span><span>Tk {{ number_format($dueApplied, 2) }}</span></div>
            <div class="d-flex justify-content-between small"><span>Cash paid to customer</span><span>Tk {{ number_format($cashPaid, 2) }}</span></div>
        @elseif($cashPaid > 0.001)
            <div class="fw-medium">Cash paid to customer — Tk {{ number_format($cashPaid, 2) }}</div>
        @elseif((float) $return->subtotal > 0.001)
            <div class="fw-medium">Applied to customer due — Tk {{ number_format($dueApplied, 2) }}</div>
        @else
            <div class="small text-muted">No payment recorded.</div>
        @endif
    </div>

    <button type="button"
            onclick="window.print()"
            class="driver-icon-action driver-icon-action--print w-100 border-0"
            title="Print"
            aria-label="Print">
        <i class="bi bi-printer" aria-hidden="true"></i>
        <span class="visually-hidden">Print</span>
    </button>
@endsection
