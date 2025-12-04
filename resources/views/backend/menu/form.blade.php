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
                    placeholder="Enter Sl" value="{{ old('sl') }}">
                @error('sl')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="section_id" class="form-label">Select Section</label><span class="text-danger">*</span>
                <select class="form-select @error('section_id') is-invalid @enderror" id="section_id" name="section_id">
                    <option value="">Chose One</option>
                    @forelse($sections as $section)
                        <option value="{{ $section->id }}" @if(old('section_id')==$section->id) selected @endif>{{ $section->name }}</option>
                    @empty
                        <option value="">No Section Found</option>
                    @endforelse
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="parent_id" class="form-label">Select Parent</label>
                <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                    <option value="">Chose One</option>
                    @forelse($parents as $parent)
                        <option value="{{ $parent->id }}" @if(old('parent_id')==$parent->id) selected @endif>{{ $parent->name }}</option>
                    @empty
                        <option value="">No Parent Found</option>
                    @endforelse
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label> <span class="text-danger">*</span>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    placeholder="Enter Name" value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="system_name" class="form-label">System Name</label> <span class="text-danger">*</span>
                <input type="text" class="form-control @error('system_name') is-invalid @enderror" id="system_name" name="system_name"
                    placeholder="Enter Name" value="{{ old('system_name') }}">
                @error('system_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="route" class="form-label">Route</label> <span class="text-danger">*</span>
                <input type="text" class="form-control @error('route') is-invalid @enderror" id="route" name="route"
                    placeholder="Enter Route Name" value="{{ old('route') }}">
                @error('route')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label> <span class="text-danger">*</span>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
                    placeholder="Enter Slug ( Eg : index, create, edit)" value="{{ old('slug') }}">
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="icon" class="form-label">Icon</label> <span class="text-danger">*</span>
                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon"
                    placeholder="" value="{{ old('icon') }}">
                @error('icon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="status" class="form-label">Status</label> <span class="text-danger">*</span>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    @foreach(\App\Models\Menu::STATUS as $key => $value)
                        <option value="{{ $key }}" @if(old('status')==$key) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label for="type" class="form-label">Type</label><span class="text-danger">*</span> 
                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                    <option value="">Chose One</option>
                    @foreach(\App\Models\Menu::TYPE as $key => $value)
                        <option value="{{ $key }}" @if(old('type')==$key) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <b>Permissions :</b><br>
            <div class="card">
                <div class="card-body">
                    @foreach($permissions as $permission)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}"
                            @if(is_array(old('permissions')) && in_array($permission->id, old('permissions'))) checked @endif>
                            <label class="form-check-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @if(isset($buttonText))
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    @else
    <button type="submit" class="btn btn-primary">Submit</button>
    @endif
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

        function updatePermissions() {
            const selectedType = typeSelect.value;

            permissionCheckboxes.forEach(ch => {
                if (selectedType != '1' && selectedType !== '') {
                    ch.checked = true;   // auto select
                } else {
                    ch.checked = false;  // unselect
                }
            });
        }

        typeSelect.addEventListener('change', updatePermissions);
    });
</script>
