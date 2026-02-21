<?php

namespace App\Http\Controllers;

use App\Models\SalesPayment;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SalesPayment::with('customer')
            ->where('type', 1); // normal payment only

        // 🔹 Role based restriction
        if (Auth::user()->hasRole('Driver')) {
            $query->where('create_by', Auth::user()->id);
        }

        // 🔹 Default today filter (only if no date selected)
        if (!$request->from_date && !$request->to_date) {
            $query->whereDate('date', now()->toDateString());
        }

        // 🔹 Date filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // 🔹 Customer filter
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // 🔹 Amount filter
        if ($request->amount) {
            $query->where('amount', $request->amount);
        }

        $payments = $query
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // 🔹 Customers list (driver wise)
        $customers = (new CustomerService())
            ->getrDriverCustomer(Auth::user()->driver_id)[2] ?? [];

        $data = [
            'payments'  => $payments,
            'customers' => $customers,
        ];

        // 🔹 View switch (same pattern as Sales index)
        if (Auth::user()->hasRole('Driver')) {
            return view('driver.customer_payment.index', $data);
        }

        return view('backend.customer_payment.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Driver user
        if (Auth::user()->hasRole('Driver')) {
            $customers = (new CustomerService())
                ->getrDriverCustomer(Auth::user()->driver_id)[2] ?? [];

            return view('driver.customer_payment.create', [
                'customers' => $customers
            ]);
        }

        // Backend user (Admin / Staff)
        $customers = (new CustomerService())
            ->getrDriverCustomer(null)[2] ?? [];

        return view('backend.customer_payment.create', [
            'customers' => $customers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'customer_id' => 'required',

            ]);

            SalesPayment::create([
                'date'           => now()->toDateString(),
                'time'           => now()->toTimeString(),
                'customer_id'    => $request->customer_id,
                'amount'         => $request->amount,
                'type'           => 1, // normal payment
                'reference_type' => 'payment',
                'reference_id'   => null,
                'note'           => $request->note,
                'create_by' => Auth::user()->id,
            ]);
            DB::commit();
            return back()->with('success', 'Payment Added Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
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
        $payment = SalesPayment::findOrFail($id);

        $payment->delete();

        return back()->with('success', 'Payment Deleted Successfully');
    }
}
