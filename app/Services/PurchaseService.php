<?php
namespace App\Services;

use App\Models\Product;
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
            $status_message = ApiService::friendlyExceptionMessage($th);
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
                'total_amount'  => (float) ($request->total_amount ?? 0),
                'discount'      => (float) ($request->discount ?? 0),
                'paid_amount'   => (float) ($request->paid ?? 0),
                'note'          => $request->note,
                'created_by'    => Auth::user()->id,
            ]);
            $cartItems = json_decode($request->cart_items, true);
            foreach ($cartItems as $item) {
                $product = (new Product())->find($item['product_id']);
                $salePrice = (float) ($item['sale_price'] ?? 0);
                $stockSalePrice = $salePrice > 0 ? $salePrice : (float) ($product->sale_price ?? 0);
                (new PurchaseEntry())::create([
                    'purchase_ledger_id'    => $purchase->id,
                    'product_id'     => $item['product_id'],
                    'sub_unit_id'    => $item['sub_unit_id'],
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $item['unit_price'],
                    'discount'       => $item['discount'] ?? 0,
                    'total_price'    => $item['total_price'],
                    'final_quantity' => $item['final_quantity'],
                    'sale_price'     => $stockSalePrice,
                ]);

                $unitPrice = WareHouseStocks::normalizePurchasePrice($item['unit_price'] ?? 0);
                $stock = WareHouseStocks::findMatchingStockRow((int) $item['product_id'], $unitPrice);

                if ($stock) {
                    $stock->increment('purchase_qty', $item['final_quantity']);
                } else {
                    WareHouseStocks::create([
                        'product_id'        => $item['product_id'],
                        'purchase_price'    => $unitPrice,
                        'purchase_qty'      => $item['final_quantity'],
                        'sales_qty'         => 0,
                        'sales_return_qty'  => 0,
                        'return_qty'        => 0,
                        'sale_price'        => $stockSalePrice,
                    ]);
                }

                if($salePrice > 0)
                {
                    $product->update(['sale_price' => $salePrice]);
                }
            }

            $paidAtPurchase = (float) ($request->paid ?? 0);

            SupplierPayment::create([
                'ledger_id' => $purchase->id,
                'supplier_id'    => $request->supplier_id,
                'payment_date'   => $request->purchase_date,
                'amount'         => $paidAtPurchase,
                'payment_method' => 'cash',
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
            $status_message = ApiService::friendlyExceptionMessage($th);
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
                $this->rollbackPurchaseEntryFromWarehouse($entry);
            }

            // 4️⃣ DELETE OLD ENTRIES
            PurchaseEntry::where('purchase_ledger_id', $purchase->id)->delete();

            // 5️⃣ DELETE OLD SUPPLIER PAYMENTS (invoice rows for this purchase)
            SupplierPayment::where('ledger_id', $purchase->id)
                ->where('type', SupplierPayment::TYPE_INVOICE_PAYMENT)
                ->delete();

            // 6️⃣ UPDATE PURCHASE LEDGER
            $purchase->update([
                'supplier_id'   => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'total_amount'  => (float) ($request->total_amount ?? 0),
                'discount'      => (float) ($request->discount ?? 0),
                'paid_amount'   => (float) ($request->paid ?? 0),
                'note'          => $request->note,
            ]);

            // 7️⃣ INSERT NEW ENTRIES + UPDATE STOCK
            $cartItems = json_decode($request->cart_items, true);

            foreach ($cartItems as $item) {

                $product = Product::find($item['product_id']);
                $salePrice = (float) ($item['sale_price'] ?? 0);
                $stockSalePrice = $salePrice > 0 ? $salePrice : (float) ($product->sale_price ?? 0);

                PurchaseEntry::create([
                    'purchase_ledger_id' => $purchase->id,
                    'product_id'         => $item['product_id'],
                    'sub_unit_id'        => $item['sub_unit_id'],
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $item['unit_price'],
                    'sale_price'         => $stockSalePrice,
                    'discount'           => $item['discount'] ?? 0,
                    'total_price'        => $item['total_price'],
                    'final_quantity'     => $item['final_quantity'],
                ]);


                $unitPrice = WareHouseStocks::normalizePurchasePrice($item['unit_price'] ?? 0);
                $stock = WareHouseStocks::findMatchingStockRow((int) $item['product_id'], $unitPrice);

                if ($stock) {
                    $stock->increment('purchase_qty', $item['final_quantity']);

                    if ($salePrice > 0) {
                        $stock->update(['sale_price' => $salePrice]);
                    }
                } else {
                    WareHouseStocks::create([
                        'product_id'        => $item['product_id'],
                        'purchase_price'    => $unitPrice,
                        'purchase_qty'      => $item['final_quantity'],
                        'sales_qty'         => 0,
                        'sales_return_qty'  => 0,
                        'return_qty'        => 0,
                        'sale_price'        => $stockSalePrice,
                    ]);
                }

                if ($salePrice > 0) {
                    $product->update([
                        'sale_price' => $salePrice
                    ]);
                }


            }

            // 8️⃣ INSERT NEW SUPPLIER PAYMENT (always, including 0 — matches storePurchase)
            $paidAtPurchase = (float) ($request->paid ?? 0);

            SupplierPayment::create([
                'ledger_id'      => $purchase->id,
                'supplier_id'    => $request->supplier_id,
                'payment_date'   => $request->purchase_date,
                'amount'         => $paidAtPurchase,
                'payment_method' => 'cash',
                'note'           => 'Purchase payment',
                'type'           => SupplierPayment::TYPE_INVOICE_PAYMENT,
                'created_by'     => Auth::id(),
            ]);

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
                ApiService::friendlyExceptionMessage($th),
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
            $status_message = ApiService::friendlyExceptionMessage($th);
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

            // 3️⃣ ROLLBACK STOCK (PRICE-AWARE; blocks if sales/returns consumed this batch)
            foreach ($entries as $entry) {
                $this->rollbackPurchaseEntryFromWarehouse($entry);
            }

            // 4️⃣ DELETE PURCHASE ENTRIES
            PurchaseEntry::where('purchase_ledger_id', $purchase->id)->delete();

            // 5️⃣ DELETE SUPPLIER PAYMENTS (invoice rows for this purchase)
            SupplierPayment::where('ledger_id', $purchase->id)
                ->where('type', SupplierPayment::TYPE_INVOICE_PAYMENT)
                ->delete();

            // 6️⃣ DELETE PURCHASE LEDGER
            $purchase->delete();

            DB::commit();

            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Purchase Deleted';

        } catch (\Throwable $th) {
            DB::rollBack();

            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = ApiService::friendlyExceptionMessage($th);
        }

        return [$status_code, $status_message];

    }

    /**
     * Remove purchase quantity from the matching FIFO warehouse row.
     * Fails closed if the batch row is missing or if net stock would go negative (sales / returns / driver issue already applied).
     */
    private function rollbackPurchaseEntryFromWarehouse(PurchaseEntry $entry): void
    {
        $stock = WareHouseStocks::findMatchingStockRow(
            (int) $entry->product_id,
            WareHouseStocks::normalizePurchasePrice($entry->unit_price)
        );

        if (! $stock) {
            throw new \RuntimeException(
                'Warehouse batch not found for purchase line (product #'.$entry->product_id.'). Cannot roll back stock.'
            );
        }

        $finalQty = (float) $entry->final_quantity;
        if ($finalQty <= 0) {
            return;
        }

        $newPurchaseQty = (float) $stock->purchase_qty - $finalQty;
        $sr = (float) ($stock->sr_issue_qty ?? 0);
        $projectedNet = $newPurchaseQty
            + (float) $stock->sales_return_qty
            - (float) $stock->sales_qty
            - (float) $stock->return_qty
            - $sr;

        if ($projectedNet < -0.0001) {
            throw new \RuntimeException(
                'Cannot roll back this purchase: sales, returns, or issues already used quantity from the same cost batch (product #'.$entry->product_id.'). Adjust or delete those records first.'
            );
        }

        $stock->decrement('purchase_qty', $finalQty);
        $stock->refresh();

        if ((float) $stock->purchase_qty < -0.0001) {
            throw new \RuntimeException('Stock mismatch after rollback for product ID: '.$entry->product_id);
        }

        if (abs((float) $stock->stock_qty) < 0.0001) {
            $stock->delete();
        }
    }

}
