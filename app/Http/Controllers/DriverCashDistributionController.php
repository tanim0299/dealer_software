<?php

namespace App\Http\Controllers;

use App\Models\DriverCashDistribution;
use App\Models\DriverClosing;
use App\Models\Employee;
use App\Models\EmployeeSalaryWithdraw;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
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

        $query = DriverCashDistribution::with('employee')
            ->where('driver_id', $driverId);

        if (!$request->from_date && !$request->to_date) {
            $query->whereDate('date', now()->toDateString());
        }

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

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

        [$totalCollectedCash, $alreadyGiven, $availableBalance] = $this->getAvailableCollectionCash($driverId, now()->toDateString());

        return view('driver.cash_distribution.create', compact('employees', 'totalCollectedCash', 'alreadyGiven', 'availableBalance'));
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

        $closingExists = DriverClosing::where('driver_id', $driverId)->whereDate('date', $date)->exists();
        if ($closingExists) {
            return back()->withInput()->with('error', 'Closing already submitted for this date.');
        }

        [, , $availableBalance] = $this->getAvailableCollectionCash($driverId, $date);

        if ((float) $request->amount > $availableBalance) {
            return back()->withInput()->with('error', 'Given amount can not exceed your available sales balance for this date.');
        }

        DB::beginTransaction();

        try {
            $salaryWithdraw = EmployeeSalaryWithdraw::create([
                'employee_id' => $request->employee_id,
                'withdraw_date' => $date,
                'salary_month' => date('Y-m', strtotime($date)),
                'amount' => $request->amount,
                'note' => 'Daily expense salary',
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
            return back()->withInput()->with('error', $th->getMessage());
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

        $closingExists = DriverClosing::where('driver_id', $driverId)
            ->whereDate('date', $distribution->date)
            ->exists();

        if ($closingExists) {
            return back()->with('error', 'Can not delete after closing submission.');
        }

        DB::beginTransaction();
        try {
            if (!empty($distribution->employee_salary_withdraw_id)) {
                EmployeeSalaryWithdraw::where('id', $distribution->employee_salary_withdraw_id)->delete();
            }

            $distribution->delete();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }

        return back()->with('success', 'Given amount entry deleted successfully.');
    }

    private function getAvailableCollectionCash(int $driverId, string $date): array
    {
        $todayPaid = (float) SalesLedger::where('driver_id', $driverId)
            ->whereDate('date', $date)
            ->sum('paid');

        $todayDueCollection = (float) SalesPayment::where('type', 1)
            ->where('create_by', Auth::id())
            ->whereDate('date', $date)
            ->sum('amount');

        $alreadyGiven = (float) DriverCashDistribution::where('driver_id', $driverId)
            ->whereDate('date', $date)
            ->sum('amount');

        $totalCollectedCash = $todayPaid + $todayDueCollection;
        $availableBalance = $totalCollectedCash - $alreadyGiven;

        return [$totalCollectedCash, $alreadyGiven, $availableBalance];
    }
}
