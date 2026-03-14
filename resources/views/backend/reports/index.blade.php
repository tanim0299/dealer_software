@extends('backend.layouts.master')
@section('title','Inventory Reports')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Inventory Reports'])
        </div>

        <div class="row g-3">
            <div class="col-md-3 col-sm-6"><a class="btn btn-outline-primary w-100" href="{{ route('sales_report.index') }}">Sales Report</a></div>
            <div class="col-md-3 col-sm-6"><a class="btn btn-outline-primary w-100" href="{{ route('purchase_report.index') }}">Purchase Report</a></div>
            <div class="col-md-3 col-sm-6"><a class="btn btn-outline-success w-100" href="{{ route('stock_report.index') }}">Stock Movement Report</a></div>
            <div class="col-md-3 col-sm-6"><a class="btn btn-outline-warning w-100" href="{{ route('cash_report.index') }}">Cash Report</a></div>
            <div class="col-md-3 col-sm-6"><a class="btn btn-outline-danger w-100" href="{{ route('sales_return_report.index') }}">Sales Return Report</a></div>
            <div class="col-md-3 col-sm-6"><a class="btn btn-outline-danger w-100" href="{{ route('purchase_return_report.index') }}">Purchase Return Report</a></div>
            <div class="col-md-3 col-sm-6"><a class="btn btn-outline-info w-100" href="{{ route('income_report.index') }}">Income Report</a></div>
            <div class="col-md-3 col-sm-6"><a class="btn btn-outline-secondary w-100" href="{{ route('expense_report.index') }}">Expense Report</a></div>
        </div>
    </div>
</div>
@endsection
