@php $isEdit = isset($sale); @endphp

<!-- Header -->
<nav class="navbar navbar-dark bg-primary">
    <div class="container-fluid">
        <button class="btn btn-primary" onclick="history.back()">←</button>
        <span class="navbar-brand mx-auto">
            {{ $isEdit ? 'Edit Sale' : 'New Sale' }}
        </span>
    </div>
</nav>

<div class="container-fluid mt-3">

    <!-- Customer -->
    <div class="card mb-2">
        <div class="card-body">
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-select">
                <option value="">Walk-in</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}"
                        {{ $isEdit && $sale->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Invoice Info -->
    <div class="card mb-2">
        <div class="card-body row g-2">
            <div class="col-6">
                <label class="form-label">Date</label>
                <input type="date" name="sale_date" class="form-control"
                       value="{{ $isEdit ? $sale->sale_date : date('Y-m-d') }}">
            </div>
            <div class="col-6">
                <label class="form-label">Voucher No</label>
                <input type="text" name="voucher_no" class="form-control"
                       value="{{ $isEdit ? $sale->voucher_no : '' }}">
            </div>
        </div>
    </div>

    <!-- Slip -->
    <div class="card mb-2">
        <div class="card-body">
            <label class="form-label">Upload Slip</label>
            <input type="file" name="slip_image" class="form-control">
        </div>
    </div>

    <!-- Add Product -->
    <div class="d-grid mb-2">
        <button type="button"
                class="btn btn-success btn-lg"
                data-bs-toggle="modal"
                data-bs-target="#productModal">
            ➕ Add Product
        </button>

    </div>

    <!-- Cart -->
    <div id="cartItems">
        {{-- JS inject cart rows --}}
    </div>

    <!-- Summary -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal</span>
                <strong id="subtotal">0</strong>
            </div>

            <div class="mb-2">
                <label class="form-label">Discount</label>
                <input type="number" name="discount" class="form-control"
                       value="{{ $isEdit ? $sale->discount : 0 }}">
            </div>

            <div class="d-flex justify-content-between mb-2">
                <span>Grand Total</span>
                <strong id="grandTotal">0</strong>
            </div>

            <div class="mb-2">
                <label class="form-label">Paid</label>
                <input type="number" name="paid_amount" class="form-control"
                       value="{{ $isEdit ? $sale->paid_amount : 0 }}">
            </div>

            <div class="d-flex justify-content-between text-danger fw-bold">
                <span>Due</span>
                <span id="dueAmount">0</span>
            </div>
        </div>
    </div>

</div>

<!-- Fixed Save Button -->
<div class="fixed-action px-3">
    <button type="submit" class="btn btn-primary btn-lg w-100">
        {{ $isEdit ? 'Update Sale' : 'Save Sale' }}
    </button>
</div>
<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Search -->
                <input type="text" id="productSearch" class="form-control mb-2"
                       placeholder="Search product...">

                <!-- Product List -->
                <div id="productList" class="list-group">
                    @foreach($products as $product)
                        <button type="button"
                            class="list-group-item list-group-item-action"
                            onclick="selectProduct(
                                {{ $product->product_id }},
                                '{{ $product->product_name }}',
                                {{ $product->sale_price ?? 0 }},
                                {{ $product->available_qty }}
                            )">
                            {{ $product->product_name }}
                            <span class="float-end text-muted">
                                {{ $product->available_qty }} pcs | ৳ {{ $product->sale_price ?? 0 }}
                            </span>
                        </button>
                        @endforeach

                </div>

                <hr>

                <!-- Selected Product -->
                <div id="selectedProductArea" class="d-none">
                    <h6 id="selectedProductName"></h6>

                    <div class="row g-2">
                        <div class="col-6">
                            <input type="number" id="productQty" class="form-control"
                                   placeholder="Qty" value="1">
                        </div>
                        <div class="col-6">
                            <input type="number" id="productPrice" class="form-control"
                                   placeholder="Price">
                        </div>
                    </div>

                    <button type="button" class="btn btn-success w-100 mt-3"
                            onclick="addToCart()">
                        Add to Cart
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
let cart = [];
let selectedProduct = null;

/* select product */
function selectProduct(id, name, price, availableQty) {
    selectedProduct = { id, name, price, availableQty };

    document.getElementById('selectedProductArea').classList.remove('d-none');
    document.getElementById('selectedProductName').innerText =
        `${name} (Available: ${availableQty})`;

    document.getElementById('productPrice').value = price;
    document.getElementById('productQty').value = 1;
}


/* add to cart */
function addToCart() {
    const qty = parseFloat(document.getElementById('productQty').value);
    const price = parseFloat(document.getElementById('productPrice').value);

    if (!qty || qty <= 0)
        return alert('Invalid quantity');

    if (qty > selectedProduct.availableQty)
        return alert('Quantity exceeds available stock');

    // prevent duplicate product
    const exists = cart.find(p => p.product_id === selectedProduct.id);
    if (exists) {
        return alert('Product already added');
    }

    cart.push({
        product_id: selectedProduct.id,
        name: selectedProduct.name,
        qty,
        price,
        availableQty: selectedProduct.availableQty,
        total: qty * price
    });

    renderCart();
    bootstrap.Modal.getInstance(
        document.getElementById('productModal')
    ).hide();
}

function renderCart() {
    const cartDiv = document.getElementById('cartItems');
    cartDiv.innerHTML = '';

    let subtotal = 0;

    cart.forEach((item, index) => {
        subtotal += item.total;

        cartDiv.innerHTML += `
        <div class="card mb-2">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">
                    <strong>${item.name}</strong>
                    <button class="btn btn-sm btn-danger"
                            onclick="removeItem(${index})">
                        ✕
                    </button>
                </div>

                <small class="text-muted">
                    Available: ${item.availableQty}
                </small>

                <div class="row g-2 mt-2">
                    <div class="col-4">
                        <input type="number"
                               class="form-control"
                               min="1"
                               max="${item.availableQty}"
                               value="${item.qty}"
                               onchange="updateQty(${index}, this.value)">
                    </div>

                    <div class="col-4">
                        <input type="number"
                               class="form-control"
                               value="${item.price}"
                               onchange="updatePrice(${index}, this.value)">
                    </div>

                    <div class="col-4 text-end fw-bold">
                        ৳ ${item.total.toFixed(2)}
                    </div>
                </div>
            </div>
        </div>
        `;
    });

    updateTotals(subtotal);
}


function updateQty(index, qty) {
    qty = parseFloat(qty);

    if (qty <= 0) qty = 1;

    if (qty > cart[index].availableQty) {
        alert('Quantity exceeds available stock');
        qty = cart[index].availableQty;
    }

    cart[index].qty = qty;
    cart[index].total = qty * cart[index].price;

    renderCart();
}

function removeItem(index) {
    if (!confirm('Remove this item?')) return;

    cart.splice(index, 1);
    renderCart();
}

/* update price */
function updatePrice(index, price) {
    cart[index].price = price;
    cart[index].total = cart[index].qty * price;
    renderCart();
}

/* totals */
function updateTotals(subtotal) {
    const discount = parseFloat(document.querySelector('[name="discount"]').value || 0);
    const paid = parseFloat(document.querySelector('[name="paid_amount"]').value || 0);

    document.getElementById('subtotal').innerText = subtotal.toFixed(2);
    document.getElementById('grandTotal').innerText = (subtotal - discount).toFixed(2);
    document.getElementById('dueAmount').innerText =
        ((subtotal - discount) - paid).toFixed(2);
}
</script>
