<form action="@if(isset($route)) {{ $route }} @endif" method="POST">
    @csrf
    @if(isset($method))
        @method($method)
    @endif

    <div class="row">
        {{-- Supplier Name --}}
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Supplier Name</label><span class="text-danger">*</span>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       name="name"
                       placeholder="Enter Supplier Name"
                       value="{{ old('name', @$data->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Phone --}}
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label><span class="text-danger">*</span>
                <input type="text"
                       class="form-control @error('phone') is-invalid @enderror"
                       id="phone"
                       name="phone"
                       placeholder="Enter Phone Number"
                       value="{{ old('phone', @$data->phone) }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       placeholder="Enter Email Address"
                       value="{{ old('email', @$data->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label><span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @foreach(\App\Models\Supplier::STATUS as $key => $value)
                        <option value="{{ $key }}" {{ old('status', @$data->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Address --}}
        <div class="col-md-12 col-lg-12 col-12">
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control @error('address') is-invalid @enderror"
                          id="address"
                          name="address"
                          rows="3"
                          placeholder="Enter Address">{{ old('address', @$data->address) }}</textarea>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ $buttonText ?? 'Submit' }}
    </button>
</form>
