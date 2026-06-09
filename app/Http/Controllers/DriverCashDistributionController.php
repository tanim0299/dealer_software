<?php

namespace App\Http\Controllers;

use App\Models\DriverCashDistribution;
use App\Models\Employee;
use App\Models\EmployeeSalaryWithdraw;
use App\Services\DriverCashService;
use App\Services\DriverPeriodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverCashDistributionController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->hasRole('Driver')) {
            abort(403);
        }

        $driverId = Auth::user()->driver_id;

        $period = DriverPeriodService::periodForDriver((int) $driverId);

        $query = DriverCashDistribution::with('employee')
            ->where('driver_id', $driverId);

        $from = $request->filled('from_date') ? $request->from_date : $period['start_date'];
        $to = $request->filled('to_date') ? $request->to_date : $period['end_date'];
        $query->whereBetween('date', [$from, $to]);

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->amount) {
            $query->where('amount', $request->amount);
        }

        $distributions = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10)->appends($request->all());

        $driverEmployee = Employee::where('driver_id', $driverId)->first();
        $employees = Employee::where('designation', '!=', 'DSR')
            ->when($driverEmployee, function ($query) use ($driverEmployee) {
                $query->where('id', '!=', $driverEmployee->id);
            })
            ->orderBy('name')
            ->get();

        return view('driver.cash_distribution.index', compact('distributions', 'employees'));
    }

    public function create()
    {
        if (!Auth::user()->hasRole('Driver')) {
            abort(403);
        }

        $driverId = Auth::user()->driver_id;
        $driverEmployee = Employee::where('driver_id', $driverId)->first();

        $employees = Employee::where('designation', '!=', 'DSR')
            ->when($driverEmployee, function ($query) use ($driverEmployee) {
                $query->where('id', '!=', $driverEmployee->id);
            })
            ->orderBy('name')
            ->get();

        extract($this->periodCashFigures((int) $driverId));

        return view('driver.cash_distribution.create', compact(
            'employees',
            'totalCollectedCash',
            'alreadyGiven',
            'deductionsOther',
            'availableBalance'
        ));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('Driver')) {
            abort(403);
        }

        $driverId = Auth::user()->driver_id;
        $date = $request->date ?? now()->toDateString();

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:1000',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $driverEmployee = Employee::where('driver_id', $driverId)->first();

        if ($employee->designation === 'DSR' || ($driverEmployee && (int)$employee->id === (int)$driverEmployee->id)) {
            return back()->withInput()->with('error', 'You can give amount only to non-DSR other employees.');
        }

        $closingExists = DriverPeriodService::isDriverPanelDateInClosedLedger((int) $driverId, $date);
        if ($closingExists) {
            return back()->withInput()->with('error', DriverPeriodService::DRIVER_PANEL_CLOSED_LEDGER_MESSAGE);
        }

        $figures = $this->periodCashFigures((int) $driverId);
        $availableBalance = $figures['availableBalance'];

        if ((float) $request->amount > $availableBalance) {
            return back()->withInput()->with('error', 'Given amount cannot exceed your available carrying cash for this period (after expenses and cash returns).');
        }

        DB::beginTransaction();

        try {
            $salaryWithdraw = EmployeeSalaryWithdraw::create([
                'employee_id' => $request->employee_id,
                'withdraw_date' => $date,
                'salary_month' => date('Y-m', strtotime($date)),
                'amount' => $request->amount,
                'note' => 'Daily expense salary (DSR cash given)',
                'created_by' => Auth::id(),
            ]);

            DriverCashDistribution::create([
                'driver_id' => $driverId,
                'employee_id' => $request->employee_id,
                'employee_salary_withdraw_id' => $salaryWithdraw->id,
                'date' => $date,
                'amount' => $request->amount,
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('error', \App\Services\ApiService::friendlyExceptionMessage($th));
        }

        return redirect()->route('driver_cash_distribution.index')->with('success', 'Amount given entry saved successfully.');
    }

    public function destroy(string $id)
    {
        if (!Auth::user()->hasRole('Driver')) {
            abort(403);
        }

        $driverId = Auth::user()->driver_id;

        $distribution = DriverCashDistribution::where('driver_id', $driverId)->findOrFail($id);

        $locked = DriverPeriodService::isDriverPanelDateInClosedLedger((int) $driverId, (string) $distribution->date);
        if ($locked) {
            return back()->with('error', DriverPeriodService::DRIVER_PANEL_CLOSED_LEDGER_MESSAGE);
        }

        DB::beginTransaction();
        try {
            $distribution->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', \App\Services\ApiService::friendlyExceptionMessage($th));
        }

        return back()->with('success', 'Given amount entry deleted successfully.');
    }

    /**
     * @return array{totalCollectedCash: float, alreadyGiven: float, deductionsOther: float, availableBalance: float}
     */
    private function periodCashFigures(int $driverId): array
    {
        $b = DriverCashService::openPeriodCashBreakdown((int) Auth::id(), $driverId);

        return [
            'totalCollectedCash' => (float) $b['total_collected'],
            'alreadyGiven' => (float) $b['cash_distributions'],
            'deductionsOther' => (float) $b['return_cash_out'] + (float) $b['expenses'],
            'availableBalance' => (float) $b['available'],
        ];
    }
}
