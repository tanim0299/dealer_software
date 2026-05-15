@extends('driver.layouts.master')

@section('page_title', 'Issues')

@section('body')
    <div class="page-card p-3 mb-3">
        <form method="GET" action="{{ route('driver-issues.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-6">
                    <label class="form-label small mb-0">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="open" {{ ($search['status'] ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="accepted" {{ ($search['status'] ?? '') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ ($search['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="closed" {{ ($search['status'] ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small mb-0">Search</label>
                    <input type="text" name="free_text" class="form-control" placeholder="Note / ref" value="{{ $search['free_text'] ?? '' }}">
                </div>
                <div class="col-6">
                    <label class="form-label small mb-0">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $search['from_date'] ?? '' }}">
                </div>
                <div class="col-6">
                    <label class="form-label small mb-0">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $search['to_date'] ?? '' }}">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('driver-issues.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
    @forelse($issues as $issue)
        <div class="driver-tile">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small text-muted">{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</span>
                @if($issue->status === 'open')
                    <span class="badge rounded-pill bg-warning text-dark">Open</span>
                @elseif($issue->status === 'accepted')
                    <span class="badge rounded-pill bg-success">Accepted</span>
                @elseif($issue->status === 'closed')
                    <span class="badge rounded-pill bg-secondary">Closed</span>
                @elseif($issue->status == 'rejected')
                    <span class="badge rounded-pill bg-danger">Rejected</span>
                @else
                    <span class="badge rounded-pill bg-light text-dark">{{ $issue->status }}</span>
                @endif
            </div>

            <div class="fw-medium mb-1">{{ $issue->driver->name ?? 'N/A' }}</div>
            <div class="small text-muted mb-2">{{ $issue->driver->phone ?? '' }}</div>

            <div class="row small text-muted mb-3">
                <div class="col-6">Items: <strong class="text-dark">{{ $issue->items->count() }}</strong></div>
                <div class="col-6">Qty: <strong class="text-dark">{{ $issue->items->sum('issue_qty') }}</strong></div>
            </div>

            @if($issue->status === 'open')
                <div class="driver-actions">
                    <form method="POST" action="{{ route('driver-issues.accept', $issue->id) }}">
                        @csrf
                        <button type="submit"
                                class="driver-icon-action driver-icon-action--success"
                                title="Accept issue"
                                aria-label="Accept issue">
                            <i class="bi bi-check2" aria-hidden="true"></i>
                            <span class="visually-hidden">Accept issue</span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('driver-issues.reject', $issue->id) }}" onsubmit="return confirm('Reject this stock issue?');">
                        @csrf
                        <button type="submit"
                                class="driver-icon-action driver-icon-action--reject"
                                title="Reject issue"
                                aria-label="Reject issue">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                            <span class="visually-hidden">Reject issue</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="driver-empty page-card">No issues found.</div>
    @endforelse
    @if(method_exists($issues, 'hasPages') && $issues->hasPages())
        <div class="mt-3 d-flex justify-content-center">{{ $issues->links('pagination::bootstrap-5') }}</div>
    @endif
@endsection
