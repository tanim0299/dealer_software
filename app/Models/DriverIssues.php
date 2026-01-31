<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverIssues extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(DriverIssueItem::class,'driver_issue_id');
    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class);
    }
}
