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

        // 🔹 Default today filter
        if (!$request->from_date && !$request->to_date) {
            $query->whereDate('date', now()->toDateString());
        }

        if(Auth::user()->hasRole('Driver'))
        {
            $query->where('create_by',Auth::user()->id);   
        }

        // 🔹 Date Filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // 🔹 Customer Filter
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // 🔹 Amount Filter
        if ($request->amount) {
            $query->where('amount', $request->amount);
        }

        $payments = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $data['customers'] = (new CustomerService())->getrDriverCustomer(Auth::user()->driver_id)[2];
        $data['payments'] = $payments;
        if(Auth::user()->hasRole('Driver'))
        {
            return view('driver.customer_payment.index',$data);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if(Auth::user()->hasRole('Driver'))
        {
            $data['customers'] = (new CustomerService())->getrDriverCustomer(Auth::user()->driver_id)[2];
            return view('driver.customer_payment.create',$data);
        }
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
            return back()->with('error',$th->getMessage());
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

        return back()->with('success','Payment Deleted Successfully');
    }
}
