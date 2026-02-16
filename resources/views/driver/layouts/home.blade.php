@extends('driver.layouts.master')

@section('body')
    <div class="container-fluid mt-3">

        <!-- SUMMARY CARDS -->
        <div class="row g-2">

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <a href="{{ route('sales.index') }}">
                        <div class="card-body p-3">
                            <small class="text-muted">Sales</small>
                            <h5 class="fw-bold mt-1">0</h5>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <div class="card-body p-3">
                        <small class="text-muted">Dues</small>
                        <h5 class="fw-bold mt-1">₹3,200</h5>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card shadow-sm text-center">
                    <a href="">
                        <div class="card-body p-3">
                            <small class="text-muted">Expenses</small>
                            <h5 class="fw-bold mt-1">₹1,100</h5>
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
                    <button class="btn btn-outline-success w-100 py-2">
                        💰 Collect Due
                    </button>
                </div>
                <div class="col-6">
                    <button class="btn btn-outline-danger w-100 py-2">
                        🧾 Add Expense
                    </button>
                </div>
                <div class="col-6">
                    <a class="btn btn-outline-danger w-100 py-2" href="">
                        🧾 Sales Return
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
