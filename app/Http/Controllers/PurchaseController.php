<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
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
        $data['suppliers'] = (new SupplierService())->getSupplierList([],false,false)[2];
        $data['products'] = (new ProductService())->getProductList([],false,false)[2];
        $data['purchase'] = (new PurchaseService())->getPurchaseById($id)[2];
        $data['entries'] = [];
        foreach($data['purchase']->entries as $entry)
        {
            $data['entries'][] = [
                'product_id' => $entry->product_id,
                'product_name' => $entry->product->name,
                'sub_unit_id' => $entry->sub_unit_id,
                'sub_unit_name' => $entry->sub_unit->name,
                'sub_units' => $entry->product->unit->sub_unit,
                'unit_data' => $entry->sub_unit->unit_data,
                'qty' => $entry->quantity,
                'final_quantity' => $entry->final_quantity,
                'unit_price' => $entry->unit_price,
                'discount' => $entry->discount,
                'total_price' => $entry->total_price,
                'sale_price' => $entry->sale_price ?? 0,
            ];   
        }
        return view($this->PATH.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return (new PurchaseService())->updatePurchase($request,$id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new PurchaseService())->deletePurchase($id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->back()
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->with('error', $status_message);
    }

    public function invoice($id)
    {
        $data['purchase'] = (new PurchaseService())->getPurchaseById($id)[2];
        return view($this->PATH.'.invoice',$data);
    }
}
