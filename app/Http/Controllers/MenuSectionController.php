<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use App\Services\MenuSectionService;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class MenuSectionController extends Controller
{
    protected $PATH = 'backend.menu_section.';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        [$status_code, $status_message, $response] = (new MenuSectionService())->MenuSectionList($data['search'], true);
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
        [$status_code, $status_message , $error_message] = (new MenuSectionService())->storeMenuSection($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('menu_section.index')
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
        [$status_code, $status_message, $data] = (new MenuSectionService())->getMenuSectionById($id);
        $data['data'] = $data;
        return view($this->PATH.'.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] =
            (new MenuSectionService())->updateMenuSection($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('menu_section.index')
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
        [$status_code, $status_message] = (new MenuSectionService())->deleteMenuSection($id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->back()
                ->with('success', $status_message);
        }

        return redirect()
            ->back()
            ->with('error', $status_message);
    }

}
