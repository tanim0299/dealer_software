<form action="@if(isset($route)) {{ $route }} @endif" method="POST">
    @csrf
    @if(isset($method))
        @method($method)
    @endif

    <div class="row">

        {{-- Driver Name --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Driver Name</label>
                <span class="text-danger">*</span>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name"
                       name="name"
                       placeholder="Enter Driver Name"
                       value="{{ old('name', @$data->name) }}">
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Phone --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
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

        {{-- Vehicle No --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="vehicle_no" class="form-label">Vehicle No</label>
                <input type="text"
                       class="form-control @error('vehicle_no') is-invalid @enderror"
                       id="vehicle_no"
                       name="vehicle_no"
                       placeholder="Enter Vehicle Number"
                       value="{{ old('vehicle_no', @$data->vehicle_no) }}">
                @error('vehicle_no')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Address --}}
        <div class="col-md-8 col-lg-8 col-12">
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea
                    class="form-control @error('address') is-invalid @enderror"
                    id="address"
                    name="address"
                    placeholder="Enter Address"
                    rows="2">{{ old('address', @$data->address) }}</textarea>
                @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Status --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror"
                        id="status"
                        name="status">
                    <option value="active" {{ old('status', @$data->status) == 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="inactive" {{ old('status', @$data->status) == 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

    </div>

    <button type="submit" class="btn btn-primary">
        {{ $buttonText ?? 'Submit' }}
    </button>
</form>
