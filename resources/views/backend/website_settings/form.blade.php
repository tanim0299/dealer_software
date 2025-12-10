<form method="post" enctype="multipart/form-data" action="{{ $route ?? '' }}">
    @csrf
    @if(!empty($method))
        @method($method)
    @endif

    <div class="card border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Website Settings</h5>
        </div>

        <div class="card-body">

            <!-- Logo -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Logo</label>
                <div class="d-flex align-items-center">

                    <div class="border rounded bg-light d-flex justify-content-center align-items-center me-3"
                         style="width: 120px; height: 120px;">
                        @if(!empty($settings->logo))
                            <img src="{{ asset('storage/' . $settings->logo) }}"
                                 alt="Logo" class="img-fluid" style="max-height:100px;">
                        @else
                            <i class="fas fa-image text-muted fa-2x"></i>
                        @endif
                    </div>

                    <div>
                        <input type="file"
                               class="form-control form-control-sm @error('logo') is-invalid @enderror"
                               name="logo" accept="image/*">
                        <small class="text-muted">Recommended: 200x60px</small>
                        @error('logo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Favicon -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Favicon</label>
                <div class="d-flex align-items-center">

                    <div class="border rounded bg-light d-flex justify-content-center align-items-center me-3" style="width: 80px; height: 80px;">
                        @if(!empty($settings->favicon))
                            <img src="{{ asset('storage/' . $settings->favicon) }}" alt="Favicon" class="img-fluid" style="max-height:60px;">
                        @else
                            <i class="fas fa-image text-muted"></i>
                        @endif
                    </div>

                    <div>
                        <input type="file" class="form-control form-control-sm @error('favicon') is-invalid @enderror" name="favicon" accept="image/*">
                        <small class="text-muted">Recommended: 32x32px</small>
                        @error('favicon')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Title -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" name="title" value="{{ old('title', $settings->title ?? '') }}">
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Phone -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $settings->phone ?? '') }}">
                @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Address</label>
                <textarea class="form-control form-control-sm @error('address') is-invalid @enderror" name="address" rows="3">{{ old('address', $settings->address ?? '') }}</textarea>
                @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <div class="card-footer text-end bg-white border-top">
            <button type="submit" class="btn btn-primary btn-sm">
                {{ $buttonText ?? 'Submit' }}
            </button>
        </div>
    </div>
</form>
