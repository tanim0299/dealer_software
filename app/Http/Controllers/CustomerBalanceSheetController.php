<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerBalanceSheetController extends Controller
{
    protected $path = 'backend.customer_balance_sheet';
    
    public function __construct()
    {
        $this->middleware(['permission:Customer Balance Sheet view'])->only(['index']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['customers'] = Customer::orderBy('name')->get();
        return view($this->path.'.index', $data);
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
     * Show the form for editing the resource.
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

    /**
     * Print balance sheet report
     */
    public function print(Request $request)
    {
        $data['report_type'] = $request->report_type;
        
        if($request->report_type == 'daily') {
            $data['first_date'] = $request->daily_date; 
            $data['date'] = $request->daily_date;
            $data['report_title'] = 'Daily Customer Balance Sheet Report for '.Carbon::createFromFormat('Y-m-d', $request->daily_date)->format('d M Y'); 
        }
        elseif($request->report_type == 'date_to_date') {
            $data['first_date'] = $request->from_date; 
            $data['from_date'] = $request->from_date;
            $data['to_date'] = $request->to_date; 
            $data['report_title'] = 'Date to Date Customer Balance Sheet Report from '.Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d M Y').' to '.Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d M Y');
        }
        elseif($request->report_type == 'monthly') {
            $data['first_date'] = $request->month.'-01';
            $data['month'] = $request->month;
            $data['report_title'] = 'Monthly Customer Balance Sheet Report for '.Carbon::createFromFormat('Y-m', $request->month)->format('F Y');
        }
        elseif($request->report_type == 'yearly') {
            $data['first_date'] = $request->year.'-01-01';
            $data['year'] = $request->year;
            $data['report_title'] = 'Yearly Customer Balance Sheet Report for '.Carbon::createFromFormat('Y', $request->year)->format('Y');
        }   
        
        $data['initial_date'] = '2000-01-01';
        $data['previous_date'] = Carbon::createFromFormat('Y-m-d', $data['first_date'])
                                ->subDay()
                                ->format('Y-m-d');
        
        $data['previous_balance'] = (new CustomerService())->getCustomerDueByIdWithDateRange($request->customer_id, $data['initial_date'], $data['previous_date']);

        $data['customer_id'] = $request->customer_id ?? null;
        $data['customer'] = Customer::find($request->customer_id);

        $data['items'] = (new CustomerService())->getCustomerData($data);

        return view($this->path.'.print', $data);
    }
}
