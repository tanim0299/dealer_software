<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ExpenseEntry extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function expense()
    {
        return $this->belongsTo(IncomeExpenseTitle::class, 'title_id');
    }
// ExpenseEntry.php
public function driver()
{
    return $this->belongsTo(
        User::class,
        'driver_id',   // expense_entries.driver_id
        'driver_id'    // users.driver_id
    );
}

    public function ExpenseEntryList($search = [], $is_paginate = true)
    {
        $query = self::query();

        if (!empty($search['driver_id'])) {
            $query->where('driver_id', $search['driver_id']);
        }

        if (!empty($search['title_id'])) {
            $query->where('title_id', $search['title_id']);
        }

        if (!empty($search['amount'])) {
            $query->where('amount', $search['amount']);
        }

        if (!empty($search['from_date'])) {
            $query->whereDate('date', '>=', $search['from_date']);
        }

        if (!empty($search['to_date'])) {
            $query->whereDate('date', '<=', $search['to_date']);
        }

        if (!empty($search['free_text'])) {
            $query->where(function ($q) use ($search) {
                $q->where('amount', 'like', '%' . $search['free_text'] . '%')
                  ->orWhere('note', 'like', '%' . $search['free_text'] . '%');
            });
        }

        $query->with('expense')->latest('date')->latest('id');

        if ($is_paginate) {
            $query =  $query->paginate(10)->withQueryString();
        } else {
            $query =  $query->get();
        }
        return $query;
    }

    public function createExpenseEntry($request)
    {
        $this->date = $request->date;
        $this->title_id = $request->title_id;
        $this->amount = $request->amount;
        $this->note = $request->note;
        $this->driver_id = Auth::user()->driver_id ?? null;
        $this->save();
        return $this;
    }

    public function getExpenseEntryById($id)
    {
        $query = self::query();
        $query = $query->where('id', $id)->first();
        return $query;
    }

    public function findById($id)
    {
        return self::where('id', $id)->first();
    }

    public function updateExpenseEntry($request, $id)
    {
        $query = self::find($id);

        if (!$query) {
            return false;
        }

        $query->date = $request->date;
        $query->title_id = $request->title_id;
        $query->amount = $request->amount;
        $query->note = $request->note;
        $query->save();
        return $query;
    }
}
