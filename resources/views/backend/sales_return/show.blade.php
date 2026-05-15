@extends('backend.layouts.master')

@section('title', 'Sales return detail')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-center justify-content-between mb-4">
                @include('backend.layouts.partials.breadcrumb', ['page_title' => 'Sales return detail'])
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Return ref.</div>
                            <div class="fw-bold">{{ $return->invoice_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Date</div>
                            <div>{{ $return->date }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Customer</div>
                            <div>{{ $return->customer->name ?? '—' }}</div>
                            @if(($return->return_mode ?? '') === 'without_invoice')
                                <span class="badge bg-secondary mt-1">Without invoice</span>
                            @elseif(($return->return_mode ?? '') === 'with_invoice')
                                <span class="badge bg-light text-dark border mt-1">With invoice</span>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
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

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Lines subtotal</span>
                                    <span class="fw-medium">Tk {{ number_format($linesSub, 2) }}</span>
                                </div>
                                @if($hdrDisc > 0.001)
                                    <div class="d-flex justify-content-between small mt-1">
                                        <span class="text-muted">Extra discount</span>
                                        <span class="text-danger">− Tk {{ number_format($hdrDisc, 2) }}</span>
                                    </div>
                                @endif
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Grand total</span>
                                    <span class="fw-bold text-primary">Tk {{ number_format($return->subtotal, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small mb-2">Settlement</div>
                                @if($cashPaid > 0.001 && $dueApplied > 0.001)
                                    <div class="d-flex justify-content-between small"><span>Applied to due</span><span>Tk {{ number_format($dueApplied, 2) }}</span></div>
                                    <div class="d-flex justify-content-between small"><span>Cash paid</span><span>Tk {{ number_format($cashPaid, 2) }}</span></div>
                                @elseif($cashPaid > 0.001)
                                    <div>Cash paid — Tk {{ number_format($cashPaid, 2) }}</div>
                                @elseif((float) $return->subtotal > 0.001)
                                    <div>Applied to customer due — Tk {{ number_format($dueApplied, 2) }}</div>
                                @else
                                    <div class="text-muted small">No payment row.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('sales_return.index') }}" class="btn btn-secondary">Back to list</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
