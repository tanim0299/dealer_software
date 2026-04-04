<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif
    <div class="row">
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="account_name" class="form-label">Account Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name"
                    placeholder="Enter Account Name" value="{{ old('account_name', @$bankAccount->account_name) }}">
                @error('account_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="account_number" class="form-label">Account Number</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number"
                    placeholder="Enter Account Number" value="{{ old('account_number', @$bankAccount->account_number) }}" @if(isset($bankAccount)) readonly @endif>
                @error('account_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="bank_name" class="form-label">Bank Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name"
                    placeholder="Enter Bank Name" value="{{ old('bank_name', @$bankAccount->bank_name) }}">
                @error('bank_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        @if(!isset($bankAccount))
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="initial_balance" class="form-label">Initial Balance</label><span class="text-danger">*</span>
                <input type="number" step="0.01" class="form-control @error('initial_balance') is-invalid @enderror" id="initial_balance" name="initial_balance"
                    placeholder="Enter Initial Balance" value="{{ old('initial_balance', 0) }}">
                @error('initial_balance')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        @else
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="balance" class="form-label">Current Balance</label>
                <input type="text" class="form-control" id="balance" name="balance" readonly value="{{ number_format($bankAccount->balance, 2) }}">
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label><span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @foreach(\App\Models\BankAccount::STATUS as $key => $value)
                        <option value="{{ $key }}" {{ old('status', @$bankAccount->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                    placeholder="Enter Description" rows="1">{{ old('description', @$bankAccount->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if(isset($buttonText))
                <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
            @else
                <button type="submit" class="btn btn-primary">Submit</button>
            @endif
            <a href="{{ route('bank_account.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</form>

