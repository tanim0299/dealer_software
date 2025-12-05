<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = [];

    public function getRoleList($search = [], $is_paginate = true)
    {
        $query = self::query();
        if(!empty($search['free_text']))
        {
            $query = $query->where('name','like','%'.$search['free_text'].'%');
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

    public function createRole($request)
    {
        $this->index = $request->index;
        $this->name = $request->name;
        $this->guard_name = 'web';
        $this->save();
        return $this;
    }

    public function getRoleById($id)
    {
        return self::find($id);
    }

    public function findById($id)
    {
        return self::where('id',$id)->first();
    }

    public function updateRole($request, $id)
    {
        $query = self::find($id);

        if (!$query) {
            return false;
        }

        $query->index     = $request->index;
        $query->name   = $request->name;
        $query->guard_name = 'web';

        $query->save();

        return $query;
    }

    public function deleteRoleById($id)
    {
        $role = self::find($id);
        $role->delete();
        return;
    }
}
