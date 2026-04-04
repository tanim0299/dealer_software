@extends('backend.layouts.master')
@section('title','Bank Transactions')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Bank Transactions'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Bank Transaction create'))
                    <a href="{{ route('bank_transaction.create') }}" class="btn btn-primary btn-round"><i class="fa fa-plus"></i> Create Transaction</a>
                @endif
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('bank_transaction.index') }}">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <select name="bank_account_id" class="form-select">
                                <option value="">All Accounts</option>
                                @foreach($bankAccounts as $account)
                                    <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                @foreach(\App\Models\BankTransaction::TYPES as $key => $value)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="date" name="start_date" class="form-control" 
                                value="{{ request('start_date') }}" placeholder="From Date">
                        </div>
                        <div class="col-md-2 mb-2">
                            <input type="date" name="end_date" class="form-control" 
                                value="{{ request('end_date') }}" placeholder="To Date">
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-primary w-100"><i class="fa fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="card">
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Bank Account</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Previous Balance</th>
                                    <th>New Balance</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->transaction_date }}</td>
                                        <td>
                                            <strong>{{ $transaction->bankAccount->account_name }}</strong><br>
                                            <small class="text-muted">{{ $transaction->bankAccount->account_number }}</small>
                                        </td>
                                        <td>
                                            @if($transaction->type == 'deposit')
                                                <span class="badge badge-success">Deposit â†“</span>
                                            @else
                                                <span class="badge badge-danger">Withdraw â†‘</span>
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
                                        <td>{{ Str::limit($transaction->description ?? 'N/A', 30) }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if(Auth::user()->can('Bank Transaction view'))
                                                    <a href="{{ route('bank_transaction.show', $transaction->id) }}" class="btn btn-info" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if(Auth::user()->can('Bank Transaction destroy'))
                                                    <form action="{{ route('bank_transaction.destroy', $transaction->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Delete this transaction? Balance will be reversed.')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $transactions->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No transactions found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

