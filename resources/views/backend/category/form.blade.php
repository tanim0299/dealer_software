<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif
    <div class="row">
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="sl" class="form-label">Sl</label><span class="text-danger">*</span>
                <input type="number" class="form-control @error('sl') is-invalid @enderror" id="sl" name="sl"
                    placeholder="Enter Sl" value="{{ old('sl', @$category->sl) }}">
                @error('sl')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="item_id" class="form-label">Select Item</label><span class="text-danger">*</span>
                <select class="form-select @error('item_id') is-invalid @enderror" name="item_id" id="item_id">
                    <option value="">Chose One</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" @if(old('item_id', $category->item_id ?? '') == $item->id) selected @endif>{{ $item->name }}</option>
                    @endforeach
                </select>
                @error('item_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    placeholder="Enter Category Name" value="{{ old('name', @$category->name) }}">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label><span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @foreach(\App\Models\Category::STATUS as $key => $value)
                        <option value="{{ $key }}" {{ old('status', @$category->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
