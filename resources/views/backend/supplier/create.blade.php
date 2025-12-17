@extends('backend.layouts.master')
@section('title','Create Supplier')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Create Supplier'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Supplier view'))
                <a href="{{route('supplier.index')}}" class="btn btn-label-info btn-round me-2">View</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.supplier.form',[
                        'route'=>route('supplier.store'),
                        'buttonText'=>'Create Supplier'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
