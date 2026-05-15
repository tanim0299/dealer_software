<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnEntry extends Model
{
    protected $guarded = [];

    public function ledger()
    {
        return $this->belongsTo(PurchaseReturnLedger::class, 'purchase_return_ledger_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
