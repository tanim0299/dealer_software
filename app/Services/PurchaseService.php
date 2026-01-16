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
    
    public function updatePurchase($request, $purchaseId)
    {
        try {
            DB::beginTransaction();

            // 1️⃣ Fetch purchase
            $purchase = PurchaseLedger::findOrFail($purchaseId);

            // 2️⃣ Fetch old entries
            $oldEntries = PurchaseEntry::where('purchase_ledger_id', $purchase->id)->get();

            // 3️⃣ ROLLBACK OLD STOCK
            foreach ($oldEntries as $entry) {
                $stock = WareHouseStocks::where('product_id', $entry->product_id)->first();

                if ($stock) {
                    $stock->decrement('purchase_qty', $entry->final_quantity);
                }
            }

            // 4️⃣ DELETE OLD ENTRIES
            PurchaseEntry::where('purchase_ledger_id', $purchase->id)->delete();

            // 5️⃣ DELETE OLD SUPPLIER PAYMENT
            SupplierPayment::where([
                'supplier_id' => $purchase->supplier_id,
                'type'        => SupplierPayment::TYPE_INVOICE_PAYMENT,
                'note'        => 'Purchase payment'
            ])->delete();

            // 6️⃣ UPDATE PURCHASE LEDGER
            $purchase->update([
                'supplier_id'   => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount'  => $request->total_amount,
                'discount'      => $request->discount ?? 0,
                'paid_amount'   => $request->paid ?? 0,
                'note'          => $request->note,
            ]);

            // 7️⃣ INSERT NEW ENTRIES + UPDATE STOCK
            $cartItems = json_decode($request->cart_items, true);

            foreach ($cartItems as $item) {

                PurchaseEntry::create([
                    'purchase_ledger_id' => $purchase->id,
                    'product_id'         => $item['product_id'],
                    'sub_unit_id'        => $item['sub_unit_id'],
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $item['unit_price'],
                    'discount'           => $item['discount'] ?? 0,
                    'total_price'        => $item['total_price'],
                    'final_quantity'     => $item['final_quantity'],
                ]);

                $stock = WareHouseStocks::where('product_id', $item['product_id'])->first();

                if ($stock) {
                    $stock->increment('purchase_qty', $item['final_quantity']);
                } else {
                    WareHouseStocks::create([
                        'product_id'       => $item['product_id'],
                        'purchase_qty'     => $item['final_quantity'],
                        'sales_qty'        => 0,
                        'sales_return_qty' => 0,
                        'return_qty'       => 0,
                    ]);
                }
            }

            // 8️⃣ INSERT NEW SUPPLIER PAYMENT
            if ($request->paid > 0) {
                SupplierPayment::create([
                    'supplier_id'    => $request->supplier_id,
                    'payment_date'   => $request->purchase_date,
                    'amount'         => $request->paid,
                    'payment_method' => 'cash',
                    'note'           => 'Purchase payment',
                    'type'           => SupplierPayment::TYPE_INVOICE_PAYMENT,
                    'created_by'     => Auth::id(),
                ]);
            }

            DB::commit();

            return [
                ApiService::API_SUCCESS,
                'Purchase Updated',
                $purchase->id
            ];

        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                ApiService::API_SERVER_ERROR,
                $th->getMessage(),
                null
            ];
        }
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

    public function deletePurchase($purchaseId)
    {
        $status_code = $status_message = '';
        try {
            DB::beginTransaction();

            // 1️⃣ Fetch purchase
            $purchase = PurchaseLedger::findOrFail($purchaseId);

            // 2️⃣ Fetch purchase entries
            $entries = PurchaseEntry::where('purchase_ledger_id', $purchase->id)->get();

            // 3️⃣ ROLLBACK STOCK
            foreach ($entries as $entry) {
                $stock = WareHouseStocks::where('product_id', $entry->product_id)->first();

                if ($stock) {
                    $stock->decrement('purchase_qty', $entry->final_quantity);

                    // Optional safety: prevent negative stock
                    if ($stock->purchase_qty < 0) {
                        throw new \Exception('Stock mismatch detected.');
                    }
                }
            }

            // 4️⃣ DELETE PURCHASE ENTRIES
            PurchaseEntry::where('purchase_ledger_id', $purchase->id)->delete();

            // 5️⃣ DELETE SUPPLIER PAYMENTS (Invoice Payments)
            SupplierPayment::where([
                'supplier_id' => $purchase->supplier_id,
                'type'        => SupplierPayment::TYPE_INVOICE_PAYMENT,
                'note'        => 'Purchase payment'
            ])->delete();

            // 6️⃣ DELETE PURCHASE LEDGER
            $purchase->delete();

            DB::commit();

            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Purchase Deleted';

        } catch (\Throwable $th) {
            DB::rollBack();

            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }

}
