@extends('backend.layouts.master')
@section('title','Driver Issue List')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- Page Header --}}
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',[
                'page_title' => 'Driver Issue List'
            ])

            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{ route('driver-issues.create') }}"
                   class="btn btn-primary btn-round">
                    + New Issue
                </a>
            </div>
        </div>

        {{-- Main Card --}}
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">SL</th>
                                        <th>Issue Date</th>
                                        <th>Driver</th>
                                        <th>Total Items</th>
                                        <th>Total Qty</th>
                                        <th>Status</th>
                                        <th width="150">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($issues as $issue)
    <tr>
        <!-- Serial -->
        <td>{{ $loop->iteration }}</td>

        <!-- Date -->
        <td>
            {{ optional($issue->issue_date)->format('d-m-Y') ?? '-' }}
        </td>

        <!-- Driver -->
        <td>
            <strong>{{ optional($issue->driver)->name ?? 'N/A' }}</strong><br>
            <small class="text-muted">
                {{ optional($issue->driver)->phone ?? '' }}
            </small>
        </td>

        <!-- Total Items -->
        <td>
            {{ $issue->items ? $issue->items->count() : 0 }}
        </td>

        <!-- Total Qty -->
        <td>
            {{ $issue->items ? $issue->items->sum('issue_qty') : 0 }}
        </td>

        <!-- Status -->
        <td>
            @if($issue->status === 'open')
                <span class="badge bg-warning">Open</span>
            @else
                <span class="badge bg-success">Closed</span>
            @endif
        </td>

        <!-- Actions -->
        <td>
            <!-- View -->
            <a href="{{ route('driver-issues.show', $issue->id) }}"
               class="btn btn-sm btn-info">
                View
            </a>

            @if($issue->status === 'open')

                <!-- Edit -->
                <a href="{{ route('driver-issues.edit', $issue->id) }}"
                   class="btn btn-sm btn-primary">
                    Edit
                </a>

                <!-- Delete -->
                <form action="{{ route('driver-issues.destroy', $issue->id) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this issue?');">

                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger btn-sm">
                        Delete
                    </button>
                </form>

            @endif
        </td>
    </tr>

@empty
    <tr>
        <td colspan="7" class="text-center text-muted">
            No driver issue found
        </td>
    </tr>
@endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3">
                            {{ $issues->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection





