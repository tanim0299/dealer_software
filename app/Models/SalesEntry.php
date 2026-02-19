<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesEntry extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function subUnit()
    {
        return $this->belongsTo(SubUnit::class,'sub_unit_id');
    }

    public function returnEntries()
    {
        return $this->hasMany(SalesReturnEntries::class, 'sales_entry_id');
    }
}
