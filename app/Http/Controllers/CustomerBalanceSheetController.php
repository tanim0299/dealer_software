<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\WebsiteSettings;
use App\Services\CustomerService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'report_type' => 'required|in:daily,date_to_date,monthly,yearly',
            'daily_date' => 'exclude_unless:report_type,daily|required|date_format:Y-m-d',
            'from_date' => 'exclude_unless:report_type,date_to_date|required|date_format:Y-m-d',
            'to_date' => 'exclude_unless:report_type,date_to_date|required|date_format:Y-m-d|after_or_equal:from_date',
            'month' => 'exclude_unless:report_type,monthly|required|regex:/^\d{4}-\d{2}$/',
            'year' => 'exclude_unless:report_type,yearly|required|integer|min:1990|max:2100',
        ]);

        $data['report_type'] = $validated['report_type'];

        if ($validated['report_type'] === 'daily') {
            $data['first_date'] = $validated['daily_date'];
            $data['date'] = $validated['daily_date'];
            $data['report_title'] = 'Daily Customer Balance Sheet Report for '.Carbon::createFromFormat('Y-m-d', $validated['daily_date'])->format('d M Y');
        } elseif ($validated['report_type'] === 'date_to_date') {
            $data['first_date'] = $validated['from_date'];
            $data['from_date'] = $validated['from_date'];
            $data['to_date'] = $validated['to_date'];
            $data['report_title'] = 'Date to Date Customer Balance Sheet Report from '.Carbon::createFromFormat('Y-m-d', $validated['from_date'])->format('d M Y').' to '.Carbon::createFromFormat('Y-m-d', $validated['to_date'])->format('d M Y');
        } elseif ($validated['report_type'] === 'monthly') {
            $data['first_date'] = $validated['month'].'-01';
            $data['month'] = $validated['month'];
            $data['report_title'] = 'Monthly Customer Balance Sheet Report for '.Carbon::createFromFormat('Y-m', $validated['month'])->format('F Y');
        } else {
            $data['first_date'] = $validated['year'].'-01-01';
            $data['year'] = $validated['year'];
            $data['report_title'] = 'Yearly Customer Balance Sheet Report for '.Carbon::createFromFormat('Y', (string) $validated['year'])->format('Y');
        }

        $data['initial_date'] = '2000-01-01';
        $data['previous_date'] = Carbon::createFromFormat('Y-m-d', $data['first_date'])
            ->subDay()
            ->format('Y-m-d');

        $data['previous_balance'] = (new CustomerService())->getCustomerDueByIdWithDateRange(
            $validated['customer_id'],
            $data['initial_date'],
            $data['previous_date']
        );

        $data['customer_id'] = $validated['customer_id'];
        $data['customer'] = Customer::with('customerArea')->find($validated['customer_id']);
        $data['settings'] = WebsiteSettings::query()->first();
        $data['items'] = (new CustomerService())->getCustomerData($data);

        return view($this->path.'.print', $data);
    }
}
