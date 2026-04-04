@extends('backend.layouts.master')

@section('title','Dashboard')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Dashboard'])
            <div class="ms-md-auto py-2 py-md-0 d-flex gap-2">
                <a href="{{ route('purchase.create') }}" class="btn btn-label-info btn-round">New Purchase</a>
                <a href="{{ route('driver-issues.create') }}" class="btn btn-primary btn-round">Issue Driver Stock</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="numbers">
                            <p class="card-category">Current Stock Qty</p>
                            <h4 class="card-title">{{ number_format($currentStockQty, 2) }}</h4>
                            <a href="{{ route('warehouse_stock.index') }}" class="small">View Stock</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="numbers">
                            <p class="card-category">Current Stock Value</p>
                            <h4 class="card-title">à§³ {{ number_format($currentStockValue, 2) }}</h4>
                            <a href="{{ route('warehouse_stock.index') }}" class="small">Inventory Value</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="numbers">
                            <p class="card-category">Customer Receivable</p>
                            <h4 class="card-title">à§³ {{ number_format($customerReceivable, 2) }}</h4>
                            <a href="{{ route('customer_due_list.index') }}" class="small">View Customer Due</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="numbers">
                            <p class="card-category">Supplier Payable</p>
                            <h4 class="card-title">à§³ {{ number_format($supplierPayable, 2) }}</h4>
                            <a href="{{ route('supplier_due_list.index') }}" class="small">View Supplier Due</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 col-6">
                <div class="card"><div class="card-body text-center"><div class="fw-bold">{{ $stats['products'] }}</div><small>Products</small></div></div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card"><div class="card-body text-center"><div class="fw-bold">{{ $stats['customers'] }}</div><small>Customers</small></div></div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card"><div class="card-body text-center"><div class="fw-bold">{{ $stats['suppliers'] }}</div><small>Suppliers</small></div></div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card"><div class="card-body text-center"><div class="fw-bold">{{ $stats['drivers'] }}</div><small>Drivers</small></div></div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card"><div class="card-body text-center"><div class="fw-bold">{{ $stats['employees'] }}</div><small>Employees</small></div></div>
            </div>
            <div class="col-md-2 col-6">
                <div class="card"><div class="card-body text-center"><div class="fw-bold">{{ $stats['open_driver_issues'] }}</div><small>Open Issues</small></div></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card card-round">
                    <div class="card-header"><h4 class="card-title mb-0">Today Business Summary</h4></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2"><span>Sales Amount</span><strong>à§³ {{ number_format($todaySalesAmount,2) }}</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Sales Paid</span><strong>à§³ {{ number_format($todaySalesPaid,2) }}</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Due Collection</span><strong>à§³ {{ number_format($todayCollectionAmount,2) }}</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Purchase Amount</span><strong>à§³ {{ number_format($todayPurchaseAmount,2) }}</strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Expense</span><strong class="text-danger">à§³ {{ number_format($todayExpenseAmount,2) }}</strong></div>
                        <div class="d-flex justify-content-between"><span>Return Paid</span><strong class="text-danger">à§³ {{ number_format($todayReturnPaid,2) }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-round">
                    <div class="card-header"><h4 class="card-title mb-0">Quick Links</h4></div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6"><a href="{{ route('sales.index') }}" class="btn btn-outline-primary w-100">Sales List</a></div>
                            <div class="col-6"><a href="{{ route('purchase.index') }}" class="btn btn-outline-primary w-100">Purchase List</a></div>
                            <div class="col-6"><a href="{{ route('driver-issues.index') }}" class="btn btn-outline-warning w-100">Driver Issues</a></div>
                            <div class="col-6"><a href="{{ route('driver_closing.index') }}" class="btn btn-outline-warning w-100">Driver Closing</a></div>
                            <div class="col-6"><a href="{{ route('expense_entry.index') }}" class="btn btn-outline-danger w-100">Expense Entry</a></div>
                            <div class="col-6"><a href="{{ route('warehouse_stock.index') }}" class="btn btn-outline-success w-100">Warehouse Stock</a></div>
                            <div class="col-6"><a href="{{ route('employee_salary_withdraw.index') }}" class="btn btn-outline-dark w-100">Salary Withdraw</a></div>
                            <div class="col-6"><a href="{{ route('supplier_payment.index') }}" class="btn btn-outline-dark w-100">Supplier Payment</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card card-round">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Recent Sales</h4>
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-label-info">View All</a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr><th>Invoice</th><th>Driver Name</th><th>Customer Name</th><th class="text-end">Amount</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    @php
                                        $driverDisplay = $sale->driver->name
                                            ?? (!empty($sale->driver_id) ? ('Driver #' . $sale->driver_id) : 'N/A');
                                        $customerDisplay = $sale->customer->name
                                            ?? (!empty($sale->customer_id) ? ('Customer #' . $sale->customer_id) : 'Unknown Customer');
                                    @endphp
                                    <tr>
                                        <td>{{ $sale->invoice_no }}</td>
                                        <td>{{ $driverDisplay }}</td>
                                        <td>{{ $customerDisplay }}</td>
                                        <td class="text-end">à§³ {{ number_format(($sale->subtotal - $sale->discount),2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No sales found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-round">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Recent Purchases</h4>
                        <a href="{{ route('purchase.index') }}" class="btn btn-sm btn-label-info">View All</a>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr><th>Invoice</th><th>Supplier</th><th class="text-end">Amount</th></tr>
                            </thead>
                            <tbody>
                                @forelse($recentPurchases as $purchase)
                                    <tr>
                                        <td>{{ $purchase->invoice_no }}</td>
                                        <td>{{ $purchase->supplier->name ?? '-' }}</td>
                                        <td class="text-end">à§³ {{ number_format(($purchase->total_amount - $purchase->discount),2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">No purchases found.</td></tr>
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

