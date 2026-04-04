@extends('backend.layouts.master')
@section('title','Salary Deposit List')

@section('content')
<div class="container">
    <div class="page-inner">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Employee Salary Deposit List</h4>

            @if(Auth::user()->can('Employee Salary Deposit create'))
            <a href="{{ route('employee_salary_deposit.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Create New
            </a>
            @endif
        </div>

        <br>

        <form method="GET" class="card p-3 mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <select name="employee_id" class="form-select">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ data_get($search ?? [], 'employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="month" name="month" value="{{ data_get($search ?? [], 'month') }}" class="form-control">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Month</th>
                            <th>Employee</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                            <tr>
                                <td>{{ $deposit->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($deposit->salary_month)->format('F Y') }}</td>
                                <td>{{ $deposit->employee->name ?? 'N/A' }}</td>
                                <td>{{ number_format($deposit->amount, 2) }}</td>
                                <td>{{ $deposit->note }}</td>
                                <td>
                                    @if(Auth::user()->can('Employee Salary Deposit destroy'))
                                    <form action="{{ route('employee_salary_deposit.destroy', $deposit->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure to delete this deposit?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No deposits found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $deposits->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection





