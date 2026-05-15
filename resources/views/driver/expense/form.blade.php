@php
    $isEdit = isset($expenseentry);
@endphp

<form action="{{ $route ?? '' }}" method="POST" enctype="multipart/form-data"
      id="driver-expense-form"
      data-max-cash="{{ isset($remainingCarryingCash) ? number_format((float) $remainingCarryingCash, 2, '.', '') : '' }}">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <input type="hidden" name="type" value="2">
    <input type="hidden" name="driver_id" value="{{ Auth::user()->driver_id }}">

    @if(isset($remainingCarryingCash))
        <div class="driver-tile mb-3">
            <div class="small text-muted">Maximum expense (carrying cash)</div>
            <div class="fw-bold text-success">Tk {{ number_format($remainingCarryingCash, 2) }}</div>
        </div>
    @endif

    <div class="page-card p-3 mb-3">
        <label class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
               value="{{ old('date', $expenseentry?->date ?? date('Y-m-d')) }}" required>
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="page-card p-3 mb-3">
        <label class="form-label">Expense title <span class="text-danger">*</span></label>
        <select name="title_id" class="form-select js-example-basic-single @error('title_id') is-invalid @enderror" required>
            <option value="">Choose one</option>
            @foreach ($expenses as $expense)
                <option value="{{ $expense->id }}" {{ old('title_id', $expenseentry?->title_id ?? '') == $expense->id ? 'selected' : '' }}>
                    {{ $expense->title }}
                </option>
            @endforeach
        </select>
        @error('title_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="page-card p-3 mb-3">
        <label class="form-label">Amount <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="amount" id="driver-expense-amount"
               class="form-control @error('amount') is-invalid @enderror"
               value="{{ old('amount', $expenseentry?->amount ?? '') }}"
               @if(isset($remainingCarryingCash))
                   max="{{ number_format((float) $remainingCarryingCash + 0.000001, 4, '.', '') }}"
               @endif
               required>
        @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="page-card p-3 mb-3">
        <label class="form-label">Note</label>
        <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="Optional">{{ old('note', $expenseentry?->note ?? '') }}</textarea>
        @error('note')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="pb-5 mb-4" aria-hidden="true"></div>

    <div class="fixed-action">
        <button type="submit" class="btn btn-primary btn-lg w-100 shadow">
            {{ $buttonText ?? ($isEdit ? 'Update expense' : 'Save expense') }}
        </button>
    </div>
</form>

@if(isset($remainingCarryingCash))
    @push('scripts')
    <script>
        document.getElementById('driver-expense-form')?.addEventListener('submit', function (e) {
            var max = parseFloat(this.getAttribute('data-max-cash'));
            if (!isFinite(max)) return;
            var amt = parseFloat(document.getElementById('driver-expense-amount')?.value || '0');
            if (amt > max + 0.02) {
                e.preventDefault();
                alert('Amount cannot exceed carrying cash (Tk ' + max.toFixed(2) + ').');
            }
        });
    </script>
    @endpush
@endif
