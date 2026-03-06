<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashClose extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'close_date',
        'opening_balance',
        'total_sales',
        'other_income',
        'purchase_return_cash',
        'due_collection',
        'bank_withdraw',
        'total_purchases',
        'supplier_payment',
        'expenses',
        'sales_return_cash',
        'salary_payment',
        'bank_deposit',
        'total_cash_in',
        'total_cash_out',
        'closing_balance',
        'closed_by',
        'closed_at',
        'reopened_by',
        'reopened_at',
    ];

    protected $casts = [
        'close_date' => 'date',
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'other_income' => 'decimal:2',
        'purchase_return_cash' => 'decimal:2',
        'due_collection' => 'decimal:2',
        'bank_withdraw' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'supplier_payment' => 'decimal:2',
        'expenses' => 'decimal:2',
        'sales_return_cash' => 'decimal:2',
        'salary_payment' => 'decimal:2',
        'bank_deposit' => 'decimal:2',
        'total_cash_in' => 'decimal:2',
        'total_cash_out' => 'decimal:2',
        'closing_balance' => 'decimal:2',
    ];

    /**
     * Relationship: User who closed the cash
     */
    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Relationship: User who reopened the cash
     */
    public function reopenedByUser()
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    /**
     * Calculate total income
     */
    public function calculateTotalCashIn()
    {
        return $this->total_sales + $this->other_income + $this->bank_withdraw + $this->due_collection;
    }

    /**
     * Calculate total expenses
     */
    public function calculateTotalCashOut()
    {
        return $this->total_purchases + $this->supplier_payment + $this->salary_payment + $this->bank_deposit;
    }

    /**
     * Calculate closing balance
     */
    public function calculateClosingBalance()
    {
        return $this->opening_balance + $this->calculateTotalCashIn() - $this->calculateTotalCashOut();
    }

    /**
     * Check if can reopen within 2 hours
     */
    public function canReopen()
    {
        if ($this->reopened_at) {
            return false; // Already reopened once
        }

        $hoursSinceClosed = $this->closed_at->diffInHours(now());
        return $hoursSinceClosed < 2;
    }

    /**
     * Check if this is today's close
     */
    public function isTodayClose()
    {
        return $this->close_date->isToday();
    }

    /**
     * Get previous day's closing balance as opening balance for today
     */
    public static function getPreviousDayClosingBalance($date)
    {
        $previousDay = $date->subDay();
        return self::where('close_date', $previousDay)->first()?->closing_balance ?? 0;
    }

    /**
     * Get today's cash close if exists
     */
    public static function getTodayClose()
    {
        return self::where('close_date', today())->first();
    }

    /**
     * Check if today already has a cash close
     */
    public static function hasClosedToday()
    {
        return self::where('close_date', today())->exists();
    }
}
