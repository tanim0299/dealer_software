@extends('driver.layouts.master')

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
            @endphp

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <a href="{{ route('sales.index') }}">
                        <div class="card-body p-3">
                            <small class="text-muted">Today Sales</small>
                            <h5 class="fw-bold mt-1">
                                ৳ {{ number_format($todaySalesAmount ?? 0, 2) }}
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
                            ৳ {{ number_format($todayDuesAmount ?? 0, 2) }}
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
                                ৳ {{ number_format($todayExpensesAmount ?? 0, 2) }}
                            </h5>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <div class="card-body p-3">
                        <small class="text-muted">Stock</small>
                        <h5 class="fw-bold mt-1">245</h5>
                    </div>
                </div>
            </div>

        </div>

        <!-- QUICK ACTIONS -->
        <div class="mt-4">

            <a href="{{ route('sales.create') }}" class="btn btn-primary w-100 py-3 mb-2">
                ➕ New Sale
            </a>

            <div class="row g-2">
                <div class="col-6">
                    <a class="btn btn-outline-success w-100 py-2" href="{{ route('customer_payment.create') }}">
                        💰 Collect Due
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-success w-100 py-2" href="{{ route('customer_payment.index') }}">
                        💰 Collection List
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-danger w-100 py-2" href="{{ route('expense_entry.create') }}">
                        🧾 Add Expense
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-danger w-100 py-2" href="{{ route('sales_return.create') }}">
                        🧾 Sales Return
                    </a>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-danger w-100 py-2" href="{{ route('sales_return.index') }}">
                        🧾 Sales Return List
                    </a>
                </div>
            </div>
            <div class="col-6">
                <a class="btn btn-outline-danger w-100 py-2" href="{{ route('driver-issues.index') }}">
                    🧾 Issue List
                </a>
            </div>
        </div>

        <!-- RECENT ACTIVITY -->
        <div class="mt-4">

            <h6 class="fw-bold mb-2">Recent Activity</h6>

            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Sale</span>
                    <span class="fw-bold text-success">₹500</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Expense</span>
                    <span class="fw-bold text-danger">₹120</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Due Collected</span>
                    <span class="fw-bold text-primary">₹300</span>
                </li>
            </ul>

        </div>

    </div>
@endsection
