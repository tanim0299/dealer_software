<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverIssueItem extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function driverIssue()
    {
        return $this->belongsTo(DriverIssues::class,'driver_issue_id');
    }

    public function warehouseStock()
    {
        return $this->belongsTo(WareHouseStocks::class, 'warehouse_stock_id');
    }

    /**
     * Spread sales-return qty across FIFO driver-issue lines (same product).
     */
    public static function addReturnQtyAcrossLines(int $driverIssueId, int $productId, float $qty): void
    {
        if ($qty <= 0) {
            return;
        }
        $remaining = $qty;
        $lines = self::query()
            ->where('driver_issue_id', $driverIssueId)
            ->where('product_id', $productId)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($lines as $line) {
            if ($remaining <= 0) {
                break;
            }
            // Cap attributed returns per line at what was sold from this line (FIFO fill).
            $room = max(0, (float) $line->sold_qty - (float) $line->return_qty);
            if ($room <= 0) {
                continue;
            }
            $add = min($room, $remaining);
            $line->increment('return_qty', $add);
            $remaining -= $add;
        }

        if ($remaining > 0 && $lines->isNotEmpty()) {
            $lines->last()->increment('return_qty', $remaining);
        }
    }

    /**
     * Roll back return qty (reverse of addReturnQtyAcrossLines), newest lines first.
     */
    public static function removeReturnQtyAcrossLines(int $driverIssueId, int $productId, float $qty): void
    {
        if ($qty <= 0) {
            return;
        }
        $remaining = $qty;
        $lines = self::query()
            ->where('driver_issue_id', $driverIssueId)
            ->where('product_id', $productId)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($lines as $line) {
            if ($remaining <= 0) {
                break;
            }
            $can = min((float) $line->return_qty, $remaining);
            if ($can > 0) {
                $line->decrement('return_qty', $can);
                $remaining -= $can;
            }
        }
    }
}
