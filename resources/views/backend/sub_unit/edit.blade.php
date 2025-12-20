@extends('backend.layouts.master')
@section('title','Edit Sub Unit')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Edit Sub Unit'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{route('sub_unit.index')}}" class="btn btn-label-info btn-round me-2">View</a>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.sub_unit.form',[
                        'route' => route('sub_unit.update',$subunit->id),
                        'method' => 'PUT'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
