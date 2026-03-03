@extends('backend.layouts.master')
@section('title','Edit Bank Account')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Edit Bank Account'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{ route('bank_account.index') }}" class="btn btn-label-info btn-round me-2">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Bank Account: {{ $bankAccount->account_name }}</h4>
                    </div>
                    <div class="card-body">
                        @include('backend.bank_account.form',[
                            'route' => route('bank_account.update', $bankAccount->id),
                            'method' => 'PUT',
                            'buttonText' => 'Update Bank Account'
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
