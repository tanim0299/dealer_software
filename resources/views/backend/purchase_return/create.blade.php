@extends('backend.layouts.master')
@section('title','Create Purchase Return')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Create Purchase Return'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(auth()->user()->can('Purchase Return view'))
                <a href="{{ route('purchase_return.index') }}" class="btn btn-primary btn-round">
                    <i class="fa fa-eye"></i> View Purchase Returns
                </a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('purchase_return.store') }}" method="POST">
                        @csrf

                        {{-- Supplier Select --}}
                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Select Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="form-select" required>
                                <option value="">-- Choose Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Return Type --}}
                        <div class="mb-3">
                            <label for="return_type" class="form-label">Return Type</label>
                            <select name="return_type" id="return_type" class="form-select" required>
                                <option value="1">Cash</option>
                                <option value="2">Minus From Due</option>
                            </select>
                        </div>

                        {{-- Products Table --}}
                        <div class="mb-3">
                            <label class="form-label">Select Products to Return</label>
                            <table class="table table-bordered" id="products_table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Available Stock</th>
                                        <th>Purchase Price</th>
                                        <th>Return Qty</th>
                                        <th>Total</th>
                                        <th><button type="button" class="btn btn-success btn-sm" id="add_row">+</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="products[0][product_id]" class="form-select product-select" required>
                                                <option value="">-- Select Product --</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->product_id }}" data-price="{{ $product->purchase_price }}" data-stock="{{ $product->available_qty }}">
                                                        {{ $product->product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="stock">0</td>
                                        <td class="price">0</td>
                                        <td><input type="number" name="products[0][return_qty]" class="form-control return-qty" min="0" value="0" required></td>
                                        <td class="total">0</td>
                                        <td><button type="button" class="btn btn-danger btn-sm remove_row">x</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Submit --}}
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Process Return</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS for dynamic table --}}
@push('scripts')
<script>
    let rowIndex = 1;

// Keep track of selected product IDs
let selectedProducts = [];

// Add new row
$('#add_row').click(function() {
    let row = `
    <tr>
        <td>
            <select name="products[${rowIndex}][product_id]" class="form-select product-select" required>
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->product_id }}" data-price="{{ $product->purchase_price }}" data-stock="{{ $product->available_qty }}">
                        {{ $product->product->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td class="stock">0</td>
        <td class="price">0</td>
        <td><input type="number" name="products[${rowIndex}][return_qty]" class="form-control return-qty" min="0" value="0" required></td>
        <td class="total">0</td>
        <td><button type="button" class="btn btn-danger btn-sm remove_row">x</button></td>
    </tr>`;
    $('#products_table tbody').append(row);
    rowIndex++;
    updateProductOptions();
});

// Remove row
$(document).on('click', '.remove_row', function() {
    let removedProduct = $(this).closest('tr').find('.product-select').val();
    if (removedProduct) {
        selectedProducts = selectedProducts.filter(id => id != removedProduct);
    }
    $(this).closest('tr').remove();
    updateProductOptions();
});

// Update stock, price, total
$(document).on('change', '.product-select', function() {
    let option = $(this).find('option:selected');
    let price = parseFloat(option.data('price')) || 0;
    let stock = parseFloat(option.data('stock')) || 0;
    let row = $(this).closest('tr');
    let prevSelected = row.data('prev-product'); // previous product in this row

    // Prevent duplicate selection
    let currentVal = $(this).val();
    
    // Update selectedProducts
    if (prevSelected) {
        selectedProducts = selectedProducts.filter(id => id != prevSelected);
    }
    selectedProducts.push(currentVal);
    row.data('prev-product', currentVal);

    row.find('.price').text(price);
    row.find('.stock').text(stock);

    let qty = parseFloat(row.find('.return-qty').val()) || 0;
    if (qty > stock) {
        qty = stock;
        row.find('.return-qty').val(stock);
        alert('Return quantity cannot exceed available stock!');
    }
    row.find('.total').text(qty * price);
    updateProductOptions();
});

// Validate quantity input
$(document).on('input', '.return-qty', function() {
    let row = $(this).closest('tr');
    let qty = parseFloat($(this).val()) || 0;
    let stock = parseFloat(row.find('.stock').text()) || 0;
    if (qty > stock) {
        $(this).val(stock);
        qty = stock;
        alert('Return quantity cannot exceed available stock!');
    }
    let price = parseFloat(row.find('.price').text()) || 0;
    row.find('.total').text(qty * price);
});

// Disable already selected products in other rows
function updateProductOptions() {
    $('.product-select').each(function() {
        let currentVal = $(this).val();
        $(this).find('option').each(function() {
            if ($(this).val() == '') return;
            if (selectedProducts.includes($(this).val()) && $(this).val() != currentVal) {
                $(this).attr('disabled', true);
            } else {
                $(this).attr('disabled', false);
            }
        });
    });
}
</script>
@endpush
@endsection