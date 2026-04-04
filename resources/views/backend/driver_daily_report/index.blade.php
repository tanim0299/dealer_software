@extends('backend.layouts.master')
@section('title','Driver Daily Report')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Driver Daily Report'])
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('driver_daily_report.statement') }}" target="_blank">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Select Driver <span class="text-danger">*</span></label>
                            <select class="form-select js-example-basic-single" name="driver_id" required>
                                <option value="">Choose One</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ data_get($search ?? [], 'driver_id') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" value="{{ data_get($search ?? [], 'date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary me-2" type="submit">Generate Statement</button>
                            <a href="{{ route('driver_daily_report.index') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection





