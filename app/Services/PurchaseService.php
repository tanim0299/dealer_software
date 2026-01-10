<?php
namespace App\Services;

use App\Models\PurchaseEntry;
use App\Models\PurchaseLedger;
use App\Models\SupplierPayment;
use App\Models\WareHouseStocks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService {

    public function getPurchaseList($search = [], $is_paginate = true, $is_relation = true)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new PurchaseLedger())->getPurchase($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Purchases Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code, $status_message, $response];
    }

    public function storePurchase($request)
    {
        $status_code = $status_message = $response = '';
        try {
            DB::beginTransaction();
             $purchase = (new PurchaseLedger())->create([
                'supplier_id'   => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount'  => $request->total_amount,
                'discount'      => $request->discount ?? 0,
                'paid_amount'   => $request->paid ?? 0,
                'note'          => $request->note,
                'created_by'    => Auth::user()->id,
            ]);
            $cartItems = json_decode($request->cart_items, true);

            foreach ($cartItems as $item) {
                (new PurchaseEntry())::create([
                    'purchase_ledger_id'    => $purchase->id,
                    'product_id'     => $item['product_id'],
                    'sub_unit_id'    => $item['sub_unit_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'discount'       => $item['discount'] ?? 0,
                    'total_price'    => $item['total_price'],
                    'final_quantity' => $item['final_quantity'],
                ]);

                $stock = WareHouseStocks::where('product_id', $item['product_id'])->first();

                if ($stock) {
                    // Update existing stock
                    $stock->increment('purchase_qty', $item['final_quantity']);
                } else {
                    // Create new stock row
                    WareHouseStocks::create([
                        'product_id'        => $item['product_id'],
                        'purchase_qty'      => $item['final_quantity'],
                        'sales_qty'         => 0,
                        'sales_return_qty'  => 0,
                        'return_qty'        => 0,
                    ]);
                }
            }

            SupplierPayment::create([
                'supplier_id'    => $request->supplier_id,
                'payment_date'   => $request->purchase_date,
                'amount'         => $request->paid,
                'payment_method' => 'cash', // or from request
                'note'           => 'Purchase payment',
                'type'           => SupplierPayment::TYPE_INVOICE_PAYMENT,
                'created_by'     => Auth::user()->id,
            ]);

            DB::commit();
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Purchase Stored';
            $response = $purchase->id;
        } catch (\Throwable $th) {
            DB::rollBack();
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
            $response = '';
        }

        return [$status_code, $status_message, $response];
    }

    public function getPurchaseById($id)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new PurchaseLedger())->find($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }
}
