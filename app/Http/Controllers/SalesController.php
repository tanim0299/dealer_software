<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\CustomerService;
use App\Services\SalesService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['driver_id'] = auth()->user()->driver_id ?? null;
        $data['search']['free_text'] = $request->free_text ?? null;
        $data['search']['from_date'] = $request->from_date ?? date('Y-m-d');
        $data['search']['to_date'] = $request->to_date ?? date('Y-m-d');
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
            $data['customers'] = (new CustomerService())->getrDriverCustomer(auth()->user()->driver_id)[2];
            $data['products'] = (new StockService())->getDriverStock(auth()->user()->driver_id, date('Y-m-d'))[2];
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
}
