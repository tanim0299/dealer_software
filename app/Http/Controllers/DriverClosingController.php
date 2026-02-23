<?php

namespace App\Http\Controllers;

use App\Models\DriverClosing;
use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\Drivers;
use App\Models\ExpenseEntry;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Models\User;
use App\Models\WareHouseStocks;
use App\Services\DriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverClosingController extends Controller
{
    protected $path = 'backend.driver_closing';
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
            $closingData = [
                'driver_id' => $request->driver_id,
                'date' => $request->date ?? date('Y-m-d'),
                'cash_sales' => $request->cash_sales ?? '0',
                'total_collection' => $request->total_collection ?? '0',
                'total_return' => $request->total_return ?? '0',
                'total_expense' => $request->total_expense ?? '0',
                'cash_in_hand'=> $request->cash_in_hand ?? '0',
            ];
            $closingData = (new DriverClosing())->create($closingData);

            $issue = DriverIssues::where('driver_id', $request->driver_id)
            ->whereDate('issue_date', now()->toDateString())
            ->where('status', 'accepted')
            ->with('items')
            ->first();

            foreach ($issue->items as $item) {

                /*
                |--------------------------------------------------------------------------
                | 1️⃣ SOLD QTY → warehouse sales_qty (FIFO)
                |--------------------------------------------------------------------------
                */
                $remainingSoldQty = $item->sold_qty;

                if ($remainingSoldQty > 0) {

                    $stocks = WareHouseStocks::where('product_id', $item->product_id)
                        ->where('purchase_price', $item->purchase_price)
                        ->orderBy('id', 'asc') // FIFO
                        ->get();

                    foreach ($stocks as $stock) {

                        if ($remainingSoldQty <= 0) break;

                        $availableQty = $stock->purchase_qty 
                                        - $stock->sales_qty 
                                        - $stock->return_qty;

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
                        ->where('purchase_price', $item->purchase_price)
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($stocks as $stock) {

                        if ($remainingReturnQty <= 0) break;

                        $stock->increment('sales_return_qty', $remainingReturnQty);

                        $remainingReturnQty = 0;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | 3️⃣ issue_qty → sr_issue_qty থেকে minus
                |--------------------------------------------------------------------------
                */

                WareHouseStocks::where('product_id', $item->product_id)
                    ->where('purchase_price', $item->purchase_price)
                    ->orderBy('id', 'asc')
                    ->decrement('sr_issue_qty', $item->issue_qty);
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
        $user = (new User())->where('driver_id',$request->driver_id)->first();
        $data['driver'] = (new Drivers())->where('id',$request->driver_id)->first();
        $data['sales'] = (new SalesLedger())->where('driver_id',$request->driver_id)->where('date',$request->date)->get();
        $data['collections'] = (new SalesPayment())->where('date',$request->date)->where('create_by',$user->id)->get();
        $data['expenses'] = (new ExpenseEntry())->where('date',$request->date)->where('driver_id',$request->driver_id)->get();
        $data['products'] = DriverIssueItem::leftjoin('driver_issues','driver_issues.id','driver_issue_items.driver_issue_id')
                            ->where('driver_issues.status','accepted')
                            ->where('issue_date',$request->date)
                            ->select('driver_issue_items.*')->get();

        $data['returnpaids'] = (new SalesPayment())->where('type',2)->where('amount','<','0')->where('date',$request->date)->get(); 
        $data['closingStatus'] = (new DriverClosing())->where('date',$request->date)->where('driver_id',$request->driver_id)->first();
        return view($this->path.'.show_closing',$data);
    }
}
