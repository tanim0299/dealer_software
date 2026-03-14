<?php

namespace App\Http\Controllers;

use App\Models\CashClose;
use App\Models\BankTransaction;
use App\Models\EmployeeSalaryWithdraw;
use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use App\Models\SalesPayment;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashCloseController extends Controller
{
    /**
     * Display cash close page
     */
    public function index()
    {
        $lastCashClose = (new CashClose())->latest()->first();
        $data['previousCash'] = $lastCashClose ? $lastCashClose->closing_balance : 0;
       
        $data['lastCloseDate'] = $lastCashClose ? $lastCashClose->close_date->copy()->addDay() : '2000-01-01';
        $data['total_sales'] = SalesPayment::whereBetween('date', [$data['lastCloseDate'], today()])
            ->where('type', SalesPayment::TYPE_SALE)
            ->sum('amount');
        $data['other_income'] = IncomeEntry::whereBetween('date', [$data['lastCloseDate'], today()])->sum('amount');
        $data['purchase_return'] = SupplierPayment::whereBetween('payment_date', [$data['lastCloseDate'], today()])
            ->where('type', 3)
            ->sum('amount') * -1;

        $data['due_collection'] = SalesPayment::whereBetween('date', [$data['lastCloseDate'], today()])
            ->where('type', 1)
            ->sum('amount');

        $data['bank_withdraw'] = BankTransaction::whereBetween('transaction_date', [$data['lastCloseDate'], today()])
            ->where('type', 'withdraw')
            ->sum('amount');

        
        $data['total_cash_in'] = $data['total_sales'] + $data['other_income'] + $data['purchase_return'] + $data['due_collection'] + $data['bank_withdraw'];

        // expense 

        $data['total_purchases'] = SupplierPayment::whereBetween('payment_date', [$data['lastCloseDate'], today()])
            ->where('type', 1)
            ->sum('amount');

        $data['supplier_payment'] = SupplierPayment::whereBetween('payment_date', [$data['lastCloseDate'], today()])
            ->where('type', 2)
            ->sum('amount');

        $data['total_expenses'] = ExpenseEntry::whereBetween('date', [$data['lastCloseDate'], today()])
            ->sum('amount');  
            
        $data['sales_return'] = SalesPayment::whereBetween('date', [$data['lastCloseDate'], today()])
            ->where('type', 3)
            ->sum('amount') * -1;

        $data['salary_payment'] = EmployeeSalaryWithdraw::whereBetween('withdraw_date', [$data['lastCloseDate'], today()])
            ->sum('amount');
        $data['bank_deposit'] = BankTransaction::whereBetween('transaction_date', [$data['lastCloseDate'], today()])
            ->where('type', 'deposit')
            ->sum('amount');

        $data['total_cash_out'] = $data['total_purchases'] + $data['supplier_payment'] + $data['total_expenses'] + $data['sales_return'] + $data['salary_payment'] + $data['bank_deposit'];

        $data['closing_balance'] = $data['previousCash'] + $data['total_cash_in'] - $data['total_cash_out'];

        return view('backend.cash-close.index', $data);
    }

    /**
     * Store new cash close
     */
    public function store(Request $request)
    {
        if (CashClose::hasClosedToday()) {
            return redirect()->route('cash_close.index')
                ->with('error', 'Cash close for this date already submitted.');
        }

        $validated = $request->validate([
            'opening_balance' => 'required|numeric',
            'total_cash_in' => 'required|numeric',
            'total_cash_out' => 'required|numeric',
            'closing_balance' => 'required|numeric',
        ]);

        CashClose::create([
            'close_date' => today(),
            'opening_balance' => $validated['opening_balance'],
            'total_cash_in' => $validated['total_cash_in'],
            'total_cash_out' => $validated['total_cash_out'],
            'closing_balance' => $validated['closing_balance'],
            'closed_by' => Auth::id(),
            'closed_at' => now(),
        ]);

        return redirect()->route('cash_close.index')
            ->with('success', 'Cash close completed successfully.');
    }
}

