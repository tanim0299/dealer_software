<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnLedger extends Model
{
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function entries()
    {
        return $this->hasMany(PurchaseReturnEntry::class,'purchase_return_ledger_id');
    }
}
