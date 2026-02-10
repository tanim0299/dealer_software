<?php

namespace App\Http\Controllers;

use App\Models\WebsiteSettings;
use Illuminate\Http\Request;
use App\Services\GlobalSetting;
use App\Services\ApiService;
use Brian2694\Toastr\Facades\Toastr;

class WebsiteSettingsController extends Controller
{
    protected $PATH = 'backend.website_settings.';
    public function __construct()
    {
        $this->middleware(['permission:Website Settings view'])->only(['index']);
        $this->middleware(['permission:Website Settings create'])->only(['create']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        [$status_code, $status_message, $settingData] = (new GlobalSetting)->getWebsiteSettings();
        $data['settings'] = $settingData;
        return view($this->PATH . '.index', $data);
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
    public function show(WebsiteSettings $websiteSettings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WebsiteSettings $websiteSettings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        [$status_code, $status_message, $error_message] =
            (new GlobalSetting())->updateWebsiteSettings($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('website_settings.index')
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
    public function destroy(WebsiteSettings $websiteSettings)
    {
        //
    }
}
