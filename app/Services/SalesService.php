<?php

namespace App\Services;

use App\Models\DriverIssueItem;
use App\Models\SalesEntry;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Models\WareHouseStocks;
use App\Traits\FileUploader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesService {
    public function getSalesList($search = [], $is_paginate = true, $is_relation = true)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new SalesLedger())->getSalesList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SUCCESS;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }    

    public function storeSales($request)
    {
        $status_code = $status_message = $invoice_url = '';
        try {
            DB::beginTransaction();
            $cartItems = json_decode($request->cart_items, true);

            if (!$cartItems || count($cartItems) == 0) {
                throw new \Exception('Cart empty');
            }

            $subtotal = 0;

            foreach ($cartItems as $item) {
                $subtotal += ($item['qty'] * $item['price']) - $item['discount'];
            }

            $discount = $request->discount ?? 0;
            $grandTotal = $subtotal - $discount;
            $paid = $request->paid_amount ?? 0;

            // Auto Invoice No if empty
            $invoiceNo = $request->voucher_no;
            if (!$invoiceNo) {
                $lastId = DB::table('sales_ledgers')->max('id') + 1;
                $invoiceNo = 'INV-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);
            }

            if($request->file('slip_image'))
            {
                $slipPath = FileUploader::upload($request->file('slip_image'), 'sales_slip');
            }
            else
            {
                $slipPath = '';   
            }
            

            // 🔥 Create Ledger
            $ledger = SalesLedger::create([
                'invoice_no' => $invoiceNo,
                'date'       => $request->sale_date,
                'time'       => now()->format('H:i:s'),
                'customer_id'=> $request->customer_id,
                'subtotal'   => $subtotal,
                'discount'   => $discount,
                'paid'       => $paid,
                'note'       => null,
                'slip_image' => $slipPath,
                'create_by'  => Auth::id(),
                'driver_id'  => Auth::user()->driver_id ?? null,
            ]);

            // 🔥 LOOP PRODUCTS
            foreach ($cartItems as $item) {

                $requiredQty = $item['final_quantity'];
                $remainingQty = $requiredQty;

                $driverId = Auth::user()->driver_id;

                $today = Carbon::today()->toDateString(); // 'YYYY-MM-DD'

                $issueItems = DriverIssueItem::where('product_id', $item['product_id'])
                    ->whereHas('driverIssue', function ($q) use ($driverId, $today) {
                        $q->where('driver_id', $driverId)
                        ->whereDate('issue_date', $today)   // today's date
                        ->where('status', 'open');    // status = open
                    })
                    ->lockForUpdate()
                    ->orderBy('created_at', 'asc') // FIFO
                    ->get();

                $totalAvailable = 0;

                foreach ($issueItems as $issue) {
                    $available = $issue->issue_qty - $issue->sold_qty;
                    $totalAvailable += $available;
                }

                if ($totalAvailable < $requiredQty) {
                    throw new \Exception('Driver stock not sufficient');
                }

                $totalPurchaseCost = 0;
                $totalIssued = 0;

                foreach ($issueItems as $issue) {

                    $available = $issue->issue_qty - $issue->sold_qty;

                    if ($available <= 0) continue;

                    $deduct = min($available, $remainingQty);

                    // 🔥 Increment sold_qty
                    $issue->increment('sold_qty', $deduct);

                    // 🔥 Take purchase_price directly from driver_issue_items
                    $totalPurchaseCost += $deduct * $issue->purchase_price;

                    $totalIssued += $deduct;
                    $remainingQty -= $deduct;

                    if ($remainingQty <= 0) break;
                }

                if ($remainingQty > 0) {
                    throw new \Exception('Driver stock not sufficient');
                }

                // Weighted average purchase price (if multiple issue rows used)
                $avgPurchasePrice = $totalPurchaseCost / $totalIssued;


                // 🔥 Insert Entry
                SalesEntry::create([
                    'ledger_id'      => $ledger->id,
                    'product_id'     => $item['product_id'],
                    'quantity'       => $item['qty'],
                    'final_quantity' => $item['final_quantity'],
                    'sub_unit_id'    => $item['sub_unit_id'],
                    'sale_price'     => $item['price'],
                    'discount'       => $item['discount'],
                    'purchase_price' => $avgPurchasePrice,
                ]);
            }

            // 🔥 Insert Payment If Paid > 0
            
            SalesPayment::create([
                'date'        => $request->sale_date,
                'time'        => now()->format('H:i:s'),
                'ledger_id'   => $ledger->id,
                'customer_id' => $request->customer_id,
                'amount'      => $paid ?? 0,
                'type'        => 0, // sale payment
                'note'        => null,
            ]);

            $invoice_url = $ledger->id;
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Sales Submitted';
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $invoice_url];
    }

    public function getSalesLedgerById($id)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new SalesLedger())->find($id);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Sales Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }
        return [$status_code, $status_message, $response];
    }

    public function deleteSalesById($id)
    {
        $status_code = $status_message = '';
        try {
            DB::beginTransaction();
            $ledger = SalesLedger::with('items')->findOrFail($id);

            // 1️⃣ Rollback sold_qty in driver_issue_items
            foreach ($ledger->items as $entry) {
                $requiredQty = $entry->final_quantity;

                $driverId = $ledger->driver_id;
                $today = $ledger->date;

                // Get the driver issue items that were used (FIFO)
                $issueItems = DriverIssueItem::where('product_id', $entry->product_id)
                    ->whereHas('driverIssue', function ($q) use ($driverId, $today) {
                        $q->where('driver_id', $driverId)
                        ->whereDate('issue_date', $today)
                        ->where('status', 'open'); 
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();

                $remainingQty = $requiredQty;

                foreach ($issueItems as $issue) {
                    $deduct = min($issue->sold_qty, $remainingQty); // make sure we don't go negative
                    if ($deduct > 0) {
                        $issue->decrement('sold_qty', $deduct);
                        $remainingQty -= $deduct;
                    }
                    if ($remainingQty <= 0) break;
                }
            }

            // 2️⃣ Delete Sales Entries
            SalesEntry::where('ledger_id', $ledger->id)->delete();

            // 3️⃣ Delete Payments
            SalesPayment::where('ledger_id', $ledger->id)->delete();

            // 4️⃣ Delete Ledger
            $ledger->delete();
            DB::commit();
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Sales Removed';
        } catch (\Throwable $th) {
            DB::rollBack();
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message];
    }
}