@php
    $closingDate = $closingDate ?? $period['end_date'];
    $givenAmountTotal = (float) (($givenAmounts ?? collect())->sum('amount'));
    $savedCashGivenToOthers = (float) ($closingStatus->cash_given_to_others ?? $givenAmountTotal);
    $savedCashInHand = (float) ($closingStatus->cash_in_hand ?? $cashInHand);
    $savedDriverCashTake = (float) ($closingStatus->driver_cash_take ?? ($savedCashInHand - $savedCashGivenToOthers));
    $returnPaids = $returnpaids->sum('amount') * -1;
    $cashInHand = $sales->sum('paid') + $collections->sum('amount') - $expenses->sum('amount') - $returnPaids;
    $totalIncome = (float) $sales->sum('paid') + (float) $collections->sum('amount');
    $totalOutflows = (float) $expenses->sum('amount') + (float) $returnPaids;
    $totalOutflowsWithStaff = $totalOutflows + $givenAmountTotal;
    $givenCol = $givenAmounts ?? collect();
    $expenseSummaryByTitle = $expenses->groupBy(function ($e) {
        return $e->expense->title ?? '—';
    })->map(fn ($grp) => (float) $grp->sum('amount'))->sortKeys();
    $employeeSummaryByTitle = $givenCol->groupBy(function ($g) {
        $d = trim((string) ($g->employee->designation ?? ''));

        return $d !== '' ? $d : '—';
    })->map(fn ($grp) => (float) $grp->sum('amount'))->sortKeys();
@endphp

<form method="post" action="{{ route('driver_closing.store') }}" class="driver-closing-report" id="driverClosingForm">
    @csrf
    <input type="hidden" name="driver_id" value="{{ $driver->id }}">
    <input type="hidden" name="date" value="{{ $closingDate }}">

    {{-- Summary strip --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-body py-4 closing-summary-gradient text-white">
            <div class="row g-3 align-items-center">
                <div class="col-lg-5">
                    <div class="small text-white-50 text-uppercase fw-semibold mb-1">Driver</div>
                    <div class="fs-5 fw-bold">{{ $driver->name }}</div>
                </div>
                <div class="col-lg-4">
                    <div class="small text-white-50 text-uppercase fw-semibold mb-1">Open period</div>
                    <div class="fw-semibold">
                        {{ date('d M Y', strtotime($period['start_date'])) }}
                        <span class="text-white-50 mx-1">→</span>
                        {{ date('d M Y', strtotime($period['end_date'])) }}
                    </div>
                </div>
                <div class="col-lg-3 text-lg-end">
                    <div class="small text-white-50 text-uppercase fw-semibold mb-1">Closing as of</div>
                    <div class="fw-bold">{{ date('d F Y', strtotime($closingDate)) }}</div>
                    @if($closingStatus)
                        <span class="badge bg-light text-success mt-2">Closed</span>
                    @else
                        <span class="badge bg-warning text-dark mt-2">Pending</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center mb-3 no-print">
        @if(empty($closingStatus))
            <button type="submit" form="driverClosingForm" class="btn btn-primary px-4">
                <i class="fa fa-check me-1"></i> Submit closing
            </button>
        @endif
        <button type="button" class="btn btn-outline-secondary px-4" onclick="window.print()">
            <i class="fa fa-print me-1"></i> Print
        </button>
    </div>

    {{-- Income (left) | Expenses & outflows (right) --}}
    <div class="row g-4 mb-4 align-items-stretch">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-success"><i class="fa fa-plus-circle me-2"></i> Total income</h5>
                    <small class="text-muted">Cash from sales (paid) and customer due collection (type 1).</small>
                </div>
                <div class="card-body p-0">
                    <div class="px-3 pt-3 pb-2 small text-uppercase text-muted fw-semibold">Sales — paid at invoice</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th class="text-end">Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td class="font-monospace small">{{ $sale->invoice_no ?? '—' }}</td>
                                        <td>{{ $sale->customer->name ?? '—' }}</td>
                                        <td class="text-end font-monospace">{{ number_format($sale->paid, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No sales in period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 pt-3 pb-2 small text-uppercase text-muted fw-semibold">Due collection</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($collections as $collection)
                                    <tr>
                                        <td>{{ $collection->customer->name ?? '—' }}</td>
                                        <td class="text-end font-monospace">{{ number_format($collection->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-3">No collection in period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer closing-total-strip closing-total-strip--income border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold closing-total-strip__label">Total income</span>
                            <span class="fs-5 fw-bold font-monospace closing-total-strip__value">Tk {{ number_format($totalIncome, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 text-danger"><i class="fa fa-arrow-up me-2"></i> Expenses &amp; cash out</h5>
                    <small class="text-muted">Expense heads, cash to employees (by designation), and sales return refunds.</small>
                </div>
                <div class="card-body p-0">
                    <div class="px-3 pt-3 pb-2 small text-uppercase text-muted fw-semibold">Title-wise summary</div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenseSummaryByTitle as $title => $amt)
                                    <tr>
                                        <td class="small text-muted text-nowrap">Expense</td>
                                        <td>{{ $title }}</td>
                                        <td class="text-end font-monospace">{{ number_format($amt, 2) }}</td>
                                    </tr>
                                @endforeach
                                @foreach($employeeSummaryByTitle as $title => $amt)
                                    <tr>
                                        <td class="small text-muted text-nowrap">Employee</td>
                                        <td>{{ $title }}</td>
                                        <td class="text-end font-monospace">{{ number_format($amt, 2) }}</td>
                                    </tr>
                                @endforeach
                                @if($returnPaids > 0)
                                    <tr>
                                        <td class="small text-muted text-nowrap">Return</td>
                                        <td>Sales return — cash to customer</td>
                                        <td class="text-end font-monospace text-danger">{{ number_format($returnPaids, 2) }}</td>
                                    </tr>
                                @endif
                                @if($expenseSummaryByTitle->isEmpty() && $employeeSummaryByTitle->isEmpty() && $returnPaids <= 0)
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No expenses, staff payments, or return cash in period.</td>
                                    </tr>
                                @endif
                                @if($expenseSummaryByTitle->isNotEmpty() || $employeeSummaryByTitle->isNotEmpty() || $returnPaids > 0)
                                    <tr class="table-secondary fw-bold">
                                        <td colspan="2">Summary total</td>
                                        <td class="text-end font-monospace">{{ number_format((float) $expenseSummaryByTitle->sum() + (float) $employeeSummaryByTitle->sum() + $returnPaids, 2) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="px-3 pt-4 pb-2 small text-uppercase text-muted fw-semibold">Details</div>
                    <div class="px-3 pt-1 pb-2 small text-muted">Expense entries</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Head</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->expense->title ?? '—' }}</td>
                                        <td class="text-end font-monospace">{{ number_format($expense->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-3">No expenses in period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 pt-3 pb-2 small text-muted">Cash given to employees</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Title</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($givenCol->sortBy('date') as $given)
                                    <tr>
                                        <td class="text-nowrap small">{{ $given->date ? date('d M Y', strtotime($given->date)) : '—' }}</td>
                                        <td>{{ $given->employee->name ?? '—' }}</td>
                                        <td>{{ $given->employee->designation ?? '—' }}</td>
                                        <td class="text-end font-monospace">{{ number_format((float) $given->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-3">No employee payments in period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 pt-3 pb-2 small text-muted">Sales return — cash paid</div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th class="text-end">Cash out</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returnpaids as $return)
                                    <tr>
                                        <td>{{ $return->customer->name ?? '—' }}</td>
                                        <td class="text-end font-monospace text-danger">{{ number_format((float) $return->amount * -1, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-3">No return cash in period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer closing-total-strip closing-total-strip--out border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small closing-total-strip__meta">Operating out (expense + return)</span>
                            <span class="small fw-bold font-monospace closing-total-strip__meta">Tk {{ number_format($totalOutflows, 2) }}</span>
                        </div>
                        @if($givenAmountTotal > 0)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small closing-total-strip__meta">Cash to employees</span>
                                <span class="small fw-bold font-monospace closing-total-strip__meta">Tk {{ number_format($givenAmountTotal, 2) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center closing-total-strip__divider pt-2 mt-1">
                            <span class="fw-bold closing-total-strip__label">Total cash out (all)</span>
                            <span class="fs-5 fw-bold font-monospace closing-total-strip__value">Tk {{ number_format($totalOutflowsWithStaff, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="cash_sales" value="{{ $sales->sum('paid') }}">
    <input type="hidden" name="total_collection" value="{{ $collections->sum('amount') }}">
    <input type="hidden" name="total_return" value="{{ $returnpaids->sum('amount') }}">
    <input type="hidden" name="total_expense" value="{{ $expenses->sum('amount') }}">

    {{-- Cash in hand --}}
    <div class="card border-0 shadow-lg mb-4 overflow-hidden">
        <div class="card-body py-4 px-4 closing-cash-in-hand">
            <div class="row align-items-center g-3">
                <div class="col-md-7">
                    <div class="small text-white-50 text-uppercase fw-semibold mb-1">Cash in hand (period)</div>
                    <div class="text-white small mb-0">
                        Paid {{ number_format($sales->sum('paid'), 2) }}
                        + Collection {{ number_format($collections->sum('amount'), 2) }}
                        − Expense {{ number_format($expenses->sum('amount'), 2) }}
                        − Return cash {{ number_format($returnPaids, 2) }}
                        @if($givenAmountTotal > 0)
                            <br><span class="text-white-50">(Cash to employees {{ number_format($givenAmountTotal, 2) }} is settled in distribution below.)</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="display-6 fw-bold text-white font-monospace">Tk {{ number_format($cashInHand, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="cash_in_hand" value="{{ $cashInHand }}">

    {{-- Stock summary (issued, sold, return, available) --}}
    <div class="card border-0 shadow-sm mb-4 closing-section">
        <div class="card-header bg-white py-3 border-bottom">
            <span class="fw-semibold mb-0 d-block">Stock summary (open period)</span>
            <small class="text-muted">One row per product (all issues merged). Purchase price is weighted average by issued qty; multiple rates show below the average.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Avg. purchase</th>
                            <th class="text-end">Issued (given)</th>
                            <th class="text-end">Sold</th>
                            <th class="text-end">Returned</th>
                            <th class="text-end">Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $stockRows = $stockSummaryRows ?? collect();
                            $sumIssue = (float) $stockRows->sum('issue_qty');
                            $sumSold = (float) $stockRows->sum('sold_qty');
                            $sumRet = (float) $stockRows->sum('return_qty');
                            $sumAvail = (float) $stockRows->sum(fn ($r) => (float) $r['issue_qty'] - (float) $r['sold_qty'] + (float) $r['return_qty']);
                        @endphp
                        @foreach($stockRows as $row)
                            @php
                                $avail = (float) $row['issue_qty'] - (float) $row['sold_qty'] + (float) $row['return_qty'];
                                $p = $row['product'] ?? null;
                            @endphp
                            <tr>
                                <td>{{ $p->name ?? '—' }}</td>
                                <td class="text-end font-monospace">
                                    {{ number_format((float) $row['purchase_price'], 2) }}
                                    @if(!empty($row['purchase_price_note']))
                                        <div class="small text-muted">({{ $row['purchase_price_note'] }})</div>
                                    @endif
                                </td>
                                <td class="text-end font-monospace">{{ number_format((float) $row['issue_qty'], 2) }}</td>
                                <td class="text-end font-monospace">{{ number_format((float) $row['sold_qty'], 2) }}</td>
                                <td class="text-end font-monospace">{{ number_format((float) $row['return_qty'], 2) }}</td>
                                <td class="text-end font-monospace fw-semibold">{{ number_format((float) $avail, 2) }}</td>
                            </tr>
                        @endforeach
                        @if($stockRows->isNotEmpty())
                            <tr class="table-secondary fw-bold">
                                <td colspan="2">Total</td>
                                <td class="text-end font-monospace">{{ number_format($sumIssue, 2) }}</td>
                                <td class="text-end font-monospace">{{ number_format($sumSold, 2) }}</td>
                                <td class="text-end font-monospace">{{ number_format($sumRet, 2) }}</td>
                                <td class="text-end font-monospace">{{ number_format($sumAvail, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Return invoice & product detail --}}
    <div class="card border-0 shadow-sm mb-4 closing-section">
        <div class="card-header bg-white py-3 border-bottom">
            <span class="fw-semibold mb-0 d-block">Sales return — invoices &amp; products</span>
            <small class="text-muted">Line-level detail for returns in this period.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Return invoice</th>
                            <th>Customer</th>
                            <th>Products</th>
                            <th class="text-end">Amount</th>
                            <th>Adjustment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($salesReturns ?? collect()) as $returnLedger)
                            @php
                                $productText = $returnLedger->entries->map(function ($entry) {
                                    return ($entry->product->name ?? 'Product') . ' × ' . rtrim(rtrim(number_format((float) $entry->return_qty, 4, '.', ''), '0'), '.');
                                })->implode(', ');
                                $payment = $returnLedger->payments->first();
                                $adjustmentType = 'Due adjust';
                                if ($payment && (float) $payment->amount < 0) {
                                    $adjustmentType = 'Cash paid';
                                }
                            @endphp
                            <tr>
                                <td>{{ $returnLedger->invoice_no ?? '—' }}</td>
                                <td>{{ $returnLedger->customer->name ?? '—' }}</td>
                                <td class="small">{{ $productText ?: '—' }}</td>
                                <td class="text-end font-monospace">{{ number_format((float) $returnLedger->subtotal, 2) }}</td>
                                <td><span class="badge rounded-pill bg-light text-dark border">{{ $adjustmentType }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No sales return for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 3 Distribution --}}
    <div class="card border-0 shadow-sm mb-4 closing-section">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
            <span class="closing-section__num">3</span>
            <span class="fw-semibold mb-0">Sales cash distribution</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0 align-middle">
                <tbody>
                    <tr>
                        <th class="w-50 ps-3">Cash available from sales</th>
                        <td class="text-end pe-3 font-monospace fw-semibold">{{ number_format($savedCashInHand, 2) }}</td>
                    </tr>
                    <tr>
                        <th class="ps-3">Cash given to other employees</th>
                        <td class="text-end pe-3 font-monospace" id="cashGivenCell">{{ number_format($savedCashGivenToOthers, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <th class="ps-3">Driver own cash take</th>
                        <td class="text-end pe-3 font-monospace fw-bold text-success" id="driverCashTakeCell">{{ number_format($savedDriverCashTake, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mt-2 mb-5">
        <div class="col-md-6 text-center">
            <div class="border-top pt-3 d-inline-block px-5">
                <span class="small text-muted">Driver signature</span>
            </div>
        </div>
        <div class="col-md-6 text-center">
            <div class="border-top pt-3 d-inline-block px-5">
                <span class="small text-muted">Manager signature</span>
            </div>
        </div>
    </div>

    <div class="no-print d-flex flex-wrap gap-2 justify-content-end pb-4">
        @if(empty($closingStatus))
            <button type="submit" form="driverClosingForm" class="btn btn-primary px-4">
                <i class="fa fa-check me-1"></i> Submit closing
            </button>
        @endif
        <button type="button" class="btn btn-outline-secondary px-4" onclick="window.print()">
            <i class="fa fa-print me-1"></i> Print
        </button>
    </div>
</form>
