@extends('backend.layouts.master')
@section('title','Employee List')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Employee Management'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Employee create'))
                <a href="{{ route('employee.create') }}" class="btn btn-label-info btn-round me-2">Create Employee</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Employee List</h4>
            </div>
            <div class="card-body">
                <form method="get" action="{{ route('employee.index') }}" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-0">Search</label>
                            <input type="text" name="free_text" class="form-control" placeholder="Name, phone, email, NID…" value="{{ $search['free_text'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted mb-0">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All</option>
                                <option value="1" {{ ($search['status'] ?? '') === '1' || ($search['status'] ?? '') === 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ ($search['status'] ?? '') === '0' || ($search['status'] ?? '') === 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                            <a href="{{ route('employee.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Designation</th>
                                <th>NID</th>
                                <th>Salary</th>
                                <th class="text-end">Salary balance</th>
                                <th>Status</th>
                                <th width="130">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if(!empty($employee->image))
                                            <img src="{{ asset('storage'.$employee->image) }}" width="45" height="45" style="object-fit:cover;border-radius:50%;" alt="employee">
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->phone }}</td>
                                    <td>{{ $employee->designation }}</td>
                                    <td>{{ $employee->nid }}</td>
                                    <td>{{ number_format($employee->salary, 2) }}</td>
                                    @php $salBal = $employee->salaryBalanceDisplayed(); @endphp
                                    <td class="text-end fw-semibold {{ $salBal < 0 ? 'text-danger' : '' }}">Tk {{ number_format($salBal, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $employee->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $employee->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(Auth::user()->can('Employee edit'))
                                        <a href="{{ route('employee.edit', $employee->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        @endif

                                        @if(Auth::user()->can('Employee destroy'))
                                        <form action="{{ route('employee.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">No employees found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $employees->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





