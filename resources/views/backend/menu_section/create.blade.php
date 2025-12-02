@extends('backend.layouts.master')
@section('title','Create Menu Section')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Create Menu Section'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="#" class="btn btn-label-info btn-round me-2">Manage</a>
                <a href="#" class="btn btn-primary btn-round">Add Customer</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.menu_section.form',[
                        'route'=>route('menu_section.store'),
                        'buttonText'=>'Create Section'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection