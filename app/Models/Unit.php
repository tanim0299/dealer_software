<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function UnitList($search = [], $is_paginate = true)
    {
        $query = self::query();
        if(!empty($search['free_text']))
        {
            $query = $query->where('name','like','%'.$search['free_text'].'%');
        }
        if(!empty($search['status']))
        {
            $query = $query->where('status',$search['status']);
        }
        if($is_paginate)
        {
            $query =  $query->paginate(10);
        }
        else
        {
            $query =  $query->get();
        }
        return $query;
    }

    public function createUnit($request)
    {
        $this->name = $request->name;
        $this->status = $request->status;
        $this->save();
        return $this;
    }

    public function getUnitById($id)
    {
        $query = self::query();
        $query = $query->where('id', $id)->first();
        return $query;
    }

    public function findById($id)
    {
        return self::where('id',$id)->first();
    }

    public function updateUnit($request, $id)
    {
        $query = self::find($id);

        if (!$query) {
            return false;
        }

        $query->name   = $request->name;
        $query->status = $request->status;

        $query->save();

        return $query;
    }

    public function deleteUnit($id)
    {
        $query = self::find($id);

        if (!$query) {
            return false;
        }

        return $query->delete();
    }

    public function updateStatus()
    {
        $this->status = $this->status == self::STATUS_ACTIVE
            ? self::STATUS_INACTIVE
            : self::STATUS_ACTIVE;

        return $this->save();
    }


}
