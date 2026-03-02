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
                                    <td colspan="10" class="text-center">No employees found.</td>
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
