<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ];


    public function customerArea()
    {
        return $this->belongsTo(CustomerArea::class, 'area_id');
    }

    public function CustomerList($search = [], $is_paginate = true)
    {
        $query = self::query();

        if (!empty($search['area_id'])) {
            $query->where('area_id', $search['area_id']);
        }

        if (!empty($search['free_text'])) {
            $query->where('name', 'like', '%' . $search['free_text'] . '%');
        }

        if (!empty($search['status'])) {
            $query->where('status', $search['status']);
        }

        return $is_paginate ? $query->paginate(10) : $query->get();
    }

    public function createCustomer($request)
    {
        $this->name     = $request->name;
        $this->phone    = $request->phone;
        $this->email    = $request->email;
        $this->area_id  = $request->area_id;
        $this->address  = $request->address;

        $this->save();

        return $this;
    }

    public function getCustomerById($id)
    {
        return self::where('id', $id)->first();
    }

    public function findById($id)
    {
        return self::where('id', $id)->first();
    }

    public function updateCustomer($request, $id)
    {
        $customer = self::find($id);

        if (!$customer) {
            return false;
        }

        $customer->name     = $request->name;
        $customer->phone    = $request->phone;
        $customer->email    = $request->email;
        $customer->area_id  = $request->area_id;
        $customer->address  = $request->address;

        $customer->save();

        return $customer;
    }

    public function updateStatus($status)
    {
        $this->status = $status;
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
