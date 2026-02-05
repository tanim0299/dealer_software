<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\DriverService;
use App\Services\AreaService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    protected $path = 'backend.driver';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['free_text'] = $request->input('free_text', '');
        $data['search']['status'] = $request->input('status', '');
        $data['drivers'] = (new DriverService())->getDriverList($data['search'], true, true)[2];
        return view($this->path . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$status_code, $status_message, $customer_area] = (new AreaService())->CustomerAreaList([], false);
        $data['status_code'] = $status_code;
        $data['status_message'] = $status_message;
        $data['customer_areas'] = $customer_area;
        return view($this->path.'.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message, $error_message] = (new DriverService())->storeDriver($request);
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
        [$status_code, $status_message, $customer_areas] = (new AreaService())->CustomerAreaList([],false);
        $data['customer_areas'] = $customer_areas;
        [$status_code, $status_message, $driver] = (new DriverService())->getDriverById($id);
        $data['data'] = $driver;
        return view($this->path.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] = (new DriverService())->updateDriver($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('driver.index')
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
        [$status_code, $status_message] = (new DriverService())->deleteDriver($id);

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
        [$status_code, $status_message] = (new DriverService())->updateDriverStatus($request->id);

        return response()->json([
            'status_code' => $status_code,
            'status_message' => __($status_message),
            'message_type' => $status_code == ApiService::API_SUCCESS ? 'success' : 'error'
        ]);
    }
}
