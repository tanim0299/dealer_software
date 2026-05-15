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
                    <form action="{{ route('purchase_return.store') }}" method="POST" id="purchaseReturnForm">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="supplier_id" class="form-label">Select Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" id="supplier_id" class="form-select" required>
                                    <option value="">-- Choose Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div id="supplierDueBox" class="w-100 p-3 rounded border bg-light" style="display:none;">
                                    <div class="small text-muted text-uppercase fw-bold">Current supplier due</div>
                                    <div class="fs-4 fw-bold text-primary" id="supplierDueAmount">Tk 0.00</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="return_type" class="form-label">Return type <span class="text-danger">*</span></label>
                            <select name="return_type" id="return_type" class="form-select" required>
                                <option value="1">Cash settlement (refund)</option>
                                <option value="2" disabled>Minus from due (up to due balance; rest is cash paid)</option>
                            </select>
                            <small class="text-muted d-block">If the supplier has <strong>no due</strong>, only cash settlement is available. For <strong>minus from due</strong>, enter how much cash you pay; the rest reduces due (due cannot absorb more than the current balance).</small>
                            <small class="text-muted d-block mt-1">Line totals below use your ref. price; saved amounts use <strong>FIFO</strong> warehouse layers (discounts follow FIFO value).</small>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Add products (warehouse stock only)</h5>
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Product</label>
                                <select id="lineProduct" class="form-select">
                                    <option value="">-- Select supplier first --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Unit price (ref.)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="linePrice" value="">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" step="0.0001" min="0" class="form-control" id="lineQty" value="0">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label d-block">&nbsp;</label>
                                <button type="button" class="btn btn-success w-100" id="btnAddCart">Add</button>
                            </div>
                        </div>
                        <div class="small text-muted mb-3" id="lineStockHint"></div>

                        <h5 class="mb-2">Cart</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle" id="cartTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Ref. price</th>
                                        <th class="text-end">Line discount</th>
                                        <th class="text-end">Line net (est.)</th>
                                        <th style="width:70px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody">
                                    <tr id="cartEmptyRow">
                                        <td colspan="6" class="text-center text-muted py-4">No items in cart</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Subtotal (after line discounts, est.)</th>
                                        <th class="text-end" id="cartSubtotal">0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end align-middle">Order discount</th>
                                        <th class="text-end">
                                            <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end" name="order_discount" id="order_discount" value="0">
                                        </th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Grand total (est., FIFO at save)</th>
                                        <th class="text-end fw-bold" id="cartGrandTotal">0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div id="cashPaidRow" class="row g-2 mb-3" style="display:none;">
                            <div class="col-md-6">
                                <label for="cash_paid" class="form-label">Cash paid (supplier)</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="cash_paid" id="cash_paid" value="" placeholder="Leave blank for minimum required cash">
                                <small class="text-muted" id="cashPaidHint"></small>
                            </div>
                        </div>

                        <input type="hidden" name="cart_items" id="cart_items" value="[]">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>Process return</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const dueUrlBase = @json(url('supplier-payment/due'));
    const stockUrl = @json(route('purchase_return.stock_products'));

    let supplierDue = 0;
    let productCatalog = [];
    let cart = [];

    function money(n) {
        const x = parseFloat(n) || 0;
        return x.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function lineGross(row) {
        return (parseFloat(row.return_qty) || 0) * (parseFloat(row.unit_price) || 0);
    }

    function orderDiscountVal() {
        return Math.max(0, parseFloat(document.getElementById('order_discount').value) || 0);
    }

    function cartSubtotalAfterLines() {
        let s = 0;
        cart.forEach((row) => {
            const g = lineGross(row);
            const d = Math.max(0, parseFloat(row.line_discount) || 0);
            s += Math.max(0, g - d);
        });
        return s;
    }

    function cartGrandEstimate() {
        return Math.max(0, cartSubtotalAfterLines() - orderDiscountVal());
    }

    function minCashPaidEstimate() {
        const g = cartGrandEstimate();
        return Math.max(0, g - supplierDue);
    }

    function updateCashPaidHint() {
        const hint = document.getElementById('cashPaidHint');
        const rt = document.getElementById('return_type').value;
        if (rt !== '2') {
            hint.textContent = '';
            return;
        }
        const minC = minCashPaidEstimate();
        const maxC = cartGrandEstimate();
        hint.textContent = 'Required cash paid is at least Tk ' + money(minC) + ' and at most Tk ' + money(maxC) + ' (FIFO totals may differ slightly). Leave blank to use the minimum.';
    }

    function toggleCashPaidRow() {
        const rt = document.getElementById('return_type').value;
        const row = document.getElementById('cashPaidRow');
        row.style.display = rt === '2' ? 'block' : 'none';
        updateCashPaidHint();
    }

    function fetchDue(supplierId) {
        const box = document.getElementById('supplierDueBox');
        const amt = document.getElementById('supplierDueAmount');
        const optMinus = document.querySelector('#return_type option[value="2"]');
        const rt = document.getElementById('return_type');

        if (!supplierId) {
            box.style.display = 'none';
            supplierDue = 0;
            optMinus.disabled = true;
            if (rt.value === '2') rt.value = '1';
            toggleCashPaidRow();
            return;
        }

        fetch(dueUrlBase + '/' + supplierId, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                supplierDue = parseFloat(data.due) || 0;
                amt.textContent = 'Tk ' + money(supplierDue);
                box.style.display = 'block';
                optMinus.disabled = supplierDue <= 0.0001;
                if (optMinus.disabled && rt.value === '2') {
                    rt.value = '1';
                }
                toggleCashPaidRow();
                updateCashPaidHint();
            })
            .catch(() => {
                supplierDue = 0;
                amt.textContent = 'Tk 0.00';
                box.style.display = 'block';
                optMinus.disabled = true;
                toggleCashPaidRow();
            });
    }

    function loadProducts() {
        const sel = document.getElementById('lineProduct');
        const sid = document.getElementById('supplier_id').value;
        if (!sid) {
            sel.innerHTML = '<option value="">-- Select supplier first --</option>';
            productCatalog = [];
            return;
        }
        fetch(stockUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(res => {
                productCatalog = res.data || [];
                const current = sel.value;
                sel.innerHTML = '<option value="">-- Select product --</option>';
                productCatalog.forEach(p => {
                    const o = document.createElement('option');
                    o.value = p.product_id;
                    o.textContent = p.name + ' (avail: ' + money(p.available_qty) + ')';
                    o.dataset.available = p.available_qty;
                    o.dataset.avg = p.avg_purchase_price;
                    o.dataset.name = p.name;
                    sel.appendChild(o);
                });
                if (current) sel.value = current;
            });
    }

    function selectedLineProduct() {
        const sel = document.getElementById('lineProduct');
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return null;
        return {
            product_id: parseInt(opt.value, 10),
            name: opt.dataset.name || opt.textContent,
            available_qty: parseFloat(opt.dataset.available) || 0,
            avg_purchase_price: parseFloat(opt.dataset.avg) || 0,
        };
    }

    function cartQtyForProduct(pid) {
        let sum = 0;
        cart.forEach(c => { if (c.product_id === pid) sum += c.return_qty; });
        return sum;
    }

    function remainingForProduct(p) {
        return Math.max(0, p.available_qty - cartQtyForProduct(p.product_id));
    }

    function capLineDiscount(row) {
        const g = lineGross(row);
        let d = Math.max(0, parseFloat(row.line_discount) || 0);
        if (d > g) d = g;
        row.line_discount = d;
    }

    function renderCart() {
        const tbody = document.getElementById('cartBody');

        if (cart.length === 0) {
            tbody.innerHTML = '<tr id="cartEmptyRow"><td colspan="6" class="text-center text-muted py-4">No items in cart</td></tr>';
            document.getElementById('cartSubtotal').textContent = '0.00';
            document.getElementById('cartGrandTotal').textContent = '0.00';
            document.getElementById('cart_items').value = '[]';
            document.getElementById('btnSubmit').disabled = true;
            updateCashPaidHint();
            return;
        }

        tbody.innerHTML = '';
        let sub = 0;

        cart.forEach((row) => {
            capLineDiscount(row);
            const g = lineGross(row);
            const d = parseFloat(row.line_discount) || 0;
            const net = Math.max(0, g - d);
            sub += net;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${escapeHtml(row.name)}</td>
                <td class="text-end">${money(row.return_qty)}</td>
                <td class="text-end">${money(row.unit_price)}</td>
                <td class="text-end">
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm text-end line-disc-input" data-pid="${row.product_id}" value="${d ? d : ''}">
                </td>
                <td class="text-end">${money(net)}</td>
                <td><button type="button" class="btn btn-sm btn-outline-danger" data-pid="${row.product_id}">Remove</button></td>`;
            tbody.appendChild(tr);
        });

        const od = orderDiscountVal();
        const grand = Math.max(0, sub - od);

        document.getElementById('cartSubtotal').textContent = money(sub);
        document.getElementById('cartGrandTotal').textContent = money(grand);
        document.getElementById('cart_items').value = JSON.stringify(cart.map(c => ({
            product_id: c.product_id,
            return_qty: c.return_qty,
            line_discount: parseFloat(c.line_discount) || 0,
        })));
        document.getElementById('btnSubmit').disabled = cart.length === 0;
        updateCashPaidHint();
    }

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    document.getElementById('supplier_id').addEventListener('change', function () {
        cart = [];
        renderCart();
        fetchDue(this.value);
        loadProducts();
        document.getElementById('linePrice').value = '';
        document.getElementById('lineQty').value = '0';
        document.getElementById('lineStockHint').textContent = '';
        document.getElementById('cash_paid').value = '';
    });

    document.getElementById('lineProduct').addEventListener('change', function () {
        const p = selectedLineProduct();
        const hint = document.getElementById('lineStockHint');
        if (!p) {
            document.getElementById('linePrice').value = '';
            hint.textContent = '';
            return;
        }
        document.getElementById('linePrice').value = p.avg_purchase_price > 0 ? p.avg_purchase_price.toFixed(4) : '';
        const rem = remainingForProduct(p);
        hint.textContent = 'Max return qty for this product (remaining in warehouse vs cart): ' + money(rem);
    });

    document.getElementById('btnAddCart').addEventListener('click', function () {
        const p = selectedLineProduct();
        if (!p) {
            alert('Select a product');
            return;
        }
        let qty = parseFloat(document.getElementById('lineQty').value) || 0;
        if (qty <= 0) {
            alert('Enter quantity');
            return;
        }
        const rem = remainingForProduct(p);
        if (qty > rem + 1e-6) {
            alert('Quantity cannot exceed available warehouse stock (minus what is already in cart).');
            qty = rem;
            document.getElementById('lineQty').value = rem > 0 ? rem : 0;
            if (qty <= 0) return;
        }

        const unit = parseFloat(document.getElementById('linePrice').value) || p.avg_purchase_price || 0;
        const existing = cart.findIndex(c => c.product_id === p.product_id);
        if (existing >= 0) {
            cart[existing].return_qty += qty;
            cart[existing].unit_price = unit;
        } else {
            cart.push({
                product_id: p.product_id,
                name: p.name,
                return_qty: qty,
                unit_price: unit,
                available_qty: p.available_qty,
                line_discount: 0,
            });
        }
        document.getElementById('lineQty').value = '0';
        renderCart();
    });

    document.getElementById('cartBody').addEventListener('click', function (e) {
        const btn = e.target.closest('button[data-pid]');
        if (!btn) return;
        const pid = parseInt(btn.getAttribute('data-pid'), 10);
        cart = cart.filter(c => c.product_id !== pid);
        renderCart();
    });

    document.getElementById('cartBody').addEventListener('change', function (e) {
        const inp = e.target.closest('.line-disc-input');
        if (!inp) return;
        const pid = parseInt(inp.getAttribute('data-pid'), 10);
        const row = cart.find(c => c.product_id === pid);
        if (!row) return;
        row.line_discount = Math.max(0, parseFloat(inp.value) || 0);
        capLineDiscount(row);
        renderCart();
    });

    document.getElementById('order_discount').addEventListener('input', function () {
        renderCart();
    });

    document.getElementById('return_type').addEventListener('change', toggleCashPaidRow);

    document.getElementById('purchaseReturnForm').addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Cart is empty');
            return;
        }
        const rt = document.getElementById('return_type').value;
        if (rt === '2') {
            const g = cartGrandEstimate();
            const minC = Math.max(0, g - supplierDue);
            const paidRaw = document.getElementById('cash_paid').value;
            if (paidRaw !== '' && paidRaw != null) {
                const paid = parseFloat(paidRaw);
                if (paid < minC - 1e-6) {
                    e.preventDefault();
                    alert('Cash paid must be at least Tk ' + money(minC) + ' for this return estimate.');
                    return;
                }
                if (paid > g + 1e-6) {
                    e.preventDefault();
                    alert('Cash paid cannot exceed estimated grand total Tk ' + money(g) + '.');
                }
            }
        }
    });

    renderCart();
    toggleCashPaidRow();
})();
</script>
@endpush
@endsection
