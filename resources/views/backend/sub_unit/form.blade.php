<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif
    <div class="row">
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="order_by" class="form-label">Sl</label><span class="text-danger">*</span>
                <input type="number" class="form-control @error('order_by') is-invalid @enderror" id="order_by" name="order_by"
                    placeholder="Enter Sl" value="{{ old('order_by', @$subunit->order_by) }}">
                @error('order_by')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="unit_id" class="form-label">Select Unit</label><span class="text-danger">*</span>
                <select class="form-select @error('unit_id') is-invalid @enderror" name="unit_id" id="unit_id">
                    <option value="">Chose One</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" @if(old('unit_id', $subunit->unit_id ?? '') == $unit->id) selected @endif>{{ $unit->name }}</option>
                    @endforeach
                </select>
                @error('unit_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Sub Unit Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    placeholder="Enter Sub Unit Name" value="{{ old('name', @$subunit->name) }}">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="unit_data" class="form-label">Unit Data</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('unit_data') is-invalid @enderror" id="unit_data" name="unit_data"
                    placeholder="Enter Unit Data" value="{{ old('unit_data', @$subunit->unit_data) }}">
                    @error('unit_data')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label><span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @foreach(\App\Models\SubUnit::STATUS as $key => $value)
                        <option value="{{ $key }}" {{ old('status', @$subunit->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
