@extends('backend.layouts.master')
@section('title', 'Create Customer Payment')

@section('content')
<div class="container">
    <div class="page-inner">

        <div class="card">
            <div class="card-body">

                <form action="{{ route('customer_payment.store') }}" method="POST">
                    @csrf

                    {{-- Payment type --}}
                    <input type="hidden" name="type" value="1">

                    <div class="row">

                        {{-- Date --}}
                        <div class="col-md-4 col-lg-4 col-12">
                            <div class="mb-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="date"
                                       value="{{ old('date', now()->toDateString()) }}"
                                       class="form-control @error('date') is-invalid @enderror"
                                       required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Customer --}}
                        <div class="col-md-4 col-lg-4 col-12">
                            <div class="mb-3">
                                <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                                <select name="customer_id"
                                        id="customer"
                                        class="form-select js-example-basic-single @error('customer_id') is-invalid @enderror"
                                        required>
                                    <option value="">Choose One</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Due Amount --}}
                        <div class="col-md-4 col-lg-4 col-12">
                            <div class="mb-3">
                                <label class="form-label">Total Due</label>
                                <input type="text"
                                       id="due_amount"
                                       class="form-control bg-light"
                                       readonly>
                            </div>
                        </div>

                        {{-- Payment Amount --}}
                        <div class="col-md-4 col-lg-4 col-12">
                            <div class="mb-3">
                                <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                <input type="number"
                                       step="0.01"
                                       name="amount"
                                       value="{{ old('amount') }}"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="col-md-8 col-lg-8 col-12">
                            <div class="mb-3">
                                <label class="form-label">Note</label>
                                <textarea name="note"
                                          rows="2"
                                          class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary">
                        Submit Payment
                    </button>

                </form>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    $('#customer').on('change', function () {

        let customerId = $(this).val();

        if (customerId) {
            $.ajax({
                url: '/customer-due/' + customerId,
                type: 'GET',
                success: function (response) {
                    $('#due_amount').val(
                        parseFloat(response.due).toFixed(2)
                    );
                }
            });
        } else {
            $('#due_amount').val('');
        }
    });

});
</script>
@endpush
