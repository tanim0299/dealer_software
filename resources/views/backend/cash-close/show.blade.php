@extends('backend.layouts.master')

@section('title', 'Cash Close Details')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h3>Cash Close Details - {{ $cashClose->close_date->format('d M, Y') }}</h3>
                <div>
                    <a href="{{ route('cash_close.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    @if (!$cashClose->reopened_at && $cashClose->isTodayClose() && $cashClose->canReopen())
                        <a href="{{ route('cash_close.edit', $cashClose) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Reopen
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status and Info Card -->
<div class="container-fluid mb-4">
    <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5>{{ $cashClose->close_date->format('d M, Y') }}</h5>
                <small class="text-muted">
                    Closed By: <strong>{{ $cashClose->closedByUser->name }}</strong> - 
                    {{ $cashClose->closed_at->format('d M, Y h:i A') }}
                </small>
                @if ($cashClose->reopened_at)
                    <br>
                    <small class="text-warning">
                        Reopened By: <strong>{{ $cashClose->reopenedByUser->name }}</strong> - 
                        {{ $cashClose->reopened_at->format('d M, Y h:i A') }}
                    </small>
                @endif
            </div>
            <div>
                @if ($cashClose->reopened_at)
                    <span class="badge bg-warning">Reopened</span>
                @else
                    <span class="badge bg-success">Confirmed</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards (4 Cards) -->
<div class="container-fluid mb-4">
    <div class="row">
        <!-- Previous Cash Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Previous Cash</h6>
                    <h3 class="text-info mb-0">{{ number_format($cashClose->opening_balance, 2) }} à§³</h3>
                </div>
            </div>
        </div>

        <!-- Cash In Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Cash In</h6>
                    <h3 class="text-success mb-0">{{ number_format($cashClose->total_cash_in, 2) }} à§³</h3>
                </div>
            </div>
        </div>

        <!-- Cash Out Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Cash Out</h6>
                    <h3 class="text-danger mb-0">{{ number_format($cashClose->total_cash_out, 2) }} à§³</h3>
                </div>
            </div>
        </div>

        <!-- Closing Balance Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Closing Balance</h6>
                    <h3 class="text-primary mb-0">{{ number_format($cashClose->closing_balance, 2) }} à§³</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Section -->
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <!-- Income Details Section -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fa fa-plus-circle"></i> Income Details
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if ($incomeEntries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach ($incomeEntries as $entry)
                                        <tr>
                                            <td class="align-middle">
                                                <strong>{{ $entry->income->title ?? 'N/A' }}</strong>
                                                @if ($entry->description)
                                                    <br><small class="text-muted">{{ $entry->description }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle text-end text-success fw-bold">
                                                {{ number_format($entry->amount, 2) }} à§³
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-light">
                                        <td><strong>Total Income</strong></td>
                                        <td class="text-end text-success fw-bold">
                                            <h6 class="mb-0">{{ number_format($totalIncome, 2) }} à§³</h6>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">
                            <i class="fa fa-inbox"></i> No income entries found
                        </div>
                    @endif
                </div>
            </div>

            <!-- Expense Details Section -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fa fa-minus-circle"></i> Expense Details
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if ($expenseEntries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach ($expenseEntries as $entry)
                                        <tr>
                                            <td class="align-middle">
                                                <strong>{{ $entry->expense->title ?? 'N/A' }}</strong>
                                                @if ($entry->description)
                                                    <br><small class="text-muted">{{ $entry->description }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle text-end text-danger fw-bold">
                                                {{ number_format($entry->amount, 2) }} à§³
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="table-light">
                                        <td><strong>Total Expense</strong></td>
                                        <td class="text-end text-danger fw-bold">
                                            <h6 class="mb-0">{{ number_format($totalExpense, 2) }} à§³</h6>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted">
                            <i class="fa fa-inbox"></i> No expense entries found
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cash Calculation Summary -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fa fa-calculator"></i> Cash Balance Calculation
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr class="border-top">
                            <td class="fw-bold">Opening Balance</td>
                            <td class="text-end fw-bold">{{ number_format($cashClose->opening_balance, 2) }} à§³</td>
                        </tr>
                        <tr>
                            <td class="text-success fw-bold">+ Total Cash In</td>
                            <td class="text-end text-success fw-bold">{{ number_format($cashClose->total_cash_in, 2) }} à§³</td>
                        </tr>
                        <tr class="border-bottom">
                            <td class="text-danger fw-bold">- Total Cash Out</td>
                            <td class="text-end text-danger fw-bold">{{ number_format($cashClose->total_cash_out, 2) }} à§³</td>
                        </tr>
                        <tr class="border-top border-thick">
                            <td class="h5 mb-0"><strong>Closing Balance</strong></td>
                            <td class="text-end h4 mb-0 text-primary fw-bold">{{ number_format($cashClose->closing_balance, 2) }} à§³</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="small text-muted">
                                <em>This closing balance becomes the previous balance for the next day</em>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

