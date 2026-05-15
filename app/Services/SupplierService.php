<?php
namespace App\Services;

use App\Models\PurchaseLedger;
use App\Models\PurchaseReturnLedger;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                DB::beginTransaction();

                $supplier = (new Supplier())->storeSupplier($request);
                if (!$supplier) {
                    throw new \Exception('Supplier create failed.');
                }

                $previousDue = (float) ($request->previous_due ?? 0);
                if ($previousDue > 0) {
                    SupplierPayment::create([
                        'supplier_id' => $supplier->id,
                        'payment_date' => now()->toDateString(),
                        'amount' => $previousDue,
                        'payment_method' => 'Opening',
                        'note' => 'Previous due at supplier opening',
                        'type' => SupplierPayment::TYPE_PREVIOUS_DUE,
                        'created_by' => Auth::id(),
                    ]);
                }

                DB::commit();
                $status_code = ApiService::API_SUCCESS;
                $status_message = 'Supplier Created';
            } catch (\Throwable $th) {
                DB::rollBack();
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
        $returnPaidQuery = SupplierPayment::where('supplier_id', $supplier_id)
            ->where('type', SupplierPayment::TYPE_PURCHASE_RETURN);
        $openingDueQuery = SupplierPayment::where('supplier_id', $supplier_id)
            ->where('type', SupplierPayment::TYPE_PREVIOUS_DUE);

        // If date range exists
        if (!empty($from_date) && !empty($to_date)) {

            $from_date = Carbon::parse($from_date)->startOfDay();
            $to_date   = Carbon::parse($to_date)->endOfDay();

            $purchaseQuery->whereBetween('purchase_date', [$from_date, $to_date]);
            $paymentQuery->whereBetween('payment_date', [$from_date, $to_date]);
            $returnQuery->whereBetween('date', [$from_date, $to_date]);
            $returnPaidQuery->whereBetween('payment_date', [$from_date, $to_date]);
            $openingDueQuery->whereBetween('payment_date', [$from_date, $to_date]);
        }

        // Calculations
        $totalPurchase = $purchaseQuery->sum('total_amount');
        $totalPurchasePid = $purchaseQuery->sum('paid_amount');
        $totalPurchaseDiscount = $purchaseQuery->sum('discount');
        
        $totalPaid = $paymentQuery->where('type', 2)->sum('amount');
        
        $totalReturnMinus = $returnQuery->where('return_type', 2)->sum('due_adjustment');

        // Purchase-return cash rows store negative amounts (cash effect); add sum directly so due decreases.
        $totalReturnCash = (float) $returnPaidQuery->sum('amount');
        $openingDue = (float) $openingDueQuery->sum('amount');

        $due = $openingDue
            + (float) $totalPurchase
            + $totalReturnCash
            - (float) $totalPurchaseDiscount
            - (float) $totalPaid
            - (float) $totalPurchasePid
            - (float) $totalReturnMinus;

        return $due;
    }


    public function getSupplierData($search = [])
    {
        $query = SupplierPayment::query();
        if (! empty($search['supplier_id'])) {
            $query = $query->where('supplier_id', $search['supplier_id']);
        }
        $this->applySupplierPaymentReportDateFilter($query, $search);

        return $query->get();
    }

    /**
     * Payments (excluding purchase-return cash rows) merged with purchase return ledgers,
     * so "minus from due" returns appear even when no type-3 SupplierPayment exists.
     */
    public function getSupplierBalanceSheetLines(array $search): \Illuminate\Support\Collection
    {
        $supplierId = $search['supplier_id'] ?? null;
        if (! $supplierId) {
            return collect();
        }

        $paymentsQuery = SupplierPayment::query()
            ->with(['purchase.entries.product', 'return'])
            ->where('supplier_id', $supplierId)
            ->where('type', '!=', SupplierPayment::TYPE_PURCHASE_RETURN);

        $this->applySupplierPaymentReportDateFilter($paymentsQuery, $search);
        $payments = $paymentsQuery->orderBy('payment_date')->orderBy('id')->get();

        $returnsQuery = PurchaseReturnLedger::query()
            ->with(['entries.product'])
            ->where('supplier_id', $supplierId);
        $this->applyPurchaseReturnReportDateFilter($returnsQuery, $search);
        $returns = $returnsQuery->orderBy('date')->orderBy('id')->get();

        $rows = collect();
        foreach ($payments as $p) {
            $rows->push((object) [
                'kind' => 'payment',
                'payment' => $p,
                'at' => $p->created_at ?? Carbon::parse($p->payment_date),
            ]);
        }
        foreach ($returns as $r) {
            $rows->push((object) [
                'kind' => 'return',
                'return' => $r,
                'at' => $r->created_at ?? Carbon::parse($r->date),
            ]);
        }

        return $rows->sort(function ($a, $b) {
            $ta = Carbon::parse($a->at)->timestamp;
            $tb = Carbon::parse($b->at)->timestamp;
            if ($ta !== $tb) {
                return $ta <=> $tb;
            }
            $idA = $a->kind === 'payment' ? $a->payment->id : $a->return->id;
            $idB = $b->kind === 'payment' ? $b->payment->id : $b->return->id;

            return $idA <=> $idB;
        })->values();
    }

    private function applySupplierPaymentReportDateFilter($query, array $search): void
    {
        if (empty($search['report_type'])) {
            return;
        }

        switch ($search['report_type']) {
            case 'daily':
                if (! empty($search['date'])) {
                    $date = Carbon::parse($search['date'])->toDateString();
                    $query->whereDate('payment_date', $date);
                }
                break;

            case 'date_to_date':
                if (! empty($search['from_date']) && ! empty($search['to_date'])) {
                    $from = Carbon::parse($search['from_date'])->startOfDay();
                    $to = Carbon::parse($search['to_date'])->endOfDay();
                    $query->whereBetween('payment_date', [$from, $to]);
                }
                break;

            case 'monthly':
                if (! empty($search['month'])) {
                    $month = Carbon::createFromFormat('Y-m', $search['month']);
                    $query->whereMonth('payment_date', $month->month)
                        ->whereYear('payment_date', $month->year);
                }
                break;

            case 'yearly':
                if (! empty($search['year'])) {
                    $query->whereYear('payment_date', $search['year']);
                }
                break;
        }
    }

    private function applyPurchaseReturnReportDateFilter($query, array $search): void
    {
        if (empty($search['report_type'])) {
            return;
        }

        switch ($search['report_type']) {
            case 'daily':
                if (! empty($search['date'])) {
                    $query->whereDate('date', Carbon::parse($search['date'])->toDateString());
                }
                break;

            case 'date_to_date':
                if (! empty($search['from_date']) && ! empty($search['to_date'])) {
                    $from = Carbon::parse($search['from_date'])->toDateString();
                    $to = Carbon::parse($search['to_date'])->toDateString();
                    $query->whereBetween('date', [$from, $to]);
                }
                break;

            case 'monthly':
                if (! empty($search['month'])) {
                    $month = Carbon::createFromFormat('Y-m', $search['month']);
                    $query->whereYear('date', $month->year)
                        ->whereMonth('date', $month->month);
                }
                break;

            case 'yearly':
                if (! empty($search['year'])) {
                    $query->whereYear('date', (int) $search['year']);
                }
                break;
        }
    }
}