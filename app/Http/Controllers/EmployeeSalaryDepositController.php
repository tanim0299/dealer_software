<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalaryDeposit;
use App\Services\RequestRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeSalaryDepositController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Employee Salary Deposit view'])->only(['index']);
        $this->middleware(['permission:Employee Salary Deposit create'])->only(['create', 'store']);
        $this->middleware(['permission:Employee Salary Deposit destroy'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = EmployeeSalaryDeposit::with('employee')->latest('salary_month');

        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->month) {
            $query->whereMonth('salary_month', date('m', strtotime($request->month . '-01')))
                ->whereYear('salary_month', date('Y', strtotime($request->month . '-01')));
        }

        $deposits = $query->paginate(20)->appends($request->all());
        $employees = Employee::orderBy('name')->get();

        return view('backend.employee_salary_deposit.index', compact('deposits', 'employees'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();

        return view('backend.employee_salary_deposit.create', compact('employees'));
    }

    public function store(Request $request)
    {
        [$rules, $messages] = RequestRules::employeeSalaryDepositRules($request);
        $request->validate($rules, $messages);

        $salaryMonth = $request->salary_month . '-01';
        $alreadyDeposited = EmployeeSalaryDeposit::where('employee_id', $request->employee_id)
            ->whereDate('salary_month', $salaryMonth)
            ->exists();

        if ($alreadyDeposited) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['salary_month' => 'Salary is already deposited for this month.'])
                ->with('error', 'Validation failed.');
        }

        EmployeeSalaryDeposit::create([
            'employee_id' => $request->employee_id,
            'salary_month' => $salaryMonth,
            'amount' => $request->amount,
            'note' => $request->note,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('employee_salary_deposit.create')->with('success', 'Salary deposited successfully.');
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
        $deposit = EmployeeSalaryDeposit::findOrFail($id);
        $deposit->delete();

        return redirect()->route('employee_salary_deposit.index')->with('success', 'Salary deposit deleted successfully.');
    }
}
