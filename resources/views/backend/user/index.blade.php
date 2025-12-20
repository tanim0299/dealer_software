@extends('backend.layouts.master')
@section('title','List Of User')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'List Of User'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(auth()->user()->can('User create'))
                <a href="{{route('user.create')}}" class="btn btn-primary  btn-round"><i class="fa fa-plus"></i> Create User</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form method="get" action="{{ route('user.index') }}">
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
                                    <th>Role</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               @forelse($users as $key=>$user)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $user->assignedRole->name ?? 'n/a'}}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>
                                            @if(auth()->user()->can('User edit'))
                                            <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            @endif
                                            @if(auth()->user()->can('User destroy'))
                                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                 @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No Role Found</td>
                                    </tr>
                                 @endforelse
                            </tbody>
                        </table>
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
