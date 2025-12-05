<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif
    <div class="row">
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="index" class="form-label">Index</label><span class="text-danger">*</span>
                <input type="number" class="form-control @error('index') is-invalid @enderror" id="index" name="index"
                    placeholder="Enter index" value="{{ old('index', @$role->index) }}">
                @error('index')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Role Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    placeholder="Enter Role Name" value="{{ old('name', @$role->name) }}">
                    @error('name')
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
