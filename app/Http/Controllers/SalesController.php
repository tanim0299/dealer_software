<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
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
        //
    }
}
