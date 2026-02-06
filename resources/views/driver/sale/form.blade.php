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
                            onclick='selectProduct(
                                {{ $product->product_id }},
                                @json($product->product->name),
                                {{ $product->product->sale_price }},
                                {{ $product->available_qty }},
                                @json($product->product->unit->sub_unit)
                            )'>
                            {{ $product->product->name }}
                            <span class="float-end text-muted">
                                {{ $product->available_qty }} | ৳ {{ $product->product->sale_price }}
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
function selectProduct(id, name, price, availableQty, subUnits) {

    selectedProduct = {
        id,
        name,
        price,
        availableQty,
        subUnits
    };

    document.getElementById('selectedProductArea').classList.remove('d-none');
    document.getElementById('selectedProductName').innerText =
        `${name} (Available: ${availableQty})`;

    document.getElementById('productPrice').value = price;
    document.getElementById('productQty').value = 1;
}
function addToCart() {

    const qty = parseFloat(document.getElementById('productQty').value);
    const price = parseFloat(document.getElementById('productPrice').value);

    if (!qty || qty <= 0)
        return alert('Invalid quantity');

    const defaultSub = selectedProduct.subUnits[0];

    const existingIndex = cart.findIndex(
        p => p.product_id === selectedProduct.id
    );

    // 🔥 IF PRODUCT ALREADY EXISTS
    if (existingIndex !== -1) {

        let newQty = cart[existingIndex].qty + qty;

        // check stock
        if (newQty > selectedProduct.availableQty) {
            return alert('Stock limit exceeded');
        }

        cart[existingIndex].qty = newQty;
        cart[existingIndex].price = price;

        recalculateItem(existingIndex);
        renderCart();

        bootstrap.Modal.getInstance(
            document.getElementById('productModal')
        ).hide();

        return;
    }

    // 🔥 NEW PRODUCT ADD
    if (qty > selectedProduct.availableQty)
        return alert('Stock limit exceeded');

    cart.push({
        product_id: selectedProduct.id,
        name: selectedProduct.name,
        qty,
        price,
        discount: 0,
        availableQty: selectedProduct.availableQty,
        subUnits: selectedProduct.subUnits,
        sub_unit_id: defaultSub?.id || null,
        unit_data: defaultSub?.unit_data || 1,
        final_quantity: qty * (1 / (defaultSub?.unit_data || 1)),
        line_total: qty * price
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

        subtotal += item.line_total;

        let subUnitOptions = '';

        item.subUnits.forEach(sub => {
            subUnitOptions += `
                <option value="${sub.id}"
                    data-unit="${sub.unit_data}"
                    ${sub.id == item.sub_unit_id ? 'selected' : ''}>
                    ${sub.name}
                </option>
            `;
        });

        cartDiv.innerHTML += `
        <div class="card mb-2">
            <div class="card-body">

                <div class="d-flex justify-content-between">
                    <strong>${item.name}</strong>
                    <button class="btn btn-sm btn-danger"
                            onclick="removeItem(${index})">✕</button>
                </div>

                <div class="row g-2 mt-2">

                    <!-- Qty -->
                    <div class="col-3">
                        <input type="number"
                               class="form-control"
                               value="${item.qty}"
                               onchange="updateQty(${index}, this.value)">
                    </div>

                    <!-- Price -->
                    <div class="col-3">
                        <input type="number"
                               class="form-control"
                               value="${item.price}"
                               onchange="updatePrice(${index}, this.value)">
                    </div>

                    <!-- Discount -->
                    <div class="col-3">
                        <input type="number"
                               class="form-control"
                               value="${item.discount}"
                               onchange="updateDiscount(${index}, this.value)">
                    </div>

                    <!-- Sub Unit -->
                    <div class="col-3">
                        <select class="form-select"
                                onchange="changeSubUnit(${index}, this)">
                            ${subUnitOptions}
                        </select>
                    </div>

                </div>

                <div class="mt-2 d-flex justify-content-between">
                    <small class="text-muted">
                        Base Qty: ${item.final_quantity.toFixed(4)}
                    </small>
                    <strong>
                        ৳ ${item.line_total.toFixed(2)}
                    </strong>
                </div>

            </div>
        </div>
        `;
    });

    updateTotals(subtotal);
}
function recalculateItem(index) {

    const item = cart[index];

    item.final_quantity = item.qty * (1 / item.unit_data);

    // 🔥 STOCK CHECK (BASE UNIT)
    if (item.final_quantity > item.availableQty) {
        alert('Stock exceeded!');
        item.qty = 1;
        item.final_quantity = 1 * (1 / item.unit_data);
    }

    const gross = item.qty * item.price;

    if (item.discount > gross)
        item.discount = gross;

    item.line_total = gross - item.discount;
}

function changeSubUnit(index, selectElement) {

    const unitData = parseFloat(
        selectElement.options[selectElement.selectedIndex]
        .getAttribute('data-unit')
    );

    cart[index].sub_unit_id = parseInt(selectElement.value);
    cart[index].unit_data = unitData;

    recalculateItem(index);
    renderCart();
}


function updateQty(index, qty) {
    cart[index].qty = parseFloat(qty) || 1;
    recalculateItem(index);
    renderCart();
}
function updatePrice(index, price) {
    cart[index].price = parseFloat(price) || 0;
    recalculateItem(index);
    renderCart();
}
function updateDiscount(index, discount) {
    cart[index].discount = parseFloat(discount) || 0;
    recalculateItem(index);
    renderCart();
}


function removeItem(index) {
    if (!confirm('Remove this item?')) return;

    cart.splice(index, 1);
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
