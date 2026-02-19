<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnLedger extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id');
    }
    public function entries()
    {
        return $this->hasMany(SalesReturnEntries::class, 'return_ledger_id');
    }

    public function payments()
    {
        return $this->hasMany(SalesPayment::class, 'reference_id')
            ->where('reference_type', 'return');
    }
}
