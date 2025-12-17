<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->supplier_id)) {
                $lastSupplier = self::orderBy('id', 'desc')->first();
                $lastNumber = $lastSupplier
                    ? (int) str_replace('S-', '', $lastSupplier->supplier_id)
                    : 0;
                $supplier->supplier_id = 'S-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function getSupplierList($search = [], $is_paginate = true, $is_relation = true)
    {
        $query = self::query();
        if(!empty($search['free_text']))
        {
            $query->where('name','like','%'.$search['free_text'].'%')
                ->orWhere('phone','like','%'.$search['free_text'].'%')
                ->orWhere('email','like','%'.$search['free_text'].'%');
        }
        if($is_paginate)
        {
            $query = $query->paginate(10);
        }
        else
        {
            $query = $query->get();
        }

        return $query;
    }

    public function storeSupplier($request)
    {
        $preparedData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->email,
        ];
        self::create($preparedData);
        return;
    }
    public function updateSupplierById($request,$id)
    {
        $preparedData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->email,
        ];
        self::where('id',$id)->update($preparedData);
        return;
    }
}
