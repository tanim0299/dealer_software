@extends('backend.layouts.master')
@section('title','Supplier Payment')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Supplier Payment List</h5>

                <a href="{{ route('supplier_payment.index') }}"
                class="btn btn-primary btn-sm">
                    <i class="fa fa-eye"></i> View
                </a>
            </div>
            <div class="card-body">
    
                <form method="POST" action="{{ route('supplier_payment.store') }}">
                    @csrf
    
                    <div class="mb-3">
                        <label>Supplier</label>
                        <select name="supplier_id" id="supplier_id" class="form-select" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
    
                    <div class="mb-3">
                        <label>Current Due</label>
                        <input type="text" id="current_due" class="form-control" readonly>
                    </div>
    
                    <div class="mb-3">
                        <label>Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" required>
                    </div>
    
                    <div class="mb-3">
                        <label>Payment Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
    
                    <div class="mb-3">
                        <label>Note</label>
                        <textarea name="note" class="form-control"></textarea>
                    </div>
    
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
    
                </form>
    
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('supplier_id').addEventListener('change', function() {
    let supplierId = this.value;
    if(!supplierId) return;

    fetch('/supplier-payment/due/' + supplierId)
        .then(res => res.json())
        .then(data => {
            document.getElementById('current_due').value = data.due;
        });
});
</script>

@endsection