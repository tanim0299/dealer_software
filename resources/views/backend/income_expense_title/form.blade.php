<form action="{{ isset($route) ? $route : '' }}" method="POST">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="row">

        {{-- Title --}}
        <div class="col-md-4 col-12">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <span class="text-danger">*</span>

                <input type="text"
                       class="form-control @error('title') is-invalid @enderror"
                       id="title"
                       name="title"
                       placeholder="Enter Title"
                       value="{{ old('title', @$data->title) }}">

                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Type --}}
        <div class="col-md-4 col-12">
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <span class="text-danger">*</span>

                <select name="type"
                        id="type"
                        class="form-select @error('type') is-invalid @enderror">
                    <option value="">Select Type</option>
                    <option value="1" {{ old('type', @$data->type) == 1 ? 'selected' : '' }}>
                        Income
                    </option>
                    <option value="2" {{ old('type', @$data->type) == 2 ? 'selected' : '' }}>
                        Expense
                    </option>
                </select>

                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label><span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @foreach(\App\Models\Item::STATUS as $key => $value)
                        <option value="{{ $key }}" {{ old('status', @$data->status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
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
