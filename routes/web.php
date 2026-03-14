<?php

use App\Http\Controllers\BackendController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuSectionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverIssueController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WebsiteSettingsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SubUnitController;
use App\Http\Controllers\WarehouseStockController;
use App\Http\Controllers\CustomerAreaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DriverClosingController;
use App\Http\Controllers\DriverStockController;
use App\Http\Controllers\DriverCashDistributionController;
use App\Http\Controllers\DriverDailyReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\IncomeExpenseTitleController;
use App\Http\Controllers\ExpenseEntryController;
use App\Http\Controllers\IncomeEntryController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\SupplierBalanceSheetController;
use App\Http\Controllers\SupplierDueListController;
use App\Http\Controllers\CustomerDueListController;
use App\Http\Controllers\CustomerBalanceSheetController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeSalaryDepositController;
use App\Http\Controllers\EmployeeSalaryWithdrawController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\CashCloseController;
use App\Http\Controllers\InventoryReportController;
use App\Models\Item;
use App\Models\WebsiteSettings;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::get('supplier_due/{id}', [SupplierDueListController::class, 'show'])->name('supplier_due.show');

Route::get('/dashboard', [BackendController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resources([
        'menu_section' => MenuSectionController::class,
        'menu' => MenuController::class,
        'role' => RoleController::class,
        'user' => UserController::class,
        'item' => ItemController::class,
        'category' => CategoryController::class,
        'brand' => BrandController::class,
        'website_settings' => WebsiteSettingsController::class,
        'product' => ProductController::class,
        'supplier' => SupplierController::class,
        'purchase' => PurchaseController::class,
        'unit' => UnitController::class,
        'sub_unit' => SubUnitController::class,
        'warehouse_stock' => WarehouseStockController::class,
        'driver' => DriverController::class,
        'driver-issues' => DriverIssueController::class,
        'customer_area' => CustomerAreaController::class,
        'customer' => CustomerController::class,
        'sales' => SalesController::class,
        'income_expense_title' => IncomeExpenseTitleController::class,
        'expense_entry' => ExpenseEntryController::class,
        'income_entry' => IncomeEntryController::class,
        'sales_return' => SalesReturnController::class,
        'customer_payment' => CustomerPaymentController::class,
        'driver_stock' => DriverStockController::class,
        'purchase_return' => PurchaseReturnController::class,
        'supplier_payment' => SupplierPaymentController::class,
        'driver_closing' => DriverClosingController::class,
        'supplier_balance_sheet' => SupplierBalanceSheetController::class,
        'supplier_due_list' => SupplierDueListController::class,
        'customer_due_list' => CustomerDueListController::class,
        'customer_balance_sheet' => CustomerBalanceSheetController::class,
        'employee' => EmployeeController::class,
        'employee_salary_deposit' => EmployeeSalaryDepositController::class,
        'employee_salary_withdraw' => EmployeeSalaryWithdrawController::class,
        'bank_account' => BankAccountController::class,
        'bank_transaction' => BankTransactionController::class,
        'cash_close' => CashCloseController::class,
    ]);

    Route::resource('driver_cash_distribution', DriverCashDistributionController::class)
        ->only(['index', 'create', 'store', 'destroy']);

    Route::get('driver_daily_report', [DriverDailyReportController::class, 'index'])->name('driver_daily_report.index');
    Route::get('driver_daily_report/statement', [DriverDailyReportController::class, 'statement'])->name('driver_daily_report.statement');

    Route::get('supplier_balance_sheet_print',[SupplierBalanceSheetController::class,'print'])->name('supplier_balance_sheet.print');
    Route::get('customer_balance_sheet_print',[CustomerBalanceSheetController::class,'print'])->name('customer_balance_sheet.print');

    Route::get('reports', [InventoryReportController::class, 'reportsHome'])->name('reports.index');

    Route::get('reports/sales', [InventoryReportController::class, 'salesIndex'])->name('sales_report.index');
    Route::get('reports/sales/print', [InventoryReportController::class, 'salesPrint'])->name('sales_report.print');

    Route::get('reports/purchase', [InventoryReportController::class, 'purchaseIndex'])->name('purchase_report.index');
    Route::get('reports/purchase/print', [InventoryReportController::class, 'purchasePrint'])->name('purchase_report.print');

    Route::get('reports/cash', [InventoryReportController::class, 'cashIndex'])->name('cash_report.index');
    Route::get('reports/cash/print', [InventoryReportController::class, 'cashPrint'])->name('cash_report.print');

    Route::get('reports/stock', [InventoryReportController::class, 'stockIndex'])->name('stock_report.index');
    Route::get('reports/stock/print', [InventoryReportController::class, 'stockPrint'])->name('stock_report.print');

    Route::get('reports/sales-return', [InventoryReportController::class, 'salesReturnIndex'])->name('sales_return_report.index');
    Route::get('reports/sales-return/print', [InventoryReportController::class, 'salesReturnPrint'])->name('sales_return_report.print');

    Route::get('reports/purchase-return', [InventoryReportController::class, 'purchaseReturnIndex'])->name('purchase_return_report.index');
    Route::get('reports/purchase-return/print', [InventoryReportController::class, 'purchaseReturnPrint'])->name('purchase_return_report.print');

    Route::get('reports/income', [InventoryReportController::class, 'incomeIndex'])->name('income_report.index');
    Route::get('reports/income/print', [InventoryReportController::class, 'incomePrint'])->name('income_report.print');

    Route::get('reports/expense', [InventoryReportController::class, 'expenseIndex'])->name('expense_report.index');
    Route::get('reports/expense/print', [InventoryReportController::class, 'expensePrint'])->name('expense_report.print');

    Route::get('show_driver_closing',[DriverClosingController::class,'driverClosing'])->name('driver.show_closing');

    Route::get('supplier-payment/due/{supplier}', [SupplierPaymentController::class, 'getDue']);
    Route::get('employee-salary/balance/{employee}', [EmployeeSalaryWithdrawController::class, 'getBalance'])->name('employee_salary.balance');

    Route::get('customer-due/{id}', [SalesController::class, 'getCustomerDue'])->name('customer.get_due');
    Route::post('customer_due_list/payment', [CustomerDueListController::class, 'storePayment'])->name('customer_due_list.storePayment');
    Route::post('sales/driver/customer', [SalesController::class, 'storeDriverCustomer'])->name('sales.driver_customer.store');

    Route::get('driver-issues/{id}/accept', [DriverIssueController::class, 'accept'])
    ->name('driver-issues.accept');

    Route::get('driver-issues/{id}/reject', [DriverIssueController::class, 'reject'])
        ->name('driver-issues.reject');


    Route::get('sales_invoice/{id}',[SalesController::class,'invoice'])->name('sales.invoice');
    Route::get('/api/products', [ProductController::class, 'fetch']);

    Route::post('get_itemwise_category',[CategoryController::class,'itemWiseCategory'])->name('category.get_itemwise_category');

    Route::view('driver/profile', 'driver.profile')->name('driver.profile');

    // Bank Management API
    Route::get('bank-account/{id}/balance', [BankAccountController::class, 'getAccountBalance'])->name('bank_account.balance');
    Route::get('bank-transaction/account/{id}', [BankTransactionController::class, 'getAccountBalance'])->name('bank_transaction.account_balance');

});
Route::post('role_permission/{id}',[RoleController::class,'permission'])->name('role.permission');
Route::get('/purchase_invoice/{id}', [PurchaseController::class, 'invoice'])
    ->name('purchase.invoice');
//menu section
Route::post('menu_section_status', [MenuSectionController::class, 'status'])->name('menu_section.status');

require __DIR__.'/auth.php';
