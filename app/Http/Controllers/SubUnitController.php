<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubUnitService;
use App\Services\UnitService;
use App\Services\ApiService;

class SubUnitController extends Controller
{
    protected $PATH = 'backend.sub_unit.';
    public function __construct()
    {
        $this->middleware(['permission:Sub Unit view'])->only(['index']);
        $this->middleware(['permission:Sub Unit create'])->only(['create']);
        $this->middleware(['permission:Sub Unit edit'])->only(['edit']);
        $this->middleware(['permission:Sub Unit destroy'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        [$status_code, $status_message, $response] = (new SubUnitService())->SubUnitList($data['search'], true);
        $data['data'] = $response;
        return view($this->PATH.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$status_code, $status_message, $unit] = (new UnitService())->UnitList([], false);
        $data['status_code'] = $status_code;
        $data['status_message'] = $status_message;
        $data['units'] = $unit;
        return view($this->PATH.'.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message , $error_message] = (new SubUnitService())->storeSubUnit($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('sub_unit.index')
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
        [$status_code, $status_message, $units] = (new UnitService())->UnitList([],false);
        $data['units'] = $units;
        [$status_code, $status_message, $subunit] = (new SubUnitService())->getSubUnitById($id);
        $data['subunit'] = $subunit;
        return view($this->PATH.'.edit',$data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] =
            (new SubUnitService())->updateSubUnit($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('sub_unit.index')
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
        [$status_code, $status_message] = (new SubUnitService())->deleteSubUnit($id);

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
        [$status_code, $status_message] = (new SubUnitService())->updateSubUnitStatus($request->id);

        return response()->json([
            'status_code' => $status_code,
            'status_message' => __($status_message),
            'message_type' => $status_code == ApiService::API_SUCCESS ? 'success' : 'error'
        ]);
    }
}
