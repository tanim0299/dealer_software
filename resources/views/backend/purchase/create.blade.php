@extends('backend.layouts.master')
@section('title','Create Purchase')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Create Purchase'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(auth()->user()->can('Purchase view'))
                <a href="{{route('purchase.index')}}" class="btn btn-primary  btn-round"><i class="fa fa-eye"></i> View Purchase</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.purchase.form',[
                        'route'=>route('purchase.store'),
                        'buttonText'=>'Create Purchase'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection