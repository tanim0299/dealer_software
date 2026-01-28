<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverIssues extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(DriverIssueItem::class);
    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class);
    }
}
