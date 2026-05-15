@extends('driver.layouts.master')

@section('page_title', 'Cash given')

@section('body')
    <div class="page-card p-3 mb-3">
        <a href="{{ route('driver_cash_distribution.create') }}" class="btn btn-primary w-100">
            <i class="bi bi-plus-lg me-1"></i> Give amount
        </a>
    </div>

    <div class="page-card p-3 mb-3">
        <div class="fw-medium mb-3">Filter</div>
        <form method="GET" action="{{ route('driver_cash_distribution.index') }}">
            <div class="mb-3">
                <label class="form-label">Employee</label>
                <select name="employee_id" class="form-select js-example-basic-single">
                    <option value="">All employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ data_get($search ?? [], 'employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" value="{{ data_get($search ?? [], 'amount') }}" placeholder="Exact amount" class="form-control">
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
                    <button class="btn btn-primary" type="submit">Apply</button>
                </div>
                <div class="col-6 d-grid">
                    <a href="{{ route('driver_cash_distribution.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    @forelse($distributions as $distribution)
        <div class="driver-tile">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fw-bold">{{ $distribution->employee->name ?? '' }}</div>
                    <div class="small text-muted">{{ $distribution->date }}</div>
                    @if(!empty($distribution->note))
                        <div class="small mt-1">{{ $distribution->note }}</div>
                    @endif
                </div>
                <div class="text-end">
                    <div class="fw-bold text-danger">Tk {{ number_format($distribution->amount, 2) }}</div>
                </div>
            </div>
            <div class="driver-actions driver-actions--end driver-actions--single">
                <form method="POST" action="{{ route('driver_cash_distribution.destroy', $distribution->id) }}" onsubmit="return confirm('Delete this entry?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="driver-icon-action driver-icon-action--delete"
                            title="Delete entry"
                            aria-label="Delete entry">
                        <i class="bi bi-trash3" aria-hidden="true"></i>
                        <span class="visually-hidden">Delete entry</span>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="driver-empty page-card">No entries found.</div>
    @endforelse

    <div class="mt-3 d-flex justify-content-center">
        {{ $distributions->links() }}
    </div>
@endsection
