<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    /**
     * Get all transactions for this bank account
     */
    public function transactions()
    {
        return $this->hasMany(BankTransaction::class, 'bank_account_id');
    }

    /**
     * Get list of bank accounts
     */
    public function getList($search = [], $is_paginate = true)
    {
        $query = self::query();

        if (!empty($search['free_text'])) {
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', '%' . $search['free_text'] . '%')
                    ->orWhere('account_number', 'like', '%' . $search['free_text'] . '%')
                    ->orWhere('bank_name', 'like', '%' . $search['free_text'] . '%');
            });
        }

        if (!empty($search['status'])) {
            $query->where('status', $search['status']);
        }

        if ($is_paginate) {
            return $query->paginate(10);
        } else {
            return $query->get();
        }
    }

    /**
     * Create a new bank account
     */
    public function createBankAccount($request)
    {
        $this->account_name = $request->account_name;
        $this->account_number = $request->account_number;
        $this->bank_name = $request->bank_name;
        $this->balance = $request->initial_balance ?? 0;
        $this->status = $request->status ?? self::STATUS_ACTIVE;
        $this->description = $request->description ?? null;
        $this->save();

        return $this;
    }

    /**
     * Update bank account
     */
    public function updateBankAccount($request, $id)
    {
        $account = self::find($id);

        if (!$account) {
            return false;
        }

        $account->account_name = $request->account_name;
        $account->account_number = $request->account_number;
        $account->bank_name = $request->bank_name;
        $account->status = $request->status;
        $account->description = $request->description ?? null;
        $account->save();

        return $account;
    }

    /**
     * Get account by ID
     */
    public function getAccountById($id)
    {
        return self::findOrFail($id);
    }
}
