<?php

namespace App\Http\Controllers;

use App\Models\DriverClosing;
use App\Models\DriverCashDistribution;
use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\Drivers;
use App\Models\Employee;
use App\Models\EmployeeSalaryWithdraw;
use App\Models\ExpenseEntry;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Models\SalesReturnLedger;
use App\Models\User;
use App\Models\WareHouseStocks;
use App\Services\DriverCashService;
use App\Services\DriverPeriodService;
use App\Services\DriverService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverClosingController extends Controller
{
    protected $path = 'backend.driver_closing';

    public function __construct()
    {
        $this->middleware(['permission:Driver Daily Report view|Driver Closing view'])->only(['index', 'driverClosing']);
        $this->middleware(['permission:Driver Daily Report create|Driver Closing create'])->only(['store']);
    }

    /**
     * Driver closing workbench: filter + inline report on the same page.
     */
    public function index(Request $request)
    {
        return $this->renderClosingWorkbench($request);
    }

    /**
     * Legacy URL (?driver_id=) — same workbench as index.
     */
    public function driverClosing(Request $request)
    {
        return $this->renderClosingWorkbench($request);
    }

    /**
     * @return array<string, mixed>
     */
    private function closingReportPayload(int $driverId): array
    {
        $period = DriverPeriodService::periodForDriver($driverId);
        $closingDate = $period['end_date'];

        $user = User::where('driver_id', $driverId)->first();
        $driverEmployee = Employee::where('driver_id', $driverId)->first();

        $sales = SalesLedger::query()
            ->with('customer')
            ->where('driver_id', $driverId)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $closingDate)
            ->get();

        $collections = SalesPayment::query()
            ->with('customer')
            ->where('type', SalesPayment::TYPE_PAYMENT)
            ->where('create_by', $user?->id)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $closingDate)
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $expenses = ExpenseEntry::query()
            ->with('expense')
            ->where('driver_id', $driverId)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $closingDate)
            ->get();

        $products = DriverIssueItem::query()
            ->with('product')
            ->leftJoin('driver_issues', 'driver_issues.id', '=', 'driver_issue_items.driver_issue_id')
            ->where('driver_issues.status', 'accepted')
            ->where('driver_issues.driver_id', $driverId)
            ->whereDate('driver_issues.issue_date', '>=', $period['start_date'])
            ->whereDate('driver_issues.issue_date', '<=', $closingDate)
            ->select('driver_issue_items.*')
            ->get();

        $stockSummaryRows = $products
            ->groupBy('product_id')
            ->map(function ($group) {
                $first = $group->first();
                $issue = (float) $group->sum(fn (DriverIssueItem $i) => (float) $i->issue_qty);
                $weightedSum = (float) $group->sum(function (DriverIssueItem $i) {
                    $q = (float) $i->issue_qty;
                    $p = (float) ($i->getAttribute('purchase_price') ?? 0);

                    return $q * $p;
                });
                $avgPrice = $issue > 0 ? round($weightedSum / $issue, 6) : 0.0;

                $distinctPrices = $group
                    ->map(fn (DriverIssueItem $i) => round((float) ($i->getAttribute('purchase_price') ?? 0), 6))
                    ->unique()
                    ->sort()
                    ->values();
                $priceNote = $distinctPrices->count() > 1
                    ? $distinctPrices->map(fn ($p) => number_format((float) $p, 2, '.', ''))->implode(' / ')
                    : null;

                return [
                    'product' => $first->product,
                    'product_id' => (int) $first->product_id,
                    'purchase_price' => $avgPrice,
                    'purchase_price_note' => $priceNote,
                    'issue_qty' => $issue,
                    'sold_qty' => (float) $group->sum(fn (DriverIssueItem $i) => (float) $i->sold_qty),
                    'return_qty' => (float) $group->sum(fn (DriverIssueItem $i) => (float) $i->return_qty),
                ];
            })
            ->values()
            ->sortBy(function (array $row) {
                return (string) ($row['product']->name ?? '');
            })
            ->values();

        $issue = DriverIssues::query()
            ->where('driver_id', $driverId)
            ->where('status', 'accepted')
            ->whereDate('issue_date', '>=', $period['start_date'])
            ->whereDate('issue_date', '<=', $closingDate)
            ->orderByDesc('issue_date')
            ->first();

        $distributableEmployees = Employee::query()
            ->where('designation', '!=', 'DSR')
            ->when($driverEmployee, function ($query) use ($driverEmployee) {
                $query->where('id', '!=', $driverEmployee->id);
            })
            ->orderBy('name')
            ->get();

        $givenAmounts = DriverCashDistribution::query()
            ->with('employee')
            ->where('driver_id', $driverId)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $closingDate)
            ->get();

        $salesReturns = SalesReturnLedger::query()
            ->with(['customer', 'entries.product', 'payments', 'salesLedger'])
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $closingDate)
            ->where(function ($q) use ($driverId, $user) {
                $q->whereHas('salesLedger', function ($query) use ($driverId) {
                    $query->where('driver_id', $driverId);
                });
                if ($user) {
                    $q->orWhere(function ($q2) use ($user) {
                        $q2->whereNull('sales_ledger_id')
                            ->where('create_by', $user->id);
                    });
                }
            })
            ->get();

        if ($user) {
            $linkedIds = DriverCashService::returnCashRefundQuery(
                (int) $user->id,
                $driverId,
                $period['start_date'],
                $closingDate
            )->pluck('sp.id');
            $orphanIds = SalesPayment::query()
                ->where('type', SalesPayment::TYPE_RETURN)
                ->where('create_by', $user->id)
                ->whereNull('reference_id')
                ->where('amount', '<', 0)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $closingDate)
                ->pluck('id');
            $returnpaids = SalesPayment::query()
                ->with('customer')
                ->whereIn('id', $linkedIds->merge($orphanIds)->unique()->values())
                ->orderBy('date')
                ->orderBy('id')
                ->get();
        } else {
            $returnpaids = collect();
        }

        $closingStatus = DriverClosing::query()
            ->where('date', $closingDate)
            ->where('driver_id', $driverId)
            ->first();

        $returnCashDeduct = $user
            ? DriverCashService::totalReturnCashRefundsForDriver(
                (int) $user->id,
                $driverId,
                $period['start_date'],
                $closingDate
            )
            : 0.0;

        $cashInHand = (float) $sales->sum('paid')
            + (float) $collections->sum('amount')
            - (float) $expenses->sum('amount')
            - $returnCashDeduct;

        return [
            'driver' => Drivers::query()->findOrFail($driverId),
            'period' => $period,
            'closingDate' => $closingDate,
            'sales' => $sales,
            'collections' => $collections,
            'expenses' => $expenses,
            'products' => $products,
            'stockSummaryRows' => $stockSummaryRows,
            'issue' => $issue,
            'distributableEmployees' => $distributableEmployees,
            'driverEmployee' => $driverEmployee,
            'givenAmounts' => $givenAmounts,
            'salesReturns' => $salesReturns,
            'returnpaids' => $returnpaids,
            'closingStatus' => $closingStatus,
            'cashInHand' => $cashInHand,
        ];
    }

    private function renderClosingWorkbench(Request $request)
    {
        $data['drivers'] = (new DriverService())->getDriverList([], false, false)[2];
        $data['showReport'] = false;

        if ($request->filled('driver_id')) {
            $request->validate([
                'driver_id' => 'required|exists:drivers,id',
            ]);
            $data = array_merge($data, $this->closingReportPayload((int) $request->driver_id));
            $data['showReport'] = true;
        }

        return view($this->path . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $closingDate = $request->date ?? date('Y-m-d');
            $salaryMonth = date('Y-m', strtotime($closingDate));
            $period = DriverPeriodService::periodForDriver((int) $request->driver_id);

            if ($closingDate < $period['start_date'] || $closingDate > $period['end_date']) {
                throw new \Exception('Closing date must be within the open period.');
            }

            $issues = DriverIssues::where('driver_id', $request->driver_id)
                ->where('status', 'accepted')
                ->whereDate('issue_date', '>=', $period['start_date'])
                ->whereDate('issue_date', '<=', $closingDate)
                ->with('items')
                ->get();

            if ($issues->isEmpty()) {
                throw new \Exception('No accepted driver stock issue found in the open period.');
            }

            $alreadyClosed = DriverClosing::where('driver_id', $request->driver_id)
                ->whereDate('date', $closingDate)
                ->exists();

            if ($alreadyClosed) {
                throw new \Exception('Driver closing already completed for this date.');
            }

            $driverEmployee = Employee::where('driver_id', $request->driver_id)->first();
            if (!$driverEmployee) {
                throw new \Exception('No employee profile found for this driver.');
            }

            $eligibleEmployeeIds = Employee::where('designation', '!=', 'DSR')
                ->where('id', '!=', $driverEmployee->id)
                ->pluck('id')
                ->toArray();

            $distributionRows = DriverCashDistribution::where('driver_id', $request->driver_id)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $closingDate)
                ->get(['id', 'employee_id', 'employee_salary_withdraw_id', 'amount'])
                ->map(function ($row) {
                    return [
                        'id' => (int) $row->id,
                        'employee_id' => (int) $row->employee_id,
                        'employee_salary_withdraw_id' => $row->employee_salary_withdraw_id ? (int) $row->employee_salary_withdraw_id : null,
                        'amount' => (float) $row->amount,
                    ];
                })
                ->values();

            foreach ($distributionRows as $row) {
                if (!in_array((int) $row['employee_id'], $eligibleEmployeeIds, true)) {
                    throw new \Exception('Invalid employee selected for cash distribution.');
                }
            }

            $driverUser = User::where('driver_id', $request->driver_id)->first();

            $salesPaid = (float) SalesLedger::where('driver_id', $request->driver_id)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $closingDate)
                ->sum('paid');

            $dueCollection = (float) SalesPayment::where('type', 1)
                ->whereDate('date', '>=', $period['start_date'])
                ->whereDate('date', '<=', $closingDate)
                ->when($driverUser, function ($query) use ($driverUser) {
                    $query->where('create_by', $driverUser->id);
                })
                ->sum('amount');

            $cashFromSales = $salesPaid + $dueCollection;
            $cashGivenToOthers = (float) $distributionRows->sum(function ($row) {
                return (float) ($row['amount'] ?? 0);
            });

            if ($cashGivenToOthers > $cashFromSales) {
                throw new \Exception('Distributed cash can not be greater than available sales balance.');
            }

            $driverCashTake = $cashFromSales - $cashGivenToOthers;

            $closingData = [
                'driver_id' => $request->driver_id,
                'date' => $closingDate,
                'cash_sales' => $request->cash_sales ?? '0',
                'total_collection' => $request->total_collection ?? '0',
                'total_return' => $request->total_return ?? '0',
                'total_expense' => $request->total_expense ?? '0',
                'cash_in_hand'=> $cashFromSales,
                'cash_from_manager' => 0,
                'cash_given_to_others' => $cashGivenToOthers,
                'driver_cash_take' => $driverCashTake,
            ];
            $closingData = (new DriverClosing())->create($closingData);

            foreach ($distributionRows as $row) {
                if (!empty($row['employee_salary_withdraw_id'])) {
                    continue;
                }

                $withdraw = EmployeeSalaryWithdraw::create([
                    'employee_id' => (int) $row['employee_id'],
                    'withdraw_date' => $closingDate,
                    'salary_month' => $salaryMonth,
                    'amount' => (float) $row['amount'],
                    'note' => 'Daily expense salary',
                    'created_by' => Auth::id(),
                ]);

                DriverCashDistribution::where('id', (int) $row['id'])->update([
                    'employee_salary_withdraw_id' => $withdraw->id,
                ]);
            }

            if ($driverCashTake > 0) {
                EmployeeSalaryWithdraw::create([
                    'employee_id' => (int) $driverEmployee->id,
                    'withdraw_date' => $closingDate,
                    'salary_month' => $salaryMonth,
                    'amount' => $driverCashTake,
                    'note' => 'Daily expense salary',
                    'created_by' => Auth::id(),
                ]);
            }

            foreach ($issues as $issue) {
                foreach ($issue->items as $item) {
                    $this->applyWarehouseStockForClosedDriverIssueLine($item);
                }

                $issue->update([
                    'status' => 'closed',
                ]);
            }
            DB::commit();

            return redirect()
                ->route('driver_closing.index', ['driver_id' => $request->driver_id])
                ->with('success', 'Driver closing finished.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()
                ->route('driver_closing.index', array_filter(['driver_id' => $request->driver_id]))
                ->with('error', $th->getMessage());
        }
    }

    /**
     * When a driver issue line is closed: record sales & returns and release SR issue qty to warehouse.
     *
     * Uses warehouse_stock_id when set; otherwise a warehouse row with the same
     * purchase price (4 dp) and enough sr_issue_qty for the full line; otherwise FIFO fallback.
     */
    private function applyWarehouseStockForClosedDriverIssueLine(DriverIssueItem $item): void
    {
        $productId = (int) $item->product_id;
        $sold = (float) $item->sold_qty;
        $ret = (float) $item->return_qty;
        $issued = (float) $item->issue_qty;

        $directRow = null;

        if ($item->warehouse_stock_id) {
            $directRow = WareHouseStocks::query()
                ->whereKey((int) $item->warehouse_stock_id)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();
            if (! $directRow) {
                throw new \Exception(
                    "Warehouse batch #{$item->warehouse_stock_id} for product ID {$productId} is missing. Cannot complete closing."
                );
            }
        } else {
            $priceNorm = WareHouseStocks::normalizePurchasePrice($item->getAttribute('purchase_price') ?? 0);
            $directRow = WareHouseStocks::query()
                ->where('product_id', $productId)
                ->whereRaw('ROUND(purchase_price, 4) = ?', [$priceNorm])
                ->where('sr_issue_qty', '>=', $issued - 0.0001)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();
        }

        if ($directRow) {
            if ((float) $directRow->sr_issue_qty + 0.0001 < $issued) {
                throw new \Exception(
                    "Warehouse batch #{$directRow->id} does not have enough SR-issued quantity to close this driver line (product ID {$productId}). Reconcile stock or contact support."
                );
            }
            if ($sold > 0) {
                $directRow->increment('sales_qty', $sold);
            }
            if ($ret > 0) {
                $directRow->increment('sales_return_qty', $ret);
            }
            if ($issued > 0) {
                $directRow->decrement('sr_issue_qty', $issued);
            }

            return;
        }

        $this->applyWarehouseStockForClosedDriverIssueLineFifo($item);
    }

    /**
     * Legacy: one driver line may span batches (weighted average price, no warehouse_stock_id).
     */
    private function applyWarehouseStockForClosedDriverIssueLineFifo(DriverIssueItem $item): void
    {
        $productId = (int) $item->product_id;

        $remainingSoldQty = (float) $item->sold_qty;

        if ($remainingSoldQty > 0) {
            $stocks = WareHouseStocks::where('product_id', $productId)
                ->where('sr_issue_qty', '>', 0)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($stocks as $stock) {
                if ($remainingSoldQty <= 0) {
                    break;
                }

                $availableQty = (float) $stock->sr_issue_qty;

                if ($availableQty <= 0) {
                    continue;
                }

                $deductQty = min($availableQty, $remainingSoldQty);

                $stock->increment('sales_qty', $deductQty);

                $remainingSoldQty -= $deductQty;
            }
        }

        $remainingReturnQty = (float) $item->return_qty;

        if ($remainingReturnQty > 0) {
            $stocks = WareHouseStocks::where('product_id', $productId)
                ->where('sr_issue_qty', '>', 0)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($stocks as $stock) {
                if ($remainingReturnQty <= 0) {
                    break;
                }

                $availableQty = (float) $stock->sr_issue_qty;

                if ($availableQty <= 0) {
                    continue;
                }

                $returnQty = min($availableQty, $remainingReturnQty);

                $stock->increment('sales_return_qty', $returnQty);

                $remainingReturnQty -= $returnQty;
            }
        }

        $issuedToAdjust = (float) $item->issue_qty;
        $stocks = WareHouseStocks::where('product_id', $productId)
            ->where('sr_issue_qty', '>', 0)
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        foreach ($stocks as $stock) {
            if ($issuedToAdjust <= 0) {
                break;
            }

            $decrementQty = min($issuedToAdjust, (float) $stock->sr_issue_qty);
            if ($decrementQty > 0) {
                $stock->decrement('sr_issue_qty', $decrementQty);
                $issuedToAdjust -= $decrementQty;
            }
        }

        if ($issuedToAdjust > 0.0001) {
            throw new \Exception(
                "Could not release full issued quantity to warehouse for product ID {$productId}. SR issue qty mismatch — check driver issues and warehouse stock."
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
