<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif

    <div class="row">
        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter employee name" value="{{ old('name', @$data->name) }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter email" value="{{ old('email', @$data->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Enter phone" value="{{ old('phone', @$data->phone) }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="designation" class="form-label">Designation</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" placeholder="Enter designation" value="{{ old('designation', @$data->designation) }}">
                @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="nid" class="form-label">NID</label>
                <input type="text" class="form-control @error('nid') is-invalid @enderror" id="nid" name="nid" placeholder="Enter NID" value="{{ old('nid', @$data->nid) }}">
                @error('nid') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="salary" class="form-label">Salary</label><span class="text-danger">*</span>
                <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror" id="salary" name="salary" placeholder="Enter salary" value="{{ old('salary', @$data->salary) }}">
                @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label><span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @foreach(
                        \App\Models\Employee::STATUS as $key => $value
                    )
                        <option value="{{ $key }}" {{ old('status', @$data->status ?? \App\Models\Employee::STATUS_ACTIVE) == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-12">
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                @if(!empty(@$data->image))
                    <div class="mt-2">
                        <img src="{{ asset('storage'.$data->image) }}" width="80" height="80" style="object-fit:cover;border-radius:8px;" alt="employee">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">{{ $buttonText ?? 'Submit' }}</button>
</form>

