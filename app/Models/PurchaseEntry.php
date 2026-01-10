<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseEntry extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function sub_unit()
    {
        return $this->belongsTo(SubUnit::class,'sub_unit_id');
    }
    
}
