<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuSection extends Model
{
    protected $guarded = [];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const STATUS = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function createMenuSection($request)
    {
        $this->sl = $request->sl;
        $this->name = $request->name;
        $this->status = $request->status;
        $this->save();
        return $this;
    }
}
