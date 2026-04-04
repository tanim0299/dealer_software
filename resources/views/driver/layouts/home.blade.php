@extends('driver.layouts.master')

@section('page_title', 'Dashboard')

@section('body')
    <div class="container-fluid mt-3">

        <!-- SUMMARY CARDS -->
        <div class="row g-2">

            @php
                use Illuminate\Support\Facades\DB;
                use Carbon\Carbon;

                $driverId = auth()->user()->driver_id;

                // Today Sales
                $todaySalesAmount = DB::table('sales_ledgers')
                    ->where('driver_id', $driverId)
                    ->whereDate('date', Carbon::today())
                    ->sum('subtotal');

                // Today Paid
                $todayPaid = DB::table('sales_ledgers')
                    ->where('driver_id', $driverId)
                    ->whereDate('date', Carbon::today())
                    ->sum('paid');

                // Today Discount
                $todayDiscount = DB::table('sales_ledgers')
                    ->where('driver_id', $driverId)
                    ->whereDate('date', Carbon::today())
                    ->sum('discount');

                // Today Dues = subtotal - discount - paid
                $todayDuesAmount = $todaySalesAmount - $todayDiscount - $todayPaid;

                // Today Expenses
                $todayExpensesAmount = DB::table('expense_entries')
                    ->where('driver_id', $driverId)
                    ->whereDate('date', Carbon::today())
                    ->sum('amount');

                // Current Stock Qty
                $currentStockQty = DB::table('driver_issue_items')
                    ->join('driver_issues', 'driver_issue_items.driver_issue_id', '=', 'driver_issues.id')
                    ->where('driver_issues.driver_id', $driverId)
                    ->where('driver_issues.status', 'accepted')
                    ->sum(DB::raw('driver_issue_items.issue_qty - driver_issue_items.sold_qty - driver_issue_items.return_qty'));

                // Manager cash issued today
                $cashFromManager = DB::table('driver_issues')
                    ->where('driver_id', $driverId)
                    ->whereDate('issue_date', Carbon::today())
                    ->where('status', '!=', 'rejected')
                    ->sum('cash_from_manager');

                // Due collected today
                $todayDueCollection = DB::table('sales_payments')
                    ->where('type', 1)
                    ->where('create_by', auth()->id())
                    ->whereDate('date', Carbon::today())
                    ->sum('amount');

                // Sales return cash paid today (only cash paid, not due adjustment)
                $todayReturnPaid = abs((float) DB::table('sales_payments as sp')
                    ->join('sales_return_ledgers as srl', 'srl.id', '=', 'sp.reference_id')
                    ->where('sp.type', 2)
                    ->where('sp.reference_type', 'return')
                    ->where('sp.amount', '<', 0)
                    ->where('srl.create_by', auth()->id())
                    ->whereDate('sp.date', Carbon::today())
                    ->sum('sp.amount'));

                // Given amount to other employees today
                $todayGivenAmount = DB::table('driver_cash_distributions')
                    ->where('driver_id', $driverId)
                    ->whereDate('date', Carbon::today())
                    ->sum('amount');

                // Live carrying cash
                $currentCarryingCash = $cashFromManager
                    + $todayPaid
                    + $todayDueCollection
                    - $todayReturnPaid
                    - $todayExpensesAmount
                    - $todayGivenAmount;

                $todayClosingExists = DB::table('driver_closings')
                    ->where('driver_id', $driverId)
                    ->whereDate('date', Carbon::today())
                    ->exists();

                if ($todayClosingExists) {
                    $currentCarryingCash = 0;
                }
            @endphp

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <a href="{{ route('sales.index') }}">
                        <div class="card-body p-3">
                            <small class="text-muted">Today Sales</small>
                            <h5 class="fw-bold mt-1">
                                à§³ {{ number_format($todaySalesAmount ?? 0, 2) }}
                            </h5>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <div class="card-body p-3">
                        <small class="text-muted">Dues</small>
                        <h5 class="fw-bold mt-1">
                            à§³ {{ number_format($todayDuesAmount ?? 0, 2) }}
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <a href="{{ route('expense_entry.index') }}">
                        <div class="card-body p-3">
                            <small class="text-muted">Expenses</small>
                            <h5 class="fw-bold mt-1">
                                à§³ {{ number_format($todayExpensesAmount ?? 0, 2) }}
                            </h5>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <a href="{{ route('driver_stock.index') }}">
                        <div class="card-body p-3">
                            <small class="text-muted">Current Stock</small>
                            <h5 class="fw-bold mt-1">{{ max(0, (int) $currentStockQty) }}</h5>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm text-center border-success">
                    <div class="card-body p-3">
                        <small class="text-muted">Current Carrying Cash</small>
                        <h4 class="fw-bold mt-1 text-success">
                            à§³ {{ number_format($currentCarryingCash, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        <!-- QUICK ACTIONS -->
        <div class="mt-4">

            <a href="{{ route('sales.create') }}" class="btn btn-primary w-100 py-3 mb-2">
                âž• New Sale
            </a>

            <div class="row g-2">
                <div class="col-6">
                    <a class="btn btn-outline-success w-100 py-2" href="{{ route('customer_payment.create') }}">
                        ðŸ’° Collect Due
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-success w-100 py-2" href="{{ route('customer_payment.index') }}">
                        ðŸ’° Collection List
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-warning w-100 py-2" href="{{ route('driver_cash_distribution.create') }}">
                        ðŸ’¸ Give Amount
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-warning w-100 py-2" href="{{ route('driver_cash_distribution.index') }}">
                        ðŸ’¸ Given Amount List
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-danger w-100 py-2" href="{{ route('expense_entry.create') }}">
                        ðŸ§¾ Add Expense
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-primary w-100 py-2" href="{{ route('driver_stock.index') }}">
                        ðŸ“¦ Current Stock
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-danger w-100 py-2" href="{{ route('sales_return.create') }}">
                        ðŸ§¾ Sales Return
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-danger w-100 py-2" href="{{ route('sales_return.index') }}">
                        ðŸ§¾ Sales Return List
                    </a>
                </div>
            </div>
            <div class="col-6">
                <a class="btn btn-outline-danger w-100 py-2" href="{{ route('driver-issues.index') }}">
                    ðŸ§¾ Issue List
                </a>
            </div>
        </div>

        <!-- RECENT ACTIVITY -->
        <div class="mt-4">

            <h6 class="fw-bold mb-2">Recent Activity</h6>

            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Sale</span>
                    <span class="fw-bold text-success">â‚¹500</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Expense</span>
                    <span class="fw-bold text-danger">â‚¹120</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Due Collected</span>
                    <span class="fw-bold text-primary">â‚¹300</span>
                </li>
            </ul>

        </div>

    </div>
@endsection

