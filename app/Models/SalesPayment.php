<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function sale()
    {
        return $this->belongsTo(SalesLedger::class,'ledger_id');
    }

    public function returnLedger()
    {
        return $this->belongsTo(SalesReturnLedger::class, 'reference_id');
    }
}
