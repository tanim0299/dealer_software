<?php
namespace App\Services;

use App\Models\DriverIssueItem;
use App\Models\WareHouseStocks;
use Illuminate\Support\Facades\DB;

class StockService {
    public function getWarehouseStocks($search = [], $is_paginate = true, $is_relation = false)
    {
        $status_code = $status_message = $response = '';
        try {
            $response = (new WareHouseStocks())->getStockList($search, $is_paginate, $is_relation);
            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Stock Data Found';
        } catch (\Throwable $th) {
            $status_code =  ApiService::API_SERVER_ERROR;
            $status_message = ApiService::friendlyExceptionMessage($th);
        }
        return [$status_code , $status_message, $response];
    }

    public function getDriverStock($driver_id, $date = null)
    {
        $status_code = $status_message = $response = '';
        try {
            $period = DriverPeriodService::periodForDriver((int) $driver_id);
            if ($date) {
                $period = DriverPeriodService::packPeriod(
                    \Carbon\Carbon::parse($period['start_date']),
                    \Carbon\Carbon::parse($date)
                );
            }

            $response = DriverIssueItem::query()
                ->select(
                    'product_id',
                    DB::raw('SUM(issue_qty) as issue_qty'),
                    DB::raw('SUM(sold_qty) as sold_qty'),
                    DB::raw('SUM(return_qty) as return_qty'),
                    DB::raw('
                        SUM(issue_qty)
                        - SUM(sold_qty)
                        + SUM(return_qty)
                        AS available_qty
                    ')
                )
                ->whereHas('driverIssue', function ($q) use ($driver_id, $period) {
                    $q->where('driver_id', $driver_id)
                        ->where('status', 'accepted')
                        ->whereDate('issue_date', '>=', $period['start_date'])
                        ->whereDate('issue_date', '<=', $period['end_date']);
                })
                ->groupBy('product_id')
                ->havingRaw('available_qty > 0')
                ->with([
                    'product:id,name,sale_price,unit_id',   // 🔥 sale_price from products
                    'product.unit.sub_unit'                 // 🔥 sub units
                ])
                ->get();




            $status_code = ApiService::API_SUCCESS;
            $status_message = 'Stock Found';
        } catch (\Throwable $th) {
            $status_code = ApiService::API_SERVER_ERROR;
            $status_message = ApiService::friendlyExceptionMessage($th);
        }
        return [$status_code, $status_message, $response];
    }

    /**
     * @return array<int, array{product_id:int,name:string,available_qty:float,avg_purchase_price:float}>
     */
    public function getProductsWithStockForPurchaseReturn(?string $search = null): array
    {
        $avExpr = '(purchase_qty + sales_return_qty - sales_qty - return_qty - COALESCE(sr_issue_qty,0))';

        $q = WareHouseStocks::query()
            ->select([
                'product_id',
                DB::raw("SUM($avExpr) as available_qty"),
                DB::raw("SUM(CASE WHEN $avExpr > 0 THEN $avExpr * purchase_price ELSE 0 END) / NULLIF(SUM(CASE WHEN $avExpr > 0 THEN $avExpr ELSE 0 END), 0) as avg_purchase_price"),
            ])
            ->groupBy('product_id')
            ->havingRaw("SUM($avExpr) > 0")
            ->orderBy('product_id');

        if ($search !== null && $search !== '') {
            $term = '%' . addcslashes($search, '%_\\') . '%';
            $q->whereHas('product', function ($p) use ($term) {
                $p->where('name', 'like', $term);
            });
        }

        return $q->with('product:id,name')
            ->get()
            ->map(function ($row) {
                return [
                    'product_id' => (int) $row->product_id,
                    'name' => $row->product->name ?? '',
                    'available_qty' => round((float) $row->available_qty, 4),
                    'avg_purchase_price' => round((float) $row->avg_purchase_price, 4),
                ];
            })
            ->values()
            ->all();
    }
}