@extends('driver.layouts.master')

@section('page_title', 'Give cash')

@section('body')
    <div class="driver-banner mb-3">
        <div class="mb-1"><strong>Collected (open period):</strong> Tk {{ number_format((float) ($totalCollectedCash ?? 0), 2) }}</div>
        <div class="mb-1"><strong>Cash given to staff:</strong> Tk {{ number_format((float) ($alreadyGiven ?? 0), 2) }}</div>
        <div class="mb-1"><strong>Expenses + cash return refunds:</strong> Tk {{ number_format((float) ($deductionsOther ?? 0), 2) }}</div>
        <div><strong>Available to give:</strong> Tk {{ number_format(max(0, (float) ($availableBalance ?? 0)), 2) }}</div>
        <div class="small text-muted mt-2 mb-0">Same basis as Home &ldquo;Available cash&rdquo;. Salary withdraw is also recorded for the employee.</div>
    </div>

    <div class="page-card p-3">
        <form action="{{ route('driver_cash_distribution.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Employee <span class="text-danger">*</span></label>
                <select name="employee_id" class="form-select js-example-basic-single" required>
                    <option value="">Select employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} ({{ $employee->designation }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount (Tk) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
                <div class="form-text">Cannot exceed your available carrying cash for the open period.</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Note</label>
                <textarea name="note" rows="2" class="form-control" placeholder="Optional">{{ old('note') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Save</button>
        </form>
    </div>
@endsection
