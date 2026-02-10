<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeExpenseTitle extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUS = [
        self::STATUS_ACTIVE   => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    const TYPE_INCOME  = 1;
    const TYPE_EXPENSE = 2;

    const TYPE = [
        self::TYPE_INCOME  => 'Income',
        self::TYPE_EXPENSE => 'Expense',
    ];

    public function IncomeExpenseTitleList($search = [], $is_paginate = true)
    {
        $query = self::query();

        if (!empty($search['free_text'])) {
            $query->where('title', 'like', '%' . $search['free_text'] . '%');
        }

        if (!empty($search['status'])) {
            $query->where('status', $search['status']);
        }

        if (!empty($search['type'])) {
            $query->where('type', $search['type']);
        }

        return $is_paginate ? $query->paginate(10) : $query->get();
    }

    public function createIncomeExpenseTitle($request)
    {
        return self::create([
            'title'  => $request->title,
            'type'   => $request->type,
            'status' => $request->status ?? self::STATUS_ACTIVE,
        ]);
    }

    public function getIncomeExpenseTitleById($id)
    {
        return self::find($id);
    }

    public function findById($id)
    {
        return self::find($id);
    }

    public function updateIncomeExpenseTitle($request, $id)
    {
        $query = self::find($id);

        if (!$query) {
            return false;
        }

        $query->update([
            'title'  => $request->title,
            'type'   => $request->type,
            'status' => $request->status,
        ]);

        return $query;
    }

    public function updateStatus()
    {
        $this->status = $this->status === self::STATUS_ACTIVE
            ? self::STATUS_INACTIVE
            : self::STATUS_ACTIVE;

        return $this->save();
    }
}
