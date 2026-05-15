<div class="page-card p-3 mb-3">
    <label class="form-label">Extra discount (Tk)</label>
    <input type="number" name="return_discount" id="sr-header-discount" class="form-control" min="0" step="0.01" value="0">
    <div class="d-flex justify-content-between mt-3 small">
        <span class="text-muted">Lines subtotal</span>
        <strong id="sr-subtotal-display">0.00</strong>
    </div>
    <div class="d-flex justify-content-between small">
        <span class="text-muted">Grand total</span>
        <strong class="text-danger" id="sr-grand-display">0.00</strong>
    </div>
    <div id="sr-breakdown-panel" class="mt-2 p-2 rounded-3 bg-light small d-none">
        <div class="d-flex justify-content-between"><span>Applied to due</span><span id="sr-break-due">0.00</span></div>
        <div class="d-flex justify-content-between"><span>Cash from you</span><span id="sr-break-cash">0.00</span></div>
    </div>
</div>

<div class="page-card p-3 mb-3">
    <label class="form-label">Settlement <span class="text-danger">*</span></label>
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="settlement_mode" id="sr-settlement-cash_only" value="cash_only" checked>
        <label class="form-check-label" for="sr-settlement-cash_only">Cash paid to customer (full amount)</label>
    </div>
    <div class="form-check mb-0" id="sr-settlement-due_first_wrap">
        <input class="form-check-input" type="radio" name="settlement_mode" id="sr-settlement-due_first" value="due_first" disabled>
        <label class="form-check-label" for="sr-settlement-due_first">Reduce due first, pay cash for remainder (if any)</label>
    </div>
    <p class="small text-muted mt-2 mb-0">If the customer has no outstanding due, only cash payment is available. Cash must fit your carrying cash.</p>
</div>

<button type="submit" class="btn btn-danger w-100">Save return</button>
