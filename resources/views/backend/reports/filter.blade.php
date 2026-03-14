@extends('backend.layouts.master')
@section('title', $reportTitle)

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ $reportTitle }}</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ $printRoute }}" target="_blank">
                            @foreach($extraFilters as $filter)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ $filter['label'] }}</label>
                                    <select name="{{ $filter['name'] }}" class="form-control js-example-basic-single">
                                        <option value="">{{ $filter['placeholder'] }}</option>
                                        @foreach($filter['options'] as $option)
                                            <option value="{{ $option->{$filter['value_field']} }}">{{ $option->{$filter['text_field']} }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach

                            @include('report_type')

                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-print"></i> Generate Report
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
