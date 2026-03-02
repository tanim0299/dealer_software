<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DriverClosing;
use App\Models\DriverIssues;
use App\Models\Drivers;
use App\Models\Employee;
use App\Models\ExpenseEntry;
use App\Models\Product;
use App\Models\PurchaseLedger;
use App\Models\SalesLedger;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BackendController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Dashboard view'])->only(['index']);
    }
    
    public function index()
    {
        if(Auth::user()->hasRole('Driver'))
        {
            return view('driver.layouts.home');
        }

        $today = Carbon::today();

        $currentStockQty = (float) DB::table('ware_house_stocks')
            ->sum(DB::raw('purchase_qty + sales_return_qty - sales_qty - return_qty - sr_issue_qty'));

        $currentStockValue = (float) DB::table('ware_house_stocks')
            ->sum(DB::raw('(purchase_qty + sales_return_qty - sales_qty - return_qty - sr_issue_qty) * purchase_price'));

        $todaySalesAmount = (float) SalesLedger::whereDate('date', $today)
            ->sum(DB::raw('subtotal - discount'));

        $todaySalesPaid = (float) SalesLedger::whereDate('date', $today)->sum('paid');

        $todayPurchaseAmount = (float) PurchaseLedger::whereDate('purchase_date', $today)
            ->sum(DB::raw('total_amount - discount'));

        $todayExpenseAmount = (float) ExpenseEntry::whereDate('date', $today)->sum('amount');

        $todayCollectionAmount = (float) DB::table('sales_payments')
            ->where('type', 1)
            ->whereDate('date', $today)
            ->sum('amount');

        $todayReturnPaid = abs((float) DB::table('sales_payments')
            ->where('type', 2)
            ->where('amount', '<', 0)
            ->whereDate('date', $today)
            ->sum('amount'));

        $customerReceivable = (float) SalesLedger::sum(DB::raw('(subtotal - discount) - paid'));
        $supplierPayable = (float) PurchaseLedger::sum(DB::raw('(total_amount - discount) - paid_amount'));

        $stats = [
            'products' => Product::count(),
            'customers' => Customer::count(),
            'suppliers' => Supplier::count(),
            'drivers' => Drivers::count(),
            'employees' => Employee::count(),
            'open_driver_issues' => DriverIssues::where('status', 'open')->count(),
            'today_driver_closings' => DriverClosing::whereDate('date', $today)->count(),
        ];

        $recentSales = SalesLedger::with(['customer', 'driver'])
            ->latest('date')
            ->latest('id')
            ->take(5)
            ->get();

        $recentPurchases = PurchaseLedger::with('supplier')
            ->latest('purchase_date')
            ->latest('id')
            ->take(5)
            ->get();

        return view('backend.layouts.home', compact(
            'currentStockQty',
            'currentStockValue',
            'todaySalesAmount',
            'todaySalesPaid',
            'todayPurchaseAmount',
            'todayExpenseAmount',
            'todayCollectionAmount',
            'todayReturnPaid',
            'customerReceivable',
            'supplierPayable',
            'stats',
            'recentSales',
            'recentPurchases'
        ));
    }
}
