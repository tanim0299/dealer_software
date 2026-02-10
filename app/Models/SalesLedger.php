<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesLedger extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(SalesEntry::class,'ledger_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function getSalesList($search = [], $is_paginate = true, $is_relation = true)
    {
        $query = self::query();

        // Eager load relations if needed
        if ($is_relation) {
            $query->with(['customer', 'items.product', 'items.subUnit']);
        }

        // Filter by driver
        if (!empty($search['driver_id'])) {
            $query->where('driver_id', $search['driver_id']);
        }

        // Filter by free text (invoice no or customer name)
        if (!empty($search['free_text'])) {
            $free_text = $search['free_text'];
            $query->where(function($q) use ($free_text) {
                $q->where('invoice_no', 'like', "%{$free_text}%")
                  ->orWhereHas('customer', function($qc) use ($free_text) {
                      $qc->where('name', 'like', "%{$free_text}%");
                  });
            });
        }

        // Filter by date range
        if (!empty($search['from_date'])) {
            $query->whereDate('date', '>=', $search['from_date']);
        }

        if (!empty($search['to_date'])) {
            $query->whereDate('date', '<=', $search['to_date']);
        }

        // Order latest first
        $query->orderBy('date', 'desc')->orderBy('id', 'desc');

        // Return paginated or full list
        return $is_paginate ? $query->paginate(20) : $query->get();
    }
}
