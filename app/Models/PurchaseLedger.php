<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseLedger extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->invoice_no)) {
                $model->invoice_no = self::generateInvoiceNo();
            }
        });
    }

    protected static function generateInvoiceNo()
    {
        // Get last invoice number
        $lastInvoice = self::orderBy('id', 'desc')->value('invoice_no');

        if (!$lastInvoice) {
            return 'P-000001';
        }

        // Extract numeric part
        $number = (int) str_replace('P-', '', $lastInvoice);

        // Increment and format
        return 'P-' . str_pad($number + 1, 6, '0', STR_PAD_LEFT);
    }

    public function entries()
    {
        return $this->hasMany(PurchaseEntry::class,'purchase_ledger_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
    

    public function getPurchase($search = [], $is_paginate= true, $is_relation = true)
    {
        $query = self::with('supplier')->orderBy('id', 'desc');

        /* 🔍 Search by Invoice No or Supplier Name */
        if (!empty($search['free_text'])) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', '%' . $search['free_text'] . '%')
                  ->orWhereHas('supplier', function ($supplier) use ($search) {
                      $supplier->where('name', 'like', '%' . $search['free_text'] . '%');
                  });
            });
        }

        /* 📅 Date Range Filter */
        if (!empty($search['from_date']) && !empty($search['to_date'])) {
            $query->whereBetween('purchase_date', [
                $search['from_date'],
                $search['to_date']
            ]);
        }

        if (!empty($search['from_date']) && empty($search['to_date'])) {
            $query->whereDate('purchase_date', '>=', $search['from_date']);
        }

        if (empty($search['from_date']) && !empty($search['to_date'])) {
            $query->whereDate('purchase_date', '<=', $search['to_date']);
        }

        /* 📄 Pagination or Full List */
        return $is_paginate
            ? $query->paginate(10)
            : $query->get();
    }

}
