<form action="{{ $route ?? '' }}" method="POST" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="row">
        {{-- Date --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <span class="text-danger">*</span>
                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                    name="date" value="{{ old('date', $incomeentry->date ?? '') }}" required>
                @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Income Title --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="title_id" class="form-label">Select Income Title</label>
                <span class="text-danger">*</span>
                <select class="form-select @error('title_id') is-invalid @enderror" name="title_id" id="title_id"
                    required>
                    <option value="">Choose One</option>
                    @foreach ($incomes as $income)
                        <option value="{{ $income->id }}"
                            {{ old('title_id', $incomeentry->title_id ?? '') == $income->id ? 'selected' : '' }}>
                            {{ $income->title }}
                        </option>
                    @endforeach
                </select>
                @error('title_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Amount --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <span class="text-danger">*</span>
                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror"
                    id="amount" name="amount" placeholder="Enter Amount"
                    value="{{ old('amount', $incomeentry->amount ?? '') }}" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Note --}}
        <div class="col-md-8 col-lg-8 col-12">
            <div class="mb-3">
                <label for="note" class="form-label">Note</label>
                <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note"
                    placeholder="Enter note" rows="2">{{ old('note', $incomeentry->note ?? '') }}</textarea>
                @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $buttonText ?? 'Submit' }}
    </button>
</form>
