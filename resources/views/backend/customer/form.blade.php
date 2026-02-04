<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif
    <div class="row">
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="area_id" class="form-label">Select Customer Area</label><span class="text-danger">*</span>
                <select class="form-select @error('area_id') is-invalid @enderror" name="area_id" id="area_id">
                    <option value="">Chose One</option>
                    @foreach($customer_areas as $area)
                        <option value="{{ $area->id }}" @if(old('area_id', $data->area_id ?? '') == $area->id) selected @endif>{{ $area->name }}</option>
                    @endforeach
                </select>
                @error('area_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Customer Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    placeholder="Enter Customer Name" value="{{ old('name', @$data->name) }}">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="phone" class="form-label">Customer Phone</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                    placeholder="Enter Customer phone" value="{{ old('phone', @$data->phone) }}">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                    placeholder="Enter Customer email" value="{{ old('email', @$data->email) }}">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
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
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label><span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @foreach(\App\Models\Customer::STATUS as $key => $value)
                        <option value="{{ $key }}" {{ old('status', @$data->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    @if(isset($buttonText))
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    @else
    <button type="submit" class="btn btn-primary">Submit</button>
    @endif
</form>
