@extends('backend.layouts.master')

@section('title', 'New Cash Close')

@section('content')
<div class="container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12">
                <h3>New Cash Close - {{ date('d M, Y') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Error Messages -->
@if ($errors->any())
    <div class="container-fluid mb-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fa fa-exclamation-circle"></i> Validation Errors
            </h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

<!-- Summary Cards (4 Cards) -->
<div class="container-fluid mb-4">
    <div class="row">
        <!-- Previous Cash Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Previous Cash</h6>
                    <h3 class="text-info mb-0" id="card_opening">{{ number_format($cashCloseData['opening_balance'], 2) }} à§³</h3>
                </div>
            </div>
        </div>

        <!-- Cash In Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Cash In</h6>
                    <h3 class="text-success mb-0" id="card_income">{{ number_format($totalIncome, 2) }} à§³</h3>
                </div>
            </div>
        </div>

        <!-- Cash Out Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Cash Out</h6>
                    <h3 class="text-danger mb-0" id="card_expense">{{ number_format($totalExpense, 2) }} à§³</h3>
                </div>
            </div>
        </div>

        <!-- Closing Balance Card -->
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Closing Balance</h6>
                    <h3 class="text-primary mb-0" id="card_closing">{{ number_format($cashCloseData['opening_balance'] + $totalIncome - $totalExpense, 2) }} à§³</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">
                    <h5>Today's Cash Close Details - {{ date('d M, Y') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cash_close.store') }}" method="POST">
                        @csrf

                        <!-- Income Details Section -->
                        <div class="mb-4">
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="fa fa-plus-circle"></i> Income Details
                            </h6>
                            @if ($incomeEntries->count() > 0)
                                <div class="table-responsive mb-3">
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
                                                <td><strong>Total Income Entries</strong></td>
                                                <td class="text-end text-success fw-bold">
                                                    <h6 class="mb-0">{{ number_format($totalIncome, 2) }} à§³</h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-3 mb-3 text-center text-muted bg-light rounded">
                                    <i class="fa fa-inbox"></i> No income entries found today
                                </div>
                            @endif
                        </div>

                        <!-- Expense Details Section -->
                        <div class="mb-4">
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="fa fa-minus-circle"></i> Expense Details
                            </h6>
                            @if ($expenseEntries->count() > 0)
                                <div class="table-responsive mb-3">
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
                                                <td><strong>Total Expense Entries</strong></td>
                                                <td class="text-end text-danger fw-bold">
                                                    <h6 class="mb-0">{{ number_format($totalExpense, 2) }} à§³</h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-3 mb-3 text-center text-muted bg-light rounded">
                                    <i class="fa fa-inbox"></i> No expense entries found today
                                </div>
                            @endif
                        </div>

                        <hr>

                        <!-- Hidden Fields -->
                        <input type="hidden" id="opening_balance" name="opening_balance" value="{{ $cashCloseData['opening_balance'] }}">
                        <input type="hidden" id="total_sales" name="total_sales" value="{{ $cashCloseData['total_sales'] }}">
                        <input type="hidden" id="other_income" name="other_income" value="{{ $cashCloseData['other_income'] }}">
                        <input type="hidden" id="bank_withdraw" name="bank_withdraw" value="{{ $cashCloseData['bank_withdraw'] }}">
                        <input type="hidden" id="due_collection" name="due_collection" value="{{ $cashCloseData['due_collection'] }}">
                        <input type="hidden" id="total_purchases" name="total_purchases" value="{{ $cashCloseData['total_purchases'] }}">
                        <input type="hidden" id="supplier_payment" name="supplier_payment" value="{{ $cashCloseData['supplier_payment'] }}">
                        <input type="hidden" id="salary_payment" name="salary_payment" value="{{ $cashCloseData['salary_payment'] }}">
                        <input type="hidden" id="bank_deposit" name="bank_deposit" value="{{ $cashCloseData['bank_deposit'] }}">

                        <!-- Summary Calculation -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fa fa-calculator"></i> Cash Balance Calculation
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td class="fw-bold">Opening Balance</td>
                                        <td class="text-end fw-bold">{{ number_format($cashCloseData['opening_balance'], 2) }} à§³</td>
                                    </tr>
                                    <tr>
                                        <td class="text-success fw-bold">+ Total Cash In</td>
                                        <td class="text-end text-success fw-bold">{{ number_format($totalIncome, 2) }} à§³</td>
                                    </tr>
                                    <tr class="border-bottom">
                                        <td class="text-danger fw-bold">- Total Cash Out</td>
                                        <td class="text-end text-danger fw-bold">{{ number_format($totalExpense, 2) }} à§³</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="h5 mb-0"><strong>Closing Balance</strong></td>
                                        <td class="text-end h4 mb-0 text-primary fw-bold">{{ number_format($cashCloseData['opening_balance'] + $totalIncome - $totalExpense, 2) }} à§³</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="small text-muted">
                                            <em>This closing balance becomes the previous balance for the next day</em>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-check-circle"></i> Confirm Cash Close
                            </button>
                            <a href="{{ route('cash_close.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</div>

@endsection
        const closingElement = document.getElementById('summary_closing');
        if (closingBalance >= 0) {
            closingElement.classList.remove('text-danger');
            closingElement.classList.add('text-success');
        } else {
            closingElement.classList.remove('text-success');
            closingElement.classList.add('text-danger');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
@endpush

@push('styles')
<style>
    .page-title h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    h6 {
        font-weight: 600;
        color: #333;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .text-info {
        color: #0dcaf0 !important;
    }

    .text-success {
        color: #198754 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .fw-bold {
        font-weight: 600;
    }

    .fs-5 {
        font-size: 1.25rem !important;
    }
</style>
@endpush

