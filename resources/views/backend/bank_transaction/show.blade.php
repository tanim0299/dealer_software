@extends('backend.layouts.master')
@section('title','Transaction Details')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Transaction Details'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Bank Transaction destroy'))
                    <form action="{{ route('bank_transaction.destroy', $bankTransaction->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-round me-2" onclick="return confirm('Delete this transaction? Balance will be reversed.')">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </form>
                @endif
                <a href="{{ route('bank_transaction.index') }}" class="btn btn-label-info btn-round me-2">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Transaction Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Bank Account</label>
                                <p class="h5">
                                    {{ $bankTransaction->bankAccount->account_name }}
                                    <br>
                                    <small class="text-muted">({{ $bankTransaction->bankAccount->account_number }})</small>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Transaction Type</label>
                                <p>
                                    @if($bankTransaction->type == 'deposit')
                                        <span class="badge badge-success badge-lg">Deposit</span>
                                    @else
                                        <span class="badge badge-danger badge-lg">Withdraw</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Transaction Date</label>
                                <p class="h5">{{ \Carbon\Carbon::parse($bankTransaction->transaction_date)->format('d M Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Amount</label>
                                <p class="h5">
                                    <strong class="text-primary">{{ number_format($bankTransaction->amount, 2) }}</strong>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Previous Balance</label>
                                <p class="h6">{{ number_format($bankTransaction->previous_balance, 2) }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">Transaction</label>
                                <p class="h6">
                                    @if($bankTransaction->type == 'deposit')
                                        <i class="fa fa-plus text-success"></i> +{{ number_format($bankTransaction->amount, 2) }}
                                    @else
                                        <i class="fa fa-minus text-danger"></i> -{{ number_format($bankTransaction->amount, 2) }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted">New Balance</label>
                                <p class="h6">
                                    <span class="badge badge-info badge-lg">
                                        {{ number_format($bankTransaction->new_balance, 2) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label text-muted">Description</label>
                                <p>{{ $bankTransaction->description ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label text-muted">Created At</label>
                                <p class="small text-muted">{{ $bankTransaction->created_at->format('d M Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-header">
                        <h5 class="card-title">Balance Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">Previous Balance</small>
                            <h6>{{ number_format($bankTransaction->previous_balance, 2) }}</h6>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">
                                @if($bankTransaction->type == 'deposit')
                                    Deposited
                                @else
                                    Withdrawn
                                @endif
                            </small>
                            <h6 class="@if($bankTransaction->type == 'deposit') text-success @else text-danger @endif">
                                @if($bankTransaction->type == 'deposit')
                                    +
                                @else
                                    -
                                @endif
                                {{ number_format($bankTransaction->amount, 2) }}
                            </h6>
                        </div>
                        <hr>
                        <div>
                            <small class="text-muted d-block">Current Balance</small>
                            <h5 class="text-primary">{{ number_format($bankTransaction->new_balance, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

