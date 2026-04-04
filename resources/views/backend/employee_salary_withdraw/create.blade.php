@extends('backend.layouts.master')
@section('title','Salary Withdraw')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Employee Salary Withdraw</h5>

                @if(Auth::user()->can('Employee Salary Withdraw view'))
                <a href="{{ route('employee_salary_withdraw.index') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-eye"></i> View
                </a>
                @endif
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('employee_salary_withdraw.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label>Employee</label>
                        <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }} ({{ $employee->designation }})</option>
                            @endforeach
                        </select>
                        @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Available Salary Balance</label>
                        <input type="text" id="available_balance" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Withdraw Date</label>
                        <input type="date" name="withdraw_date" value="{{ old('withdraw_date', now()->toDateString()) }}" class="form-control @error('withdraw_date') is-invalid @enderror" required>
                        @error('withdraw_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Withdraw Amount</label>
                        <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" required>
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Note</label>
                        <textarea name="note" class="form-control">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Withdraw</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('employee_id').addEventListener('change', function() {
        let employeeId = this.value;
        document.getElementById('available_balance').value = '';

        if (!employeeId) return;

        fetch("{{ url('employee-salary/balance') }}/" + employeeId)
            .then(res => res.json())
            .then(data => {
                document.getElementById('available_balance').value = data.balance;
            });
    });

    if (document.getElementById('employee_id').value) {
        document.getElementById('employee_id').dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection

