<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drivers extends Model
{
    protected $guarded = [];

    public function issues()
    {
        return $this->hasMany(DriverIssues::class);
    }

    public function getrDriverList($search = [], $is_paginate = true, $is_relation = false)
    {
        $query = self::query();

        if(!empty($search['free_text']))
        {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%'.$search['free_text'].'%')
                  ->orWhere('phone', 'like', '%'.$search['free_text'].'%')
                  ->orWhere('vehicle_no', 'like', '%'.$search['free_text'].'%');
            });
        }

        if($is_paginate)
        {
            return $query->paginate(15);
        }
        else
        {
            return $query->get();
        }
    }

    public function createDriver($request)
    {
        
        $this->name = $request->name;
        $this->phone =  $request->phone ?? null;
        $this->vehicle_no = $request->vehicle_no ?? null;
        $this->address =  $request->address ?? null;
        $this->status = $request->status ?? 'active';
        $this->save();
    }
}
