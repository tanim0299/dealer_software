@extends('driver.layouts.master')

@section('page_title', 'Home')

@section('body')
    @php
        use App\Services\DriverPeriodService;
        use Illuminate\Support\Facades\DB;

        $driverId = auth()->user()?->driver_id;
        $period = $driverId ? DriverPeriodService::periodForDriver((int) $driverId) : null;

        if ($driverId && $period) {
            $periodSalesAmount = (float) DB::table('sales_ledgers')
                ->where('driver_id', $driverId)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $period['end_date'])
                ->sum('subtotal');

            $periodPaid = (float) DB::table('sales_ledgers')
                ->where('driver_id', $driverId)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $period['end_date'])
                ->sum('paid');

            $periodDiscount = (float) DB::table('sales_ledgers')
                ->where('driver_id', $driverId)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $period['end_date'])
                ->sum('discount');

            $periodDuesAmount = $periodSalesAmount - $periodDiscount - $periodPaid;

            $periodExpensesAmount = (float) DB::table('expense_entries')
                ->where('driver_id', $driverId)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $period['end_date'])
                ->sum('amount');

            $currentStockQty = (new \App\Models\Drivers())->getOpenPeriodDriverStock($driverId)
                ->sum(fn ($row) => max(0, (float) $row->issue_qty - (float) $row->sold_qty + (float) $row->return_qty));

            $periodDueCollection = (float) DB::table('sales_payments')
                ->where('type', 1)
                ->where('create_by', auth()->id())
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $period['end_date'])
                ->sum('amount');

            $periodGivenAmount = (float) DB::table('driver_cash_distributions')
                ->where('driver_id', $driverId)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $period['end_date'])
                ->sum('amount');

            $totalCollectedCash = $periodPaid + $periodDueCollection;

            if (DriverPeriodService::hasClosingOnDate((int) $driverId)) {
                $currentCarryingCash = 0;
            } else {
                $currentCarryingCash = \App\Services\DriverCashService::openPeriodAvailableCash(
                    (int) auth()->id(),
                    (int) $driverId
                );
            }
        } else {
            $periodSalesAmount = $periodPaid = $periodDiscount = $periodDuesAmount = 0;
            $periodExpensesAmount = $currentStockQty = $periodDueCollection = $periodGivenAmount = 0;
            $currentCarryingCash = 0;
        }
    @endphp

    @if(! $driverId)
        <div class="alert alert-warning rounded-4 mb-3">
            This user is not linked to a DSR profile. Ask admin to set <strong>driver</strong> on your user account.
        </div>
    @elseif($period)
        <div class="driver-issue-banner mb-3" style="cursor: default;">
            <div class="driver-issue-banner__title">
                <i class="bi bi-calendar-range me-1"></i> Open period
            </div>
            <div class="driver-issue-banner__hint">
                {{ \Carbon\Carbon::parse($period['start_date'])->format('d M Y') }}
                – {{ \Carbon\Carbon::parse($period['end_date'])->format('d M Y') }}
                (since last closing + 1 day)
            </div>
        </div>
    @endif

    @if(!empty($driverPanelDayClosed))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-3 small" role="status">
            <div class="fw-bold mb-1"><i class="bi bi-lock-fill me-1"></i> Closing completed for today</div>
            <p class="mb-0 text-dark">For <strong>{{ now()->format('d M Y') }}</strong>, new sales, expenses, returns, due collections, and cash given to staff are disabled. Quick action buttons below are turned off; use list pages to review past activity.</p>
        </div>
    @endif

    <div class="row g-2">
        <div class="col-6">
            <a href="{{ route('sales.index') }}" class="d-block text-decoration-none text-reset">
                <div class="driver-metric-card">
                    <div class="driver-metric-label">Sales (open period)</div>
                    <div class="driver-metric-value">Tk {{ number_format($periodSalesAmount ?? 0, 2) }}</div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <div class="driver-metric-card">
                <div class="driver-metric-label">Dues (open period)</div>
                <div class="driver-metric-value text-warning">Tk {{ number_format($periodDuesAmount ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-6">
            <a href="{{ route('expense_entry.index') }}" class="d-block text-decoration-none text-reset">
                <div class="driver-metric-card">
                    <div class="driver-metric-label">Expenses (open period)</div>
                    <div class="driver-metric-value text-danger">Tk {{ number_format($periodExpensesAmount ?? 0, 2) }}</div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('driver_stock.index') }}" class="d-block text-decoration-none text-reset">
                <div class="driver-metric-card">
                    <div class="driver-metric-label">Stock qty</div>
                    <div class="driver-metric-value">{{ max(0, (int) $currentStockQty) }}</div>
                </div>
            </a>
        </div>
        <div class="col-12">
            <div class="driver-metric-card py-3" style="border-left: 4px solid var(--drv-success);">
                <div class="driver-metric-label">Available cash (open period)</div>
                <div class="driver-metric-value text-success">Tk {{ number_format($currentCarryingCash, 2) }}</div>
                <div class="small text-muted mt-1">After expenses, cash given out &amp; cash refunds</div>
            </div>
        </div>
    </div>

    <div class="driver-section-title">Quick actions</div>

    <div class="d-grid gap-2 mb-2">
        @if(empty($driverPanelDayClosed))
            <a href="{{ route('sales.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="bi bi-plus-circle me-2"></i>New sale
            </a>
        @else
            <button type="button" class="btn btn-secondary btn-lg shadow-sm" disabled title="Closing completed for today">
                <i class="bi bi-lock me-2"></i>New sale — day closed
            </button>
        @endif
    </div>

    <div class="row g-2">
        <div class="col-6">
            @if(empty($driverPanelDayClosed))
                <a class="btn btn-outline-primary w-100 py-3 rounded-4" href="{{ route('customer_payment.create') }}">
                    <i class="bi bi-wallet2 d-block mb-1 fs-5"></i>
                    <span class="small fw-medium">Collect due</span>
                </a>
            @else
                <button type="button" class="btn btn-outline-secondary w-100 py-3 rounded-4" disabled title="Closing completed for today">
                    <i class="bi bi-lock d-block mb-1 fs-5"></i>
                    <span class="small fw-medium">Collect due</span>
                </button>
            @endif
        </div>
        <div class="col-6">
            <a class="btn btn-outline-primary w-100 py-3 rounded-4" href="{{ route('customer_payment.index') }}">
                <i class="bi bi-list-ul d-block mb-1 fs-5"></i>
                <span class="small fw-medium">Collections</span>
            </a>
        </div>
        <div class="col-6">
            @if(empty($driverPanelDayClosed))
                <a class="btn btn-outline-secondary w-100 py-3 rounded-4" href="{{ route('driver_cash_distribution.create') }}">
                    <i class="bi bi-send d-block mb-1 fs-5"></i>
                    <span class="small fw-medium">Give cash</span>
                </a>
            @else
                <button type="button" class="btn btn-outline-secondary w-100 py-3 rounded-4" disabled title="Closing completed for today">
                    <i class="bi bi-lock d-block mb-1 fs-5"></i>
                    <span class="small fw-medium">Give cash</span>
                </button>
            @endif
        </div>
        <div class="col-6">
            <a class="btn btn-outline-secondary w-100 py-3 rounded-4" href="{{ route('driver_cash_distribution.index') }}">
                <i class="bi bi-journal-text d-block mb-1 fs-5"></i>
                <span class="small fw-medium">Given list</span>
            </a>
        </div>
        <div class="col-6">
            @if(empty($driverPanelDayClosed))
                <a class="btn btn-outline-secondary w-100 py-3 rounded-4" href="{{ route('expense_entry.create') }}">
                    <i class="bi bi-receipt d-block mb-1 fs-5"></i>
                    <span class="small fw-medium">Add expense</span>
                </a>
            @else
                <button type="button" class="btn btn-outline-secondary w-100 py-3 rounded-4" disabled title="Closing completed for today">
                    <i class="bi bi-lock d-block mb-1 fs-5"></i>
                    <span class="small fw-medium">Add expense</span>
                </button>
            @endif
        </div>
        <div class="col-6">
            <a class="btn btn-outline-secondary w-100 py-3 rounded-4" href="{{ route('driver_stock.index') }}">
                <i class="bi bi-box-seam d-block mb-1 fs-5"></i>
                <span class="small fw-medium">Stock</span>
            </a>
        </div>
        <div class="col-6">
            @if(empty($driverPanelDayClosed))
                <a class="btn btn-outline-danger w-100 py-3 rounded-4" href="{{ route('sales_return.create') }}">
                    <i class="bi bi-arrow-return-left d-block mb-1 fs-5"></i>
                    <span class="small fw-medium">Return</span>
                </a>
            @else
                <button type="button" class="btn btn-outline-danger w-100 py-3 rounded-4" disabled title="Closing completed for today">
                    <i class="bi bi-lock d-block mb-1 fs-5"></i>
                    <span class="small fw-medium">Return</span>
                </button>
            @endif
        </div>
        <div class="col-6">
            <a class="btn btn-outline-danger w-100 py-3 rounded-4" href="{{ route('sales_return.index') }}">
                <i class="bi bi-card-list d-block mb-1 fs-5"></i>
                <span class="small fw-medium">Returns list</span>
            </a>
        </div>
        <div class="col-12">
            <a class="btn btn-outline-dark w-100 py-3 rounded-4" href="{{ route('driver-issues.index') }}">
                <i class="bi bi-inboxes d-block mb-1 fs-5"></i>
                <span class="small fw-medium">Issue list</span>
            </a>
        </div>
    </div>
@endsection
