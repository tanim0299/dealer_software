@extends('backend.layouts.master')

@section('title','Dashboard')

@section('content')
<div class="container">
    <div class="page-inner fresh-dashboard">
        <header class="fresh-dash-header">
            <div>
                @include('backend.layouts.partials.breadcrumb',['page_title'=>'Dashboard'])
                <p class="fresh-dash-header__subtitle">Overview of stock, receivables, and today&apos;s activity</p>
            </div>
            <div class="fresh-dash-header__actions">
                <a href="{{ route('purchase.create') }}" class="btn btn-label-info btn-round">
                    <i class="fa fa-shopping-cart me-1"></i> New Purchase
                </a>
                <a href="{{ route('driver-issues.create') }}" class="btn btn-primary btn-round">
                    <i class="fa fa-truck me-1"></i> Issue Driver Stock
                </a>
            </div>
        </header>

        <div class="row g-3 g-lg-4 mb-2">
            <div class="col-md-3 col-sm-6">
                <div class="card card-stats card-round fresh-stat-card fresh-stat--teal h-100">
                    <div class="card-body">
                        <div class="fresh-stat-icon text-success"><i class="fa fa-cubes"></i></div>
                        <div class="numbers">
                            <p class="card-category">Current Stock Qty</p>
                            <h4 class="card-title">{{ number_format($currentStockQty, 2) }}</h4>
                            <a href="{{ route('warehouse_stock.index') }}" class="small">View stock</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-stats card-round fresh-stat-card fresh-stat--indigo h-100">
                    <div class="card-body">
                        <div class="fresh-stat-icon text-primary"><i class="fa fa-line-chart"></i></div>
                        <div class="numbers">
                            <p class="card-category">Current Stock Value</p>
                            <h4 class="card-title">Tk {{ number_format($currentStockValue, 2) }}</h4>
                            <a href="{{ route('warehouse_stock.index') }}" class="small">Inventory value</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-stats card-round fresh-stat-card fresh-stat--amber h-100">
                    <div class="card-body">
                        <div class="fresh-stat-icon text-warning"><i class="fa fa-users"></i></div>
                        <div class="numbers">
                            <p class="card-category">Customer Receivable</p>
                            <h4 class="card-title">Tk {{ number_format($customerReceivable, 2) }}</h4>
                            <a href="{{ route('customer_due_list.index') }}" class="small">Customer due</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-stats card-round fresh-stat-card fresh-stat--rose h-100">
                    <div class="card-body">
                        <div class="fresh-stat-icon text-danger"><i class="fa fa-truck"></i></div>
                        <div class="numbers">
                            <p class="card-category">Supplier Payable</p>
                            <h4 class="card-title">Tk {{ number_format($supplierPayable, 2) }}</h4>
                            <a href="{{ route('supplier_due_list.index') }}" class="small">Supplier due</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 fresh-dash-minis mb-2">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card fresh-mini-stat h-100">
                    <div class="card-body">
                        <div class="fresh-mini-stat__icon text-primary"><i class="fa fa-cube"></i></div>
                        <div class="fresh-mini-stat__num">{{ $stats['products'] }}</div>
                        <small>Products</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card fresh-mini-stat h-100">
                    <div class="card-body">
                        <div class="fresh-mini-stat__icon text-info"><i class="fa fa-user"></i></div>
                        <div class="fresh-mini-stat__num">{{ $stats['customers'] }}</div>
                        <small>Customers</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card fresh-mini-stat h-100">
                    <div class="card-body">
                        <div class="fresh-mini-stat__icon text-secondary"><i class="fa fa-building"></i></div>
                        <div class="fresh-mini-stat__num">{{ $stats['suppliers'] }}</div>
                        <small>Suppliers</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card fresh-mini-stat h-100">
                    <div class="card-body">
                        <div class="fresh-mini-stat__icon text-success"><i class="fa fa-truck"></i></div>
                        <div class="fresh-mini-stat__num">{{ $stats['drivers'] }}</div>
                        <small>Drivers</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card fresh-mini-stat h-100">
                    <div class="card-body">
                        <div class="fresh-mini-stat__icon text-warning"><i class="fa fa-id-badge"></i></div>
                        <div class="fresh-mini-stat__num">{{ $stats['employees'] }}</div>
                        <small>Employees</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card fresh-mini-stat h-100">
                    <div class="card-body">
                        <div class="fresh-mini-stat__icon text-danger"><i class="fa fa-inbox"></i></div>
                        <div class="fresh-mini-stat__num">{{ $stats['open_driver_issues'] }}</div>
                        <small>Open issues</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-lg-4">
            <div class="col-md-6">
                <div class="card card-round h-100 fresh-dash-panel">
                    <div class="card-header fresh-dash-panel__head">
                        <h4 class="card-title mb-0">Today&apos;s business</h4>
                    </div>
                    <div class="card-body fresh-dash-summary">
                        <div class="fresh-dash-summary__row">
                            <span class="fresh-dash-summary__label">Sales amount</span>
                            <strong class="fresh-dash-summary__value">Tk {{ number_format($todaySalesAmount, 2) }}</strong>
                        </div>
                        <div class="fresh-dash-summary__row">
                            <span class="fresh-dash-summary__label">Sales paid</span>
                            <strong class="fresh-dash-summary__value">Tk {{ number_format($todaySalesPaid, 2) }}</strong>
                        </div>
                        <div class="fresh-dash-summary__row">
                            <span class="fresh-dash-summary__label">Due collection</span>
                            <strong class="fresh-dash-summary__value">Tk {{ number_format($todayCollectionAmount, 2) }}</strong>
                        </div>
                        <div class="fresh-dash-summary__row">
                            <span class="fresh-dash-summary__label">Purchase amount</span>
                            <strong class="fresh-dash-summary__value">Tk {{ number_format($todayPurchaseAmount, 2) }}</strong>
                        </div>
                        <div class="fresh-dash-summary__row">
                            <span class="fresh-dash-summary__label">Expense</span>
                            <strong class="fresh-dash-summary__value text-danger">Tk {{ number_format($todayExpenseAmount, 2) }}</strong>
                        </div>
                        <div class="fresh-dash-summary__row fresh-dash-summary__row--last">
                            <span class="fresh-dash-summary__label">Return paid</span>
                            <strong class="fresh-dash-summary__value text-danger">Tk {{ number_format($todayReturnPaid, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-round h-100 fresh-dash-panel">
                    <div class="card-header fresh-dash-panel__head">
                        <h4 class="card-title mb-0">Quick links</h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 fresh-dash-links">
                            <div class="col-6">
                                <a href="{{ route('sales.index') }}" class="fresh-dash-link fresh-dash-link--teal">
                                    <span class="fresh-dash-link__icon"><i class="fa fa-list-alt"></i></span>
                                    <span class="fresh-dash-link__text">Sales list</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('purchase.index') }}" class="fresh-dash-link fresh-dash-link--indigo">
                                    <span class="fresh-dash-link__icon"><i class="fa fa-shopping-basket"></i></span>
                                    <span class="fresh-dash-link__text">Purchase list</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('driver-issues.index') }}" class="fresh-dash-link fresh-dash-link--amber">
                                    <span class="fresh-dash-link__icon"><i class="fa fa-road"></i></span>
                                    <span class="fresh-dash-link__text">Driver issues</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('driver_closing.index') }}" class="fresh-dash-link fresh-dash-link--sun">
                                    <span class="fresh-dash-link__icon"><i class="fa fa-check-square-o"></i></span>
                                    <span class="fresh-dash-link__text">Driver closing</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('expense_entry.index') }}" class="fresh-dash-link fresh-dash-link--rose">
                                    <span class="fresh-dash-link__icon"><i class="fa fa-file-text-o"></i></span>
                                    <span class="fresh-dash-link__text">Expense entry</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('warehouse_stock.index') }}" class="fresh-dash-link fresh-dash-link--emerald">
                                    <span class="fresh-dash-link__icon"><i class="fa fa-archive"></i></span>
                                    <span class="fresh-dash-link__text">Warehouse stock</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('employee_salary_withdraw.index') }}" class="fresh-dash-link fresh-dash-link--slate">
                                    <span class="fresh-dash-link__icon"><i class="fa fa-money"></i></span>
                                    <span class="fresh-dash-link__text">Salary withdraw</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('supplier_payment.index') }}" class="fresh-dash-link fresh-dash-link--violet">
                                    <span class="fresh-dash-link__icon"><i class="fa fa-credit-card"></i></span>
                                    <span class="fresh-dash-link__text">Supplier payment</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 g-lg-4 mt-1">
            <div class="col-md-6">
                <div class="card card-round h-100 fresh-dash-panel fresh-dash-table-card">
                    <div class="card-header fresh-dash-panel__head d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Recent sales</h4>
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-label-info">View all</a>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-sm table-hover align-middle mb-0 fresh-dash-table">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Driver</th>
                                    <th>Customer</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    @php
                                        $driverDisplay = $sale->driver->name
                                            ?? (!empty($sale->driver_id) ? ('Driver #' . $sale->driver_id) : 'N/A');
                                        $customerDisplay = $sale->customer->name
                                            ?? (!empty($sale->customer_id) ? ('Customer #' . $sale->customer_id) : 'Unknown');
                                    @endphp
                                    <tr>
                                        <td class="fw-medium">{{ $sale->invoice_no }}</td>
                                        <td>{{ $driverDisplay }}</td>
                                        <td>{{ $customerDisplay }}</td>
                                        <td class="text-end">Tk {{ number_format(($sale->subtotal - $sale->discount), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No sales yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-round h-100 fresh-dash-panel fresh-dash-table-card">
                    <div class="card-header fresh-dash-panel__head d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Recent purchases</h4>
                        <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-label-info">View all</a>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-sm table-hover align-middle mb-0 fresh-dash-table">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Supplier</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPurchases as $purchase)
                                    <tr>
                                        <td class="fw-medium">{{ $purchase->invoice_no }}</td>
                                        <td>{{ $purchase->supplier->name ?? '—' }}</td>
                                        <td class="text-end">Tk {{ number_format(($purchase->total_amount - $purchase->discount), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No purchases yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
