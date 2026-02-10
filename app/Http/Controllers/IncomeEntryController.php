<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use App\Services\IncomeExpenseService;
use App\Services\IncomeEntryService;

class IncomeEntryController extends Controller
{
    protected $PATH = 'backend.income_entry.';
    public function __construct()
    {
        $this->middleware(['permission:Income Entry view'])->only(['index']);
        $this->middleware(['permission:Income Entry create'])->only(['create']);
        $this->middleware(['permission:Income Entry edit'])->only(['edit']);
        $this->middleware(['permission:Income Entry destroy'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['search']['free_text'] = $request->free_text ?? '';
        [$status_code, $status_message, $response] = (new IncomeEntryService())->IncomeEntryList($data['search'], true);
        $data['data'] = $response;
        return view($this->PATH . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        [$status_code, $status_message, $income] =
            (new IncomeExpenseService())->IncomeExpenseTitleList([
                'type' => 1   // ONLY Income
            ], false);

        $data['status_code'] = $status_code;
        $data['status_message'] = $status_message;
        $data['incomes'] = $income;

        return view($this->PATH . '.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message, $error_message] = (new IncomeEntryService())->storeIncomeEntry($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('income_entry.index')
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
        [$status_code, $status_message, $income_titles] = (new IncomeExpenseService())->IncomeExpenseTitleList(['type' => 1], false);
        $data['incomes'] = $income_titles;
        [$status_code, $status_message, $incomeentry] = (new IncomeEntryService())->getIncomeEntryById($id);
        $data['incomeentry'] = $incomeentry;

        return view($this->PATH . '.edit', $data);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] = (new IncomeEntryService())->updateIncomeEntry($request, $id);

        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('income_entry.index')
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
        [$status_code, $status_message] = (new IncomeEntryService())->deleteIncomeEntry($id);

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
