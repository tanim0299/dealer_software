<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    protected $guarded = [];

    const TYPE_SALE = 0;
    const TYPE_PAYMENT = 1;
    const TYPE_RETURN = 2;
    const TYPE_PREVIOUS_DUE = 4;

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
