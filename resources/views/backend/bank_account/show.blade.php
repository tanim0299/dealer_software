@extends('backend.layouts.master')
@section('title','Bank Account Details')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Bank Account Details'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Bank Account edit'))
                    <a href="{{ route('bank_account.edit', $bankAccount->id) }}" class="btn btn-warning btn-round me-2">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                @endif
                <a href="{{ route('bank_account.index') }}" class="btn btn-label-info btn-round me-2">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        {{-- Account Details Card --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Account Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label text-muted">Account Name</label>
                                <p class="h5">{{ $bankAccount->account_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Account Number</label>
                                <p class="h5">{{ $bankAccount->account_number }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Bank Name</label>
                                <p class="h5">{{ $bankAccount->bank_name }}</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted">Current Balance</label>
                                <p class="h5">
                                    <span class="badge badge-success badge-lg">
                                        {{ number_format($bankAccount->balance, 2) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted">Status</label>
                                <p>
                                    @if($bankAccount->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-9">
                                <label class="form-label text-muted">Description</label>
                                <p>{{ $bankAccount->description ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recent Transactions</h4>
                        @if(Auth::user()->can('Bank Transaction create'))
                            <a href="{{ route('bank_transaction.create') }}?account_id={{ $bankAccount->id }}" class="btn btn-primary btn-sm float-right">
                                <i class="fa fa-plus"></i> Add Transaction
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($bankAccount->transactions && $bankAccount->transactions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Previous Balance</th>
                                            <th>New Balance</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bankAccount->transactions as $transaction)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $transaction->transaction_date }}</td>
                                                <td>
                                                    @if($transaction->type == 'deposit')
                                                        <span class="badge badge-success">Deposit</span>
                                                    @else
                                                        <span class="badge badge-danger">Withdraw</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    <strong>{{ number_format($transaction->amount, 2) }}</strong>
                                                </td>
                                                <td class="text-right">{{ number_format($transaction->previous_balance, 2) }}</td>
                                                <td class="text-right">
                                                    <span class="badge badge-info">
                                                        {{ number_format($transaction->new_balance, 2) }}
                                                    </span>
                                                </td>
                                                <td>{{ $transaction->description ?? 'N/A' }}</td>
                                                <td>
                                                    @if(Auth::user()->can('Bank Transaction view'))
                                                        <a href="{{ route('bank_transaction.show', $transaction->id) }}" class="btn btn-sm btn-info" title="View">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if(Auth::user()->can('Bank Transaction destroy'))
                                                        <form action="{{ route('bank_transaction.destroy', $transaction->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this transaction?')">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No transactions found for this account.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

