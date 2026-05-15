<?php

namespace App\Http\Controllers;

use App\Models\Drivers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = (new Drivers())->getTodayDriverStock(Auth::user()->driver_id);
        $search = ['free_text' => $request->input('free_text', '')];

        if (! empty($search['free_text'])) {
            $needle = mb_strtolower($search['free_text']);
            $items = $items->filter(function ($row) use ($needle) {
                $name = mb_strtolower((string) ($row->product->name ?? ''));
                $code = mb_strtolower((string) ($row->product->product_code ?? ''));

                return str_contains($name, $needle) || str_contains($code, $needle);
            })->values();
        }

        return view('driver.stock.index', [
            'items' => $items,
            'search' => $search,
        ]);
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
