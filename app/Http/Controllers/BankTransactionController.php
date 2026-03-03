<?php

namespace App\Http\Controllers;

use App\Models\BankTransaction;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->only(['bank_account_id', 'type', 'start_date', 'end_date']);
        $bankAccounts = BankAccount::where('status', 'active')->get();

        // If specific account selected, show its transactions
        if (!empty($search['bank_account_id'])) {
            $transactions = (new BankTransaction())->getTransactionsByAccount(
                $search['bank_account_id'],
                $search
            );
        } else {
            $transactions = BankTransaction::orderBy('transaction_date', 'DESC')->paginate(10);
        }

        return view('backend.bank_transaction.index', compact('transactions', 'bankAccounts', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bankAccounts = BankAccount::where('status', 'active')->get();
        $types = BankTransaction::TYPES;
        return view('backend.bank_transaction.create', compact('bankAccounts', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'type' => 'required|in:deposit,withdraw',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'transaction_date' => 'nullable|date',
        ]);

        $transaction = new BankTransaction();
        $result = $transaction->createTransaction($request);

        // Check if transaction was successful
        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message'])->withInput();
        }

        return redirect()->route('bank_transaction.index')->with('success', 'Transaction created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankTransaction $bankTransaction)
    {
        $bankTransaction->load('bankAccount');
        return view('backend.bank_transaction.show', compact('bankTransaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankTransaction $bankTransaction)
    {
        // Note: We don't allow editing of existing transactions
        // For data integrity in financial records
        return redirect()->route('bank_transaction.index')->with('info', 'Transactions cannot be edited. Please delete and create a new one.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankTransaction $bankTransaction)
    {
        return redirect()->route('bank_transaction.index')->with('info', 'Transactions cannot be edited for audit purposes.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankTransaction $bankTransaction)
    {
        $bankAccount = $bankTransaction->bankAccount;

        // Reverse the transaction
        if ($bankTransaction->type === BankTransaction::TYPE_DEPOSIT) {
            $newBalance = $bankAccount->balance - $bankTransaction->amount;
        } else {
            $newBalance = $bankAccount->balance + $bankTransaction->amount;
        }

        // Update account balance
        $bankAccount->balance = $newBalance;
        $bankAccount->save();

        // Delete transaction
        $bankTransaction->delete();

        return redirect()->route('bank_transaction.index')->with('success', 'Transaction deleted and balance reversed successfully!');
    }

    /**
     * Get bank account balance via AJAX
     */
    public function getAccountBalance($id)
    {
        $bankAccount = BankAccount::find($id);

        if (!$bankAccount) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        return response()->json([
            'balance' => $bankAccount->balance,
            'account_name' => $bankAccount->account_name,
        ]);
    }
}
