@extends('backend.layouts.master')
@section('title', 'Purchase Return #'.$ledger->id)
@section('content')
@php
    $grand = (float) (($ledger->grand_total ?? 0) > 0 ? $ledger->grand_total : $ledger->subtotal);
    $entriesGross = $ledger->entries->sum(fn ($e) => (float) $e->return_qty * (float) $e->purchase_price);
@endphp
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb', ['page_title' => 'Purchase Return #'.$ledger->id])
            <div class="ms-md-auto py-2 py-md-0 d-flex flex-wrap gap-2">
                @if(auth()->user()->can('Purchase Return destroy'))
                    <form action="{{ route('purchase_return.destroy', $ledger->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this purchase return? Stock will be rolled back.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-round">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </form>
                @endif
                <a href="{{ route('purchase_return.index') }}" class="btn btn-label-info btn-round">
                    <i class="fa fa-arrow-left"></i> Back to list
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Return summary</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase">Supplier</label>
                                <p class="h5 mb-0">{{ $ledger->supplier->name ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase">Return type</label>
                                <p class="mb-0">
                                    @if((int) $ledger->return_type === 1)
                                        <span class="badge badge-success">Cash settlement</span>
                                    @else
                                        <span class="badge badge-info">Minus from due</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase">Return date</label>
                                <p class="h6 mb-0">{{ $ledger->date ? \Carbon\Carbon::parse($ledger->date)->format('d M Y') : '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small text-uppercase">Recorded at</label>
                                <p class="h6 mb-0 text-muted">{{ $ledger->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block">FIFO gross (lines)</small>
                                <strong>{{ number_format($entriesGross, 2) }}</strong>
                            </div>
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block">Line discounts</small>
                                <strong>{{ number_format($ledger->line_discount_total ?? 0, 2) }}</strong>
                            </div>
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block">Subtotal (after line disc.)</small>
                                <strong>{{ number_format($ledger->subtotal, 2) }}</strong>
                            </div>
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block">Order discount</small>
                                <strong>{{ number_format($ledger->order_discount ?? 0, 2) }}</strong>
                            </div>
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block">Grand total</small>
                                <strong class="text-primary fs-5">{{ number_format($grand, 2) }}</strong>
                            </div>
                            @if((int) $ledger->return_type === 2)
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block">Due adjustment</small>
                                    <strong>{{ number_format($ledger->due_adjustment ?? 0, 2) }}</strong>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block">Cash portion (paid)</small>
                                    <strong>{{ number_format($ledger->cash_portion ?? 0, 2) }}</strong>
                                </div>
                            @else
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block">Cash (full)</small>
                                    <strong>{{ number_format($ledger->cash_portion ?? 0, 2) }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">FIFO line items</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Unit cost</th>
                                        <th class="text-end">Gross</th>
                                        <th class="text-end">Discount</th>
                                        <th class="text-end">Net</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ledger->entries as $i => $entry)
                                        @php
                                            $g = (float) $entry->return_qty * (float) $entry->purchase_price;
                                            $d = (float) ($entry->discount ?? 0);
                                        @endphp
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $entry->product->name ?? ('#'.$entry->product_id) }}</td>
                                            <td class="text-end">{{ number_format($entry->return_qty, 4) }}</td>
                                            <td class="text-end">{{ number_format($entry->purchase_price, 4) }}</td>
                                            <td class="text-end">{{ number_format($g, 2) }}</td>
                                            <td class="text-end">{{ number_format($d, 2) }}</td>
                                            <td class="text-end fw-semibold">{{ number_format(max(0, $g - $d), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No line items.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Linked payment</h5>
                    </div>
                    <div class="card-body">
                        @if($supplierPayment)
                            <p class="mb-2"><span class="text-muted">Date</span><br>{{ \Carbon\Carbon::parse($supplierPayment->payment_date)->format('d M Y') }}</p>
                            <p class="mb-2"><span class="text-muted">Method</span><br>{{ $supplierPayment->payment_method ?? '—' }}</p>
                            <p class="mb-2"><span class="text-muted">Amount (ledger)</span><br>
                                <strong class="@if($supplierPayment->amount < 0) text-danger @else text-success @endif">
                                    {{ number_format($supplierPayment->amount, 2) }}
                                </strong>
                                <small class="text-muted d-block">Negative = cash out for return</small>
                            </p>
                            @if($supplierPayment->note)
                                <p class="mb-0 small"><span class="text-muted">Note</span><br>{{ $supplierPayment->note }}</p>
                            @endif
                        @else
                            <p class="text-muted mb-0">No supplier payment row (e.g. full due settlement with zero cash).</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
