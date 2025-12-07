@extends('backend.layouts.master')
@section('title','List Of Role')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'List Of Role'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{route('role.create')}}" class="btn btn-primary  btn-round"><i class="fa fa-plus"></i> Create Role</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form method="get" action="{{ route('role.index') }}">
                        <div class="filter row">
                            <div class="col-lg-4 col-md-4">
                                <input type="text" id="free_text" name="free_text" class="form-control" placeholder="Search..." value="{{ request()->get('free_text') }}">
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <button id="filterBtn" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Permission</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               @forelse($roles as $role)
                                    <tr>
                                        <td>{{ $role->index }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            <a class="btn btn-info btn-sm" href="{{ route('role.show',$role->id) }}">
                                                <i class="fa fa-key"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('role.edit', $role->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('role.destroy', $role->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                 @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No Role Found</td>
                                    </tr>
                                 @endforelse
                            </tbody>
                        </table>
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
