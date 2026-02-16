@extends('driver.layouts.master')

@section('body')
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <button class="btn btn-light" onclick="history.back()">← Back</button>
        <span class="navbar-brand mx-auto">Issue List</span>
    </div>
</nav>

<div class="container mx-auto p-2">
    <div class="space-y-4">
        <div class="">
            @forelse($issues as $key => $issue)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">

                        <h6 class="mb-1">
                            <strong>Date:</strong>
                            {{ \Carbon\Carbon::parse($issue->issue_date)->format('d-m-Y') }}
                        </h6>

                        <p class="mb-1">
                            <strong>Driver:</strong>
                            {{ $issue->driver->name ?? 'N/A' }} <br>
                            <small class="text-muted">
                                {{ $issue->driver->phone ?? '' }}
                            </small>
                        </p>

                        <p class="mb-1">
                            <strong>Total Items:</strong>
                            {{ $issue->items->count() }}
                        </p>

                        <p class="mb-2">
                            <strong>Total Qty:</strong>
                            {{ $issue->items->sum('issue_qty') }}
                        </p>

                        <div class="d-flex justify-content-between">

                            @if($issue->status === 'open')
                                <a class="btn btn-success btn-sm" href="{{ route('driver-issues.accept', $issue->id) }}">
                                    ✔ Accept
                                </a>
                                
                                <a class="btn btn-danger btn-sm" href="{{ route('driver-issues.reject', $issue->id) }}">
                                    ✖ Reject
                                </a>
                            @elseif($issue->status == 'rejected')
                                <span class="badge bg-danger w-100 text-center">
                                    Rejected
                                </span>
                            @else
                                <span class="badge bg-success w-100 text-center">
                                    Already Processed
                                </span>
                            @endif

                        </div>

                    </div>
                </div>
            @empty
                <div class="text-center text-muted">
                    No driver issue found
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
