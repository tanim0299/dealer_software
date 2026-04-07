@extends('backend.layouts.master')
@section('title','Customer Due Details')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- Header --}}
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Customer Due Details'])
        </div>

        <div class="row">
            {{-- Customer Information --}}
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Customer Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Customer Name</label>
                            <p class="form-control-plaintext">{{ $customer->name }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <p class="form-control-plaintext">{{ $customer->phone ?? 'N/A' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Area</label>
                            <p class="form-control-plaintext">
                                @if($customer->customerArea)
                                    <span class="badge badge-info">{{ $customer->customerArea->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <p class="form-control-plaintext">{{ $customer->address ?? 'N/A' }}</p>
                        </div>

                        <hr>

                        <div class="alert alert-danger" role="alert">
                            <h6 class="mb-2">Total Outstanding Due</h6>
                            <h3 class="mb-0">
                                <strong>Tk {{ number_format($due, 2) }}</strong>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Form --}}
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Record Payment</h4>
                    </div>
                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <strong>Error!</strong>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <strong>Success!</strong> {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                <strong>Error!</strong> {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('customer_due_list.storePayment') }}">
                            @csrf

                            {{-- Hidden Customer ID --}}
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                            {{-- Current Due --}}
                            <div class="mb-3">
                                <label class="form-label">Current Due Amount</label>
                                <input type="text" 
                                       class="form-control fw-bold" 
                                       value="Tk {{ number_format($due, 2) }}"
                                       readonly
                                       style="font-size: 1.1rem; background-color: #fff3cd;">
                            </div>

                            {{-- Payment Amount --}}
                            <div class="mb-3">
                                <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Tk</span>
                                    <input type="number"
                                           name="amount"
                                           id="amount"
                                           step="0.01"
                                           min="0.01"
                                           class="form-control"
                                           placeholder="Enter payment amount"
                                           value="{{ old('amount') }}"
                                           required>
                                </div>
                                @error('amount')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Validate Amount --}}
                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    Payment cannot exceed due amount of Tk {{ number_format($due, 2) }}
                                </small>
                            </div>

                            {{-- Note --}}
                            <div class="mb-3">
                                <label class="form-label">Note (Optional)</label>
                                <textarea name="note"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Add any payment notes...">{{ old('note') }}</textarea>
                                @error('note')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-credit-card"></i> Record Payment
                                </button>
                                <a href="{{ route('customer_due_list.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Validate payment amount on form submission
    $('form').on('submit', function(e) {
        let amount = parseFloat($('#amount').val());
        let due = {{ $due }};

        if(amount > due) {
            e.preventDefault();
            alert('Payment amount cannot exceed the due amount of Tk ' + due.toFixed(2));
            return false;
        }

        if(amount <= 0) {
            e.preventDefault();
            alert('Please enter a valid payment amount');
            return false;
        }
    });
});
</script>
@endpush

@endsection


