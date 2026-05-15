@extends('driver.layouts.master')

@section('page_title', 'Expenses')

@section('body')
    @if(isset($remainingCarryingCash))
        <div class="driver-tile mb-3">
            <div class="small text-muted">Cash left after expenses (open period)</div>
            <div class="fw-bold text-success">Tk {{ number_format($remainingCarryingCash ?? 0, 2) }}</div>
            <div class="small text-muted mt-1">New expense cannot exceed this amount.</div>
        </div>
    @endif

    <div class="page-card p-3 mb-3">
        <a href="{{ route('expense_entry.create') }}" class="btn btn-primary w-100">
            <i class="bi bi-plus-lg me-1"></i> Add expense
        </a>
    </div>

    <div class="page-card p-3 mb-3">
        <div class="fw-medium mb-3">Filter</div>
        <form method="GET" action="{{ route('expense_entry.index') }}">
            <div class="mb-3">
                <label class="form-label">Search</label>
                <input type="text" name="free_text" class="form-control" placeholder="Note / title / amount" value="{{ data_get($search ?? [], 'free_text') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="title_id" class="form-select js-example-basic-single">
                    <option value="">All types</option>
                    @foreach(($expenseTitles ?? []) as $title)
                        <option value="{{ $title->id }}" {{ data_get($search ?? [], 'title_id') == $title->id ? 'selected' : '' }}>
                            {{ $title->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" value="{{ data_get($search ?? [], 'amount') }}" class="form-control" placeholder="Filter by amount">
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" value="{{ data_get($search ?? [], 'from_date') }}" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" value="{{ data_get($search ?? [], 'to_date') }}" class="form-control">
                </div>
            </div>

            <div class="row g-2">
                <div class="col-6 d-grid">
                    <button class="btn btn-primary" type="submit">Apply</button>
                </div>
                <div class="col-6 d-grid">
                    <a href="{{ route('expense_entry.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    @forelse($expenses as $expense)
        <div class="driver-tile">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <div class="fw-bold">{{ $expense->expense->title ?? 'Expense' }}</div>
                    <div class="small text-muted">{{ $expense->date }}</div>
                    @if(!empty($expense->note))
                        <div class="small mt-1">{{ $expense->note }}</div>
                    @endif
                </div>
                <div class="text-end fw-bold text-danger">Tk {{ number_format($expense->amount, 2) }}</div>
            </div>
            <div class="driver-actions">
                <a href="{{ route('expense_entry.edit', $expense->id) }}"
                   class="driver-icon-action driver-icon-action--edit"
                   title="Edit expense"
                   aria-label="Edit expense">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    <span class="visually-hidden">Edit expense</span>
                </a>
                <form method="POST" action="{{ route('expense_entry.destroy', $expense->id) }}" onsubmit="return confirm('Delete this expense?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="driver-icon-action driver-icon-action--delete"
                            title="Delete expense"
                            aria-label="Delete expense">
                        <i class="bi bi-trash3" aria-hidden="true"></i>
                        <span class="visually-hidden">Delete expense</span>
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="driver-empty page-card">No expenses found.</div>
    @endforelse

    <div class="mt-3 d-flex justify-content-center">
        {{ $expenses->links() }}
    </div>
@endsection
