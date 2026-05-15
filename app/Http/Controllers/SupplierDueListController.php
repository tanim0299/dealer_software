<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierDueListController extends Controller
{
    protected $path = 'backend.supplier_due_list.';

    /**
     * Display suppliers with due (with optional filters).
     */
    public function index(Request $request, SupplierService $supplierService)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');
        $useDateRange = $request->filled('from_date') && $request->filled('to_date');

        $query = Supplier::query()->orderBy('name');

        if ($request->filled('search')) {
            $term = '%'.$request->input('search').'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('supplier_id', 'like', $term);
            });
        }

        $suppliers = $query->get();

        $dueSuppliers = $suppliers->map(function ($supplier) use ($supplierService, $useDateRange, $from, $to) {
            $due = $useDateRange
                ? $supplierService->getSupplierDueById($supplier->id, $from, $to)
                : $supplierService->getSupplierDueById($supplier->id);

            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'phone' => $supplier->phone,
                'email' => $supplier->email,
                'supplier_id' => $supplier->supplier_id,
                'due' => (float) $due,
            ];
        });

        if (! $request->boolean('include_zero')) {
            $dueSuppliers = $dueSuppliers->filter(function ($item) {
                return $item['due'] > 0.0001;
            });
        }

        $dueSuppliers = $dueSuppliers->sortByDesc('due')->values();

        $search = $request->only(['search', 'from_date', 'to_date', 'include_zero']);

        $data = [
            'suppliers' => $dueSuppliers,
            'search' => $search,
            'use_date_range' => $useDateRange,
        ];

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
     * JSON due for a supplier (used elsewhere / API-style).
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
