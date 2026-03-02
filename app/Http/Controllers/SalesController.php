<?php

namespace App\Http\Controllers;

use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Models\SalesReturnLedger;
use App\Models\Customer;
use App\Models\Drivers;
use App\Services\ApiService;
use App\Services\CustomerService;
use App\Services\SalesService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['driver_id'] = Auth::user()->driver_id ?? null;
        $data['search']['free_text'] = $request->free_text ?? null;
        $data['search']['from_date'] = $request->from_date;
        $data['search']['to_date'] = $request->to_date;
        $data['sales'] = (new SalesService())->getSalesList($data['search'],true,true)[2];
        if(Auth::user()->hasRole('Driver'))
        {
            return view('driver.sale.index',$data);
        }
        else
        {
            return view('backend.sales.index',$data);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        if(Auth::user()->hasRole('Driver'))
        {
            $driverId = Auth::user()->driver_id;
            $driver = Drivers::with('areas')->findOrFail($driverId);

            $cashCustomer = (new CustomerService())->getGlobalCashCustomer();

            $data['customers'] = (new CustomerService())->getrDriverCustomer($driverId)[2];
            $data['products'] = (new StockService())->getDriverStock($driverId, date('Y-m-d'))[2];
            $data['driverAreas'] = $driver->areas;
            $data['cashCustomerId'] = $cashCustomer?->id;

            return view('driver.sale.create',$data);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        [$status_code, $status_message, $invoice_url] = (new SalesService())->storeSales($request);
        return response()->json([
            'status_code' => $status_code,
            'status_message' => $status_message,
            'invoice_url' => url('/sales_invoice/' . $invoice_url), // add slash
        ]);

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
        [$status_code, $status_message] = (new SalesService())->deleteSalesById($id);
        if ($status_code == ApiService::API_SUCCESS) {
                return redirect()
                    ->route('sales.index')
                    ->with('success', $status_message);
            }
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $status_message);
    }

    public function invoice($id)
    {
        [$status_code, $status_message, $ledger] = (new SalesService())->getSalesLedgerById($id);
        // return $ledger;
        $data['ledger'] = $ledger;
        return view('driver.sale.invoice',$data);
    }

    public function getCustomerDue($id)
    {
        $totalSales   = SalesLedger::where('customer_id', $id)->sum(DB::raw('subtotal - discount'));
        $totalReturn  = SalesReturnLedger::where('customer_id', $id)->sum('subtotal');
        $totalPaid    = SalesPayment::where('customer_id', $id)->whereIn('type',[0,1])->sum('amount');
        $totalReturnPaid = SalesPayment::where('customer_id', $id)->where('type',2)->sum('amount') * -1;

        $due = ($totalSales - $totalReturn) - $totalPaid + $totalReturnPaid;

        return response()->json([
            'due' => $due
        ]);
    }

    public function storeDriverCustomer(Request $request)
    {
        if (!Auth::user()->hasRole('Driver')) {
            return response()->json([
                'status_code' => ApiService::API_FORBIDDEN,
                'status_message' => 'Unauthorized'
            ], 403);
        }

        $driver = Drivers::with('areas')->findOrFail(Auth::user()->driver_id);
        $allowedAreaIds = $driver->areas->pluck('id')->toArray();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'area_id' => 'required|exists:customer_areas,id',
            'address' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => ApiService::Api_VALIDATION_ERROR,
                'status_message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!in_array((int) $request->area_id, $allowedAreaIds, true)) {
            return response()->json([
                'status_code' => ApiService::Api_VALIDATION_ERROR,
                'status_message' => 'You can select only your assigned area.',
            ], 422);
        }

        try {
            $customer = (new Customer())->createCustomer($request);

            return response()->json([
                'status_code' => ApiService::API_SUCCESS,
                'status_message' => 'Customer created successfully.',
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status_code' => ApiService::API_SERVER_ERROR,
                'status_message' => $th->getMessage(),
            ], 500);
        }
    }

}
