<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Drivers extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];


    public function issues()
    {
        return $this->hasMany(DriverIssues::class);
    }

    public function customerAreas()
    {
        return $this->belongsToMany(CustomerArea::class);
    }

    public function areas()
    {
        return $this->belongsToMany(
            CustomerArea::class,
            'driver_areas',
            'driver_id',
            'area_id'
        );
    }

    public function getrDriverList($search = [], $is_paginate = true, $is_relation = false)
    {
        $query = self::query();

        if (!empty($search['free_text'])) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search['free_text'] . '%')
                    ->orWhere('phone', 'like', '%' . $search['free_text'] . '%')
                    ->orWhere('vehicle_no', 'like', '%' . $search['free_text'] . '%');
            });
        }

        if ($is_paginate) {
            return $query->paginate(15);
        } else {
            return $query->get();
        }
    }

    public function createDriver($request)
    {
        $this->name       = $request->name;
        $this->phone      = $request->phone ?? null;
        $this->vehicle_no = $request->vehicle_no ?? null;
        $this->address    = $request->address ?? null;
        $this->status     = $request->status ?? 'active';
        $this->save();

        return $this;
    }

    public function updateDriver($request)
    {
        $this->name       = $request->name;
        $this->phone      = $request->phone ?? null;
        $this->vehicle_no = $request->vehicle_no ?? null;
        $this->address    = $request->address ?? null;
        $this->status     = $request->status ?? 'active';
        $this->save();

        return $this;
    }

    public function getDriverById($id)
    {
        $query = self::find($id);

        return $query;
    }

    public function getTodayDriverStock($driver_id = null)
    {
        $driverId = $driver_id ?? Auth::user()->driver_id;

        $issue = DriverIssues::with(['items.product'])
            ->where('driver_id', $driverId)
            ->whereDate('issue_date', now()->toDateString())
            ->where('status', 'accepted')
            ->first();

        $items = collect();

        if ($issue) {
            $items = $issue->items;
        }

        return $items;
    }
}
