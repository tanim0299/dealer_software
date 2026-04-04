@extends('driver.layouts.master')

@section('page_title', 'Expense List')

@section('body')
<div class="page-card p-3 mb-3">
    <a href="{{ route('expense_entry.create') }}" class="btn btn-primary w-100">
        <i class="bi bi-plus-circle me-1"></i> Add New Expense
    </a>
</div>

<div class="page-card p-3 mb-3">
    <h6 class="mb-3 text-center">Filter Expenses</h6>

    <form method="GET" action="{{ route('expense_entry.index') }}">
        <div class="mb-2">
            <select name="title_id" class="form-control js-example-basic-single">
                <option value="">All Expense Types</option>
                @foreach(($expenseTitles ?? []) as $title)
                    <option value="{{ $title->id }}" {{ data_get($search ?? [], 'title_id') == $title->id ? 'selected' : '' }}>
                        {{ $title->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <input type="number" step="0.01"
                   name="amount"
                     value="{{ data_get($search ?? [], 'amount') }}"
                   placeholder="Filter by Amount"
                   class="form-control">
        </div>

        <div class="row g-2">
            <div class="col-6">
                <input type="date" name="from_date" value="{{ data_get($search ?? [], 'from_date') }}" class="form-control">
            </div>
            <div class="col-6">
                <input type="date" name="to_date" value="{{ data_get($search ?? [], 'to_date') }}" class="form-control">
            </div>
        </div>

        <div class="row g-2 mt-1">
            <div class="col-6 d-grid">
                <button class="btn btn-primary btn-sm">Filter</button>
            </div>
            <div class="col-6 d-grid">
                <a href="{{ route('expense_entry.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </div>
    </form>
</div>

@forelse($expenses as $expense)
    <div class="page-card p-3 mb-2">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="mb-1 fw-bold">{{ $expense->expense->title ?? 'Expense' }}</h6>
                <div class="small text-muted">{{ $expense->date }}</div>
                @if(!empty($expense->note))
                    <div class="small mt-1">{{ $expense->note }}</div>
                @endif
            </div>
            <div class="text-end">
                <div class="fw-bold text-danger">à§³ {{ number_format($expense->amount, 2) }}</div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('expense_entry.edit', $expense->id) }}" class="btn btn-outline-primary btn-sm w-100">
                Edit
            </a>
            <form method="POST" action="{{ route('expense_entry.destroy', $expense->id) }}" class="w-100" onsubmit="return confirm('Delete this expense?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm w-100">Delete</button>
            </form>
        </div>
    </div>
@empty
    <div class="page-card p-4 text-center text-muted">
        No expense found.
    </div>
@endforelse

<div class="mt-3">
    {{ $expenses->links() }}
</div>
@endsection

