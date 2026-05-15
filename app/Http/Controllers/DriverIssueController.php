<?php

namespace App\Http\Controllers;

use App\Models\DriverIssues;
use App\Models\WareHouseStocks;
use App\Services\ApiService;
use App\Services\DriverIssueService;
use App\Services\DriverService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverIssueController extends Controller
{
    protected $path = 'backend.driver_issues';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['search']['free_text'] = $request->input('free_text', '');
        $data['search']['status'] = $request->input('status', '');
        $data['search']['from_date'] = $request->input('from_date', '');
        $data['search']['to_date'] = $request->input('to_date', '');

        if (Auth::user()->hasRole('Driver')) {
            $data['search']['driver_id'] = Auth::user()->driver_id ?? null;
        } else {
            $data['search']['driver_id'] = $request->input('driver_id', '');
            $data['drivers'] = (new DriverService())->getDriverList([], false, false)[2];
        }

        $data['issues'] = (new DriverIssueService())->getDriverIssueList($data['search'], true, false)[2];
        if (Auth::user()->hasRole('Driver')) {
            return view('driver.issues.index', $data);
        }
        return view($this->path . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $drivers = (new DriverService())->getDriverList([], false, false)[2];
        $blockedDriverIds = DriverIssues::query()
            ->whereDate('issue_date', now()->toDateString())
            ->where('status', 'open')
            ->whereHas('items')
            ->pluck('driver_id');

        $data['drivers'] = $drivers instanceof \Illuminate\Support\Collection
            ? $drivers->whereNotIn('id', $blockedDriverIds)->values()
            : $drivers;
        $data['products'] = (new StockService())->getWarehouseStocks(['available_only' => true], false, true)[2];

        return view($this->path.'.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        [$status_code, $status_message , $error_message] = (new DriverIssueService())->storeDriverIssue($request);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('driver-issues.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->withErrors($error_message)->with('error', $status_message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['issue'] = (new DriverIssueService())->getIssueDataById($id)[2];
        return view($this->path.'.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['drivers'] = (new DriverService())->getDriverList([],false,false)[2];
        $data['issue'] = (new DriverIssueService())->getIssueDataById($id)[2];
        $allProducts = (new StockService())->getWarehouseStocks([], false, true)[2];
        $onIssueIds = $data['issue']->items->pluck('product_id')->unique();
        $data['products'] = $allProducts->filter(function ($row) use ($onIssueIds) {
            return (float) ($row->available_qty ?? 0) > 0 || $onIssueIds->contains($row->product_id);
        })->values();
        if ($data['issue']->status !== 'open') {
            return redirect()
                ->route('driver-issues.index')
                ->with('error', 'Closed issue cannot be edited');
        }
        return view($this->path.'.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        [$status_code, $status_message, $error_message] = (new DriverIssueService())->updateDriverIssueById($request,$id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('driver-issues.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->withErrors($error_message)->with('error', $status_message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        [$status_code, $status_message] = (new DriverIssueService())->deleteIssuebyId($id);
        if ($status_code == ApiService::API_SUCCESS) {
            return redirect()
                ->route('driver-issues.index')
                ->with('success', $status_message);
        }
        return redirect()->back()->withInput()->with('error', $status_message);
    }

    public function accept($id)
    {
        try {
            DB::beginTransaction();
            $issue = DriverIssues::with('items')->lockForUpdate()->findOrFail($id);

            if (! Auth::user()->driver_id || (int) Auth::user()->driver_id !== (int) $issue->driver_id) {
                abort(403, 'Only this DSR can accept their stock issue.');
            }

            if ($issue->status !== 'open') {
                throw new \Exception('Issue already processed');
            }

            if ($issue->items->isEmpty()) {
                throw new \Exception('This issue has no line items.');
            }

            foreach ($issue->items as $item) {

                $requiredQty = (float) $item->issue_qty;

                if ($item->warehouse_stock_id) {
                    WareHouseStocks::applyDriverIssueDeductionOnWarehouseRow(
                        (int) $item->warehouse_stock_id,
                        (int) $item->product_id,
                        (float) $requiredQty
                    );

                    continue;
                }

                $totalCost = 0;
                $totalSale = 0;
                $totalIssued = 0;

                $stocks = WareHouseStocks::where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->orderBy('created_at', 'asc')
                    ->get();

                $totalAvailable = $stocks->sum(function ($stock) {
                    return $stock->purchase_qty
                        + $stock->sales_return_qty
                        - $stock->sales_qty
                        - $stock->return_qty
                        - $stock->sr_issue_qty;
                });

                if ($totalAvailable < $requiredQty) {
                    throw new \Exception('Warehouse stock is no longer sufficient for one or more products. Contact the warehouse.');
                }

                foreach ($stocks as $stock) {

                    $availableQty = $stock->purchase_qty
                        + $stock->sales_return_qty
                        - $stock->sales_qty
                        - $stock->return_qty
                        - $stock->sr_issue_qty;

                    if ($availableQty <= 0) {
                        continue;
                    }

                    $issueFromThisStock = min($availableQty, $requiredQty);

                    $totalCost += $issueFromThisStock * (float) $stock->purchase_price;
                    $totalSale += $issueFromThisStock * (float) ($stock->sale_price ?? 0);
                    $totalIssued += $issueFromThisStock;

                    $stock->increment('sr_issue_qty', $issueFromThisStock);

                    $requiredQty -= $issueFromThisStock;

                    if ($requiredQty <= 0) {
                        break;
                    }
                }

                if ($totalIssued <= 0) {
                    throw new \Exception('Stock not sufficient');
                }

                $item->update([
                    'purchase_price' => $totalCost / $totalIssued,
                    'sale_price' => $totalIssued > 0 ? $totalSale / $totalIssued : 0,
                ]);
            }

            $issue->update([
                'status' => 'accepted',
            ]);

            DB::commit();
            return back()->with('success', 'Stock issue accepted. Warehouse stock has been updated and your daily stock is ready.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', $th->getMessage());
        }
    }

    public function reject($id)
    {
        $issue = DriverIssues::findOrFail($id);

        if (! Auth::user()->driver_id || (int) Auth::user()->driver_id !== (int) $issue->driver_id) {
            abort(403, 'Only this DSR can reject their stock issue.');
        }

        if ($issue->status !== 'open') {
            return back()->with('error', 'This issue can no longer be rejected.');
        }

        $issue->status = 'rejected';
        $issue->save();

        return back()->with('success', 'Issue rejected. The warehouse can send a new issue for this date if needed.');
    }

}
