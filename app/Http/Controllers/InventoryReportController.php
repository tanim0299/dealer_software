<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use App\Models\IncomeExpenseTitle;
use App\Models\Product;
use App\Models\PurchaseLedger;
use App\Models\PurchaseReturnLedger;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Models\SalesReturnLedger;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Reports Dashboard view'])->only(['reportsHome']);

        $this->middleware(['permission:Sales Report view'])->only(['salesIndex']);
        $this->middleware(['permission:Sales Report create'])->only(['salesPrint']);

        $this->middleware(['permission:Purchase Report view'])->only(['purchaseIndex']);
        $this->middleware(['permission:Purchase Report create'])->only(['purchasePrint']);

        $this->middleware(['permission:Cash Report view'])->only(['cashIndex']);
        $this->middleware(['permission:Cash Report create'])->only(['cashPrint']);

        $this->middleware(['permission:Stock Report view'])->only(['stockIndex']);
        $this->middleware(['permission:Stock Report create'])->only(['stockPrint']);

        $this->middleware(['permission:Sales Return Report view'])->only(['salesReturnIndex']);
        $this->middleware(['permission:Sales Return Report create'])->only(['salesReturnPrint']);

        $this->middleware(['permission:Purchase Return Report view'])->only(['purchaseReturnIndex']);
        $this->middleware(['permission:Purchase Return Report create'])->only(['purchaseReturnPrint']);

        $this->middleware(['permission:Income Report view'])->only(['incomeIndex']);
        $this->middleware(['permission:Income Report create'])->only(['incomePrint']);

        $this->middleware(['permission:Expense Report view'])->only(['expenseIndex']);
        $this->middleware(['permission:Expense Report create'])->only(['expensePrint']);
    }

    public function reportsHome()
    {
        return view('backend.reports.index');
    }

    public function salesIndex()
    {
        $extraFilters = [
            [
                'name' => 'customer_id',
                'label' => 'Customer',
                'placeholder' => 'All Customers',
                'options' => Customer::orderBy('name')->get(),
                'value_field' => 'id',
                'text_field' => 'name',
            ],
        ];

        return view('backend.reports.filter', [
            'reportTitle' => 'Sales Report Filter',
            'printRoute' => route('sales_report.print'),
            'extraFilters' => $extraFilters,
        ]);
    }

    public function salesPrint(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $query = SalesLedger::with(['customer', 'driver'])
            ->whereBetween('date', [$period['from'], $period['to']]);

        if (!empty($request->customer_id)) {
            $query->where('customer_id', $request->customer_id);
        }

        $rows = $query->orderBy('date')->orderBy('id')->get();

        $summary = [
            'invoice_count' => $rows->count(),
            'subtotal' => (float) $rows->sum('subtotal'),
            'discount' => (float) $rows->sum('discount'),
            'net_sales' => (float) $rows->sum(function ($row) {
                return (float) $row->subtotal - (float) $row->discount;
            }),
            'paid' => (float) $rows->sum('paid'),
        ];
        $summary['due'] = $summary['net_sales'] - $summary['paid'];

        return view('backend.reports.sales_print', compact('rows', 'summary', 'period'));
    }

    public function purchaseIndex()
    {
        $extraFilters = [
            [
                'name' => 'supplier_id',
                'label' => 'Supplier',
                'placeholder' => 'All Suppliers',
                'options' => Supplier::orderBy('name')->get(),
                'value_field' => 'id',
                'text_field' => 'name',
            ],
        ];

        return view('backend.reports.filter', [
            'reportTitle' => 'Purchase Report Filter',
            'printRoute' => route('purchase_report.print'),
            'extraFilters' => $extraFilters,
        ]);
    }

    public function purchasePrint(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $query = PurchaseLedger::with('supplier')
            ->whereBetween('purchase_date', [$period['from'], $period['to']]);

        if (!empty($request->supplier_id)) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $rows = $query->orderBy('purchase_date')->orderBy('id')->get();

        $summary = [
            'invoice_count' => $rows->count(),
            'total_amount' => (float) $rows->sum('total_amount'),
            'discount' => (float) $rows->sum('discount'),
            'net_purchase' => (float) $rows->sum(function ($row) {
                return (float) $row->total_amount - (float) $row->discount;
            }),
            'paid' => (float) $rows->sum('paid_amount'),
        ];
        $summary['due'] = $summary['net_purchase'] - $summary['paid'];

        return view('backend.reports.purchase_print', compact('rows', 'summary', 'period'));
    }

    public function cashIndex()
    {
        return view('backend.reports.filter', [
            'reportTitle' => 'Cash Report Filter',
            'printRoute' => route('cash_report.print'),
            'extraFilters' => [],
        ]);
    }

    public function cashPrint(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $salesCash = (float) SalesPayment::whereBetween('date', [$period['from'], $period['to']])
            ->where('type', SalesPayment::TYPE_SALE)
            ->sum('amount');

        $dueCollection = (float) SalesPayment::whereBetween('date', [$period['from'], $period['to']])
            ->where('type', SalesPayment::TYPE_PAYMENT)
            ->sum('amount');

        $otherIncome = (float) IncomeEntry::whereBetween('date', [$period['from'], $period['to']])
            ->sum('amount');

        $purchaseReturnCash = (float) SupplierPayment::whereBetween('payment_date', [$period['from'], $period['to']])
            ->where('type', SupplierPayment::TYPE_PURCHASE_RETURN)
            ->where('amount', '<', 0)
            ->sum('amount') * -1;

        $bankWithdraw = (float) DB::table('bank_transactions')
            ->whereBetween('transaction_date', [$period['from'], $period['to']])
            ->where('type', 'withdraw')
            ->sum('amount');

        $purchasePaid = (float) SupplierPayment::whereBetween('payment_date', [$period['from'], $period['to']])
            ->where('type', SupplierPayment::TYPE_INVOICE_PAYMENT)
            ->sum('amount');

        $supplierDuePaid = (float) SupplierPayment::whereBetween('payment_date', [$period['from'], $period['to']])
            ->where('type', SupplierPayment::TYPE_DUE_PAYMENT)
            ->sum('amount');

        $expenseAmount = (float) ExpenseEntry::whereBetween('date', [$period['from'], $period['to']])
            ->sum('amount');

        $salesReturnCash = (float) SalesPayment::whereBetween('date', [$period['from'], $period['to']])
            ->where('type', SalesPayment::TYPE_RETURN)
            ->where('amount', '<', 0)
            ->sum('amount') * -1;

        $salaryWithdraw = (float) DB::table('employee_salary_withdraws')
            ->whereBetween('withdraw_date', [$period['from'], $period['to']])
            ->sum('amount');

        $bankDeposit = (float) DB::table('bank_transactions')
            ->whereBetween('transaction_date', [$period['from'], $period['to']])
            ->where('type', 'deposit')
            ->sum('amount');

        $cashIn = [
            'sales_cash' => $salesCash,
            'due_collection' => $dueCollection,
            'other_income' => $otherIncome,
            'purchase_return_cash' => $purchaseReturnCash,
            'bank_withdraw' => $bankWithdraw,
        ];

        $cashOut = [
            'purchase_paid' => $purchasePaid,
            'supplier_due_paid' => $supplierDuePaid,
            'expense' => $expenseAmount,
            'sales_return_cash' => $salesReturnCash,
            'salary_withdraw' => $salaryWithdraw,
            'bank_deposit' => $bankDeposit,
        ];

        $summary = [
            'total_in' => array_sum($cashIn),
            'total_out' => array_sum($cashOut),
        ];
        $summary['net_cash'] = $summary['total_in'] - $summary['total_out'];

        return view('backend.reports.cash_print', compact('cashIn', 'cashOut', 'summary', 'period'));
    }

    public function stockIndex()
    {
        $extraFilters = [
            [
                'name' => 'product_id',
                'label' => 'Product',
                'placeholder' => 'All Products',
                'options' => Product::orderBy('name')->get(),
                'value_field' => 'id',
                'text_field' => 'name',
            ],
        ];

        return view('backend.reports.filter', [
            'reportTitle' => 'Stock Movement Report Filter',
            'printRoute' => route('stock_report.print'),
            'extraFilters' => $extraFilters,
        ]);
    }

    public function stockPrint(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $products = Product::query()
            ->when(!empty($request->product_id), function ($query) use ($request) {
                $query->where('id', $request->product_id);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        $fromDate = $period['from'];
        $toDate = $period['to'];

        $purchaseBefore = $this->getQtyMap('purchase_entries as pe', 'purchase_ledgers as pl', 'pe.purchase_ledger_id', 'pl.id', 'pl.purchase_date', 'pe.product_id', 'pe.final_quantity', '<', $fromDate);
        $purchasePeriod = $this->getQtyMap('purchase_entries as pe', 'purchase_ledgers as pl', 'pe.purchase_ledger_id', 'pl.id', 'pl.purchase_date', 'pe.product_id', 'pe.final_quantity', 'between', [$fromDate, $toDate]);

        $salesBefore = $this->getQtyMap('sales_entries as se', 'sales_ledgers as sl', 'se.ledger_id', 'sl.id', 'sl.date', 'se.product_id', 'se.final_quantity', '<', $fromDate);
        $salesPeriod = $this->getQtyMap('sales_entries as se', 'sales_ledgers as sl', 'se.ledger_id', 'sl.id', 'sl.date', 'se.product_id', 'se.final_quantity', 'between', [$fromDate, $toDate]);

        $salesReturnBefore = $this->getQtyMap('sales_return_entries as sre', 'sales_return_ledgers as srl', 'sre.return_ledger_id', 'srl.id', 'srl.date', 'sre.product_id', 'sre.return_qty', '<', $fromDate);
        $salesReturnPeriod = $this->getQtyMap('sales_return_entries as sre', 'sales_return_ledgers as srl', 'sre.return_ledger_id', 'srl.id', 'srl.date', 'sre.product_id', 'sre.return_qty', 'between', [$fromDate, $toDate]);

        $purchaseReturnBefore = $this->getQtyMap('purchase_return_entries as pre', 'purchase_return_ledgers as prl', 'pre.purchase_return_ledger_id', 'prl.id', 'prl.date', 'pre.product_id', 'pre.return_qty', '<', $fromDate);
        $purchaseReturnPeriod = $this->getQtyMap('purchase_return_entries as pre', 'purchase_return_ledgers as prl', 'pre.purchase_return_ledger_id', 'prl.id', 'prl.date', 'pre.product_id', 'pre.return_qty', 'between', [$fromDate, $toDate]);

        $rows = collect();
        foreach ($products as $product) {
            $opening = ($purchaseBefore[$product->id] ?? 0)
                + ($salesReturnBefore[$product->id] ?? 0)
                - ($salesBefore[$product->id] ?? 0)
                - ($purchaseReturnBefore[$product->id] ?? 0);

            $purchaseIn = (float) ($purchasePeriod[$product->id] ?? 0);
            $salesOut = (float) ($salesPeriod[$product->id] ?? 0);
            $salesReturnIn = (float) ($salesReturnPeriod[$product->id] ?? 0);
            $purchaseReturnOut = (float) ($purchaseReturnPeriod[$product->id] ?? 0);

            $closing = $opening + $purchaseIn + $salesReturnIn - $salesOut - $purchaseReturnOut;

            if ($opening == 0 && $purchaseIn == 0 && $salesOut == 0 && $salesReturnIn == 0 && $purchaseReturnOut == 0 && $closing == 0) {
                continue;
            }

            $rows->push([
                'product_name' => $product->name,
                'opening' => $opening,
                'purchase_in' => $purchaseIn,
                'sales_out' => $salesOut,
                'sales_return_in' => $salesReturnIn,
                'purchase_return_out' => $purchaseReturnOut,
                'closing' => $closing,
            ]);
        }

        $summary = [
            'opening' => (float) $rows->sum('opening'),
            'purchase_in' => (float) $rows->sum('purchase_in'),
            'sales_out' => (float) $rows->sum('sales_out'),
            'sales_return_in' => (float) $rows->sum('sales_return_in'),
            'purchase_return_out' => (float) $rows->sum('purchase_return_out'),
            'closing' => (float) $rows->sum('closing'),
        ];

        return view('backend.reports.stock_print', compact('rows', 'summary', 'period'));
    }

    public function salesReturnIndex()
    {
        $extraFilters = [
            [
                'name' => 'customer_id',
                'label' => 'Customer',
                'placeholder' => 'All Customers',
                'options' => Customer::orderBy('name')->get(),
                'value_field' => 'id',
                'text_field' => 'name',
            ],
        ];

        return view('backend.reports.filter', [
            'reportTitle' => 'Sales Return Report Filter',
            'printRoute' => route('sales_return_report.print'),
            'extraFilters' => $extraFilters,
        ]);
    }

    public function salesReturnPrint(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $query = SalesReturnLedger::with('customer')
            ->whereBetween('date', [$period['from'], $period['to']]);

        if (!empty($request->customer_id)) {
            $query->where('customer_id', $request->customer_id);
        }

        $rows = $query->orderBy('date')->orderBy('id')->get();

        $cashPaid = (float) SalesPayment::whereBetween('date', [$period['from'], $period['to']])
            ->where('type', SalesPayment::TYPE_RETURN)
            ->where('amount', '<', 0)
            ->sum('amount') * -1;

        $dueAdjusted = (float) $rows->sum('subtotal') - $cashPaid;

        $summary = [
            'return_count' => $rows->count(),
            'subtotal' => (float) $rows->sum('subtotal'),
            'cash_paid' => $cashPaid,
            'due_adjusted' => $dueAdjusted,
        ];

        return view('backend.reports.sales_return_print', compact('rows', 'summary', 'period'));
    }

    public function purchaseReturnIndex()
    {
        $extraFilters = [
            [
                'name' => 'supplier_id',
                'label' => 'Supplier',
                'placeholder' => 'All Suppliers',
                'options' => Supplier::orderBy('name')->get(),
                'value_field' => 'id',
                'text_field' => 'name',
            ],
        ];

        return view('backend.reports.filter', [
            'reportTitle' => 'Purchase Return Report Filter',
            'printRoute' => route('purchase_return_report.print'),
            'extraFilters' => $extraFilters,
        ]);
    }

    public function purchaseReturnPrint(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $query = PurchaseReturnLedger::with('supplier')
            ->whereBetween('date', [$period['from'], $period['to']]);

        if (!empty($request->supplier_id)) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $rows = $query->orderBy('date')->orderBy('id')->get();

        $summary = [
            'return_count' => $rows->count(),
            'subtotal' => (float) $rows->sum('subtotal'),
            'cash_return' => (float) $rows->where('return_type', 1)->sum('subtotal'),
            'due_adjusted' => (float) $rows->where('return_type', 2)->sum('subtotal'),
        ];

        return view('backend.reports.purchase_return_print', compact('rows', 'summary', 'period'));
    }

    public function incomeIndex()
    {
        $extraFilters = [
            [
                'name' => 'title_id',
                'label' => 'Income Head',
                'placeholder' => 'All Income Heads',
                'options' => IncomeExpenseTitle::where('type', 1)->orderBy('title')->get(),
                'value_field' => 'id',
                'text_field' => 'title',
            ],
        ];

        return view('backend.reports.filter', [
            'reportTitle' => 'Income Report Filter',
            'printRoute' => route('income_report.print'),
            'extraFilters' => $extraFilters,
        ]);
    }

    public function incomePrint(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $query = IncomeEntry::with('income')
            ->whereBetween('date', [$period['from'], $period['to']]);

        if (!empty($request->title_id)) {
            $query->where('title_id', $request->title_id);
        }

        $rows = $query->orderBy('date')->orderBy('id')->get();
        $summary = [
            'entry_count' => $rows->count(),
            'total' => (float) $rows->sum('amount'),
        ];

        return view('backend.reports.income_print', compact('rows', 'summary', 'period'));
    }

    public function expenseIndex()
    {
        $extraFilters = [
            [
                'name' => 'title_id',
                'label' => 'Expense Head',
                'placeholder' => 'All Expense Heads',
                'options' => IncomeExpenseTitle::where('type', 2)->orderBy('title')->get(),
                'value_field' => 'id',
                'text_field' => 'title',
            ],
        ];

        return view('backend.reports.filter', [
            'reportTitle' => 'Expense Report Filter',
            'printRoute' => route('expense_report.print'),
            'extraFilters' => $extraFilters,
        ]);
    }

    public function expensePrint(Request $request)
    {
        $period = $this->resolvePeriod($request);

        $query = ExpenseEntry::with('expense')
            ->whereBetween('date', [$period['from'], $period['to']]);

        if (!empty($request->title_id)) {
            $query->where('title_id', $request->title_id);
        }

        $rows = $query->orderBy('date')->orderBy('id')->get();
        $summary = [
            'entry_count' => $rows->count(),
            'total' => (float) $rows->sum('amount'),
        ];

        return view('backend.reports.expense_print', compact('rows', 'summary', 'period'));
    }

    private function resolvePeriod(Request $request): array
    {
        $request->validate([
            'report_type' => 'required|in:daily,date_to_date,monthly,yearly',
        ]);

        $type = $request->report_type;
        $from = null;
        $to = null;
        $label = '';

        if ($type === 'daily') {
            $request->validate(['daily_date' => 'required|date']);
            $from = Carbon::parse($request->daily_date)->startOfDay();
            $to = Carbon::parse($request->daily_date)->endOfDay();
            $label = 'Daily (' . $from->format('d M Y') . ')';
        } elseif ($type === 'date_to_date') {
            $request->validate([
                'from_date' => 'required|date',
                'to_date' => 'required|date|after_or_equal:from_date',
            ]);
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $label = 'Date to Date (' . $from->format('d M Y') . ' to ' . $to->format('d M Y') . ')';
        } elseif ($type === 'monthly') {
            $request->validate(['month' => 'required|date_format:Y-m']);
            $month = Carbon::createFromFormat('Y-m', $request->month);
            $from = $month->copy()->startOfMonth()->startOfDay();
            $to = $month->copy()->endOfMonth()->endOfDay();
            $label = 'Monthly (' . $month->format('F Y') . ')';
        } else {
            $request->validate(['year' => 'required|digits:4']);
            $year = Carbon::createFromDate((int) $request->year, 1, 1);
            $from = $year->copy()->startOfYear()->startOfDay();
            $to = $year->copy()->endOfYear()->endOfDay();
            $label = 'Yearly (' . $year->format('Y') . ')';
        }

        return [
            'type' => $type,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'label' => $label,
        ];
    }

    private function getQtyMap(
        string $entryTable,
        string $ledgerTable,
        string $entryJoinKey,
        string $ledgerJoinKey,
        string $dateColumn,
        string $productColumn,
        string $qtyColumn,
        string $operator,
        $value
    ): array {
        $query = DB::table($entryTable)
            ->join($ledgerTable, $entryJoinKey, '=', $ledgerJoinKey)
            ->select($productColumn . ' as product_id', DB::raw('SUM(' . $qtyColumn . ') as qty'))
            ->groupBy($productColumn);

        if ($operator === 'between') {
            $query->whereBetween($dateColumn, [$value[0], $value[1]]);
        } else {
            $query->whereDate($dateColumn, $operator, $value);
        }

        return $query->pluck('qty', 'product_id')->map(function ($qty) {
            return (float) $qty;
        })->toArray();
    }
}
