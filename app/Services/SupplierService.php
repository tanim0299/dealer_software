<?php
namespace App\Services;

use App\Models\PurchaseLedger;
use App\Models\PurchaseReturnLedger;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;

class SupplierService {

    public function getSupplierList($search = [], $is_paginate = true, $is_relation = false)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Supplier())->getSupplierList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Suppliers Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }
    public function getSupplierById($id)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new Supplier())->find($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Suppliers Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }
    public function deleteSupplierById($id)
    {
        $status_code = $status_message ='';
        try {
            (new Supplier())->where('id',$id)->delete();
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Suppliers Deleted';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

    public function storeSupplier($request)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::supplierRules($request);
        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if($validation[0] == ApiService::Api_VALIDATION_ERROR)
        {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Supplier())->storeSupplier($request);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Supplier Created';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }
        return [$status_code, $status_message, $error_message];
    }
    public function updateSupplierById($request,$id)
    {
        $status_code = $status_message = $error_message = '';
        [$rules, $messages] = RequestRules::supplierRules($request,$id);
        $validation = RequestRules::validate($request->all(), $rules, $messages);
        if($validation[0] == ApiService::Api_VALIDATION_ERROR)
        {
            $status_code = $validation[0];
            $status_message = $validation[1];
            $error_message = $validation[2];
        }
        else
        {
            try {
                (new Supplier())->updateSupplierById($request,$id);
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Supplier Created';
            } catch (\Throwable $th) {
                $status_code = ApiService::API_SERVER_ERROR;
                $status_message = $th->getMessage();
            }
        }
        return [$status_code, $status_message, $error_message];
    }

    

    public function getSupplierDueById($supplier_id, $from_date = null, $to_date = null)
    {
        // Base Queries
        $purchaseQuery = PurchaseLedger::where('supplier_id', $supplier_id);
        $paymentQuery = SupplierPayment::where('supplier_id', $supplier_id);
        $returnQuery = PurchaseReturnLedger::where('supplier_id', $supplier_id);
        $returnPaidQuery = SupplierPayment::where('supplier_id', $supplier_id)->where('type', 3);

        // If date range exists
        if (!empty($from_date) && !empty($to_date)) {

            $from_date = Carbon::parse($from_date)->startOfDay();
            $to_date   = Carbon::parse($to_date)->endOfDay();

            $purchaseQuery->whereBetween('purchase_date', [$from_date, $to_date]);
            $paymentQuery->whereBetween('payment_date', [$from_date, $to_date]);
            $returnQuery->whereBetween('date', [$from_date, $to_date]);
            $returnPaidQuery->whereBetween('payment_date', [$from_date, $to_date]);
        }

        // Calculations
        $totalPurchase = $purchaseQuery->sum('total_amount');
        $totalPurchasePid = $purchaseQuery->sum('paid_amount');
        $totalPurchaseDiscount = $purchaseQuery->sum('discount');
        
        $totalPaid = $paymentQuery->where('type', 2)->sum('amount');
        
        $totalReturnMinus = $returnQuery->where('return_type', 2)->sum('subtotal');
        
        $totalReturnPaid = $returnPaidQuery->sum('amount') * -1;
      
        
        $due = $totalPurchase + $totalReturnPaid - $totalPurchaseDiscount - $totalPaid - $totalPurchasePid - $totalReturnMinus;

        return $due;
    }


    public function getSupplierData($search = [])
    {
        $query = SupplierPayment::query();
        if(!empty($search['supplier_id']))
        {
            $query  = $query->where('supplier_id',$search['supplier_id']);
        }
        if (!empty($search['report_type'])) {

            switch ($search['report_type']) {

                case 'daily':
                    if (!empty($search['date'])) {
                        $date = Carbon::parse($search['date'])->toDateString();
                        $query->whereDate('payment_date', $date);
                    }
                    break;

                case 'date_to_date':
                    if (!empty($search['from_date']) && !empty($search['to_date'])) {
                        $from = Carbon::parse($search['from_date'])->startOfDay();
                        $to   = Carbon::parse($search['to_date'])->endOfDay();
                        $query->whereBetween('payment_date', [$from, $to]);
                    }
                    break;

                case 'monthly':
                    if (!empty($search['month'])) {
                        $month = Carbon::createFromFormat('Y-m', $search['month']);
                        $query->whereMonth('payment_date', $month->month)
                            ->whereYear('payment_date', $month->year);
                    }
                    break;

                case 'yearly':
                    if (!empty($search['year'])) {
                        $query->whereYear('payment_date', $search['year']);
                    }
                    break;
            }
        }

        return $query->get();
    }
}