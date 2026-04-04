@extends('backend.layouts.master')
@section('title','Salary Deposit')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Employee Salary Deposit</h5>

                @if(Auth::user()->can('Employee Salary Deposit view'))
                <a href="{{ route('employee_salary_deposit.index') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-eye"></i> View
                </a>
                @endif
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('employee_salary_deposit.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label>Employee</label>
                        <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" data-salary="{{ $employee->salary }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->designation }})</option>
                            @endforeach
                        </select>
                        @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Monthly Salary</label>
                        <input type="text" id="monthly_salary" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Salary Month</label>
                        <input type="month" name="salary_month" value="{{ old('salary_month') }}" class="form-control @error('salary_month') is-invalid @enderror" required>
                        @error('salary_month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Deposit Amount</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required>
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Note</label>
                        <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Deposit</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setSalaryFromEmployee() {
        const employee = document.getElementById('employee_id');
        const selected = employee.options[employee.selectedIndex];
        document.getElementById('monthly_salary').value = selected ? (selected.dataset.salary || '') : '';
    }

    document.getElementById('employee_id').addEventListener('change', setSalaryFromEmployee);
    setSalaryFromEmployee();
</script>
@endpush
@endsection

