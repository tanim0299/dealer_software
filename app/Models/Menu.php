<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

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

    public function menuSection()
    {
        return $this->belongsTo(MenuSection::class, 'menu_section_id');
    }   

    public function permissions()
    {
        return $this->hasMany(Permission::class,'parent','system_name');
    }

    public function getMenuList($search = [], $is_paginate = true, $is_relation = true)
    {
        $query = self::query();

        if (isset($search['free_text']) && $search['free_text'] != '') {
            $query->where('name', 'like', '%' . $search['free_text'] . '%')
            ->orWhere('system_name', 'like', '%' . $search['free_text'] . '%')
            ->orWhere('route', 'like', '%' . $search['free_text'] . '%')
            ->orWhere('slug', 'like', '%' . $search['free_text'] . '%')
            ->orWhere('icon', 'like', '%' . $search['free_text'] . '%');
        }

        if(!empty($search['parent_id']))
        {
            $query->where('parent_id',$search['parent_id']);
        }

        if(!empty($search['section_id']))
        {
            $query->where('menu_section_id',$search['section_id']);
        }

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

    public function createMenuWithPermission($request)
    {
        $preparedData = [
            'sl' => $request->sl,
            'name' => $request->name,
            'menu_section_id' => $request->section_id,
            'status' => $request->status,
            'type' => $request->type,
            'parent_id' => $request->type != self::TYPE_PARENT ? $request->parent_id : null,
            'system_name' => in_array($request->type, [self::TYPE_CHILD, self::TYPE_SINGLE]) ? $request->system_name : null,
            'route' => in_array($request->type, [self::TYPE_CHILD, self::TYPE_SINGLE]) ? $request->route : null,
            'slug' => in_array($request->type, [self::TYPE_CHILD, self::TYPE_SINGLE]) ? $request->slug : null,
            'icon' => $request->type != self::TYPE_CHILD ? $request->icon : null,
        ];
        $menu = self::create($preparedData);

        if($request->type != self::TYPE_PARENT)
        {
            Artisan::call('cache:forget spatie.permission.cache');
            for ($i = 0; $i < count($request->permissions); $i++) {
                Permission::create([
                    'name' => $request->system_name . ' ' . $request->permissions[$i],
                    'guard_name' => 'web',
                    'parent' => $request->system_name,
                ]);
            }
        }
        return $menu;
    }
    public function updateMenuWithPermissions($request, $id)
    {
        $preparedData = [
            'sl' => $request->sl,
            'name' => $request->name,
            'menu_section_id' => $request->section_id,
            'status' => $request->status,
            'type' => $request->type,
            'parent_id' => $request->type != self::TYPE_PARENT ? $request->parent_id : null,
            'system_name' => in_array($request->type, [self::TYPE_CHILD, self::TYPE_SINGLE]) ? $request->system_name : null,
            'route' => in_array($request->type, [self::TYPE_CHILD, self::TYPE_SINGLE]) ? $request->route : null,
            'slug' => in_array($request->type, [self::TYPE_CHILD, self::TYPE_SINGLE]) ? $request->slug : null,
            'icon' => $request->type != self::TYPE_CHILD ? $request->icon : null,
        ];
        $menu = self::where('id',$id)->update($preparedData);

        if($request->type != self::TYPE_PARENT)
        {
            Permission::where('parent',$request->system_name)->delete();
            Artisan::call('cache:forget spatie.permission.cache');
            for ($i = 0; $i < count($request->permissions); $i++) {
                Permission::create([
                    'name' => $request->system_name . ' ' . $request->permissions[$i],
                    'guard_name' => 'web',
                    'parent' => $request->system_name,
                ]);
            }
        }
        return $menu;
    }

    public function getMenuById($id)
    {
        return self::find($id);
    }

    public function deleteMenuById($id)
    {
        $menu = self::find($id);
        Permission::where('parent',$menu->system_name)->delete();
        Artisan::call('cache:forget spatie.permission.cache');
        $menu->delete();
        return;
    }
}
