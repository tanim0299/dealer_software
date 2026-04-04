@extends('backend.layouts.master')
@section('title','Customer Due List')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- Header --}}
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Customer Due List'])
         
        </div>

        {{-- Customer Due List --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Customer Due List</h4>
                        <div class="card-tools">
                            <span class="badge badge-warning">
                                Total Due Customers: {{ count($customers) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Search Form --}}
                        <div class="mb-4">
                            <form method="GET" action="{{ route('customer_due_list.index') }}" class="form-inline">
                                <div class="row w-100">
                                    <div class="col-md-6">
                                        <div class="input-group w-100">
                                            <input type="text" 
                                                   name="search" 
                                                   class="form-control" 
                                                   placeholder="Search by customer name or phone"
                                                   value="{{ request('search') }}">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                    @if(request('search'))
                                        <div class="col-md-6 text-end">
                                            <a href="{{ route('customer_due_list.index') }}" class="btn btn-secondary">
                                                <i class="fa fa-times"></i> Clear Search
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>

                        @if(request('search'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                Search Results for: <strong>{{ request('search') }}</strong>
                            </div>
                        @endif

                        @if(count($customers) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SL</th>
                                            <th>Customer Name</th>
                                            <th>Contact</th>
                                            <th>Area</th>
                                            <th class="text-right">Due Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                                            $total_due = 0;
                                        @endphp
                                        @forelse($customers as $key => $customer)
                                            @php
                                                $total_due += $customer['due'];
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $key + 1 }}</strong>
                                                </td>

                                                <td>
                                                    <strong>{{ $customer['name'] }}</strong>
                                                </td>

                                                <td>
                                                    <small class="text-muted">
                                                        {{ $customer['phone'] ?? 'N/A' }}
                                                    </small>
                                                </td>

                                                <td>
                                                    <span class="badge badge-info">{{ $customer['area'] }}</span>
                                                </td>

                                                <td class="text-right">
                                                    <span class="badge badge-danger badge-lg">
                                                        {{ number_format($customer['due'], 2) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <a href="{{ route('customer_due_list.show', $customer['id']) }}" 
                                                       class="btn btn-sm btn-success" title="Record Payment">
                                                        <i class="fa fa-credit-card"></i> Pay
                                                    </a>
                                                    
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <i class="fa fa-check-circle" style="font-size: 24px; color: #28a745;"></i>
                                                    <p class="mt-2 text-success"><strong>All Customers Paid!</strong></p>
                                                    <p class="text-muted">No customers with outstanding dues.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Total Due Row --}}
                            @if(count($customers) > 0)
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h5 class="mb-0">Total Outstanding Due</h5>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <h3 class="text-danger mb-0">
                                                            <strong>{{ number_format($total_due, 2) }}</strong>
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

