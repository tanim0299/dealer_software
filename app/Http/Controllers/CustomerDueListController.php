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
        try {
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'amount' => 'required|numeric|min:0.01',
                'note' => 'nullable|string',
            ]);

            DB::beginTransaction();

            SalesPayment::create([
                'date'           => now()->toDateString(),
                'time'           => now()->toTimeString(),
                'customer_id'    => $request->customer_id,
                'amount'         => $request->amount,
                'type'           => 1, // normal payment
                'reference_type' => 'payment',
                'reference_id'   => null,
                'note'           => $request->note,
                'create_by'      => Auth::user()->id,
            ]);

            DB::commit();

            return redirect()
                ->route('customer_due_list.show', $request->customer_id)
                ->with('success', 'Payment recorded successfully!');

        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
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
