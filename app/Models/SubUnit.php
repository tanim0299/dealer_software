<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubUnit extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function Unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function SubUnitList($search = [], $is_paginate = true)
    {
        $query = self::query();
        if(!empty($search['unit_id']))
        {
            $query = $query->where('unit_id',$search['unit_id']);
        }
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

    public function createSubUnit($request)
    {
        $this->unit_id = $request->unit_id;
        $this->order_by = $request->order_by;
        $this->name = $request->name;
        $this->unit_data = $request->unit_data;
        $this->status = $request->status;
        $this->save();
        return $this;
    }

    public function getSubUnitById($id)
    {
        $query = self::query();
        $query = $query->where('id', $id)->first();
        return $query;
    }

    public function findById($id)
    {
        return self::where('id',$id)->first();
    }

    public function updateSubUnit($request, $id)
    {
        $query = self::find($id);

        if (!$query) {
            return false;
        }

        $query->unit_id = $request->unit_id;
        $query->order_by = $request->order_by;
        $query->name = $request->name;
        $query->unit_data = $request->unit_data;
        $query->status = $request->status;
        $query->save();
        return $query;
    }

    public function deleteSubUnit($id)
    {
        $query = self::find($id);

        if (!$query) {
            return false;
        }

        return $query->delete();
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        $this->save();
        return $this;
    }
}
