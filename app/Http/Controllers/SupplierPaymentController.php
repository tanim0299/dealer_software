<?php

namespace App\Http\Controllers;

use App\Models\PurchaseLedger;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierPayment::with('supplier')
                ->where('type', 2); // Only normal payments

        // 🔎 Supplier filter
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // 🔎 Date range filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('payment_date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // 🔎 Payment amount filter
        if ($request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        $payments = $query->orderBy('payment_date', 'desc')
                            ->paginate(20)
                            ->appends($request->all());

        $suppliers = \App\Models\Supplier::all();

        return view('backend.supplier_payment.index',
            compact('payments','suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = \App\Models\Supplier::all();
        return view('backend.supplier_payment.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'payment_date' => 'required|date',
                'amount' => 'required|numeric|min:0.01',
            ]);

            SupplierPayment::create([
                'supplier_id' => $request->supplier_id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => 'Cash',
                'note' => $request->note,
                'type' => 2, // Normal payment
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('supplier_payment.create')->with('success', 'Payment added successfully.');

        } catch (\Throwable $th) {
            return redirect()->route('supplier_payment.create')->with('error', $th->getMessage());
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
            $payment = (new SupplierPayment())->find($id);
            $payment->delete();

            DB::commit();

            return redirect()
                ->route('supplier_payment.index')
                ->with('success','Payment deleted successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error','Delete failed.');
        }
    }

    public function getDue($supplier_id)
    {
        $totalPurchase = PurchaseLedger::where('supplier_id', $supplier_id)->sum('total_amount');
        $totalPurchaseDiscount = PurchaseLedger::where('supplier_id', $supplier_id)->sum('discount');
        $totalPaid = SupplierPayment::where('supplier_id', $supplier_id)
                        ->where('type', 2)
                        ->sum('amount');
        $totalReturnMinus = \App\Models\PurchaseReturnLedger::where('supplier_id', $supplier_id)
                                ->where('return_type', 2)
                                ->sum('subtotal');
        $totalReturnPaid = SupplierPayment::where('supplier_id', $supplier_id)
                        ->where('type', 3)
                        ->sum('amount') * -1;

        



        $due = $totalPurchase - $totalPurchaseDiscount - $totalPaid - $totalReturnMinus + $totalReturnPaid;

        return response()->json(['due' => $due]);
    }
}
