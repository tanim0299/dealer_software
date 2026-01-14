<?php

namespace App\Http\Controllers;

use App\Services\StockService;
use Illuminate\Http\Request;

class WarehouseStockController extends Controller
{
    protected $PATH = 'backend.warehouse_stock';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['free_text']  = $request->free_text ?? '';
        $data['stocks'] = (new StockService())->getWarehouseStocks($data['search'], true, false)[2];
        return view($this->PATH.'.index',$data);
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
