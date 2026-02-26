@extends('backend.layouts.master')
@section('title','Supplier Balance Sheet')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Supplier Balance Sheet Report</h4>
                    </div>
                    <div class="card-body">

                        <form method="GET" action="{{ route('supplier_balance_sheet.print') }}" target="_blank">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="form-control js-example-basic-single" required>
                                    <option value="">-- Select Supplier --</option>
                                    @forelse($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @empty

                                    @endforelse
                                </select>
                            </div>
                            @include('report_type')

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-print"></i> Print Report
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection