<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif
    <div class="row">
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Section Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    placeholder="Enter Section Name" value="{{ old('name') }}">
            </div>
        </div>
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    @foreach(\App\Models\MenuSection::STATUS as $key => $value)
                        <option value="{{ $key }}" @if(old('status')==$key) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    @if(isset($buttonText))
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    @else
    <button type="submit" class="btn btn-primary">Submit</button>
    @endif
</form>