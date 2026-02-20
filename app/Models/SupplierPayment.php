<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $guarded = [];

    const TYPE_INVOICE_PAYMENT = 1;
    const TYPE_DUE_PAYMENT = 2;

    const TYPE = [
        self::TYPE_INVOICE_PAYMENT => 'Invoice Payment',
        self::TYPE_DUE_PAYMENT => 'Due Payment',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
 }
