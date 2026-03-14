<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $guarded = [];

    const TYPE_INVOICE_PAYMENT = 1;
    const TYPE_DUE_PAYMENT = 2;
    const TYPE_PURCHASE_RETURN = 3;
    const TYPE_PREVIOUS_DUE = 4;

    const TYPE = [
        self::TYPE_INVOICE_PAYMENT => 'Invoice Payment',
        self::TYPE_DUE_PAYMENT => 'Due Payment',
        self::TYPE_PURCHASE_RETURN => 'Purchase Return',
        self::TYPE_PREVIOUS_DUE => 'Previous Due',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function purchase()
    {
        return $this->belongsTo(PurchaseLedger::class,'ledger_id');
    }

    public function return()
    {
        return $this->belongsTo(PurchaseReturnLedger::class,'reference_no');
        }
 }
