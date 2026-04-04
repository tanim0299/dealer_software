@extends('backend.layouts.master')
@section('title','Bank Account List')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Bank Account List'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Bank Account create'))
                    <a href="{{ route('bank_account.create') }}" class="btn btn-primary btn-round"><i class="fa fa-plus"></i> Create Bank Account</a>
                @endif
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('bank_account.index') }}">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="free_text" class="form-control"
                                value="{{ $search['free_text'] ?? '' }}"
                                placeholder="Search account name, number, or bank">
                        </div>
                        <div class="col-md-4 mb-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                @foreach(\App\Models\BankAccount::STATUS as $key => $value)
                                    <option value="{{ $key }}" {{ request('status') == (string)$key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button class="btn btn-primary w-100"><i class="fa fa-filter"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bank Accounts Table --}}
        <div class="card">
            <div class="card-body">
                @if($bankAccounts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Bank Name</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bankAccounts as $account)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $account->account_name }}</strong></td>
                                        <td>{{ $account->account_number }}</td>
                                        <td>{{ $account->bank_name }}</td>
                                        <td>
                                            <span class="badge badge-success">
                                                {{ number_format($account->balance, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($account->status == 'active')
                                                <span class="badge badge-success">{{ \App\Models\BankAccount::STATUS[$account->status] }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ \App\Models\BankAccount::STATUS[$account->status] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if(Auth::user()->can('Bank Account view'))
                                                    <a href="{{ route('bank_account.show', $account->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endif
                                                @if(Auth::user()->can('Bank Account edit'))
                                                    <a href="{{ route('bank_account.edit', $account->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if(Auth::user()->can('Bank Account destroy'))
                                                    <form action="{{ route('bank_account.destroy', $account->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
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
                        {{ $bankAccounts->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> No bank accounts found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
