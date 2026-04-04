@extends('driver.layouts.master')

@section('page_title', 'Give Amount')

@section('body')
<div class="page-card p-3">
    <form action="{{ route('driver_cash_distribution.store') }}" method="POST">
        @csrf

        <div class="mb-2">
            <label class="form-label">Employee <span class="text-danger">*</span></label>
            <select name="employee_id" class="form-select js-example-basic-single" required>
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }} ({{ $employee->designation }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <label class="form-label">Date <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
        </div>

        <div class="mb-2">
            <label class="form-label">Amount (BDT) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Note</label>
            <textarea name="note" rows="2" class="form-control" placeholder="Optional note">{{ old('note') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Save</button>
    </form>
</div>
@endsection

