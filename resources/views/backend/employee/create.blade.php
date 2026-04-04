@extends('backend.layouts.master')
@section('title','Create Employee')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Create Employee'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Employee view'))
                <a href="{{ route('employee.index') }}" class="btn btn-label-info btn-round me-2">View</a>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    @include('backend.employee.form',[
                        'route' => route('employee.store'),
                        'buttonText' => 'Create Employee'
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

