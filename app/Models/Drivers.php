<?php

namespace App\Models;

use App\Services\DriverPeriodService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function employee()
    {
        return $this->hasOne(Employee::class, 'driver_id');
    }

    public function loginUser()
    {
        return $this->hasOne(User::class, 'driver_id');
    }

    public function cashCustomer()
    {
        return $this->belongsTo(Customer::class, 'cash_customer_id');
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
        return $this->getOpenPeriodDriverStock($driver_id);
    }

    /**
     * Stock from all accepted issues in the open period (last closing + 1 day through today).
     */
    public function getOpenPeriodDriverStock($driver_id = null)
    {
        $driverId = $driver_id ?? Auth::user()->driver_id;
        if (! $driverId) {
            return collect();
        }

        $period = DriverPeriodService::periodForDriver((int) $driverId);

        return DriverIssueItem::query()
            ->with(['product'])
            ->whereHas('driverIssue', function ($query) use ($driverId, $period) {
                $query->where('driver_id', $driverId)
                    ->where('status', 'accepted')
                    ->whereDate('issue_date', '>=', $period['start_date'])
                    ->whereDate('issue_date', '<=', $period['end_date']);
            })
            ->get()
            ->groupBy('product_id')
            ->map(function ($rows) {
                $r0 = $rows->first();
                $merged = $r0->replicate();
                $merged->exists = false;
                $merged->issue_qty = $rows->sum(fn ($r) => (float) $r->issue_qty);
                $merged->sold_qty = $rows->sum(fn ($r) => (float) $r->sold_qty);
                $merged->return_qty = $rows->sum(fn ($r) => (float) $r->return_qty);
                $merged->setRelation('product', $r0->product);

                return $merged;
            })
            ->filter(fn ($row) => ($row->issue_qty - $row->sold_qty + $row->return_qty) > 0)
            ->sortBy(fn ($row) => mb_strtolower((string) ($row->product->name ?? '')))
            ->values();
    }

    public function getCurrentDriverStock($driver_id = null)
    {
        $driverId = $driver_id ?? Auth::user()->driver_id;
        if (! $driverId) {
            return collect();
        }

        $period = DriverPeriodService::periodForDriver((int) $driverId);

        return DriverIssueItem::query()
            ->select(
                'product_id',
                DB::raw('SUM(issue_qty) as issue_qty'),
                DB::raw('SUM(sold_qty) as sold_qty'),
                DB::raw('SUM(return_qty) as return_qty')
            )
            ->whereHas('driverIssue', function ($query) use ($driverId, $period) {
                $query->where('driver_id', $driverId)
                    ->where('status', 'accepted')
                    ->whereDate('issue_date', '>=', $period['start_date'])
                    ->whereDate('issue_date', '<=', $period['end_date']);
            })
            ->with('product')
            ->groupBy('product_id')
            ->get()
            ->filter(function ($item) {
                return ($item->issue_qty - $item->sold_qty + $item->return_qty) > 0;
            })
            ->values();
    }
}
