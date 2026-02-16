<?php

namespace App\Http\Controllers;

use App\Models\DriverIssues;
use App\Models\Product;
use App\Services\ApiService;
use App\Services\DriverIssueService;
use App\Services\DriverService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverIssueController extends Controller
{
    protected $path = 'backend.driver_issues';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Driver'))
        {
            $data['search']['driver_id'] = Auth::user()->driver_id ?? null;
        }
        $data['search']['free_text'] = $request->get('free_text', '');
        $data['issues'] =  (new DriverIssueService())->getDriverIssueList($data['search'], true, false)[2];
        if(Auth::user()->hasRole('Driver'))
        {
            return view('driver.issues.index',$data);   
        }
        return view($this->path.'.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['drivers'] = (new DriverService())->getDriverList([],false,false)[2];
        $data['products'] = (new StockService())->getWarehouseStocks([],false,true)[2];
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
                ->route('driver-issues.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->withErrors($error_message)->with('error', $status_message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['issue'] = (new DriverIssueService())->getIssueDataById($id)[2];
        return view($this->path.'.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['drivers'] = (new DriverService())->getDriverList([],false,false)[2];
        $data['products'] = (new StockService())->getWarehouseStocks([],false,true)[2];
        $data['issue'] = (new DriverIssueService())->getIssueDataById($id)[2];
        if ($data['issue']->status !== 'open') {
            return redirect()
                ->route('driver-issues.index')
                ->with('error', 'Closed issue cannot be edited');
        }
        return view($this->path.'.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] = (new DriverIssueService())->updateDriverIssueById($request,$id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('driver-issues.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->withErrors($error_message)->with('error', $status_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new DriverIssueService())->deleteIssuebyId($id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('driver-issues.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->with('error', $status_message);
    }

    public function accept($id)
    {
        $issue = DriverIssues::findOrFail($id);
        $issue->status = 'accepted';
        $issue->save();

        return back()->with('success', 'Issue accepted successfully');
    }

    public function reject($id)
    {
        $issue = DriverIssues::findOrFail($id);
        $issue->status = 'rejected';
        $issue->save();

        return back()->with('success', 'Issue rejected successfully');
    }

}
