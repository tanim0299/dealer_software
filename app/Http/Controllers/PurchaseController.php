<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\PurchaseService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected $PATH = 'backend.purchase';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        $data['search']['from_date'] = $request->from_date ?? '';
        $data['search']['to_date'] = $request->to_date ?? '';
        $data['purchases'] = (new PurchaseService())->getPurchaseList($data['search'], true, true)[2];
        return view($this->PATH.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['suppliers'] = (new SupplierService())->getSupplierList([],false,false)[2];
        $data['products'] = (new ProductService())->getProductList([],false,false)[2];
        return view($this->PATH.'.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return (new PurchaseService())->storePurchase($request);
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

    public function invoice($id)
    {
        $data['purchase'] = (new PurchaseService())->getPurchaseById($id)[2];
        return view($this->PATH.'.invoice',$data);
    }
}
