<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WareHouseStocks extends Model
{
    protected $guarded = [];

    protected $appends = ['stock_qty'];

    /**
     * Normalize unit/purchase price so JSON floats and DB decimals match the same batch row.
     */
    public static function normalizePurchasePrice($unitPrice): float
    {
        return round((float) $unitPrice, 4);
    }

    /**
     * Find warehouse row for same product + same purchase price tier (avoids duplicate rows from float drift).
     */
    public static function findMatchingStockRow(int $productId, $unitPrice): ?self
    {
        $p = self::normalizePurchasePrice($unitPrice);

        return static::query()
            ->where('product_id', $productId)
            ->whereRaw('ROUND(purchase_price, 4) = ?', [$p])
            ->first();
    }

    public function getStockQtyAttribute()
    {
        return ($this->purchase_qty + $this->sales_return_qty)
               - ($this->sales_qty + $this->return_qty + $this->sr_issue_qty);
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    /**
     * Qty available to allocate for purchase return (FIFO layers).
     */
    public function availableQuantityForReturn(): float
    {
        $qty = (float) $this->purchase_qty
            + (float) $this->sales_return_qty
            - (float) $this->sales_qty
            - (float) $this->return_qty
            - (float) ($this->sr_issue_qty ?? 0);

        return max(0, $qty);
    }

    /**
     * Same as availableQuantityForReturn — qty still available before next driver-issue accept.
     */
    public function quantityAvailableForDriverIssue(): float
    {
        return $this->availableQuantityForReturn();
    }

    /**
     * FIFO across warehouse rows (oldest first). Caller must run inside a DB transaction.
     *
     * @return array<int, array{warehouse_stock_id:int, qty:float, purchase_price:float, sale_price:float}>
     *
     * @throws \RuntimeException
     */
    public static function planFifoSlicesForProduct(int $productId, float $qty): array
    {
        if ($qty <= 0) {
            return [];
        }

        $stocks = static::query()
            ->where('product_id', $productId)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();

        $remaining = $qty;
        $slices = [];

        foreach ($stocks as $stock) {
            if ($remaining <= 0) {
                break;
            }
            $available = $stock->quantityAvailableForDriverIssue();
            if ($available <= 0) {
                continue;
            }
            $take = min($available, $remaining);
            if ($take <= 0) {
                continue;
            }
            $slices[] = [
                'warehouse_stock_id' => (int) $stock->id,
                'qty' => (float) $take,
                'purchase_price' => (float) $stock->purchase_price,
                'sale_price' => (float) ($stock->sale_price ?? 0),
            ];
            $remaining -= $take;
        }

        if ($remaining > 0.000001) {
            throw new \RuntimeException('Insufficient warehouse stock for product ID '.$productId.'.');
        }

        return $slices;
    }

    /**
     * Apply driver-issue quantity to warehouse (increment sr_issue_qty). Caller must be in a transaction.
     *
     * @throws \RuntimeException
     */
    public static function applyDriverIssueDeductionOnWarehouseRow(int $warehouseStockId, int $productId, float $qty): void
    {
        if ($qty <= 0) {
            return;
        }

        $stock = static::query()->where('id', $warehouseStockId)->lockForUpdate()->first();

        if (! $stock || (int) $stock->product_id !== (int) $productId) {
            throw new \RuntimeException('Warehouse batch for issued line is missing or does not match the product.');
        }

        $availableQty = $stock->quantityAvailableForDriverIssue();
        if ($availableQty + 0.000001 < $qty) {
            throw new \RuntimeException('Warehouse stock is no longer sufficient for one or more products. Contact the warehouse.');
        }

        $stock->increment('sr_issue_qty', $qty);
    }

    public function getStockList($search = [], $is_paginate = true, $is_relation = false)
    {
        $query = self::query()
        ->select(
            'product_id',
            
            DB::raw('SUM(purchase_qty) as purchase_qty'),
            DB::raw('SUM(sales_qty) as sales_qty'),
            DB::raw('SUM(sales_return_qty) as sales_return_qty'),
            DB::raw('SUM(return_qty) as return_qty'),
            DB::raw('SUM(sr_issue_qty) as sr_issue_qty'),
            DB::raw('
                (SUM(purchase_qty) + SUM(sales_return_qty))
                - (SUM(sales_qty) + SUM(return_qty) + SUM(sr_issue_qty))
                AS available_qty
            '),
            DB::raw('MAX(purchase_price) as purchase_price'),
        )
        ->groupBy('product_id');


        if (!empty($search['free_text'])) {
            $query->where(function ($q) use ($search) {
                $q->where('product_id', 'like', '%' . $search['free_text'] . '%')
                ->orWhereHas('product', function ($p) use ($search) {
                    $p->where('name', 'like', '%' . $search['free_text'] . '%');
                });
            });
        }

        if (! empty($search['available_only'])) {
            $query->havingRaw(
                '(SUM(purchase_qty) + SUM(sales_return_qty)) - (SUM(sales_qty) + SUM(return_qty) + SUM(sr_issue_qty)) > 0'
            );
        }

        if($is_paginate)
        {
            $query = $query->paginate(10);   
        }
        else
        {
            $query = $query->get();
        }

        return $query;
    }
}
