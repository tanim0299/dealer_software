@extends('backend.layouts.master')
@section('title','Edit DSR')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Edit DSR'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(auth()->user()->can('Driver view'))
                <a href="{{route('driver.index')}}" class="btn btn-primary  btn-round"><i class="fa fa-eye"></i> View DSR list</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.driver.form',[
                        'route'=>route('driver.update',$data->id),
                        'method' => 'PUT',
                        'buttonText'=>'Update DSR'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

