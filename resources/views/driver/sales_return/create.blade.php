@extends('driver.layouts.master')

@section('page_title', 'Sales return')

@push('styles')
    <style>
        .sr-page .sr-mode-btn.active { background: var(--drv-primary); color: #fff; border-color: var(--drv-primary); }
        .sr-page .sr-line-total { font-variant-numeric: tabular-nums; font-weight: 600; }
        .sr-page .sr-tile-muted { font-size: .78rem; color: var(--drv-on-surface-variant); }
        .sr-page #sr-settlement-due_first_wrap.is-hidden { display: none !important; }
    </style>
@endpush

@section('body')
@php
    $intentInvoice = $intentInvoice ?? false;
@endphp
<div class="sr-page">
    <div class="page-card p-3 mb-3">
        <div class="fw-semibold mb-2">Return type</div>
        <div class="btn-group w-100 mb-3" role="group">
            <a href="{{ route('sales_return.create') }}" class="btn btn-outline-primary sr-mode-btn {{ (!$intentInvoice && !$selectedLedger) ? 'active' : '' }}">Without invoice</a>
            <a href="{{ route('sales_return.create', ['intent' => 'invoice']) }}" class="btn btn-outline-primary sr-mode-btn {{ ($intentInvoice || $selectedLedger) ? 'active' : '' }}">With invoice</a>
        </div>

        @if($intentInvoice || $selectedLedger)
            <label class="form-label">Invoice</label>
            <form method="GET" action="{{ route('sales_return.create') }}">
                <input type="hidden" name="intent" value="invoice">
                <select name="invoice_id" class="form-select" id="sr-invoice-select" onchange="this.form.submit()">
                    <option value="">Select invoice…</option>
                    @foreach($sales_ledgers as $ledger)
                        <option value="{{ $ledger->id }}" {{ ($selectedLedger && $selectedLedger->id == $ledger->id) ? 'selected' : '' }}>
                            {{ $ledger->invoice_no }} — {{ $ledger->date }}
                        </option>
                    @endforeach
                </select>
            </form>
        @else
            <p class="small text-muted mb-0">Add products from your stock. Customer due and cash will be checked on save.</p>
        @endif

        <div class="driver-tile mt-3 mb-0">
            <div class="small text-muted">Carrying cash (open period)</div>
            <div class="fw-bold text-success">Tk {{ number_format($availableCash ?? 0, 2) }}</div>
            <div id="sr-due-preview" class="small mt-2 d-none">
                <span class="text-muted">Customer due:</span>
                <strong id="sr-due-amount">0.00</strong>
            </div>
        </div>
    </div>

    @if($selectedLedger)
        <form method="POST" action="{{ route('sales_return.store') }}" id="sr-return-form">
            @csrf
            <input type="hidden" name="return_mode" value="with_invoice">
            <input type="hidden" name="sales_ledger_id" value="{{ $selectedLedger->id }}">
            <input type="hidden" name="customer_id" value="{{ $selectedLedger->customer_id }}">

            @foreach($invoiceLineGroups as $entries)
                @php
                    $first = $entries->first();
                    $returnedQty = $entries->sum(fn ($e) => (float) ($e->return_entries_sum_return_qty ?? 0));
                    $soldQty = (float) $entries->sum('quantity');
                    $remainingQty = max(0.0, $soldQty - $returnedQty);
                    $formKey = $first->sale_line_uid ?: $first->id;
                    $netSum = $entries->sum(fn ($e) => (float) $e->quantity * (float) $e->sale_price - (float) $e->discount);
                    $effUnit = $soldQty > 0.00001 ? $netSum / $soldQty : (float) $first->sale_price;
                @endphp
                <div class="driver-tile sr-inv-line" data-net-unit="{{ number_format($effUnit, 6, '.', '') }}" data-max="{{ $remainingQty }}">
                    <div class="form-check mb-2">
                        <input class="form-check-input sr-line-check" type="checkbox" name="return_line[{{ $formKey }}]" value="1" id="chk-{{ $formKey }}">
                        <label class="form-check-label fw-medium" for="chk-{{ $formKey }}">{{ $first->product->name ?? '' }}</label>
                    </div>
                    <div class="row small text-muted mb-2">
                        <div class="col-4">Sold<br><strong class="text-dark">{{ $soldQty }}</strong></div>
                        <div class="col-4">Returned<br><strong class="text-dark">{{ $returnedQty }}</strong></div>
                        <div class="col-4">Max<br><strong class="text-dark">{{ $remainingQty }}</strong></div>
                    </div>
                    <div class="sr-tile-muted mb-1">Net per sold unit: Tk {{ number_format($effUnit, 2) }}</div>
                    <label class="form-label small">Return qty</label>
                    <input type="number" name="items[{{ $formKey }}]" class="form-control sr-line-qty" min="0" step="0.01" max="{{ $remainingQty }}" value="0" disabled>
                    <div class="text-end small mt-1">Line: <span class="sr-line-total">0.00</span></div>
                </div>
            @endforeach

            @include('driver.sales_return._return_totals')
        </form>
    @elseif(!$intentInvoice)
        <form method="POST" action="{{ route('sales_return.store') }}" id="sr-return-form">
            @csrf
            <input type="hidden" name="return_mode" value="without_invoice">
            <input type="hidden" name="customer_id" id="sr-form-customer-id" value="">

            <div class="page-card p-3 mb-3">
                <label class="form-label">Customer <span class="text-danger">*</span></label>
                <select class="form-select" id="sr-standalone-customer" required>
                    <option value="">Select customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="fw-semibold">Products</div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="sr-add-row">+ Row</button>
            </div>
            <div id="sr-standalone-rows"></div>

            @include('driver.sales_return._return_totals')
        </form>
    @else
        <div class="driver-empty page-card text-center small text-muted">Choose an invoice above to load lines.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    var stock = @json($driverStockJson ?? []);
    var availableCash = {{ json_encode((float) ($availableCash ?? 0)) }};

    function fmt(n) { return (Number(n) || 0).toFixed(2); }

    function fetchDue(customerId) {
        var box = document.getElementById('sr-due-preview');
        var amt = document.getElementById('sr-due-amount');
        if (!customerId) {
            box.classList.add('d-none');
            window.__srDue = 0;
            setSettlement();
            return;
        }
        fetch('/customer-due/' + customerId, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
            .then(function (r) { return r.json(); })
            .then(function (d) {
                window.__srDue = parseFloat(d.due) || 0;
                amt.textContent = fmt(window.__srDue);
                box.classList.remove('d-none');
                setSettlement();
            })
            .catch(function () {
                window.__srDue = 0;
                box.classList.add('d-none');
                setSettlement();
            });
    }

    function setSettlement() {
        var wrap = document.getElementById('sr-settlement-due_first_wrap');
        var dueFirst = document.getElementById('sr-settlement-due_first');
        var cashOnly = document.getElementById('sr-settlement-cash_only');
        if (!wrap || !dueFirst || !cashOnly) return;
        var due = window.__srDue || 0;
        if (due > 0.009) {
            wrap.classList.remove('is-hidden');
            dueFirst.disabled = false;
        } else {
            wrap.classList.add('is-hidden');
            dueFirst.disabled = true;
            cashOnly.checked = true;
        }
        recalc();
    }

    function recalc() {
        var lines = 0;
        document.querySelectorAll('.sr-inv-line').forEach(function (tile) {
            var cb = tile.querySelector('.sr-line-check');
            var q = tile.querySelector('.sr-line-qty');
            var net = parseFloat(tile.getAttribute('data-net-unit')) || 0;
            var max = parseFloat(tile.getAttribute('data-max')) || 0;
            if (!cb || !q) return;
            if (cb.checked) {
                q.disabled = false;
                var v = Math.min(Math.max(parseFloat(q.value) || 0, 0), max);
                q.value = v;
                lines += v * net;
            } else {
                q.disabled = true;
                q.value = 0;
            }
            var lt = tile.querySelector('.sr-line-total');
            if (lt) {
                var qv = cb.checked ? (parseFloat(q.value) || 0) : 0;
                lt.textContent = fmt(qv * net);
            }
        });

        document.querySelectorAll('.sr-standalone-row').forEach(function (row) {
            var q = parseFloat(row.querySelector('.sr-s-qty').value) || 0;
            var p = parseFloat(row.querySelector('.sr-s-price').value) || 0;
            lines += q * p;
            var lt = row.querySelector('.sr-s-line');
            if (lt) lt.textContent = fmt(q * p);
        });

        var discEl = document.getElementById('sr-header-discount');
        var disc = parseFloat(discEl && discEl.value) || 0;
        if (disc < 0) disc = 0;
        if (disc > lines) disc = lines;
        var grand = Math.max(0, lines - disc);

        var elSub = document.getElementById('sr-subtotal-display');
        var elGrand = document.getElementById('sr-grand-display');
        if (elSub) elSub.textContent = fmt(lines);
        if (elGrand) elGrand.textContent = fmt(grand);

        var due = window.__srDue || 0;
        var sm = document.querySelector('input[name="settlement_mode"]:checked');
        var mode = sm ? sm.value : 'cash_only';
        var dueCredit = 0, cashPart = grand;
        if (mode === 'due_first' && due > 0.009) {
            dueCredit = Math.min(due, grand);
            cashPart = Math.max(0, grand - dueCredit);
        } else {
            cashPart = grand;
        }
        var bp = document.getElementById('sr-breakdown-panel');
        if (bp) {
            var bDue = document.getElementById('sr-break-due');
            var bCash = document.getElementById('sr-break-cash');
            if (bDue) bDue.textContent = fmt(dueCredit);
            if (bCash) bCash.textContent = fmt(cashPart);
            bp.classList.toggle('d-none', grand < 0.001);
        }
    }

    var hd = document.getElementById('sr-header-discount');
    if (hd) hd.addEventListener('input', recalc);

    document.querySelectorAll('.sr-line-check').forEach(function (c) { c.addEventListener('change', recalc); });
    document.querySelectorAll('.sr-line-qty').forEach(function (q) { q.addEventListener('input', recalc); });
    document.querySelectorAll('input[name="settlement_mode"]').forEach(function (r) { r.addEventListener('change', recalc); });

    var stCust = document.getElementById('sr-standalone-customer');
    var hidCust = document.getElementById('sr-form-customer-id');
    if (stCust && hidCust) {
        stCust.addEventListener('change', function () {
            hidCust.value = stCust.value;
            fetchDue(stCust.value);
        });
    }

    function addStandaloneRow() {
        var wrap = document.getElementById('sr-standalone-rows');
        if (!wrap) return;
        var idx = wrap.querySelectorAll('.sr-standalone-row').length;
        if (!stock.length) return;
        var opts = stock.map(function (s) {
            return '<option value="' + s.product_id + '" data-price="' + s.sale_price + '" data-max="' + s.available_qty + '">'
                + s.name + ' (avail ' + fmt(s.available_qty) + ')</option>';
        }).join('');
        var html = '<div class="driver-tile sr-standalone-row mb-2"><div class="row g-2 align-items-end">'
            + '<div class="col-12"><label class="form-label small">Product</label>'
            + '<select class="form-select sr-s-product" name="standalone_lines[' + idx + '][product_id]">' + opts + '</select></div>'
            + '<div class="col-6"><label class="form-label small">Qty (pieces)</label>'
            + '<input type="number" class="form-control sr-s-qty" name="standalone_lines[' + idx + '][qty]" min="0" step="0.01" value="0"></div>'
            + '<div class="col-6"><label class="form-label small">Unit price</label>'
            + '<input type="number" class="form-control sr-s-price" name="standalone_lines[' + idx + '][unit_price]" min="0" step="0.01" value="0"></div>'
            + '<div class="col-12 text-end small">Line <span class="sr-s-line">0.00</span></div></div></div>';
        wrap.insertAdjacentHTML('beforeend', html);
        var row = wrap.lastElementChild;
        row.querySelector('.sr-s-product').addEventListener('change', function () {
            var o = row.querySelector('.sr-s-product option:checked');
            row.querySelector('.sr-s-price').value = parseFloat(o.getAttribute('data-price')) || 0;
            recalc();
        });
        row.querySelector('.sr-s-qty').addEventListener('input', recalc);
        row.querySelector('.sr-s-price').addEventListener('input', recalc);
        var fo = row.querySelector('.sr-s-product option:checked');
        if (fo) row.querySelector('.sr-s-price').value = parseFloat(fo.getAttribute('data-price')) || 0;
    }

    document.getElementById('sr-add-row')?.addEventListener('click', addStandaloneRow);
    if (document.getElementById('sr-standalone-rows') && stock.length) addStandaloneRow();

    @if(isset($selectedLedger))
    window.__srDue = 0;
    fetchDue('{{ $selectedLedger->customer_id }}');
    @endif

    document.getElementById('sr-return-form')?.addEventListener('submit', function (e) {
        recalc();
        var grand = parseFloat(document.getElementById('sr-grand-display')?.textContent) || 0;
        if (document.querySelector('input[name="return_mode"]')?.value === 'without_invoice') {
            if (!document.getElementById('sr-form-customer-id')?.value) {
                e.preventDefault();
                alert('Select customer.');
                return;
            }
        }
        var cashPart = parseFloat(document.getElementById('sr-break-cash')?.textContent) || 0;
        if (grand > 0.001 && cashPart > availableCash + 0.02) {
            e.preventDefault();
            alert('Cash portion exceeds available carrying cash (Tk ' + fmt(availableCash) + ').');
        }
    });

    recalc();
})();
</script>
@endpush
