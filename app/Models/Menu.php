<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const TYPE_PARENT = 1;
    const TYPE_CHILD = 2;
    const TYPE_SINGLE = 3;

    const TYPE = [
        self::TYPE_PARENT => 'Parent Menu',
        self::TYPE_CHILD => 'Child Menu',
        self::TYPE_SINGLE => 'Single Menu',
    ];

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    public function getMenuList($search = [], $is_paginate = true, $is_relation = true)
    {
        $query = self::query();

        if (isset($search['status']) && $search['status'] != '') {
            $query->where('status', $search['status']);
        }

        if (isset($search['type']) && $search['type'] != '') {
            $query->where('type', $search['type']);
        }

        if ($is_relation) {
            $query->with('parent', 'children');
        }

        if ($is_paginate) {
            return $query->paginate(15);
        } else {
            return $query->get();
        }
    }
}
