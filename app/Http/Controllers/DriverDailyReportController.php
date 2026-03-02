<?php

namespace App\Http\Controllers;

use App\Models\DriverCashDistribution;
use App\Models\DriverClosing;
use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\Drivers;
use App\Models\ExpenseEntry;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Models\SalesReturnLedger;
use App\Models\User;
use Illuminate\Http\Request;

class DriverDailyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Driver Daily Report view']);
    }

    public function index(Request $request)
    {
        $drivers = Drivers::orderBy('name')->get();

        return view('backend.driver_daily_report.index', compact('drivers'));
    }

    public function statement(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'date' => 'required|date',
        ]);

        $report = $this->buildReportData((int) $request->driver_id, $request->date);
        $report_title = 'Driver Daily Statement';

        return view('backend.driver_daily_report.statement', compact('report', 'report_title'));
    }

    private function buildReportData(int $driverId, string $date): array
    {
        $driver = Drivers::findOrFail($driverId);
        $user = User::where('driver_id', $driverId)->first();

        $sales = SalesLedger::with('customer')
            ->where('driver_id', $driverId)
            ->whereDate('date', $date)
            ->get();

        $collections = SalesPayment::with('customer')
            ->where('type', 1)
            ->whereDate('date', $date)
            ->when($user, function ($query) use ($user) {
                $query->where('create_by', $user->id);
            })
            ->get();

        $salesReturns = SalesReturnLedger::with(['customer', 'entries.product', 'payments', 'salesLedger'])
            ->whereDate('date', $date)
            ->whereHas('salesLedger', function ($query) use ($driverId) {
                $query->where('driver_id', $driverId);
            })
            ->get();

        $returnCashPaid = (float) abs($salesReturns->flatMap(function ($returnLedger) {
            return $returnLedger->payments;
        })->where('amount', '<', 0)->sum('amount'));

        $expenses = ExpenseEntry::with('expense')
            ->where('driver_id', $driverId)
            ->whereDate('date', $date)
            ->get();

        $products = DriverIssueItem::leftJoin('driver_issues', 'driver_issues.id', '=', 'driver_issue_items.driver_issue_id')
            ->whereIn('driver_issues.status', ['accepted', 'closed'])
            ->where('driver_issues.driver_id', $driverId)
            ->whereDate('driver_issues.issue_date', $date)
            ->select('driver_issue_items.*')
            ->get();

        $cashFromManager = (float) DriverIssues::where('driver_id', $driverId)
            ->whereDate('issue_date', $date)
            ->where('status', '!=', 'rejected')
            ->sum('cash_from_manager');

        $givenAmounts = DriverCashDistribution::with('employee')
            ->where('driver_id', $driverId)
            ->whereDate('date', $date)
            ->get();

        $givenAmountTotal = (float) $givenAmounts->sum('amount');

        $cashInHand = (float) $sales->sum('paid')
            + (float) $collections->sum('amount')
            - (float) $expenses->sum('amount')
            - $returnCashPaid
            + $cashFromManager
            - $givenAmountTotal;

        $closingStatus = DriverClosing::where('driver_id', $driverId)
            ->whereDate('date', $date)
            ->first();

        return compact(
            'driver',
            'date',
            'sales',
            'collections',
            'salesReturns',
            'returnCashPaid',
            'expenses',
            'products',
            'cashFromManager',
            'givenAmounts',
            'givenAmountTotal',
            'cashInHand',
            'closingStatus'
        );
    }
}
