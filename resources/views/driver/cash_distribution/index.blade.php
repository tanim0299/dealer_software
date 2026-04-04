@extends('driver.layouts.master')

@section('page_title', 'Given Amount List')

@section('body')
<div class="page-card p-3 mb-3">
    <a href="{{ route('driver_cash_distribution.create') }}" class="btn btn-primary w-100">
        <i class="bi bi-plus-circle me-1"></i> Give Amount
    </a>
</div>

<div class="page-card p-3 mb-3">
    <h6 class="mb-3 text-center">Filter Given Amount</h6>
    <form method="GET" action="{{ route('driver_cash_distribution.index') }}">
        <div class="mb-2">
            <select name="employee_id" class="form-control js-example-basic-single">
                <option value="">All Employees</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ data_get($search ?? [], 'employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <input type="number" step="0.01" name="amount" value="{{ data_get($search ?? [], 'amount') }}" placeholder="Filter by Amount" class="form-control">
        </div>

        <div class="row g-2">
            <div class="col-6">
                <input type="date" name="from_date" value="{{ data_get($search ?? [], 'from_date') }}" class="form-control">
            </div>
            <div class="col-6">
                <input type="date" name="to_date" value="{{ data_get($search ?? [], 'to_date') }}" class="form-control">
            </div>
        </div>

        <div class="row g-2 mt-1">
            <div class="col-6 d-grid">
                <button class="btn btn-primary btn-sm">Filter</button>
            </div>
            <div class="col-6 d-grid">
                <a href="{{ route('driver_cash_distribution.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>
</div>

@forelse($distributions as $distribution)
    <div class="page-card p-3 mb-2">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="mb-1 fw-bold">{{ $distribution->employee->name ?? '' }}</h6>
                <div class="small text-muted">{{ $distribution->date }}</div>
                @if(!empty($distribution->note))
                    <div class="small mt-1">{{ $distribution->note }}</div>
                @endif
            </div>
            <div class="text-end">
                <div class="fw-bold text-danger">à§³ {{ number_format($distribution->amount, 2) }}</div>
            </div>
        </div>

        <div class="mt-2 text-end">
            <form method="POST" action="{{ route('driver_cash_distribution.destroy', $distribution->id) }}" onsubmit="return confirm('Delete this entry?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>
@empty
    <div class="page-card p-4 text-center text-muted">
        No amount given entries found.
    </div>
@endforelse

<div class="mt-3">
    {{ $distributions->links() }}
</div>
@endsection

