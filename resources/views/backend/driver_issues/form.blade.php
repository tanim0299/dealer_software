<form action="{{ isset($issue)
        ? route('driver-issues.update', $issue->id)
        : route('driver-issues.store') }}"
      method="POST">

    @csrf
    @if(isset($issue))
        @method('PUT')
    @endif

    <div class="row">

        {{-- Driver --}}
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label class="form-label">Driver</label>
                <span class="text-danger">*</span>

                <select name="driver_id"
                        class="form-select @error('driver_id') is-invalid @enderror"
                        {{ isset($issue) ? '' : '' }}>
                    <option value="">Select Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}"
                            {{ old('driver_id', $issue->driver_id ?? '') == $driver->id ? 'selected' : '' }}>
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
                <input type="date"
                       name="issue_date"
                       value="{{ old('issue_date', $issue->issue_date ?? date('Y-m-d')) }}"
                       class="form-control"
                       {{ isset($issue) ? 'readonly' : '' }}>
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

                @php
                    $rows = old('items', isset($issue) ? $issue->items : [0]);
                @endphp

                @foreach($rows as $key => $row)
                    @php
                        $productId = $row['product_id'] ?? $row->product_id ?? '';
                        $issueQty  = $row['issue_qty'] ?? $row->issue_qty ?? '';
                    @endphp

                    <tr>
                        <td>
                            <select name="items[{{ $key }}][product_id]"
                                    class="form-select product-select"
                                    required>
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    @php
                                        $availableStock =
                                            ($product->warehouseStock->purchase_qty ?? 0)
                                            + ($product->warehouseStock->sales_return_qty ?? 0)
                                            - (
                                                ($product->warehouseStock->sales_qty ?? 0)
                                                + ($product->warehouseStock->return_qty ?? 0)
                                                + ($product->warehouseStock->sr_issue_qty ?? 0)
                                            );
                                    @endphp

                                    <option value="{{ $product->id }}"
                                        data-stock="{{ 
                                            ($product->warehouseStock->purchase_qty ?? 0)
                                            + ($product->warehouseStock->sales_return_qty ?? 0)
                                            - (
                                                ($product->warehouseStock->sales_qty ?? 0)
                                                + ($product->warehouseStock->return_qty ?? 0)
                                                + ($product->warehouseStock->sr_issue_qty ?? 0)
                                            )
                                        }}"
                                        {{ $productId == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>

                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input type="text"
                                class="form-control stock-view"
                                readonly
                                data-old-qty="{{ $issueQty ?? 0 }}">
                        </td>


                        <td>
                            <input type="number"
                                   name="items[{{ $key }}][issue_qty]"
                                   class="form-control issue-qty"
                                   min="1"
                                   value="{{ $issueQty }}"
                                   required>
                        </td>

                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-danger btn-sm remove-row">
                                ✖
                            </button>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{-- Add Row --}}
    <button type="button" class="btn btn-secondary mb-3" id="addRow">
        + Add Product
    </button>

    <div>
        <button type="submit" class="btn btn-primary">
            {{ isset($issue) ? 'Update Issue' : 'Issue Stock' }}
        </button>
    </div>

</form>

@push('scripts')
<script>
let rowIndex = {{ count($rows) }};

document.getElementById('addRow').addEventListener('click', function () {
    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][product_id]"
                    class="form-select product-select" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    @php
                        $availableStock =
                            ($product->warehouseStock->purchase_qty ?? 0)
                            + ($product->warehouseStock->sales_return_qty ?? 0)
                            - (
                                ($product->warehouseStock->sales_qty ?? 0)
                                + ($product->warehouseStock->return_qty ?? 0)
                                + ($product->warehouseStock->sr_issue_qty ?? 0)
                            );
                    @endphp
                    <option value="{{ $product->id }}"
                            data-stock="{{ $availableStock }}">
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
function updateStock(row) {
    let productSelect = row.querySelector('.product-select');
    let stockInput    = row.querySelector('.stock-view');

    let baseStock = parseFloat(
        productSelect.selectedOptions[0]?.dataset.stock || 0
    );

    // old issued qty (for edit)
    let oldQty = parseFloat(stockInput.dataset.oldQty || 0);

    // add back old qty
    let finalStock = baseStock + oldQty;

    stockInput.value = finalStock;
}
// On page load (IMPORTANT FOR EDIT)
document.querySelectorAll('#issueTable tbody tr').forEach(row => {
    updateStock(row);
});

// On product change
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('product-select')) {
        updateStock(e.target.closest('tr'));
    }
});

// Validate issue qty
document.addEventListener('input', function (e) {
    if (e.target.classList.contains('issue-qty')) {
        let row   = e.target.closest('tr');
        let stock = parseFloat(row.querySelector('.stock-view').value) || 0;
        let qty   = parseFloat(e.target.value) || 0;

        if (qty > stock) {
            e.target.value = stock;
            alert('Issue quantity cannot exceed available stock!');
        }
    }
});
</script>
@endpush
