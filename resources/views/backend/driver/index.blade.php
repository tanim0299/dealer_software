@extends('backend.layouts.master')
@section('title','List Of Driver')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'List Of Driver'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(auth()->user()->can('Driver create'))
                <a href="{{route('driver.create')}}" class="btn btn-primary  btn-round"><i class="fa fa-plus"></i> Create driver</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form method="get" action="{{ route('driver.index') }}">
                        <div class="filter row">
                            <div class="col-lg-4 col-md-4">
                                <input type="text" id="free_text" name="free_text" class="form-control" placeholder="Search..." value="{{ request()->get('free_text') }}">
                            </div>
                            <div class="col-lg-4 col-md-4">
                                <select id="status" class="form-control" name="status">
                                    <option value="">Select Status</option>
                                    @foreach(\App\Models\Drivers::STATUS as $key => $value)
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
                                    <th>Phone</th>
                                    <th>Vehicle No</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                               @forelse($drivers as $key=>$driver)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $driver->name }}</td>
                                        <td>{{ $driver->phone }}</td>
                                        <td>{{ $driver->vehicle_no }}</td>
                                        <td>
                                            <span class="badge bg-{{ $driver->status == App\Models\Drivers::STATUS_ACTIVE ? 'success' : 'danger' }}">
                                            {{ \App\Models\Drivers::STATUS[$driver->status] }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(auth()->user()->can('Driver edit'))
                                            <a href="{{ route('driver.edit', $driver->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            @endif
                                            @if(auth()->user()->can('Driver destroy'))
                                            <form action="{{ route('driver.destroy', $driver->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this driver?')">Delete</button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                 @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No Driver Found</td>
                                    </tr>
                                 @endforelse
                            </tbody>
                        </table>
                        {{ $drivers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
