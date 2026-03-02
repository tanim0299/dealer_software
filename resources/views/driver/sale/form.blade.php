@php $isEdit = isset($sale); @endphp

@push('styles')
<style>
    .sale-form-shell .section-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(15, 23, 42, .06);
        margin-bottom: 12px;
    }

    .sale-form-shell .section-title {
        font-size: .9rem;
        font-weight: 700;
        letter-spacing: .02em;
        margin-bottom: 10px;
        color: #64748b;
        text-transform: uppercase;
    }

    .sale-form-shell .form-label {
        font-size: .85rem;
        color: #475569;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .sale-form-shell .form-control,
    .sale-form-shell .form-select {
        border-radius: 10px;
        min-height: 44px;
    }

    .sale-form-shell .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #e2e8f0;
        font-size: .95rem;
    }

    .sale-form-shell .summary-row:last-child {
        border-bottom: 0;
    }

    .sale-form-shell .summary-amount {
        font-weight: 700;
        color: #0f172a;
    }

    .sale-form-shell .summary-due {
        color: #dc2626;
    }

    .sale-form-shell .sticky-submit {
        position: sticky;
        bottom: 78px;
        z-index: 10;
        padding-top: 6px;
    }

    .sale-form-shell .cart-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .04);
    }

    .sale-form-shell .cart-line-total {
        font-weight: 700;
        color: #0f172a;
    }
</style>
@endpush

<form method="POST"
      action="{{ $isEdit ? route('sales.update', $sale->id) : route('sales.store') }}"
      id="saleForm"
      enctype="multipart/form-data">

    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
<div class="page-card p-3 sale-form-shell">
    <!-- Customer -->
    <div class="card section-card">
        <div class="card-body">
            <div class="section-title">Customer</div>
            <label class="form-label">Customer</label>
            <select name="customer_id" class="form-select js-example-basic-single">
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}"
                        data-is-cash="{{ (isset($cashCustomerId) && (int)$cashCustomerId === (int)$customer->id) ? 1 : 0 }}"
                        {{ $isEdit && $sale->customer_id == $customer->id ? 'selected' : '' }}
                        {{ !$isEdit && isset($cashCustomerId) && (int)$cashCustomerId === (int)$customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}
                        @if(isset($cashCustomerId) && (int)$cashCustomerId === (int)$customer->id)
                            (Cash Customer)
                        @endif
                    </option>
                @endforeach
            </select>
            @if(isset($cashCustomerId))
                <small class="text-muted d-block mt-1">Default cash sale customer is selected.</small>
            @endif
        </div>
    </div>

    <!-- Invoice Info -->
    <div class="card section-card">
        <div class="card-body row g-2">
            <div class="section-title">Invoice Information</div>
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
    <div class="card section-card">
        <div class="card-body">
            <div class="section-title">Attachment</div>
            <label class="form-label">Upload Slip</label>
            <input type="file" name="slip_image" class="form-control">
        </div>
    </div>

    <!-- Add Product -->
    <div class="d-grid mb-3">
        <button type="button"
                class="btn btn-success btn-lg rounded-3"
                data-bs-toggle="modal"
                data-bs-target="#productModal">
            ➕ Add Product
        </button>

    </div>

    <!-- Cart -->
    <div id="cartItems">
        {{-- JS inject cart rows --}}
    </div>
    <input type="hidden" name="cart_items" id="cartItemsInput">


    <!-- Summary -->
    <div class="card section-card mt-3">
        <div class="card-body">
            <div class="section-title">Payment Summary</div>

            <div class="summary-row">
                <span>Subtotal</span>
                <strong id="subtotal" class="summary-amount">0</strong>
            </div>

            <div class="mb-2">
                <label class="form-label">Discount</label>
                <input type="number" name="discount" class="form-control"
                       value="{{ $isEdit ? $sale->discount : 0 }}">
            </div>

            <div class="summary-row">
                <span>Grand Total</span>
                <strong id="grandTotal" class="summary-amount">0</strong>
            </div>

            <div class="mb-2">
                <label class="form-label">Paid</label>
                <input type="number" name="paid_amount" class="form-control"
                       value="{{ $isEdit ? $sale->paid_amount : 0 }}">
            </div>

            <div class="summary-row fw-bold">
                <span>Due</span>
                <span id="dueAmount" class="summary-due">0</span>
            </div>
        </div>
    </div>

</div>

<!-- Due Customer Modal -->
<div class="modal fade" id="dueCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Due Found - Create Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning py-2 mb-3">
                    Cash customer can not keep due. Please create a customer and continue sale.
                </div>

                <div class="mb-2">
                    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="dueCustomerName" placeholder="Enter customer name">
                </div>

                <div class="mb-2">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" id="dueCustomerPhone" placeholder="Enter phone">
                </div>

                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="dueCustomerEmail" placeholder="Enter email">
                </div>

                <div class="mb-2">
                    <label class="form-label">Area <span class="text-danger">*</span></label>
                    <select class="form-select" id="dueCustomerAreaId">
                        <option value="">Select area</option>
                        @foreach(($driverAreas ?? collect()) as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Only assigned areas are available.</small>
                </div>

                <div class="mb-0">
                    <label class="form-label">Address</label>
                    <textarea class="form-control" id="dueCustomerAddress" rows="2" placeholder="Enter address"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="createCustomerAndSubmitBtn">Create Customer & Submit</button>
            </div>
        </div>
    </div>
</div>

    <!-- Fixed Save Button -->
    <div class="px-3 sticky-submit">
        <button type="button" id="saleSubmitBtn" class="btn btn-primary btn-lg w-100 rounded-3 shadow-sm">
            {{ $isEdit ? 'Update Sale' : 'Save Sale' }}
        </button>

    </div>
</form>
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
                            class="list-group-item list-group-item-action py-3"
                            onclick='selectProduct(
                                {{ $product->product_id }},
                                @json($product->product->name),
                                {{ $product->product->sale_price }},
                                {{ $product->available_qty }},
                                @json($product->product->unit->sub_unit)
                            )'>
                            {{ $product->product->name }}
                            <span class="float-end text-muted fw-semibold">
                                {{ $product->available_qty }} | ৳ {{ $product->product->sale_price }}
                            </span>
                        </button>
                        @endforeach


                </div>

                <hr>

                <!-- Selected Product -->
                <div id="selectedProductArea" class="d-none">
                    <h6 id="selectedProductName" class="fw-bold"></h6>

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

                    <button type="button" class="btn btn-success w-100 mt-3 rounded-3"
                            onclick="addToCart()">
                        Add to Cart
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
let cart = [];
let selectedProduct = null;
let submitAfterCustomerCreate = false;

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
        <div class="card mb-2 cart-card">
            <div class="card-body p-3">

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
                    <strong class="cart-line-total">
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

document.querySelector('[name="discount"]').addEventListener('input', () => {
    let subtotal = cart.reduce((sum, item) => sum + item.line_total, 0);
    updateTotals(subtotal);
});

document.querySelector('[name="paid_amount"]').addEventListener('input', () => {
    let subtotal = cart.reduce((sum, item) => sum + item.line_total, 0);
    updateTotals(subtotal);
});


function updateTotals(subtotal) {
    const discount = parseFloat(document.querySelector('[name="discount"]').value || 0);
    const paid = parseFloat(document.querySelector('[name="paid_amount"]').value || 0);

    document.getElementById('subtotal').innerText = subtotal.toFixed(2);
    document.getElementById('grandTotal').innerText = (subtotal - discount).toFixed(2);
    document.getElementById('dueAmount').innerText =
        ((subtotal - discount) - paid).toFixed(2);
}
$('#saleSubmitBtn').on('click', function(e) {
    e.preventDefault();

    if(cart.length === 0) {
        alert('Cart is empty!');
        return;
    }

    const dueAmount = parseFloat(document.getElementById('dueAmount').innerText || 0);
    const selectedOption = $('[name="customer_id"] option:selected');
    const isCashCustomer = parseInt(selectedOption.data('is-cash') || 0) === 1;

    if (isCashCustomer && dueAmount > 0) {
        submitAfterCustomerCreate = true;
        const modal = new bootstrap.Modal(document.getElementById('dueCustomerModal'));
        modal.show();
        return;
    }

    submitSaleForm();
});

$('#createCustomerAndSubmitBtn').on('click', function () {
    const name = $('#dueCustomerName').val().trim();
    const phone = $('#dueCustomerPhone').val().trim();
    const email = $('#dueCustomerEmail').val().trim();
    const areaId = $('#dueCustomerAreaId').val();
    const address = $('#dueCustomerAddress').val().trim();

    if (!name) {
        alert('Customer name is required.');
        return;
    }

    if (!areaId) {
        alert('Please select area.');
        return;
    }

    $.ajax({
        url: '{{ route('sales.driver_customer.store') }}',
        method: 'POST',
        data: {
            name: name,
            phone: phone,
            email: email,
            area_id: areaId,
            address: address,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
            if (res.status_code === 200 && res.customer) {
                const optionHtml = `<option value="${res.customer.id}" data-is-cash="0" selected>${res.customer.name}</option>`;

                if ($('[name="customer_id"] option[value="' + res.customer.id + '"]').length) {
                    $('[name="customer_id"]').val(res.customer.id).trigger('change');
                } else {
                    $('[name="customer_id"]').append(optionHtml).val(res.customer.id).trigger('change');
                }

                bootstrap.Modal.getInstance(document.getElementById('dueCustomerModal')).hide();

                if (submitAfterCustomerCreate) {
                    submitAfterCustomerCreate = false;
                    submitSaleForm();
                }
            } else {
                alert(res.status_message || 'Unable to create customer.');
            }
        },
        error: function (err) {
            const message = err?.responseJSON?.status_message || 'Unable to create customer.';
            alert(message);
        }
    });
});

function submitSaleForm() {

    // Add cart items
    $('#cartItemsInput').val(JSON.stringify(cart));

    var form = $('#saleForm')[0];
    var formData = new FormData(form);

    if ($(form).find('input[name="_method"]').length) {
        formData.append('_method', $(form).find('input[name="_method"]').val());
    }


    // Dynamically pick URL and method
    var url = $(form).attr('action');
    var method = $(form).find('input[name="_method"]').val() || $(form).attr('method');

    $.ajax({
        url: url,
        method: 'POST', // use dynamic method
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
            if(res.status_code === 200){
                window.open(res.invoice_url, '_blank'); 
                form.reset();
                cart = [];
                renderCart();
                $(window).scrollTop(0);

                setTimeout(() => {
                    location.reload();
                }, 500); // reload after 0.5 seconds

            } else {
                alert(res.message || 'Something went wrong!');
            }
        },
        error: function(err){
            console.error(err);
            const message = err?.responseJSON?.status_message || 'Server error!';
            alert(message);
        }
    });
}


</script>

@endpush