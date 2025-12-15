@extends('backend.layouts.master')
@section('title','List Of Item')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'List Of Item'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{route('item.create')}}" class="btn btn-primary  btn-round"><i class="fa fa-plus"></i> Create Item</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form method="get" action="{{ route('item.index') }}">
                        <div class="filter row">
                            <div class="col-lg-4 col-md-4">
                                <input type="text" id="free_text" name="free_text" class="form-control" placeholder="Search..." value="{{ request()->get('free_text') }}">
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <select id="status" class="form-control" name="status">
                                    <option value="">Select Status</option>
                                    @foreach(\App\Models\MenuSection::STATUS as $key => $value)
                                        <option value="{{ $key }}" @if(request()->get('status')==$key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
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
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               @forelse($data as $value)
                                    <tr>
                                        <td>{{ $value->sl }}</td>
                                        <td>{{ $value->name }}</td>
                                        <td>{{ \App\Models\MenuSection::STATUS[$value->status] }}</td>
                                        <td>
                                            <a href="{{ route('item.edit', $value->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('item.destroy', $value->id) }}" method="POST" style="display:inline-block;">
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
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
