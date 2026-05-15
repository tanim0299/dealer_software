<?php

namespace App\Services;

use App\Models\SalesPayment;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DriverCashService
{
    /**
     * Sales-return rows that reduce DSR carrying cash: only cash paid to customer (negative amount).
     * Due-only settlement stores amount 0 on the return payment and does not affect cash.
     * Scoped to returns for this driver (invoiced: sales_ledger.driver_id; without invoice: return created by this user).
     */
    public static function returnCashRefundQuery(int $userId, int $driverId, string $startDate, string $endDate): Builder
    {
        return DB::table('sales_payments as sp')
            ->join('sales_return_ledgers as srl', 'srl.id', '=', 'sp.reference_id')
            ->leftJoin('sales_ledgers as sl', 'sl.id', '=', 'srl.sales_ledger_id')
            ->where('sp.type', SalesPayment::TYPE_RETURN)
            ->where('sp.create_by', $userId)
            ->where('sp.amount', '<', 0)
            ->whereDate('sp.date', '>=', $startDate)
            ->whereDate('sp.date', '<=', $endDate)
            ->where(function ($q) use ($driverId, $userId) {
                $q->where('sl.driver_id', $driverId)
                    ->orWhere(function ($q2) use ($userId) {
                        $q2->whereNull('srl.sales_ledger_id')
                            ->where('srl.create_by', $userId);
                    });
            });
    }

    /**
     * Total cash paid out on linked sales returns in the period (positive number).
     */
    public static function periodReturnCashRefunds(int $userId, int $driverId, string $startDate, string $endDate): float
    {
        return (float) self::returnCashRefundQuery($userId, $driverId, $startDate, $endDate)
            ->sum(DB::raw('ABS(sp.amount)'));
    }

    /**
     * Old returns: negative TYPE_RETURN with no reference_id (before ledger link).
     */
    public static function legacyOrphanReturnCashRefunds(int $userId, string $startDate, string $endDate): float
    {
        return (float) abs((float) DB::table('sales_payments')
            ->where('type', SalesPayment::TYPE_RETURN)
            ->where('create_by', $userId)
            ->whereNull('reference_id')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->where('amount', '<', 0)
            ->sum('amount'));
    }

    /**
     * Full cash effect of sales returns on DSR wallet (linked returns + legacy orphans).
     */
    public static function totalReturnCashRefundsForDriver(int $userId, int $driverId, string $startDate, string $endDate): float
    {
        return self::periodReturnCashRefunds($userId, $driverId, $startDate, $endDate)
            + self::legacyOrphanReturnCashRefunds($userId, $startDate, $endDate);
    }

    /**
     * Breakdown for the open period (same basis as {@see openPeriodAvailableCash}).
     *
     * @return array{
     *     period: array,
     *     sales_paid: float,
     *     due_collection: float,
     *     total_collected: float,
     *     cash_distributions: float,
     *     return_cash_out: float,
     *     expenses: float,
     *     available: float,
     * }
     */
    public static function openPeriodCashBreakdown(int $userId, int $driverId): array
    {
        $period = DriverPeriodService::periodForDriver($driverId);

        if (DriverPeriodService::hasClosingOnDate($driverId)) {
            return [
                'period' => $period,
                'sales_paid' => 0.0,
                'due_collection' => 0.0,
                'total_collected' => 0.0,
                'cash_distributions' => 0.0,
                'return_cash_out' => 0.0,
                'expenses' => 0.0,
                'available' => 0.0,
            ];
        }

        $periodPaid = (float) DB::table('sales_ledgers')
            ->where('driver_id', $driverId)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $period['end_date'])
            ->sum('paid');

        $periodDueCollection = (float) DB::table('sales_payments')
            ->where('type', SalesPayment::TYPE_PAYMENT)
            ->where('create_by', $userId)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $period['end_date'])
            ->sum('amount');

        $periodGiven = (float) DB::table('driver_cash_distributions')
            ->where('driver_id', $driverId)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $period['end_date'])
            ->sum('amount');

        $returnCashOut = self::totalReturnCashRefundsForDriver(
            $userId,
            $driverId,
            $period['start_date'],
            $period['end_date']
        );

        $periodExpense = (float) DB::table('expense_entries')
            ->where('driver_id', $driverId)
            ->whereDate('date', '>=', $period['start_date'])
            ->whereDate('date', '<=', $period['end_date'])
            ->sum('amount');

        $totalCollected = $periodPaid + $periodDueCollection;
        $available = max(0.0, $totalCollected - $periodGiven - $returnCashOut - $periodExpense);

        return [
            'period' => $period,
            'sales_paid' => $periodPaid,
            'due_collection' => $periodDueCollection,
            'total_collected' => $totalCollected,
            'cash_distributions' => $periodGiven,
            'return_cash_out' => $returnCashOut,
            'expenses' => $periodExpense,
            'available' => $available,
        ];
    }

    /**
     * Carrying cash remaining in the open period after all deductions:
     * sales paid-at-invoice + type-1 dues collected − cash distributions − cash sales-return refunds − period expenses.
     */
    public static function openPeriodAvailableCash(int $userId, int $driverId): float
    {
        return self::openPeriodCashBreakdown($userId, $driverId)['available'];
    }
}
