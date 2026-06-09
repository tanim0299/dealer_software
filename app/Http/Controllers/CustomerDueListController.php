<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SalesPayment;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerDueListController extends Controller
{
    protected $path = 'backend.customer_due_list.';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, CustomerService $customerService)
    {
        $customers = Customer::with('customerArea')->get();

        $dueCustomers = $customers->map(function ($customer) use ($customerService) {

            $due = $customerService->getCustomerDueById($customer->id);

            return [
                'id'    => $customer->id,
                'name'  => $customer->name,
                'phone' => $customer->phone,
                'area'  => $customer->area ? $customer->area->name : 'N/A',
                'due'   => $due
            ];
        })
        ->filter(function ($item) {
            return $item['due'] > 0;
        })
        ->sortByDesc('due');

        // Apply search filter
        if ($request->search) {
            $search = strtolower($request->search);
            $dueCustomers = $dueCustomers->filter(function ($item) use ($search) {
                return strpos(strtolower($item['name']), $search) !== false || 
                       strpos(strtolower($item['phone']), $search) !== false;
            });
        }

        $data['customers'] = $dueCustomers;

        return view($this->path.'index', $data);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, CustomerService $customerService)
    {
        $customer = Customer::with('customerArea')->findOrFail($id);
        $due = $customerService->getCustomerDueById($id);

        $data['customer'] = $customer;
        $data['due'] = $due;

        return view($this->path.'show', $data);
    }

    /**
     * Store payment for customer due.
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount'      => 'required|numeric|min:0.01',
            'note'        => 'nullable|string|max:2000',
        ]);

        $customerId = (int) $validated['customer_id'];
        $amount = (float) $validated['amount'];

        $due = (new CustomerService())->getCustomerDueById($customerId);

        if ($due <= 0.009) {
            return back()->with('error', 'This customer has no outstanding due.');
        }

        if (($amount - $due) > 0.02) {
            return back()->with(
                'error',
                'Amount cannot exceed outstanding due (Tk ' . number_format($due, 2) . ').'
            );
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
                'create_by'      => Auth::user()->id,
            ]);

            DB::commit();

            return redirect()
                ->route('customer_due_list.show', $customerId)
                ->with('success', 'Payment recorded successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', \App\Services\ApiService::friendlyExceptionMessage($th));
        }
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
