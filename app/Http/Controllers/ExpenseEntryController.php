<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Services\IncomeExpenseService;
use App\Services\ExpenseEntryService;
use Illuminate\Support\Facades\Auth;

class ExpenseEntryController extends Controller
{
    protected $PATH = 'backend.expense_entry.';
    public function __construct()
    {
        $this->middleware(['permission:Expense Entry view'])->only(['index']);
        $this->middleware(['permission:Expense Entry create'])->only(['create']);
        $this->middleware(['permission:Expense Entry edit'])->only(['edit']);
        $this->middleware(['permission:Expense Entry destroy'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        [$status_code, $status_message, $response] = (new ExpenseEntryService())->ExpenseEntryList($data['search'], true);
        $data['data'] = $response;
        return view($this->PATH . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$status_code, $status_message, $expense] =
            (new IncomeExpenseService())->IncomeExpenseTitleList([
                'type' => 2   // ONLY Expense
            ], false);

        $data['status_code'] = $status_code;
        $data['status_message'] = $status_message;
        $data['expenses'] = $expense;

        if(Auth::user()->hasRole('Driver'))
        {
            return view('driver.expense.create');   
        }

        return view($this->PATH . '.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message, $error_message] = (new ExpenseEntryService())->storeExpenseEntry($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('expense_entry.index')
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
        [$status_code, $status_message, $expense_titles] = (new IncomeExpenseService())->IncomeExpenseTitleList(['type' => 2], false);
        $data['expenses'] = $expense_titles;
        [$status_code, $status_message, $expenseentry] = (new ExpenseEntryService())->getExpenseEntryById($id);
        $data['expenseentry'] = $expenseentry;

        return view($this->PATH . '.edit', $data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] =
            (new ExpenseEntryService())->updateExpenseEntry($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('expense_entry.index')
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
        [$status_code, $status_message] = (new ExpenseEntryService())->deleteExpenseEntry($id);

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
