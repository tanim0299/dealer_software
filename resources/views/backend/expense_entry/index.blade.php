@extends('backend.layouts.master')
@section('title', 'List Of Expense Entry')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                @include('backend.layouts.partials.breadcrumb', ['page_title' => 'List Of Expense Entry'])

                <div class="ms-md-auto py-2 py-md-0">
                    <a href="{{ route('expense_entry.create') }}" class="btn btn-primary btn-round">
                        <i class="fa fa-plus"></i> Create Expense Entry
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="card">
                    <div class="card-body">

                        {{-- Filter --}}
                        <form method="get" action="{{ route('expense_entry.index') }}">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-md-4">
                                    <input type="text" name="free_text" class="form-control" placeholder="Search..."
                                        value="{{ request('free_text') }}">
                                </div>

                                <div class="col-lg-4 col-md-4">
                                    <button class="btn btn-primary">
                                        <i class="fa fa-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">SL</th>
                                        <th>Date</th>
                                        <th>Expense Title</th>
                                        <th>Amount</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $key => $value)
                                        <tr>
                                            <td>{{ $data->firstItem() + $key }}</td>
                                            <td>{{ $value->date }}</td>
                                            <td>
                                                {{ $value->expense->title ?? 'N/A' }}
                                                (<small class="text-muted">
                                                    {{ $value->driver->name ?? 'N/A' }}
                                                </small>)
                                            </td>
                                            <td>{{ number_format($value->amount, 2) }}</td>
                                            <td>
                                                <a href="{{ route('expense_entry.edit', $value->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    Edit
                                                </a>

                                                <form action="{{ route('expense_entry.destroy', $value->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this expense entry?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                No Expense Entry Found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            {{ $data->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
