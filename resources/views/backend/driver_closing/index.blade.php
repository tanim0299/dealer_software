@extends('backend.layouts.master')
@section('title','Driver Closing')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Driver Closing'])
            
        </div>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form method="get" action="{{ route('driver.show_closing') }}" target="_blank">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Select Driver</label><span class="text-danger">*</span>
                                    <select class="form-select js-example-basic-single" id="driver_id" name="driver_id" required> 
                                        <option value="">Chose One</option>
                                        @forelse($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Date</label><span class="text-danger">*</span>
                                    <input type="date" name="date" id="date" value="{{ date('Y-m-d') }}" readonly class="form-control">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <br>
                                    <button type="submit" class="btn btn-info btn-sm">Show Closing</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
