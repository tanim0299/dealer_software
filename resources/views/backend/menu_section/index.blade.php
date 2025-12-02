@extends('backend.layouts.master')
@section('title','View Menu Section')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Edit Menu Section'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{route('menu_section.create')}}" class="btn btn-primary btn-round">Add Menu Section</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <h4 class="card-title">Menu Section List</h4>
                    </div>
                    <div class="table-responsive">
                        <table id="basic-datatables" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Section Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            @forelse($data as $key => $value)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->status }}</td>
                                    <td>
                                        <div class="form-button-action">
                                            <a href="{{ route('menu_section.edit', $value->id) }}"
                                                class="btn btn-link btn-primary btn-lg"
                                                data-bs-toggle="tooltip"
                                                title="Edit"
                                                data-original-title="Edit Task">
                                                <i class="fa fa-edit"></i>
                                            </a>

                                            <form action="{{ route('menu_section.destroy', $value->id) }}"
                                                method="POST"
                                                onsubmit="return Sure();"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-link btn-danger btn-lg"
                                                    data-bs-toggle="tooltip"
                                                    title="Delete"
                                                    data-original-title="Remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No Data Found</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
