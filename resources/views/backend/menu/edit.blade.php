@extends('backend.layouts.master')
@section('title','Create Menu')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Create Menu'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{route('menu.index')}}" class="btn btn-primary  btn-round"><i class="fa fa-eye"></i> View Menu</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.menu.form',[
                        'route'=>route('menu.update',$menu->id),
                        'method' => 'PUT',
                        'buttonText'=>'Update Menu'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection