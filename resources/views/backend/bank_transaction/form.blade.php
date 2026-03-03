<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif
    <div class="row">
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="bank_account_id" class="form-label">Bank Account</label><span class="text-danger">*</span>
                <select class="form-select @error('bank_account_id') is-invalid @enderror" id="bank_account_id" name="bank_account_id">
                    <option value="">Select Bank Account</option>
                    @foreach($bankAccounts as $account)
                        <option value="{{ $account->id }}" 
                            data-balance="{{ $account->balance }}"
                            {{ old('bank_account_id', @$bankTransaction->bank_account_id) == $account->id ? 'selected' : '' }}>
                            {{ $account->account_name }} ({{ $account->account_number }}) - Balance: {{ number_format($account->balance, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('bank_account_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="current_balance" class="form-label">Current Balance</label>
                <input type="text" class="form-control" id="current_balance" readonly>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="type" class="form-label">Transaction Type</label><span class="text-danger">*</span>
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                    <option value="">Select Type</option>
                    @foreach(\App\Models\BankTransaction::TYPES as $key => $value)
                        <option value="{{ $key }}" {{ old('type', @$bankTransaction->type) == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label><span class="text-danger">*</span>
                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount"
                    placeholder="Enter Amount" value="{{ old('amount', @$bankTransaction->amount) }}">
                <small class="text-danger" id="balance_warning" style="display:none;">
                    ⚠️ Insufficient balance for this withdrawal!
                </small>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="transaction_date" class="form-label">Transaction Date</label>
                <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" id="transaction_date" name="transaction_date"
                    value="{{ old('transaction_date', @$bankTransaction->transaction_date ?? date('Y-m-d')) }}">
                @error('transaction_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-lg-12 col-12">
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                    placeholder="Enter Description" rows="3">{{ old('description', @$bankTransaction->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if(isset($buttonText))
                <button type="submit" class="btn btn-primary" id="submitBtn">{{ $buttonText }}</button>
            @else
                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
            @endif
            <a href="{{ route('bank_transaction.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountSelect = document.getElementById('bank_account_id');
    const amountInput = document.getElementById('amount');
    const typeSelect = document.getElementById('type');
    const balanceWarning = document.getElementById('balance_warning');
    const submitBtn = document.getElementById('submitBtn');

    function updateBalance() {
        const selected = accountSelect.options[accountSelect.selectedIndex];
        const balance = parseFloat(selected.dataset.balance) || 0;
        document.getElementById('current_balance').value = balance.toFixed(2);
        validateAmount();
    }

    function validateAmount() {
        const amount = parseFloat(amountInput.value) || 0;
        const selected = accountSelect.options[accountSelect.selectedIndex];
        const balance = parseFloat(selected.dataset.balance) || 0;
        const type = typeSelect.value;

        if (type === 'withdraw' && amount > balance) {
            balanceWarning.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            balanceWarning.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    accountSelect.addEventListener('change', updateBalance);
    amountInput.addEventListener('input', validateAmount);
    typeSelect.addEventListener('change', validateAmount);

    // Initialize on page load
    updateBalance();
});
</script>
