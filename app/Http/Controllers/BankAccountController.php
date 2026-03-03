<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->only(['free_text', 'status']);
        $bankAccounts = (new BankAccount())->getList($search);

        return view('backend.bank_account.index', compact('bankAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = BankAccount::STATUS;
        return view('backend.bank_account.create', compact('statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number',
            'bank_name' => 'required|string|max:255',
            'initial_balance' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $bankAccount = new BankAccount();
        $bankAccount->createBankAccount($request);

        return redirect()->route('bank_account.index')->with('success', 'Bank account created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load('transactions');
        return view('backend.bank_account.show', compact('bankAccount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        $statuses = BankAccount::STATUS;
        return view('bank_account.edit', compact('bankAccount', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        // Validate input
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number,' . $bankAccount->id,
            'bank_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $bankAccount->updateBankAccount($request, $bankAccount->id);

        return redirect()->route('bank_account.index')->with('success', 'Bank account updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        // Check if there are any transactions
        if ($bankAccount->transactions()->count() > 0) {
            return redirect()->route('bank_account.index')->with('error', 'Cannot delete account with existing transactions!');
        }

        $bankAccount->delete();
        return redirect()->route('bank_account.index')->with('success', 'Bank account deleted successfully!');
    }
}
