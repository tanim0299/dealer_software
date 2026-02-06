<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverIssueItem extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function driverIssue()
    {
        return $this->belongsTo(Dr::class);
    }
}
