@extends('backend.layouts.master')
@section('title','Create Supplier')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- Header --}}
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Supplier Management'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Supplier create'))
                <a href="{{route('supplier.create')}}" class="btn btn-label-info btn-round me-2">Create Supplier</a>
                @endif
            </div>
        </div>

        {{-- Supplier List --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Supplier List</h4>
                    </div>

                    <div class="card-body">
                        <form method="get" action="{{ route('supplier.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-0">Search</label>
                                    <input type="text" name="free_text" class="form-control" placeholder="Name, phone, email, ID" value="{{ $search['free_text'] ?? '' }}">
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
                                    <a href="{{ route('supplier.index') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Supplier ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th width="130">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($suppliers as $supplier)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $supplier->supplier_id }}</td>
                                            <td>{{ $supplier->name }}</td>
                                            <td>{{ $supplier->phone }}</td>
                                            <td>
                                                <span class="badge {{ $supplier->status ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $supplier->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{-- Edit --}}
                                                <a href="{{ route('supplier.edit', $supplier->id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>

                                                {{-- Delete --}}
                                                <form action="{{ route('supplier.destroy', $supplier->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                No suppliers found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $suppliers->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection





