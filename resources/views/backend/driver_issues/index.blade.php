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
                        <form method="GET" action="{{ route('driver-issues.index') }}" class="mb-3">
                            <div class="row g-2 align-items-end">
                                @unless(auth()->user()->hasRole('Driver'))
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small mb-0">DSR</label>
                                    <select name="driver_id" class="form-select js-example-basic-single">
                                        <option value="">All DSRs</option>
                                        @foreach($drivers ?? [] as $d)
                                            <option value="{{ $d->id }}" {{ ($search['driver_id'] ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endunless
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label small mb-0">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All</option>
                                        <option value="open" {{ ($search['status'] ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="accepted" {{ ($search['status'] ?? '') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="rejected" {{ ($search['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="closed" {{ ($search['status'] ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label small mb-0">From</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ $search['from_date'] ?? '' }}">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label small mb-0">To</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ $search['to_date'] ?? '' }}">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label small mb-0">Search</label>
                                    <input type="text" name="free_text" class="form-control" placeholder="DSR name / phone" value="{{ $search['free_text'] ?? '' }}">
                                </div>
                                <div class="col-lg-auto col-md-6 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                                    <a href="{{ route('driver-issues.index') }}" class="btn btn-outline-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
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
                <span class="badge bg-warning text-dark">Open</span>
            @elseif($issue->status === 'accepted')
                <span class="badge bg-success">Accepted</span>
            @elseif($issue->status === 'rejected')
                <span class="badge bg-danger">Rejected</span>
            @elseif($issue->status === 'closed')
                <span class="badge bg-secondary">Closed</span>
            @else
                <span class="badge bg-light text-dark">{{ $issue->status }}</span>
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
                            {{ $issues->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection





