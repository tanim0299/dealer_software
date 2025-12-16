@extends('backend.layouts.master')
@section('title','Edit Product')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Edit Product'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Product view'))
                <a href="{{route('product.index')}}" class="btn btn-label-info btn-round me-2">View</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.product.form',[
                        'route'=>route('product.update',$product->id),
                        'buttonText'=>'Edit Product',
                        'method' => 'PUT',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
