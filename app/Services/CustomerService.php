<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\DriverArea;
use App\Models\SalesLedger;
use App\Models\SalesReturnLedger;
use App\Models\SalesPayment;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function CustomerList($search = [], $is_paginate = true) : array
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Customer())->CustomerList($search, $is_paginate);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch(\Throwable $th){
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function storeCustomer($request)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::customerRules($request->all());

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Customer())->createCustomer($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Customer created successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function getCustomerById($id)
    {
        $status_code = $status_message = $customer = '';
        try {
            $customer = (new Customer())->getCustomerById($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $customer];
    }

    public function updateCustomer($request, $id)
    {
        $status_code = $status_message = $error_message = null;
        [$rules, $messages] = RequestRules::customerRules($request->all(), $id);

        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if ($validation[0] !== ApiService::API_SUCCESS) {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Customer())->updateCustomer($request, $id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = "Customer updated successfully.";
                $error_message = null;
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
                $error_message = [$th->getMessage()];
            }
        }

        return [$status_code, $status_message, $error_message];
    }

    public function deleteCustomer($id)
    {
        $status_code = $status_message = null;

        try {
            [$status_code, $status_message] = self::getCustomerById($id);
            Customer::where('id', $id)->delete();

            $status_code = ApiService::API_SUCCESS;
            $status_message = __('Customer deleted successfully.');
        }
        catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function updateCustomerStatus($id)
    {
        $status_code = $status_message = null;
        try {
            (new Customer())->updateStatus($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = "Customer status updated successfully.";
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function getrDriverCustomer($driver_id)
    {
        $status_code = $status_message = $response = '';
        try {
            $driverAreas = DriverArea::where('driver_id', $driver_id)
                ->pluck('area_id')
                ->toArray();

            $cashCustomer = $this->getGlobalCashCustomer();
            $cashCustomerId = $cashCustomer?->id;

            if (empty($driverAreas) && !$cashCustomerId) {
                $response = collect();
                $status_code = ApiService::API_SUCCESS;
                return [$status_code, $status_message, $response];
            }

            $response = Customer::query()
                ->where(function ($query) use ($driverAreas, $cashCustomerId) {
                    if (!empty($driverAreas)) {
                        $query->whereIn('area_id', $driverAreas);
                        $query->where(function ($subQuery) {
                            $subQuery->whereNull('email')
                                ->orWhere('email', 'not like', 'cash-driver%@example.com');
                        });
                    }

                    if ($cashCustomerId) {
                        if (!empty($driverAreas)) {
                            $query->orWhere('id', $cashCustomerId);
                        } else {
                            $query->where('id', $cashCustomerId);
                        }
                    }
                })
                ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$cashCustomerId ?? 0])
                ->orderBy('name')
                ->get();
            $status_code = ApiService::API_SUCCESS;
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }

    public function getGlobalCashCustomer(): ?Customer
    {
        return Customer::firstOrCreate(
            ['name' => 'Cash Customer', 'area_id' => null],
            [
                'phone' => null,
                'email' => null,
                'address' => 'General cash customer',
            ]
        );
    }

    public function getCustomerDueById($customer_id)
    {
        $totalSales   = SalesLedger::where('customer_id', $customer_id)->sum(DB::raw('subtotal - discount'));
        $totalReturn  = SalesReturnLedger::where('customer_id', $customer_id)->sum('subtotal');
        $totalPaid    = SalesPayment::where('customer_id', $customer_id)->whereIn('type', [0, 1])->sum('amount');
        $totalReturnPaid = SalesPayment::where('customer_id', $customer_id)->where('type', 2)->sum('amount') * -1;

        $due = ($totalSales - $totalReturn) - $totalPaid + $totalReturnPaid;

        return $due;
    }

    public function getCustomerDueByIdWithDateRange($customer_id, $from_date = null, $to_date = null)
    {
        $salesQuery = SalesLedger::where('customer_id', $customer_id);
        $returnQuery = SalesReturnLedger::where('customer_id', $customer_id);
        $paymentQuery = SalesPayment::where('customer_id', $customer_id)->whereIn('type', [0, 1]);
        $returnPaidQuery = SalesPayment::where('customer_id', $customer_id)->where('type', 2);

        // If date range exists
        if (!empty($from_date) && !empty($to_date)) {
            $from_date = \Carbon\Carbon::parse($from_date)->startOfDay();
            $to_date   = \Carbon\Carbon::parse($to_date)->endOfDay();

            $salesQuery->whereBetween('date', [$from_date, $to_date]);
            $returnQuery->whereBetween('date', [$from_date, $to_date]);
            $paymentQuery->whereBetween('date', [$from_date, $to_date]);
            $returnPaidQuery->whereBetween('date', [$from_date, $to_date]);
        }

        $totalSales = $salesQuery->sum(DB::raw('subtotal - discount'));
        $totalReturn = $returnQuery->sum('subtotal');
        $totalPaid = $paymentQuery->sum('amount');
        $totalReturnPaid = $returnPaidQuery->sum('amount') * -1;

        $due = ($totalSales - $totalReturn) - $totalPaid + $totalReturnPaid;

        return $due;
    }

    public function getCustomerData($search = [])
    {
        $query = SalesPayment::with([
            'sale.items.product',
            'sale.items.subUnit',
            'returnLedger.entries.product',
        ])->whereIn('type', [0, 1, 2]);
        
        if(!empty($search['customer_id'])) {
            $query = $query->where('customer_id', $search['customer_id']);
        }

        if (!empty($search['report_type'])) {
            switch ($search['report_type']) {
                case 'daily':
                    if (!empty($search['date'])) {
                        $date = \Carbon\Carbon::parse($search['date'])->toDateString();
                        $query->whereDate('date', $date);
                    }
                    break;

                case 'date_to_date':
                    if (!empty($search['from_date']) && !empty($search['to_date'])) {
                        $from = \Carbon\Carbon::parse($search['from_date'])->startOfDay();
                        $to   = \Carbon\Carbon::parse($search['to_date'])->endOfDay();
                        $query->whereBetween('date', [$from, $to]);
                    }
                    break;

                case 'monthly':
                    if (!empty($search['month'])) {
                        $month = \Carbon\Carbon::createFromFormat('Y-m', $search['month']);
                        $query->whereMonth('date', $month->month)
                            ->whereYear('date', $month->year);
                    }
                    break;

                case 'yearly':
                    if (!empty($search['year'])) {
                        $query->whereYear('date', $search['year']);
                    }
                    break;
            }
        }

        return $query->orderBy('date', 'asc')->orderBy('id', 'asc')->get();
    }
}
