<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $PATH = 'backend.supplier';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        $data['suppliers'] = (new SupplierService())->getSupplierList($data['search'], true, false)[2];
        return view($this->PATH.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->PATH.'.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message, $error_message] = (new SupplierService())->storeSupplier($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('supplier.index')
                ->with('success', $status_message);
        }
        return redirect()
            ->back()
            ->withInput()
            ->withErrors($error_message) // must be an array of field → messages
            ->with('error', $status_message);
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
        $data['data'] = (new SupplierService())->getSupplierById($id)[2];
        return view($this->PATH.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] = (new SupplierService())->updateSupplierById($request,$id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('supplier.index')
                ->with('success', $status_message);
        }
        return redirect()
            ->back()
            ->withInput()
            ->withErrors($error_message) // must be an array of field → messages
            ->with('error', $status_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new SupplierService())->deleteSupplierById($id);
        
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('supplier.index')
                ->with('success', $status_message);
        }
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $status_message);
    }
}
