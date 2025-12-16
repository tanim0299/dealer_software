<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function Item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function CategoryList($search = [], $is_paginate = true)
    {
        $query = self::query();
        if(!empty($search['item_id']))
        {
            $query = $query->where('item_id',$search['item_id']);
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

    public function createCategory($request)
    {
        $this->sl = $request->sl;
        $this->name = $request->name;
        $this->item_id = $request->item_id;
        $this->status = $request->status;
        $this->save();
        return $this;
    }

    public function getCategoryById($id)
    {
        $query = self::query();
        $query = $query->where('id', $id)->first();
        return $query;
    }

    public function findById($id)
    {
        return self::where('id',$id)->first();
    }

    public function updateCategory($request, $id)
    {
        $query = self::find($id);

        if (!$query) {
            return false;
        }

        $query->sl = $request->sl;
        $query->name = $request->name;
        $query->item_id = $request->item_id;
        $query->status = $request->status;
        $query->save();
        return $query;
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        $this->save();
        return $this;
    }
}
