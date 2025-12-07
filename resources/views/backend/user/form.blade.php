<form action="@if(isset($route)) {{ $route }} @endif" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($method))
        @method($method)
    @endif
    <div class="row">
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="role_id" class="form-label">Role</label><span class="text-danger">*</span>
                <select class="form-select form-select js-example-basic-single" name="role_id" id="role_id">
                    <option value="">Chose One</option>
                    @forelse($roles as $role)
                    <option @if(old('role_id', $user->role_id) ==$role->id) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                    @empty

                    @endforelse
                </select>
                @error('index')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    placeholder="Enter User Name" value="{{ old('name', @$user->name) }}">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label><span class="text-danger">*</span>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                    placeholder="Enter User email" value="{{ old('email', @$user->email) }}">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                    placeholder="Enter User phone" value="{{ old('phone', @$user->phone) }}">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        @if(empty($method))
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="password" class="form-label">Password</label><span class="text-danger">*</span>
                <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                    placeholder="Enter User password" value="{{ old('password', @$user->password) }}">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
        </div>
        @endif
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image"
                    placeholder="Enter User image" value="">
                    @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>
            @if(!empty($method))
            <img src="{{ asset('storage') }}/{{ $user->image }}" alt="" srcset="" class="img-fluid" style="height:70px;width:70px">
            @endif
        </div>
    </div>
    @if(isset($buttonText))
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    @else
    <button type="submit" class="btn btn-primary">Submit</button>
    @endif
</form>
