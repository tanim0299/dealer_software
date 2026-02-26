<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierDueListController extends Controller
{
    protected $path = 'backend.supplier_due_list.';
    /**
     * Display a listing of the resource.
     */
    public function index(SupplierService $supplierService)
    {
        $suppliers = Supplier::all();

        $dueSuppliers = $suppliers->map(function ($supplier) use ($supplierService) {

            $due = $supplierService->getSupplierDueById($supplier->id);

            return [
                'id'    => $supplier->id,
                'name'  => $supplier->name,
                'due'   => $due
            ];
        })
        ->filter(function ($item) {
            return $item['due'] > 0;
        });

        $data['suppliers'] = $dueSuppliers;
        

        return view($this->path.'index',$data);
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
        return (new SupplierService())->getSupplierDueById($id);
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
