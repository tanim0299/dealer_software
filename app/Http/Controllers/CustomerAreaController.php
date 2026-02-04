<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\AreaService;
use Illuminate\Http\Request;

class CustomerAreaController extends Controller
{
    protected $PATH = 'backend.customer_area.';
    public function __construct()
    {
        $this->middleware(['permission:Customer Area view'])->only(['index']);
        $this->middleware(['permission:Customer Area create'])->only(['create']);
        $this->middleware(['permission:Customer Area edit'])->only(['edit']);
        $this->middleware(['permission:Customer Area destroy'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        [$status_code, $status_message, $response] = (new AreaService())->CustomerAreaList($data['search'], true);
        $data['data'] = $response;
        return view($this->PATH.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view($this->PATH.'create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message , $error_message] = (new AreaService())->storeCustomerArea($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('customer_area.index')
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
        [$status_code, $status_message, $data] = (new AreaService())->getCustomerAreaById($id);
        $data['data'] = $data;
        return view($this->PATH.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] =
            (new AreaService())->updateCustomerArea($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('customer_area.index')
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->withInput()
            ->withErrors($error_message)
            ->with('error', $status_message);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new AreaService())->deleteCustomerArea($id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->back()
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->with('error', $status_message);
    }

    public function status(Request $request)
    {
        [$status_code, $status_message] = (new AreaService())->updateCustomerAreaStatus($request->id);

        return response()->json([
            'status_code' => $status_code,
            'status_message' => __($status_message),
            'message_type' => $status_code == ApiService::API_SUCCESS ? 'success' : 'error'
        ]);
    }
}
