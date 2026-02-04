<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WareHouseStocks extends Model
{
    protected $guarded = [];

    protected $appends = ['stock_qty'];

    public function getStockQtyAttribute()
    {
        return ($this->purchase_qty + $this->sales_return_qty)
               - ($this->sales_qty + $this->return_qty + $this->sr_issue_qty);
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function getStockList($search = [], $is_paginate = true, $is_relation = false)
    {
        $query = self::query()
        ->select(
            'product_id',
            DB::raw('SUM(purchase_qty) as purchase_qty'),
            DB::raw('SUM(sales_qty) as sales_qty'),
            DB::raw('SUM(sales_return_qty) as sales_return_qty'),
            DB::raw('SUM(return_qty) as return_qty'),
            DB::raw('SUM(sr_issue_qty) as sr_issue_qty'),
            DB::raw('
                (SUM(purchase_qty) + SUM(sales_return_qty))
                - (SUM(sales_qty) + SUM(return_qty) + SUM(sr_issue_qty))
                AS available_qty
            ')
        )
        ->groupBy('product_id');


        if (!empty($search['free_text'])) {
            $query->where(function ($q) use ($search) {
                $q->where('product_id', 'like', '%' . $search['free_text'] . '%')
                ->orWhereHas('product', function ($p) use ($search) {
                    $p->where('name', 'like', '%' . $search['free_text'] . '%');
                });
            });
        }


        if($is_paginate)
        {
            $query = $query->paginate(10);   
        }
        else
        {
            $query = $query->get();
        }

        return $query;
    }
}
