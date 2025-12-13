@extends('backend.layouts.master')
@section('title','Role Permission')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Role Permission -'.$role->name])
            <div class="ms-md-auto py-2 py-md-0">
                @if(auth()->user()->can('Role view'))
                <a href="{{route('role.index')}}" class="btn btn-primary  btn-round"><i class="fa fa-plus"></i> View Role</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Permissions</h5>

                    {{-- Select All --}}
                    <div class="form-check mb-3">
                        <input type="checkbox" id="check_all" class="form-check-input">
                        <label class="form-check-label fw-bold" for="check_all">
                            Select All Permissions
                        </label>
                    </div>

                    <form action="{{route('role.permission',$role->id)}}" method="post">
                        @csrf
                        <div class="row">
                            @forelse($permissions as $parent=>$children)
                            <div class="col-md-12 mb-3">
                                <div class="border p-3 rounded">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input parent-permission" id="parent_{{ Str::slug($parent) }}">
                                        <label class="form-check-label fw-bold" for="parent_{{ Str::slug($parent) }}">
                                            {{ $parent }}
                                        </label>
                                    </div>

                                    <div class="mt-3">
                                        <div class="row">
                                            @foreach($children as $child)
                                                <div class="col-3"> 
                                                    <div class="form-check">
                                                        <input type="checkbox"
                                                            class="form-check-input child-permission"
                                                            name="permissions[]"
                                                            value="{{ $child->name }}"
                                                            id="perm_{{ $child->id }}"
                                                            {{ in_array($child->id, $checked_permission) ? 'checked' : '' }}>
                                                        <label for="perm_{{ $child->id }}" class="form-check-label">
                                                            {{ $child->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @empty

                            @endforelse
                            

                        </div>

                        <button type="submit" class="btn btn-success mt-3">Save Permissions</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // SELECT ALL
    document.getElementById('check_all').addEventListener('change', function() {
        const allChecks = document.querySelectorAll('.parent-permission, .child-permission');
        allChecks.forEach(ch => ch.checked = this.checked);
    });

    // PARENT → CHILDREN
    document.querySelectorAll('.parent-permission').forEach(parent => {
        parent.addEventListener('change', function() {
            let parentBox = this;
            let card = parentBox.closest('.border');
            let children = card.querySelectorAll('.child-permission');
            children.forEach(child => child.checked = parentBox.checked);
        });
    });

    // CHILDREN → PARENT
    document.querySelectorAll('.child-permission').forEach(child => {
        child.addEventListener('change', function() {
            let card = this.closest('.border');
            let parent = card.querySelector('.parent-permission');
            let children = card.querySelectorAll('.child-permission');
            parent.checked = Array.from(children).every(c => c.checked);
        });
    });
</script>
@endpush

@endsection
