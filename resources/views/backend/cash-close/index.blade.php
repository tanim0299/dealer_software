@extends('backend.layouts.master')
@section('title', 'Cash Close Management')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb', ['page_title' => 'Cash Close'])
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle"></i> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-info h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Previous Cash</h6>
                        <h3 class="text-info mb-0" id="opening_balance" data-value="0">
                            {{ number_format($previousCash, 2) . ' Tk' }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-success h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Cash In</h6>
                        <h3 class="text-success mb-0" id="total_cash_in">
                            {{ number_format($total_cash_in, 2) }} Tk
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-danger h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Cash Out</h6>
                        <h3 class="text-danger mb-0" id="total_cash_out">
                            {{ number_format($total_cash_out, 2) }} Tk
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-primary h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2">Closing Balance</h6>
                        <h3 class="text-primary mb-0" id="closing_balance">
                            {{ number_format($closing_balance, 2) }} Tk
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fa fa-plus-circle"></i> Income Details</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Total Sales</strong></td>
                                    <td class="align-middle text-end pe-3 text-success fw-bold" style="width: 200px;"><span id="total_sales_display">
                                        {{ number_format($total_sales, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Other Income</strong></td>
                                    <td class="align-middle text-end pe-3 text-success fw-bold"><span id="other_income_display">
                                        {{ number_format($other_income, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Purchase Return (Cash)</strong></td>
                                    <td class="align-middle text-end pe-3 text-success fw-bold"><span id="purchase_return_cash_display">
                                        {{ number_format($purchase_return, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Due Collection</strong></td>
                                    <td class="align-middle text-end pe-3 text-success fw-bold"><span id="due_collection_display">
                                        {{ number_format($due_collection, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Bank Withdraw</strong></td>
                                    <td class="align-middle text-end pe-3 text-success fw-bold"><span id="bank_withdraw_display">
                                        {{ number_format($bank_withdraw, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr class="table-light border-top">
                                    <td class="ps-3"><strong>Total Cash In</strong></td>
                                    <td class="text-end pe-3"><h6 class="mb-0 text-success fw-bold" id="income_total">
                                        {{ number_format($total_cash_in, 2) }} Tk
                                    </h6></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fa fa-minus-circle"></i> Expense Details</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Total Purchases (Cash)</strong></td>
                                    <td class="align-middle text-end pe-3 text-danger fw-bold" style="width: 200px;"><span id="total_purchases_display">
                                        {{ number_format($total_purchases, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Supplier Payment</strong></td>
                                    <td class="align-middle text-end pe-3 text-danger fw-bold"><span id="supplier_payment_display">
                                        {{ number_format($supplier_payment, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Expenses</strong></td>
                                    <td class="align-middle text-end pe-3 text-danger fw-bold"><span id="expenses_display">
                                        {{ number_format($total_expenses, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Sales Return (Cash)</strong></td>
                                    <td class="align-middle text-end pe-3 text-danger fw-bold"><span id="sales_return_cash_display">
                                        {{ number_format($sales_return, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Salary Payment</strong></td>
                                    <td class="align-middle text-end pe-3 text-danger fw-bold"><span id="salary_payment_display">
                                        {{ number_format($salary_payment, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr>
                                    <td class="align-middle ps-3"><strong>Bank Deposit</strong></td>
                                    <td class="align-middle text-end pe-3 text-danger fw-bold"><span id="bank_deposit_display">
                                        {{ number_format($bank_deposit, 2) }} Tk
                                    </span></td>
                                </tr>
                                <tr class="table-light border-top">
                                    <td class="ps-3"><strong>Total Cash Out</strong></td>
                                    <td class="text-end pe-3"><h6 class="mb-0 text-danger fw-bold" id="expense_total">
                                        {{ number_format($total_cash_out, 2) }} Tk
                                    </h6></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('cash_close.store') }}" method="POST" id="cashCloseForm">
                            @csrf
                            <input type="hidden" name="opening_balance" value="{{ $previousCash }}">
                            <input type="hidden" name="total_cash_in" value="{{ $total_cash_in }}">
                            <input type="hidden" name="total_cash_out" value="{{ $total_cash_out }}">
                            <input type="hidden" name="closing_balance" value="{{ $closing_balance }}">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-check-circle"></i> Confirm Cash Close
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection






