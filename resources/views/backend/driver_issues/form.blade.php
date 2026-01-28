<form action="{{ route('driver-issues.store') }}" method="POST">
    @csrf

    <div class="row">

        {{-- Driver --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label class="form-label">Driver</label>
                <span class="text-danger">*</span>
                <select name="driver_id"
                        class="form-select @error('driver_id') is-invalid @enderror">
                    <option value="">Select Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}">
                            {{ $driver->name }} ({{ $driver->vehicle_no }})
                        </option>
                    @endforeach
                </select>
                @error('driver_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Issue Date --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label class="form-label">Issue Date</label>
                <input type="date" name="issue_date"
                       value="{{ date('Y-m-d') }}"
                       class="form-control">
            </div>
        </div>

    </div>

    <hr>

    {{-- Products Table --}}
    <div class="table-responsive">
        <table class="table table-bordered" id="issueTable">
            <thead class="table-light">
                <tr>
                    <th width="35%">Product</th>
                    <th width="20%">Available Stock</th>
                    <th width="20%">Issue Qty</th>
                    <th width="10%">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="items[0][product_id]"
                                class="form-select product-select"
                                required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-stock="{{ $product->warehouseStock->stock_qty ?? 0 }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <input type="text"
                               class="form-control stock-view"
                               readonly>
                    </td>

                    <td>
                        <input type="number"
                               name="items[0][issue_qty]"
                               class="form-control issue-qty"
                               min="1"
                               required>
                    </td>

                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            ✖
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Add Row --}}
    <button type="button" class="btn btn-secondary mb-3" id="addRow">
        + Add Product
    </button>

    <div>
        <button type="submit" class="btn btn-primary">
            Issue Stock
        </button>
    </div>

</form>
@push('scripts')
<script>
let rowIndex = 1;

document.getElementById('addRow').addEventListener('click', function () {
    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][product_id]"
                    class="form-select product-select" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                            data-stock="{{ $product->warehouseStock->stock_qty ?? 0 }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control stock-view" readonly>
        </td>
        <td>
            <input type="number"
                   name="items[${rowIndex}][issue_qty]"
                   class="form-control issue-qty"
                   min="1" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-row">✖</button>
        </td>
    </tr>
    `;
    document.querySelector('#issueTable tbody').insertAdjacentHTML('beforeend', row);
    rowIndex++;
});

// Remove row
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
    }
});

// Show stock on product select
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('product-select')) {
        let stock = e.target.selectedOptions[0].dataset.stock || 0;
        e.target.closest('tr').querySelector('.stock-view').value = stock;
    }
});

// Validate issue qty
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('issue-qty')) {
        let row = e.target.closest('tr');
        let stock = parseFloat(row.querySelector('.stock-view').value) || 0;
        let qty = parseFloat(e.target.value) || 0;

        if (qty > stock) {
            e.target.value = stock;
            alert('Issue quantity cannot exceed available stock!');
        }
    }
});
</script>
@endpush
