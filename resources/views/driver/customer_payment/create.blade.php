@extends('driver.layouts.master')

@section('page_title', 'Collect due')

@section('body')
    <div class="page-card p-3">
        <form method="POST" action="{{ route('customer_payment.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Customer <span class="text-danger">*</span></label>
                <select name="customer_id" id="customer" class="form-select js-example-basic-single" required>
                    <option value="">Select customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Total due</label>
                <input type="text" id="due_amount" class="form-control bg-light" readonly placeholder="Select customer">
            </div>

            <div class="mb-3">
                <label class="form-label">Payment amount <span class="text-danger">*</span></label>
                <input type="number" name="amount" step="0.01" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Note</label>
                <textarea name="note" class="form-control" rows="2" placeholder="Optional"></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit payment</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#customer').on('change', function () {
                var customerId = $(this).val();
                if (customerId) {
                    $.ajax({
                        url: '/customer-due/' + customerId,
                        type: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        success: function (response) {
                            var d = parseFloat(response.due);
                            $('#due_amount').val(isNaN(d) ? '' : d.toFixed(2));
                        },
                        error: function (xhr) {
                            $('#due_amount').val('');
                            var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Could not load due.';
                            if (xhr.status === 403) {
                                alert(msg);
                            }
                        }
                    });
                } else {
                    $('#due_amount').val('');
                }
            });
        });
    </script>
@endpush
