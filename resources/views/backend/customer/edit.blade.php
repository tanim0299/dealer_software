@extends('backend.layouts.master')
@section('title','Edit Customer')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Edit Customer'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{route('customer.index')}}" class="btn btn-label-info btn-round me-2">View</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.customer.form',[
                        'route' => route('customer.update',$data->id),
                        'method' => 'PUT'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
