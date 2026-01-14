<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WareHouseStocks extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function getStockList($search = [], $is_paginate = true, $is_relation = false)
    {
        $query = self::query()
            ->select('*')
            ->selectRaw('
                (purchase_qty + sales_return_qty) 
                - (sales_qty + return_qty) 
                AS available_qty
            ');

        if(!empty($search['free_text']))
        {
            $query = $query->where('product_id','like','%'.$search['free_text'].'%')
                    ->orWhereHas('product',function ($q, $search) {
                        $q->where('name','like','%'.$search['free_text'].'%');
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
