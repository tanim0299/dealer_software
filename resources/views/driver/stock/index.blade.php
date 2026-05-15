@extends('driver.layouts.master')

@section('page_title', 'Stock')

@section('body')
    @if(!empty($driverPeriod))
        <div class="small text-muted mb-2 px-1">
            Stock for open period:
            {{ \Carbon\Carbon::parse($driverPeriod['start_date'])->format('d M Y') }}
            – {{ \Carbon\Carbon::parse($driverPeriod['end_date'])->format('d M Y') }}
        </div>
    @endif
    <div class="page-card p-3 mb-3">
        <form method="GET" action="{{ route('driver_stock.index') }}">
            <label class="form-label small mb-1">Search product</label>
            <div class="input-group">
                <input type="text" name="free_text" class="form-control" placeholder="Name or code" value="{{ $search['free_text'] ?? '' }}">
                <button class="btn btn-primary" type="submit">Filter</button>
                <a class="btn btn-outline-secondary" href="{{ route('driver_stock.index') }}">Reset</a>
            </div>
        </form>
    </div>
    @if($items->count())
        @foreach($items as $item)
            @php
                $available = $item->issue_qty - $item->sold_qty + $item->return_qty;
            @endphp
            <div class="driver-tile">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <div class="fw-medium">{{ $item->product->name ?? '' }}</div>
                    <span class="badge rounded-pill bg-primary fs-6">{{ $available }}</span>
                </div>
                <div class="small text-muted mt-2">
                    Issued {{ $item->issue_qty }} · Sold {{ $item->sold_qty }} · Return {{ $item->return_qty }}
                </div>
            </div>
        @endforeach
    @else
        <div class="page-card p-4 text-center text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
            No stock loaded for today. Check issues.
        </div>
    @endif
@endsection
