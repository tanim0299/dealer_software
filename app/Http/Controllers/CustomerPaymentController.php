<?php

namespace App\Http\Controllers;

use App\Models\SalesPayment;
use App\Services\CustomerService;
use App\Services\DriverPeriodService;
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
        $query = SalesPayment::with('customer')->where('type', 1);

        if (Auth::user()->hasRole('Driver')) {
            $query->where('create_by', Auth::user()->id);
        }

        if (Auth::user()->hasRole('Driver') && Auth::user()->driver_id) {
            $period = DriverPeriodService::periodForDriver((int) Auth::user()->driver_id);
            $from = $request->filled('from_date') ? $request->from_date : $period['start_date'];
            $to = $request->filled('to_date') ? $request->to_date : $period['end_date'];
            $query->whereBetween('date', [$from, $to]);
        } elseif ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('amount')) {
            $query->where('amount', $request->amount);
        }

        if ($request->filled('free_text')) {
            $ft = '%' . $request->free_text . '%';
            $query->where(function ($q) use ($ft) {
                $q->where('note', 'like', $ft)
                    ->orWhereHas('customer', function ($cq) use ($ft) {
                        $cq->where('name', 'like', $ft)->orWhere('phone', 'like', $ft);
                    });
            });
        }

        $payments = $query
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        if (Auth::user()->hasRole('Driver')) {
            $customers = (new CustomerService())->getrDriverCustomer(Auth::user()->driver_id)[2] ?? collect();
        } else {
            $customers = \App\Models\Customer::query()->orderBy('name')->get(['id', 'name']);
        }

        $data = [
            'payments' => $payments,
            'customers' => $customers,
            'search' => $request->only(['free_text', 'from_date', 'to_date', 'customer_id', 'amount']),
        ];

        return Auth::user()->hasRole('Driver')
            ? view('driver.customer_payment.index', $data)
            : view('backend.customer_payment.index', $data);
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
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:0.01',
            'note'        => 'nullable|string|max:2000',
        ]);

        $customerId = (int) $validated['customer_id'];
        $amount = (float) $validated['amount'];

        if (Auth::user()->hasRole('Driver') && Auth::user()->driver_id) {
            if (!(new CustomerService())->driverCanCollectFromCustomer((int) Auth::user()->driver_id, $customerId)) {
                return back()->with('error', 'This customer is not assigned to you.')->withInput();
            }
            try {
                DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                    (int) Auth::user()->driver_id,
                    now()->toDateString()
                );
            } catch (\RuntimeException $e) {
                return back()->with('error', $e->getMessage())->withInput();
            }
        }

        $due = (new CustomerService())->getCustomerDueById($customerId);

        if ($due <= 0.009) {
            return back()->with('error', 'This customer has no outstanding due.')->withInput();
        }

        if (($amount - $due) > 0.02) {
            return back()->with(
                'error',
                'Amount cannot exceed outstanding due (Tk ' . number_format($due, 2) . ').'
            )->withInput();
        }

        try {
            DB::beginTransaction();

            SalesPayment::create([
                'ledger_id'      => null,
                'date'           => now()->toDateString(),
                'time'           => now()->toTimeString(),
                'customer_id'    => $customerId,
                'amount'         => $amount,
                'type'           => SalesPayment::TYPE_PAYMENT,
                'reference_type' => 'payment',
                'reference_id'   => null,
                'note'           => $validated['note'] ?? null,
                'create_by'      => Auth::id(),
            ]);
            DB::commit();

            return back()->with('success', 'Payment Added Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage())->withInput();
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

        if ((int) $payment->type !== SalesPayment::TYPE_PAYMENT) {
            return back()->with('error', 'Only due collection entries can be removed here.');
        }

        if (Auth::user()->hasRole('Driver') && (int) $payment->create_by !== (int) Auth::id()) {
            return back()->with('error', 'You can only delete your own collections.');
        }

        if (Auth::user()->hasRole('Driver') && Auth::user()->driver_id) {
            try {
                DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                    (int) Auth::user()->driver_id,
                    (string) $payment->date
                );
            } catch (\RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        $payment->delete();

        return back()->with('success', 'Payment Deleted Successfully');
    }
}
