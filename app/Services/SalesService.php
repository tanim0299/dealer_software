<?php

namespace App\Services;

use App\Models\DriverIssueItem;
use App\Models\Customer;
use App\Models\SalesEntry;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Services\DriverPeriodService;
use App\Traits\FileUploader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesService {
    /**
     * Convert a qty in the sales_entry "quantity" unit (boxes, etc.) to base pieces for driver_issue_items.
     */
    public static function returnQtyLineUnitToPieces(?SalesEntry $entry, float $qtyLineUnit): float
    {
        if (!$entry) {
            return $qtyLineUnit;
        }
        $q = (float) $entry->quantity;
        if ($q <= 0.000001) {
            return $qtyLineUnit;
        }

        return $qtyLineUnit * ((float) $entry->final_quantity / $q);
    }

    /**
     * Qty still sellable from a driver issue line (matches StockService::getDriverStock).
     */
    public static function qtyAvailableOnDriverIssueLine(DriverIssueItem $line): float
    {
        // return_qty = customer sales returns received on van (adds sellable stock)
        return max(0.0, (float) $line->issue_qty - (float) $line->sold_qty + (float) ($line->return_qty ?? 0));
    }

    public function getSalesList($search = [], $is_paginate = true, $is_relation = true)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new SalesLedger())->getSalesList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Data Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = $th->getMessage();
        }

        return [$status_code, $status_message, $response];
    }    

    public function storeSales($request)
    {
        $status_code = $status_message = $invoice_url = '';
        try {
            DB::beginTransaction();

            if (Auth::user()->driver_id) {
                DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                    (int) Auth::user()->driver_id,
                    (string) $request->sale_date
                );
            }

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
            $dueAmount = $grandTotal - $paid;

            $globalCashCustomer = Customer::where('name', 'Cash Customer')
                ->whereNull('area_id')
                ->first();

            if ($globalCashCustomer && (int) $globalCashCustomer->id === (int) $request->customer_id && $dueAmount > 0) {
                throw new \Exception('Cash sale can not have due. Please create/select customer for due sale.');
            }

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

                $requiredQty = (float) $item['final_quantity'];
                $remainingQty = $requiredQty;

                $driverId = Auth::user()->driver_id;
                $period = DriverPeriodService::periodForDriver((int) $driverId);

                $issueItems = DriverIssueItem::where('product_id', $item['product_id'])
                    ->whereHas('driverIssue', function ($q) use ($driverId, $period) {
                        $q->where('driver_id', $driverId)
                            ->where('status', 'accepted')
                            ->whereDate('issue_date', '>=', $period['start_date'])
                            ->whereDate('issue_date', '<=', $period['end_date']);
                    })
                    ->lockForUpdate()
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                $totalAvailable = $issueItems->sum(fn ($issue) => self::qtyAvailableOnDriverIssueLine($issue));

                if ($totalAvailable + 0.000001 < $requiredQty) {
                    throw new \Exception('Driver stock not sufficient');
                }

                $slices = [];

                foreach ($issueItems as $issue) {
                    $available = self::qtyAvailableOnDriverIssueLine($issue);

                    if ($available <= 0) {
                        continue;
                    }

                    $deduct = min($available, $remainingQty);

                    $linePurchase = (float) $issue->purchase_price;
                    if ($deduct > 0 && $linePurchase <= 0) {
                        throw new \Exception(
                            'Missing purchase cost on driver stock for this product. Ask warehouse to re-issue with correct batch / price.'
                        );
                    }

                    // FIFO: oldest driver_issue_items rows first — increment sold_qty
                    $issue->increment('sold_qty', $deduct);

                    $slices[] = [
                        'deduct' => $deduct,
                        'purchase_price' => $linePurchase,
                    ];
                    $remainingQty -= $deduct;

                    if ($remainingQty <= 0) {
                        break;
                    }
                }

                if ($remainingQty > 0.000001) {
                    throw new \Exception('Driver stock not sufficient');
                }

                $saleLineUid = (string) Str::uuid();
                $sliceCount = count($slices);
                $totalCartQty = (float) $item['qty'];
                $discountTotal = (float) ($item['discount'] ?? 0);
                $sumQtyAssigned = 0.0;
                $sumDiscountAssigned = 0.0;

                foreach ($slices as $idx => $slice) {
                    $deduct = (float) $slice['deduct'];
                    $isLast = $idx === $sliceCount - 1;

                    $sliceFinal = $deduct;

                    $sliceQty = $isLast
                        ? max(0.0, $totalCartQty - $sumQtyAssigned)
                        : ($requiredQty > 0.000001
                            ? round($totalCartQty * ($deduct / $requiredQty), 4)
                            : 0.0);
                    $sumQtyAssigned += $sliceQty;

                    $sliceDiscount = $isLast
                        ? max(0.0, $discountTotal - $sumDiscountAssigned)
                        : ($requiredQty > 0.000001
                            ? round($discountTotal * ($deduct / $requiredQty), 4)
                            : 0.0);
                    $sumDiscountAssigned += $sliceDiscount;

                    if ($slice['purchase_price'] <= 0 && $deduct > 0) {
                        throw new \Exception(
                            'Sale line has no valid purchase price. Check driver issue / warehouse issue lines.'
                        );
                    }

                    SalesEntry::create([
                        'sale_line_uid'  => $saleLineUid,
                        'ledger_id'      => $ledger->id,
                        'product_id'     => $item['product_id'],
                        'quantity'       => $sliceQty,
                        'final_quantity' => $sliceFinal,
                        'sub_unit_id'    => $item['sub_unit_id'],
                        'sale_price'     => $item['price'],
                        'discount'       => $sliceDiscount,
                        'purchase_price' => round((float) $slice['purchase_price'], 4),
                    ]);
                }
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
            $response = SalesLedger::with(['customer', 'driver', 'items.product', 'items.subUnit'])->find($id);
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

            if (Auth::user()->hasRole('Driver')
                && $ledger->driver_id
                && (int) $ledger->driver_id === (int) Auth::user()->driver_id
            ) {
                DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                    (int) $ledger->driver_id,
                    (string) $ledger->date
                );
            }

            // 1️⃣ Rollback sold_qty in driver_issue_items (one FIFO pass per invoice line / bundle)
            $bundles = $ledger->items->groupBy(fn (SalesEntry $e) => $e->sale_line_uid ?: ('legacy:'.$e->id));

            foreach ($bundles as $bundle) {
                $first = $bundle->first();
                $requiredQty = (float) $bundle->sum('final_quantity');

                $driverId = $ledger->driver_id;
                $period = DriverPeriodService::periodForDriver((int) $driverId);

                $issueItems = DriverIssueItem::where('product_id', $first->product_id)
                    ->whereHas('driverIssue', function ($q) use ($driverId, $period) {
                        $q->where('driver_id', $driverId)
                            ->where('status', 'accepted')
                            ->whereDate('issue_date', '>=', $period['start_date'])
                            ->whereDate('issue_date', '<=', $period['end_date']);
                    })
                    ->lockForUpdate()
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                $remainingQty = $requiredQty;

                foreach ($issueItems as $issue) {
                    $deduct = min((float) $issue->sold_qty, $remainingQty);
                    if ($deduct > 0) {
                        $issue->decrement('sold_qty', $deduct);
                        $remainingQty -= $deduct;
                    }
                    if ($remainingQty <= 0) {
                        break;
                    }
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