<?php

namespace App\Services;

use App\Models\DriverClosing;
use App\Models\DriverIssues;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DriverPeriodService
{
    /**
     * Shown when a DSR tries to post or change cash/sale/expense/return on a locked date.
     */
    public const DRIVER_PANEL_CLOSED_LEDGER_MESSAGE = 'This date is already closed. You cannot add or change sales, expenses, returns, or cash entries from the driver panel.';

    /**
     * Open business period: day after last closing through today (inclusive).
     *
     * @return array{start: \Carbon\Carbon, end: \Carbon\Carbon, start_date: string, end_date: string}
     */
    public static function periodForDriver(?int $driverId): array
    {
        $end = Carbon::today();

        if (! $driverId) {
            return self::packPeriod($end, $end);
        }

        $lastClosingDate = DriverClosing::query()
            ->where('driver_id', $driverId)
            ->orderByDesc('date')
            ->value('date');

        if ($lastClosingDate) {
            $start = Carbon::parse($lastClosingDate)->addDay()->startOfDay();
        } else {
            $firstIssueDate = DriverIssues::query()
                ->where('driver_id', $driverId)
                ->whereIn('status', ['accepted', 'open'])
                ->min('issue_date');

            $start = $firstIssueDate
                ? Carbon::parse($firstIssueDate)->startOfDay()
                : $end->copy();
        }

        if ($start->gt($end)) {
            $start = $end->copy();
        }

        return self::packPeriod($start, $end);
    }

    public static function packPeriod(Carbon $start, Carbon $end): array
    {
        return [
            'start' => $start->copy(),
            'end' => $end->copy(),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
        ];
    }

    /**
     * True if the given calendar date is on or before this driver's latest closing date
     * (ledger is locked — no new/changed entries from the driver panel).
     */
    public static function isDriverPanelDateInClosedLedger(int $driverId, string $date): bool
    {
        $lastClosed = DriverClosing::query()
            ->where('driver_id', $driverId)
            ->max('date');

        if ($lastClosed === null) {
            return false;
        }

        return Carbon::parse($date)->toDateString() <= Carbon::parse($lastClosed)->toDateString();
    }

    /**
     * @throws \RuntimeException When the driver must not post for this date.
     */
    public static function assertDriverPanelDateOpenForTransaction(?int $driverId, string $date): void
    {
        if (! $driverId) {
            return;
        }

        if (self::isDriverPanelDateInClosedLedger($driverId, $date)) {
            throw new \RuntimeException(self::DRIVER_PANEL_CLOSED_LEDGER_MESSAGE);
        }
    }

    public static function applyDateBetween(Builder $query, string $column, array $period): Builder
    {
        return $query
            ->whereDate($column, '>=', $period['start_date'])
            ->whereDate($column, '<=', $period['end_date']);
    }

    public static function acceptedIssuesInPeriodQuery(int $driverId, ?array $period = null): Builder
    {
        $period ??= self::periodForDriver($driverId);

        return DriverIssues::query()
            ->where('driver_id', $driverId)
            ->where('status', 'accepted')
            ->whereDate('issue_date', '>=', $period['start_date'])
            ->whereDate('issue_date', '<=', $period['end_date']);
    }

    public static function hasClosingOnDate(int $driverId, ?string $date = null): bool
    {
        $date = $date ?? Carbon::today()->toDateString();

        return self::isDriverPanelDateInClosedLedger($driverId, $date);
    }
}
