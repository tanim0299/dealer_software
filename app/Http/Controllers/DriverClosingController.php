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
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $data['drivers'] = (new DriverService())->getDriverList([],false,false)[2];
        return view($this->path.'.index',$data);
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

            $issue = DriverIssues::where('driver_id', $request->driver_id)
                ->whereDate('issue_date', $closingDate)
                ->where('status', 'accepted')
                ->with('items')
                ->first();

            if (!$issue) {
                throw new \Exception('No accepted driver issue found for this date.');
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
                ->whereDate('date', $closingDate)
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

            $cashFromManager = (float) ($issue->cash_from_manager ?? 0);
            $cashGivenToOthers = (float) $distributionRows->sum(function ($row) {
                return (float) ($row['amount'] ?? 0);
            });

            if ($cashGivenToOthers > $cashFromManager) {
                throw new \Exception('Distributed cash can not be greater than manager cash.');
            }

            $driverCashTake = $cashFromManager - $cashGivenToOthers;

            $closingData = [
                'driver_id' => $request->driver_id,
                'date' => $closingDate,
                'cash_sales' => $request->cash_sales ?? '0',
                'total_collection' => $request->total_collection ?? '0',
                'total_return' => $request->total_return ?? '0',
                'total_expense' => $request->total_expense ?? '0',
                'cash_in_hand'=> $request->cash_in_hand ?? '0',
                'cash_from_manager' => $cashFromManager,
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

            foreach ($issue->items as $item) {

                /*
                |--------------------------------------------------------------------------
                | 1️⃣ SOLD QTY → warehouse sales_qty (FIFO)
                |--------------------------------------------------------------------------
                */
                $remainingSoldQty = $item->sold_qty;

                if ($remainingSoldQty > 0) {

                    $stocks = WareHouseStocks::where('product_id', $item->product_id)
                        ->where('sr_issue_qty', '>', 0)
                        ->orderBy('id', 'asc') // FIFO
                        ->get();

                    foreach ($stocks as $stock) {

                        if ($remainingSoldQty <= 0) break;

                        $availableQty = $stock->sr_issue_qty;

                        if ($availableQty <= 0) continue;

                        $deductQty = min($availableQty, $remainingSoldQty);

                        $stock->increment('sales_qty', $deductQty);

                        $remainingSoldQty -= $deductQty;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | 2️⃣ RETURN QTY → warehouse sales_return_qty (FIFO)
                |--------------------------------------------------------------------------
                */
                $remainingReturnQty = $item->return_qty;

                if ($remainingReturnQty > 0) {

                    $stocks = WareHouseStocks::where('product_id', $item->product_id)
                        ->where('sr_issue_qty', '>', 0)
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($stocks as $stock) {

                        if ($remainingReturnQty <= 0) break;

                        $availableQty = $stock->sr_issue_qty;

                        if ($availableQty <= 0) continue;

                        $returnQty = min($availableQty, $remainingReturnQty);

                        $stock->increment('sales_return_qty', $returnQty);

                        $remainingReturnQty -= $returnQty;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | 3️⃣ issue_qty → sr_issue_qty থেকে minus
                |--------------------------------------------------------------------------
                */
                $issuedToAdjust = $item->issue_qty;
                $stocks = WareHouseStocks::where('product_id', $item->product_id)
                    ->where('sr_issue_qty', '>', 0)
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($stocks as $stock) {
                    if ($issuedToAdjust <= 0) {
                        break;
                    }

                    $decrementQty = min($issuedToAdjust, $stock->sr_issue_qty);
                    if ($decrementQty > 0) {
                        $stock->decrement('sr_issue_qty', $decrementQty);
                        $issuedToAdjust -= $decrementQty;
                    }
                }
            }

            $issue->update([
                'status' => 'closed'
            ]);
            DB::commit();
            return redirect()->back()->with('succcess','Driver Closing Finished');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error',$th->getMessage());
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

    public function driverClosing(Request $request)
    {
        $closingDate = $request->date ?? date('Y-m-d');
        $user = (new User())->where('driver_id',$request->driver_id)->first();
        $driverEmployee = Employee::where('driver_id', $request->driver_id)->first();

        $data['driver'] = (new Drivers())->where('id',$request->driver_id)->first();
        $data['sales'] = (new SalesLedger())->where('driver_id',$request->driver_id)->where('date',$closingDate)->get();
        $data['collections'] = (new SalesPayment())->where('date',$closingDate)->where('create_by',$user?->id)->get();
        $data['expenses'] = (new ExpenseEntry())->where('date',$closingDate)->where('driver_id',$request->driver_id)->get();
        $data['products'] = DriverIssueItem::leftjoin('driver_issues','driver_issues.id','driver_issue_items.driver_issue_id')
                            ->where('driver_issues.status','accepted')
                            ->where('driver_issues.driver_id',$request->driver_id)
                            ->where('issue_date',$closingDate)
                            ->select('driver_issue_items.*')->get();

        $data['issue'] = DriverIssues::where('driver_id', $request->driver_id)
            ->whereDate('issue_date', $closingDate)
            ->first();

        $data['distributableEmployees'] = Employee::where('designation', '!=', 'DSR')
            ->when($driverEmployee, function ($query) use ($driverEmployee) {
                $query->where('id', '!=', $driverEmployee->id);
            })
            ->orderBy('name')
            ->get();

        $data['driverEmployee'] = $driverEmployee;
        $data['givenAmounts'] = DriverCashDistribution::with('employee')
            ->where('driver_id', $request->driver_id)
            ->whereDate('date', $closingDate)
            ->get();
            $data['salesReturns'] = SalesReturnLedger::with(['customer', 'entries.product', 'payments', 'salesLedger'])
                ->whereDate('date', $closingDate)
                ->whereHas('salesLedger', function ($query) use ($request) {
                    $query->where('driver_id', $request->driver_id);
                })
                ->get();
        $data['returnpaids'] = (new SalesPayment())->where('type',2)->where('amount','<','0')->where('date',$closingDate)->get(); 
        $data['closingStatus'] = (new DriverClosing())->where('date',$closingDate)->where('driver_id',$request->driver_id)->first();
        return view($this->path.'.show_closing',$data);
    }
}
