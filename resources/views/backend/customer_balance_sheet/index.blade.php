@extends('backend.layouts.master')
@section('title','Customer Balance Sheet')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Customer Balance Sheet Report</h4>
                    </div>
                    <div class="card-body">

                        <form method="GET" action="{{ route('customer_balance_sheet.print') }}" target="_blank">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Select Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control js-example-basic-single" required>
                                    <option value="">-- Select Customer --</option>
                                    @forelse($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
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
