@extends('driver.layouts.master')

@section('body')

<div class="container py-3">

    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3 text-center">Customer Payment</h5>

            <form method="POST" action="{{ route('customer_payment.store') }}">
                @csrf

                {{-- Customer --}}
                <div class="mb-3">
                    <label class="form-label">Select Customer</label>
                    <select name="customer_id" id="customer"
                            class="form-control js-example-basic-single" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Due Show --}}
                <div class="mb-3">
                    <label class="form-label">Total Due</label>
                    <input type="text" id="due_amount"
                           class="form-control bg-light"
                           readonly>
                </div>

                {{-- Payment Amount --}}
                <div class="mb-3">
                    <label class="form-label">Payment Amount</label>
                    <input type="number"
                           name="amount"
                           step="0.01"
                           class="form-control"
                           required>
                </div>

                {{-- Note --}}
                <div class="mb-3">
                    <label class="form-label">Note (Optional)</label>
                    <textarea name="note"
                              class="form-control"
                              rows="2"></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        Submit Payment
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@push('scripts')
<script>
$(document).ready(function() {

    $('#customer').on('change', function() {

        let customerId = $(this).val();

        if(customerId) {

            $.ajax({
                url: '/customer-due/' + customerId,
                type: 'GET',
                success: function(response) {
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

@endsection
