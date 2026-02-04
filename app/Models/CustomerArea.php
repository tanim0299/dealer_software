<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerArea extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ];

    public function CustomerAreaList($search = [], $is_paginate = true)
    {
        $query = self::query();

        if (!empty($search['free_text'])) {
            $query->where('name', 'like', '%' . $search['free_text'] . '%');
        }

        if (!empty($search['status'])) {
            $query->where('status', $search['status']);
        }

        return $is_paginate ? $query->paginate(10) : $query->get();
    }

    public function createCustomerArea($request)
    {
        $this->name   = $request->name;
        $this->status = $request->status;

        $this->save();

        return $this;
    }

    public function getCustomerAreaById($id)
    {
        return self::where('id', $id)->first();
    }

    public function findById($id)
    {
        return self::where('id', $id)->first();
    }

    public function updateCustomerArea($request, $id)
    {
        $area = self::find($id);

        if (!$area) {
            return false;
        }

        $area->name   = $request->name;
        $area->status = $request->status;

        $area->save();

        return $area;
    }

    public function updateStatus()
    {
        $this->status = $this->status == self::STATUS_ACTIVE
            ? self::STATUS_INACTIVE
            : self::STATUS_ACTIVE;

        $this->save();

        return $this;
    }
    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
            }
        });
    }
}
