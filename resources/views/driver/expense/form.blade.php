@php
    $isEdit = isset($expenseentry);
@endphp

<form action="{{ $route ?? '' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    {{-- Expense type --}}
    <input type="hidden" name="type" value="2">

    {{-- Driver --}}
    <input type="hidden" name="driver_id" value="{{ Auth::user()->driver_id }}">

    <!-- Header -->
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <button type="button" class="btn btn-primary" onclick="history.back()">←</button>
            <span class="navbar-brand mx-auto">
                {{ $isEdit ? 'Edit Expense' : 'New Expense' }}
            </span>
        </div>
    </nav>

    <div class="container-fluid mt-3">

        <!-- Date -->
        <div class="card mb-2">
            <div class="card-body">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                    value="{{ old('date', $expenseentry->date ?? date('Y-m-d')) }}" required>
                @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Expense Title -->
        <div class="card mb-2">
            <div class="card-body">
                <label class="form-label">Expense Title <span class="text-danger">*</span></label>
                <select name="title_id" class="form-select @error('title_id') is-invalid @enderror" required>
                    <option value="">Choose One</option>
                    @foreach ($expenses as $expense)
                        <option value="{{ $expense->id }}"
                            {{ old('title_id', $expenseentry->title_id ?? '') == $expense->id ? 'selected' : '' }}>
                            {{ $expense->title }}
                        </option>
                    @endforeach
                </select>
                @error('title_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Amount -->
        <div class="card mb-2">
            <div class="card-body">
                <label class="form-label">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="amount"
                    class="form-control @error('amount') is-invalid @enderror"
                    value="{{ old('amount', $expenseentry->amount ?? '') }}" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Note -->
        <div class="card mb-2">
            <div class="card-body">
                <label class="form-label">Note</label>
                <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror"
                    placeholder="Enter note">{{ old('note', $expenseentry->note ?? '') }}</textarea>
                @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

    </div>

    <!-- Fixed Save Button -->
    <div class="fixed-action px-3">
        <button type="submit" class="btn btn-primary btn-lg w-100">
            {{ $isEdit ? 'Update Expense' : 'Save Expense' }}
        </button>
    </div>

</form>
