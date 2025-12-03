@extends('backend.layouts.master')
@section('title','View Menu Section')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Menu Section List'])
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="card-header">
                        <div class="row align-items-center g-2">
                            <!-- Search Section -->
                            <div class="col-lg-8 col-md-8 col-12">
                                <form action="{{ route('menu_section.index') }}" method="get">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-lg-9 col-md-9 col-8">
                                            <input type="text" class="form-control" name="free_text" id="free_text" placeholder="Search Here..." autocomplete="off" value="{{ request()->free_text ?? '' }}">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-4 d-flex gap-1">
                                            <button type="submit" class="btn btn-success btn-sm w-100">
                                                <i class="fa fa-filter"></i>
                                            </button>
                                            <a href="{{ route('menu_section.index') }}" class="btn btn-danger btn-sm w-100">
                                                <i class="fas fa-sync-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- Add Button -->
                            <div class="col-lg-4 col-md-4 col-12 text-lg-end text-md-end text-start">
                                <a href="{{ route('menu_section.create') }}" class="btn btn-primary btn-round">
                                    Add Menu Section
                                </a>
                            </div>
                        </div>
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
                                    <td>
                                        <div class="form-check form-switch mb-2">
                                            <input onclick="changeMenuSectionStatus({{ $value->id }})" type="checkbox" class="form-check-input input-primary" id="menusection{{ $value->id }}" @if($value->status == 1) checked @endif>
                                            <label onclick="changeMenuSectionStatus({{ $value->id }})" class="form-check-label" for="menusection{{ $value->id }}"></label>
                                        </div>
                                    </td>
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
                                                method="POST" onsubmit="return Sure();" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-danger btn-lg" data-bs-toggle="tooltip" title="Delete" data-original-title="Remove"> <i class="fa fa-trash"></i></button>
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


@section('scripts')
    <script>
        function Sure()
        {
            if(confirm("Are Your Sure To Delete?"))
        {
            return true;
        }
        else
        {
            return false;
        }
        }
    </script>

    <script>
        function changeMenuSectionStatus(id)
        {
            $.ajax({
                'headers' : {
                    'X-CSRF-TOKEN' : '{{ csrf_token() }}'
                },

                url : '{{ route('menu_section.status') }}',

                type : 'POST',

                data : {id},

                success : function(res)
                {

                }
            });
        }
    </script>

@endsection
