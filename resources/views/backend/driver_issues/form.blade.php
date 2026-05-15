@php
    $isEdit = isset($issue);
    $issuedByProduct = $isEdit
        ? $issue->items->groupBy('product_id')->map(fn ($g) => (float) $g->sum('issue_qty'))
        : collect();
    $productCatalog = $products->map(function ($p) use ($issuedByProduct) {
        $id = (int) $p->product_id;
        $av = (float) ($p->available_qty ?? 0);
        $issued = (float) ($issuedByProduct[$id] ?? 0);

        return [
            'id' => $id,
            'name' => $p->product->name ?? 'Product #'.$id,
            'stock' => max($av, $issued),
        ];
    })->values()->all();
    $initialCartRows = $isEdit
        ? $issue->items->map(fn ($i) => [
            'product_id' => (int) $i->product_id,
            'qty' => (float) $i->issue_qty,
        ])->values()->all()
        : collect(old('items', []))
            ->filter(fn ($r) => is_array($r) && ! empty($r['product_id']))
            ->groupBy('product_id')
            ->map(fn ($rows, $pid) => [
                'product_id' => (int) $pid,
                'qty' => (float) $rows->sum(fn ($r) => (float) ($r['issue_qty'] ?? 0)),
            ])
            ->values()
            ->filter(fn ($r) => $r['qty'] > 0)
            ->all();
@endphp

<form action="{{ $isEdit
        ? route('driver-issues.update', $issue->id)
        : route('driver-issues.store') }}"
      method="POST"
      id="driverIssueForm">

    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label class="form-label">DSR (Driver)</label>
                <span class="text-danger">*</span>

                <select name="driver_id"
                        id="issue_driver_id"
                        class="form-select @error('driver_id') is-invalid @enderror">
                    <option value="">Select DSR</option>
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

        <div class="col-md-4 col-lg-4 col-12">
            <div class="mb-3">
                <label class="form-label">Issue date</label>
                <span class="text-danger">*</span>
                <input type="date"
                       name="issue_date"
                       id="issue_date"
                       value="{{ old('issue_date', $issue->issue_date ?? now()->format('Y-m-d')) }}"
                       class="form-control"
                       {{ $isEdit ? 'readonly' : '' }}>
                <div class="form-text">
                    <strong>One issue per DSR per calendar day:</strong> first save creates the day’s issue. To change quantities before the DSR accepts, use <strong>Edit</strong>. Warehouse stock is deducted only after the DSR taps <strong>Accept</strong>.
                </div>
            </div>
        </div>
    </div>

    <div id="pickSection" class="border rounded p-3 mb-3 bg-light">
        <div class="fw-semibold mb-2">Add product to cart</div>
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small mb-0">Product</label>
                <select id="pickProduct" class="form-select">
                    <option value="">Select product</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-0">Quantity</label>
                <input type="number" id="pickQty" class="form-control" min="1" step="any" value="1">
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-secondary w-100" id="btnAddToCart">
                    <i class="fa fa-cart-plus"></i> Add to cart
                </button>
            </div>
        </div>
        <div class="form-text mt-2 mb-0">
            Only products with available warehouse stock are listed. Adding the same product again increases its quantity in the cart.
        </div>
    </div>

    <hr>

    <div class="fw-semibold mb-2">Cart</div>
    <div class="table-responsive">
        <table class="table table-bordered" id="cartTable">
            <thead class="table-light">
                <tr>
                    <th width="40%">Product</th>
                    <th width="20%">Max (warehouse)</th>
                    <th width="25%">Issue qty</th>
                    <th width="15%" class="text-center">Remove</th>
                </tr>
            </thead>
            <tbody id="cartTableBody">
            </tbody>
        </table>
    </div>
    <p class="text-muted small" id="cartEmptyMsg" style="display:none;">Cart is empty. Add products from above.</p>

    <div id="hiddenItemsMount" class="d-none" aria-hidden="true"></div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary" id="btnSubmitIssue">
            {{ $isEdit ? 'Update issue' : 'Issue stock' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
(function () {
    const PRODUCTS = @json($productCatalog);
    const INITIAL_CART = @json($initialCartRows);

    let cart = [];

    function productMeta(id) {
        return PRODUCTS.find(p => Number(p.id) === Number(id));
    }

    function refreshProductDropdown() {
        const sel = document.getElementById('pickProduct');
        if (!sel) return;
        const cartIds = new Set(cart.map(c => Number(c.product_id)));
        const frag = document.createDocumentFragment();
        const opt0 = document.createElement('option');
        opt0.value = '';
        opt0.textContent = 'Select product';
        frag.appendChild(opt0);

        PRODUCTS.forEach(p => {
            const inCart = cartIds.has(Number(p.id));
            if (Number(p.stock) <= 0 && !inCart) return;
            const o = document.createElement('option');
            o.value = p.id;
            o.textContent = p.name + ' (max ' + p.stock + ')';
            o.dataset.stock = p.stock;
            frag.appendChild(o);
        });
        sel.innerHTML = '';
        sel.appendChild(frag);
    }

    function maxQtyForLine(productId, currentQty) {
        const m = productMeta(productId);
        return m ? Number(m.stock) : currentQty;
    }

    function renderCart() {
        const tbody = document.getElementById('cartTableBody');
        const emptyMsg = document.getElementById('cartEmptyMsg');
        tbody.innerHTML = '';

        if (!cart.length) {
            emptyMsg.style.display = 'block';
            refreshProductDropdown();
            return;
        }
        emptyMsg.style.display = 'none';

        cart.forEach((line, idx) => {
            const meta = productMeta(line.product_id);
            const maxQ = maxQtyForLine(line.product_id, line.qty);
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${meta ? meta.name : line.product_id}</td>
                <td><span class="text-muted">${maxQ}</span></td>
                <td>
                    <input type="number" class="form-control cart-qty" data-idx="${idx}"
                           min="1" step="any" value="${line.qty}">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm cart-remove" data-idx="${idx}">
                        <i class="fa fa-times"></i>
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });

        refreshProductDropdown();
    }

    function addToCart(productId, addQty) {
        productId = Number(productId);
        addQty = parseFloat(addQty);
        if (!productId || !(addQty > 0)) {
            alert('Please enter a valid product and quantity.');
            return;
        }
        const meta = productMeta(productId);
        if (!meta) {
            alert('Product not found.');
            return;
        }
        const max = Number(meta.stock);
        let line = cart.find(c => Number(c.product_id) === productId);
        let next = (line ? parseFloat(line.qty) : 0) + addQty;
        if (next > max) {
            next = max;
            if (!(next > 0)) {
                alert('Insufficient warehouse stock.');
                return;
            }
            alert('Maximum available quantity: ' + max);
        }
        if (line) {
            line.qty = next;
        } else {
            cart.push({ product_id: productId, qty: next });
        }
        renderCart();
    }

    function syncHiddenInputs() {
        const mount = document.getElementById('hiddenItemsMount');
        mount.innerHTML = '';
        cart.forEach((line, i) => {
            const inpP = document.createElement('input');
            inpP.type = 'hidden';
            inpP.name = 'items[' + i + '][product_id]';
            inpP.value = line.product_id;
            mount.appendChild(inpP);
            const inpQ = document.createElement('input');
            inpQ.type = 'hidden';
            inpQ.name = 'items[' + i + '][issue_qty]';
            inpQ.value = line.qty;
            mount.appendChild(inpQ);
        });
    }

    document.getElementById('btnAddToCart')?.addEventListener('click', function () {
        const sel = document.getElementById('pickProduct');
        const qtyIn = document.getElementById('pickQty');
        const pid = sel?.value;
        const q = qtyIn?.value;
        if (!pid) {
            alert('Please select a product.');
            return;
        }
        addToCart(pid, q);
        if (qtyIn) qtyIn.value = '1';
    });

    document.getElementById('cartTableBody')?.addEventListener('click', function (e) {
        const btn = e.target.closest('.cart-remove');
        if (!btn) return;
        const idx = parseInt(btn.getAttribute('data-idx'), 10);
        cart.splice(idx, 1);
        renderCart();
    });

    document.getElementById('cartTableBody')?.addEventListener('input', function (e) {
        const inp = e.target.closest('.cart-qty');
        if (!inp) return;
        const idx = parseInt(inp.getAttribute('data-idx'), 10);
        let v = parseFloat(inp.value);
        if (!(v > 0)) return;
        const line = cart[idx];
        if (!line) return;
        const maxQ = maxQtyForLine(line.product_id, v);
        if (v > maxQ) {
            v = maxQ;
            inp.value = v;
            alert('Maximum allowed: ' + maxQ);
        }
        line.qty = v;
        refreshProductDropdown();
    });

    document.getElementById('driverIssueForm')?.addEventListener('submit', function (e) {
        if (!cart.length) {
            e.preventDefault();
            alert('Add at least one product to the cart.');
            return;
        }
        syncHiddenInputs();
    });

    (function init() {
        const dateInput = document.getElementById('issue_date');
        if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().slice(0, 10);
        }
        INITIAL_CART.forEach(r => {
            cart.push({ product_id: Number(r.product_id), qty: parseFloat(r.qty) });
        });
        renderCart();
    })();
})();
</script>
@endpush
