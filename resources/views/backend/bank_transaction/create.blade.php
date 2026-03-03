@extends('backend.layouts.master')
@section('title','Create Bank Transaction')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Create Bank Transaction'])
            <div class="ms-md-auto py-2 py-md-0">
                <a href="{{ route('bank_transaction.index') }}" class="btn btn-label-info btn-round me-2">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Transaction Details</h4>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @include('backend.bank_transaction.form',[
                            'route' => route('bank_transaction.store'),
                            'buttonText' => 'Create Transaction'
                        ])
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="card-title">Transaction Tips</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Deposit:</strong> Adds funds to the account</p>
                        <p class="mb-2"><strong>Withdraw:</strong> Removes funds from the account</p>
                        <p class="mb-0 text-danger">
                            <i class="fa fa-exclamation-circle"></i> You cannot withdraw more than the current balance
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
