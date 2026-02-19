<?php

namespace App\Http\Controllers;

use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\SalesEntry;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Models\SalesReturnEntries;
use App\Models\SalesReturnLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    protected $path;
    public function __construct()
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Driver'))
        {
             $query = SalesReturnLedger::with('customer')
                ->where('create_by', auth()->id());

            // 🔹 Date Filter
            if ($request->from_date && $request->to_date) {
                $query->whereBetween('date', [
                    $request->from_date,
                    $request->to_date
                ]);
            }

            $returns = $query->orderBy('date', 'desc')->paginate(15);
            return view('driver.sales_return.index',compact('returns'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if(Auth::user()->hasRole('Driver'))
        {
            $sales_ledgers = SalesLedger::where('driver_id', auth()->id())->get();

            $selectedLedger = null;

            if ($request->invoice_id) {
                $selectedLedger = SalesLedger::with([
                    'items.product',
                    'items' => function ($query) {
                        $query->withSum('returnEntries', 'return_qty');
                    }
                ])->find($request->invoice_id);
            }
          
            return view('driver.sales_return.create',compact(
                'sales_ledgers',
                'selectedLedger'
            ));   
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $salesLedger = SalesLedger::with('items')
                ->findOrFail($request->sales_ledger_id);

            $returnLedger = SalesReturnLedger::create([
                'sales_ledger_id' => $salesLedger->id,
                'invoice_no'      => $salesLedger->invoice_no,
                'date'            => now(),
                'customer_id'     => $salesLedger->customer_id,
                'create_by'      => auth()->id(),
            ]);

            $subtotal = 0;

            foreach ($request->items as $entryId => $returnQty) {

                if ($returnQty <= 0) continue;

                $entry = SalesEntry::find($entryId);

                $alreadyReturned = SalesReturnEntries::where('sales_entry_id', $entryId)
                    ->sum('return_qty');

                $remainingQty = $entry->quantity - $alreadyReturned;

                if ($returnQty > $remainingQty) {
                    throw new \Exception('Return quantity exceeds sold quantity');
                }

                $subtotal += $returnQty * $entry->sale_price;

                SalesReturnEntries::create([
                    'return_ledger_id' => $returnLedger->id,
                    'sales_entry_id'   => $entry->id,
                    'product_id'       => $entry->product_id,
                    'return_qty'       => $returnQty,
                    'sale_price'       => $entry->sale_price,
                    'purchase_price'   => $entry->purchase_price,
                ]);

                // 🔹 Update driver issue return_qty
                $driverIssue = DriverIssues::where('driver_id', auth()->id())
                    ->whereDate('issue_date', now()->toDateString())
                    ->where('status', 'accepted')
                    ->first();

                if ($driverIssue) {
                    $driverIssueItem = DriverIssueItem::where('driver_issue_id', $driverIssue->id)
                        ->where('product_id', $entry->product_id)
                        ->first();

                    if ($driverIssueItem) {
                        $driverIssueItem->increment('return_qty', $returnQty);
                    }
                }
            }


            $returnLedger->update([
                'subtotal' => $subtotal
            ]);

            if ($subtotal > 0) {

                if ($request->adjustment_type == 'cash') {

                    // Cash Paid to Customer (Negative Amount)
                    SalesPayment::create([
                        'date'           => now()->toDateString(),
                        'time'           => now()->toTimeString(),
                        'customer_id'    => $salesLedger->customer_id,
                        'amount'         => -$subtotal,
                        'type'           => 2, // 2 = return
                        'reference_type' => 'return',
                        'reference_id'   => $returnLedger->id,
                        'note'           => 'Sales Return Cash Paid',
                    ]);

                } elseif ($request->adjustment_type == 'due') {

                    // Due Adjust (No Cash Movement)
                    SalesPayment::create([
                        'date'           => now()->toDateString(),
                        'time'           => now()->toTimeString(),
                        'customer_id'    => $salesLedger->customer_id,
                        'amount'         => 0,
                        'type'           => 2,
                        'reference_type' => 'return',
                        'reference_id'   => $returnLedger->id,
                        'note'           => 'Sales Return Adjusted With Due',
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Sales Return Successful');

        } catch (\Throwable $th) {

            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $return = SalesReturnLedger::with([
            'customer',
            'entries.product',
            'payments'
        ])->findOrFail($id);
        if(Auth::user()->hasRole('Driver'))
        {
            return view('driver.sales_return.show',compact('return'));
        }   
        // return view('driver.sales_return_invoice', compact('return'));
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
