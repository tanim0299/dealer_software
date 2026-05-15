@extends('driver.layouts.master')

@section('page_title', 'Collections')

@section('body')
    <div class="page-card p-3 mb-3">
        <div class="fw-medium mb-3">Filter</div>
        <form method="GET" action="{{ route('customer_payment.index') }}">
            <div class="mb-3">
                <label class="form-label">Search</label>
                <input type="text" name="free_text" class="form-control" placeholder="Note / customer / phone" value="{{ data_get($search ?? [], 'free_text') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Customer</label>
                <select name="customer_id" class="form-select js-example-basic-single">
                    <option value="">All customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ data_get($search ?? [], 'customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" value="{{ data_get($search ?? [], 'amount') }}" class="form-control" placeholder="Filter by amount">
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" value="{{ data_get($search ?? [], 'from_date') }}" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" value="{{ data_get($search ?? [], 'to_date') }}" class="form-control">
                </div>
            </div>

            <div class="row g-2">
                <div class="col-6 d-grid">
                    <button class="btn btn-primary" type="submit">Apply filter</button>
                </div>
                <div class="col-6 d-grid">
                    <a class="btn btn-outline-secondary" href="{{ route('customer_payment.index') }}">Reset</a>
                </div>
            </div>
        </form>
    </div>

    @forelse($payments as $payment)
        <div class="driver-tile">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="fw-medium">{{ $payment->customer->name ?? '' }}</div>
                <span class="fw-bold text-success">Tk {{ number_format($payment->amount, 2) }}</span>
            </div>
            <div class="small text-muted mt-1">{{ $payment->date }}</div>
            @if($payment->note)
                <div class="small mt-2">{{ $payment->note }}</div>
            @endif
            <div class="driver-actions driver-actions--end driver-actions--single">
                <form method="POST" action="{{ route('customer_payment.destroy', $payment->id) }}" onsubmit="return confirm('Delete this payment?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="driver-icon-action driver-icon-action--delete"
                            title="Delete payment"
                            aria-label="Delete payment">
                        <i class="bi bi-trash3" aria-hidden="true"></i>
                        <span class="visually-hidden">Delete payment</span>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="driver-empty page-card">No payments found.</div>
    @endforelse

    <div class="mt-3 d-flex justify-content-center">
        {{ $payments->links('pagination::bootstrap-5') }}
    </div>
@endsection
