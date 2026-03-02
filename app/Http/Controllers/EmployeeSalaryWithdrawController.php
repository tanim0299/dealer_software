<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalaryWithdraw;
use App\Services\RequestRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryWithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Employee Salary Withdraw view'])->only(['index']);
        $this->middleware(['permission:Employee Salary Withdraw create'])->only(['create', 'store']);
        $this->middleware(['permission:Employee Salary Withdraw destroy'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = EmployeeSalaryWithdraw::with('employee')->latest('withdraw_date');

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('withdraw_date', [$request->from_date, $request->to_date]);
        }

        $withdraws = $query->paginate(20)->appends($request->all());
        $employees = Employee::orderBy('name')->get();

        return view('backend.employee_salary_withdraw.index', compact('withdraws', 'employees'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();

        return view('backend.employee_salary_withdraw.create', compact('employees'));
    }

    public function store(Request $request)
    {
        [$rules, $messages] = RequestRules::employeeSalaryWithdrawRules($request);
        $request->validate($rules, $messages);

        DB::beginTransaction();

        try {
            $employee = Employee::findOrFail($request->employee_id);
            $availableBalance = $employee->getAvailableSalaryBalance();

            if ((float) $request->amount > (float) $availableBalance) {
                DB::rollBack();

                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['amount' => 'Withdraw amount can not be greater than available salary balance (' . number_format($availableBalance, 2) . ').'])
                    ->with('error', 'Validation failed.');
            }

            EmployeeSalaryWithdraw::create([
                'employee_id' => $request->employee_id,
                'withdraw_date' => $request->withdraw_date,
                'salary_month' => date('Y-m', strtotime($request->withdraw_date)),
                'amount' => $request->amount,
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('employee_salary_withdraw.create')->with('success', 'Salary withdrawn successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        $withdraw = EmployeeSalaryWithdraw::findOrFail($id);
        $withdraw->delete();

        return redirect()->route('employee_salary_withdraw.index')->with('success', 'Salary withdraw deleted successfully.');
    }

    public function getBalance($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        return response()->json([
            'balance' => $employee->getAvailableSalaryBalance(),
            'salary' => (float) $employee->salary,
        ]);
    }
}
