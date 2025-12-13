@extends('backend.layouts.master')
@section('title','Create Role')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Create Role'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(auth()->user()->can('Role view'))
                <a href="{{route('role.index')}}" class="btn btn-label-info btn-round me-2">View</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.role.form',[
                        'route'=>route('role.store'),
                        'buttonText'=>'Create Role',
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
