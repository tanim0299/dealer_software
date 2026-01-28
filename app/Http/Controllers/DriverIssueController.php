<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ApiService;
use App\Services\DriverIssueService;
use App\Services\DriverService;
use Illuminate\Http\Request;

class DriverIssueController extends Controller
{
    protected $path = 'backend.driver_issues';
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
        $data['drivers'] = (new DriverService())->getDriverList([],false,false)[2];
        $data['products'] = Product::with('warehouseStock')->get();
        return view($this->path.'.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message , $error_message] = (new DriverIssueService())->storeDriverIssue($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('driver.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->withErrors($error_message)->with('error', $status_message);
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
