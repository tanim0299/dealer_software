<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $guarded = [];

    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';

    const TYPES = [
        self::TYPE_DEPOSIT => 'Deposit',
        self::TYPE_WITHDRAW => 'Withdraw',
    ];

    /**
     * Get the bank account this transaction belongs to
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    /**
     * Get transactions for a specific account
     */
    public function getTransactionsByAccount($account_id, $search = [], $is_paginate = true)
    {
        $query = self::where('bank_account_id', $account_id);

        if (!empty($search['type'])) {
            $query->where('type', $search['type']);
        }

        if (!empty($search['start_date'])) {
            $query->where('transaction_date', '>=', $search['start_date']);
        }

        if (!empty($search['end_date'])) {
            $query->where('transaction_date', '<=', $search['end_date']);
        }

        $query->orderBy('transaction_date', 'DESC');

        if ($is_paginate) {
            return $query->paginate(10);
        } else {
            return $query->get();
        }
    }

    /**
     * Create a new transaction (deposit or withdraw)
     * Validates balance on withdrawal
     */
    public function createTransaction($request)
    {
        $bankAccount = BankAccount::find($request->bank_account_id);

        if (!$bankAccount) {
            return ['success' => false, 'message' => 'Bank account not found'];
        }

        // Validate withdrawal amount
        if ($request->type === self::TYPE_WITHDRAW && $request->amount > $bankAccount->balance) {
            return [
                'success' => false,
                'message' => 'Insufficient balance. Current balance: ' . $bankAccount->balance . '. Requested withdrawal: ' . $request->amount
            ];
        }

        // Get previous balance
        $previousBalance = $bankAccount->balance;

        // Calculate new balance
        if ($request->type === self::TYPE_DEPOSIT) {
            $newBalance = $previousBalance + $request->amount;
        } else {
            $newBalance = $previousBalance - $request->amount;
        }

        // Create transaction
        $transaction = self::create([
            'bank_account_id' => $request->bank_account_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'previous_balance' => $previousBalance,
            'new_balance' => $newBalance,
            'description' => $request->description ?? null,
            'transaction_date' => $request->transaction_date ?? now()->toDateString(),
        ]);

        // Update account balance
        $bankAccount->balance = $newBalance;
        $bankAccount->save();

        return ['success' => true, 'transaction' => $transaction];
    }

    /**
     * Get transaction by ID
     */
    public function getTransactionById($id)
    {
        return self::findOrFail($id);
    }
}
