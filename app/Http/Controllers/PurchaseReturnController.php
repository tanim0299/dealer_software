<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturnEntry;
use App\Models\PurchaseReturnLedger;
use App\Models\SupplierPayment;
use App\Models\WareHouseStocks;
use App\Services\StockService;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    protected $path = 'backend.purchase_return';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchaseReturnLedger::with('supplier'); // eager load supplier

        // Filter by supplier
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by return type
        if ($request->return_type) {
            $query->where('return_type', $request->return_type);
        }

        // Filter by date range
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [$request->from_date.' 00:00:00', $request->to_date.' 23:59:59']);
        }

        $ledgers = $query->orderBy('created_at', 'desc')->paginate(20);

        // For supplier filter dropdown
        $suppliers = \App\Models\Supplier::all();

        return view('backend.purchase_return.index', compact('ledgers', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['suppliers'] = (new SupplierService())->getSupplierList([],false,false)[2];
        $data['products'] = (new StockService())->getWarehouseStocks([],false,true)[2];
        return view($this->path.'.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'return_type' => 'required|in:1,2',
           
        ]);
    
        DB::beginTransaction();
        try {
            // 1️⃣ Create ledger first
            $ledger = PurchaseReturnLedger::create([
                'date' => date('Y-m-d'),
                'supplier_id' => $request->supplier_id,
                'return_type' => $request->return_type,
                'subtotal' => 0, // will update later
            ]);

            $totalAmount = 0;

            foreach ($request->products as $item) {
                $returnQty = $item['return_qty'];
                if ($returnQty <= 0) continue;

                // 2️⃣ Get warehouse stocks for this product ordered by FIFO (purchase_price ASC)
                $stocks = WareHouseStocks::where('product_id', $item['product_id'])
                            ->orderBy('purchase_price', 'asc') // FIFO by price
                            ->get();
                
                foreach ($stocks as $stock) {
                    // Calculate available qty
                    $availableQty = $stock->purchase_qty - $stock->sales_qty + $stock->sales_return_qty - $stock->return_qty;

                    if ($availableQty <= 0) continue;

                    // Determine qty to return from this stock row
                    $applyQty = min($returnQty, $availableQty);

                    // 3️⃣ Update stock return_qty
                    $stock->increment('return_qty', $applyQty);

                    // 4️⃣ Create purchase return entry for this stock row
                    $total = $applyQty * $stock->purchase_price;
                    PurchaseReturnEntry::create([
                        'purchase_return_ledger_id' => $ledger->id,
                        'product_id' => $item['product_id'],
                        'return_qty' => $applyQty,
                        'purchase_price' => $stock->purchase_price,
                    ]);

                    $totalAmount += $total;

                    // Reduce remaining return quantity
                    $returnQty -= $applyQty;
                    if ($returnQty <= 0) break;
                }
                // If returnQty still > 0, it means user is trying to return more than available stock
                if ($returnQty > 0) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Return quantity for product ID {$item['product_id']} exceeds available stock!");
                }
            }

            // 5️⃣ Update ledger subtotal
            $ledger->update(['subtotal' => $totalAmount]);

            // 6️⃣ Handle supplier payment if Cash
            
                SupplierPayment::create([
                    'supplier_id' => $request->supplier_id,
                    'payment_date' => now()->toDateString(),
                    'amount' => $request->return_type == 1 ? -$totalAmount : 0, // Negative amount for cash return, zero for credit return
                    'payment_method' => 'Cash',
                    'note' => 'Purchase return',
                    'type' => 3,
                    'created_by' => auth()->id(),
                    'reference_no' => $ledger->id,
                ]);
            

            DB::commit();
            return redirect()->route('purchase_return.index')->with('success', 'Purchase return processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
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
        DB::beginTransaction();
        try {
            $ledger = (new PurchaseReturnLedger())->find($id);
            // 1️⃣ Rollback stock for each entry
            foreach ($ledger->entries as $entry) {
                // Get warehouse stocks for this product, ordered FIFO
                $stocks = WareHouseStocks::where('product_id', $entry->product_id)
                            ->orderBy('purchase_price', 'asc')
                            ->get();

                $remainingQty = $entry->return_qty;

                foreach ($stocks as $stock) {
                    $qtyToReduce = min($remainingQty, $stock->return_qty);

                    if ($qtyToReduce > 0) {
                        $stock->decrement('return_qty', $qtyToReduce);
                        $remainingQty -= $qtyToReduce;
                    }

                    if ($remainingQty <= 0) break;
                }
            }

            // 2️⃣ Delete entries
            $ledger->entries()->delete();

            // 3️⃣ Delete related supplier payment if exists
            SupplierPayment::where('supplier_id', $ledger->supplier_id)
                            ->where('type', 3) // purchase return
                            ->where('reference_no',$ledger->id)
                            ->delete();

            // 4️⃣ Delete ledger
            $ledger->delete();

            DB::commit();
            return redirect()->route('purchase_return.index')->with('success', 'Purchase return deleted and stock rolled back successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete purchase return: ' . $e->getMessage());
        }
    }
}
