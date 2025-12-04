@extends('backend.layouts.master')
@section('title','List Of Menu')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'List Of Menu'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{route('menu.create')}}" class="btn btn-primary  btn-round"><i class="fa fa-plus"></i> Create Menu</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="filter row">
                        <div class="col-lg-4 col-md-4">
                            <input type="text" id="search" class="form-control" placeholder="Search..." value="{{ request()->get('search') }}">
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <select id="status" class="form-control">
                                <option value="">Select Status</option>
                                @foreach(\App\Models\Menu::STATUS as $key => $value)
                                    <option value="{{ $key }}" @if(request()->get('status')==$key) selected @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <button id="filterBtn" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Section</th>
                                    <th>Parent</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Icon</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               @forelse($menus as $menu)
                                    <tr>
                                        <td>{{ $menu->sl }}</td>
                                        <td>{{ $menu->menuSection ? $menu->menuSection->name : 'N/A' }}</td>
                                        <td>{{ $menu->parent ? $menu->parent->name : 'N/A' }}</td>
                                        <td>{{ $menu->name }}</td>
                                        <td>{{ \App\Models\Menu::STATUS[$menu->status] }}</td>
                                        <td>{{ \App\Models\Menu::TYPE[$menu->type] }}</td>
                                        <td><i class="{{ $menu->icon }}"></i></td>
                                        <td>
                                            <a href="{{ route('menu.edit', $menu->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('menu.destroy', $menu->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this menu?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                 @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No Menu Found</td>
                                    </tr>
                                 @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection