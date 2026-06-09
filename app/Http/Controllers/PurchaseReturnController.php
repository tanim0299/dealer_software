<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturnEntry;
use App\Models\PurchaseReturnLedger;
use App\Models\SupplierPayment;
use App\Models\WareHouseStocks;
use App\Services\StockService;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    protected $path = 'backend.purchase_return';

    public function index(Request $request)
    {
        $query = PurchaseReturnLedger::with('supplier');

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->return_type) {
            $query->where('return_type', $request->return_type);
        }

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('created_at', [$request->from_date.' 00:00:00', $request->to_date.' 23:59:59']);
        }

        $ledgers = $query->orderBy('created_at', 'desc')->paginate(20);

        $suppliers = \App\Models\Supplier::all();

        $search = $request->only(['supplier_id', 'return_type', 'from_date', 'to_date']);

        return view($this->path.'.index', compact('ledgers', 'suppliers', 'search'));
    }

    /**
     * JSON: products that have positive warehouse stock (for purchase return).
     */
    public function stockProducts(Request $request)
    {
        $search = $request->get('search', '');
        $list = (new StockService())->getProductsWithStockForPurchaseReturn($search);

        return response()->json(['data' => $list]);
    }

    public function create()
    {
        $data['suppliers'] = (new SupplierService())->getSupplierList([], false, false)[2];

        return view($this->path.'.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'return_type' => 'required|in:1,2',
            'cart_items' => 'required|string',
            'order_discount' => 'nullable|numeric|min:0',
            'cash_paid' => 'nullable|numeric|min:0',
        ]);

        $items = json_decode($request->cart_items, true);
        if (! is_array($items) || count($items) === 0) {
            return redirect()->back()->with('error', 'Add at least one product to the cart.');
        }

        $merged = [];
        foreach ($items as $it) {
            $pid = (int) ($it['product_id'] ?? 0);
            $qty = (float) ($it['return_qty'] ?? 0);
            if ($pid < 1 || $qty <= 0) {
                continue;
            }
            $lineDisc = max(0.0, (float) ($it['line_discount'] ?? 0));
            if (! isset($merged[$pid])) {
                $merged[$pid] = [
                    'qty' => 0.0,
                    'line_discount' => 0.0,
                ];
            }
            $merged[$pid]['qty'] += $qty;
            $merged[$pid]['line_discount'] += $lineDisc;
        }

        if (count($merged) === 0) {
            return redirect()->back()->with('error', 'Invalid cart items.');
        }

        $supplierDueBefore = (new SupplierService())->getSupplierDueById($request->supplier_id);
        $returnType = (int) $request->return_type;

        if ($returnType === 2 && $supplierDueBefore <= 0.0001) {
            return redirect()->back()->with('error', 'This supplier has no due. Use Cash return only.');
        }

        $orderDiscount = max(0.0, (float) $request->input('order_discount', 0));

        DB::beginTransaction();
        try {
            $ledger = PurchaseReturnLedger::create([
                'date' => date('Y-m-d'),
                'supplier_id' => $request->supplier_id,
                'return_type' => $returnType,
                'subtotal' => 0,
                'line_discount_total' => 0,
                'order_discount' => 0,
                'grand_total' => 0,
                'due_adjustment' => 0,
                'cash_portion' => 0,
            ]);

            $fifoRows = [];

            foreach ($merged as $productId => $meta) {
                $returnQty = $meta['qty'];
                if ($returnQty <= 0) {
                    continue;
                }

                $stocks = WareHouseStocks::where('product_id', $productId)
                    ->orderBy('purchase_price', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                $remaining = $returnQty;

                foreach ($stocks as $stock) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $availableQty = $stock->availableQuantityForReturn();
                    if ($availableQty <= 0) {
                        continue;
                    }

                    $applyQty = min($remaining, $availableQty);

                    $stock->increment('return_qty', $applyQty);

                    $gross = $applyQty * (float) $stock->purchase_price;

                    $fifoRows[] = [
                        'product_id' => $productId,
                        'return_qty' => $applyQty,
                        'purchase_price' => (float) $stock->purchase_price,
                        'gross' => $gross,
                        'discount' => 0.0,
                    ];

                    $remaining -= $applyQty;
                }

                if ($remaining > 0.0000001) {
                    DB::rollBack();

                    return redirect()->back()->with('error', "Return quantity for product #{$productId} exceeds available stock.");
                }
            }

            $lineDiscountByProduct = [];
            foreach ($merged as $pid => $meta) {
                $lineDiscountByProduct[(int) $pid] = (float) $meta['line_discount'];
            }

            $fifoRows = $this->allocateFifoLineDiscounts($fifoRows, $lineDiscountByProduct);

            $grossTotal = 0.0;
            $lineDiscountTotal = 0.0;
            foreach ($fifoRows as $row) {
                $grossTotal += $row['gross'];
                $lineDiscountTotal += $row['discount'];
                PurchaseReturnEntry::create([
                    'purchase_return_ledger_id' => $ledger->id,
                    'product_id' => $row['product_id'],
                    'return_qty' => $row['return_qty'],
                    'purchase_price' => $row['purchase_price'],
                    'discount' => $row['discount'],
                ]);
            }

            $subtotalAfterLines = max(0.0, $grossTotal - $lineDiscountTotal);
            $orderDiscount = min($orderDiscount, $subtotalAfterLines);
            $grandTotal = max(0.0, $subtotalAfterLines - $orderDiscount);

            if ($grandTotal <= 0.0001) {
                DB::rollBack();

                return redirect()->back()->with('error', 'Grand total must be greater than zero after discounts.');
            }

            $dueAdj = 0.0;
            $cashPortion = 0.0;

            if ($returnType === 1) {
                $cashPortion = $grandTotal;
                $dueAdj = 0.0;
            } else {
                $minCash = max(0.0, $grandTotal - $supplierDueBefore);
                $paidInput = $request->input('cash_paid');
                if ($paidInput === null || $paidInput === '') {
                    $cashPortion = $minCash;
                } else {
                    $cashPortion = (float) $paidInput;
                }
                if ($cashPortion < $minCash - 0.0001) {
                    DB::rollBack();

                    return redirect()->back()->with(
                        'error',
                        'Cash paid must be at least '.number_format($minCash, 2).' (return value minus supplier due).'
                    );
                }
                if ($cashPortion > $grandTotal + 0.0001) {
                    DB::rollBack();

                    return redirect()->back()->with('error', 'Cash paid cannot exceed the grand total.');
                }
                $cashPortion = min(max($cashPortion, $minCash), $grandTotal);
                $dueAdj = max(0.0, $grandTotal - $cashPortion);
            }

            $ledger->update([
                'subtotal' => $subtotalAfterLines,
                'line_discount_total' => $lineDiscountTotal,
                'order_discount' => $orderDiscount,
                'grand_total' => $grandTotal,
                'due_adjustment' => $dueAdj,
                'cash_portion' => $cashPortion,
            ]);

            if ($cashPortion > 0.0001) {
                SupplierPayment::create([
                    'supplier_id' => $request->supplier_id,
                    'payment_date' => now()->toDateString(),
                    'amount' => -1 * $cashPortion,
                    'payment_method' => 'Cash',
                    'note' => 'Purchase return',
                    'type' => SupplierPayment::TYPE_PURCHASE_RETURN,
                    'created_by' => Auth::id(),
                    'reference_no' => $ledger->id,
                ]);
            }

            DB::commit();

            return redirect()->route('purchase_return.index')->with('success', 'Purchase return processed successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Something went wrong: '.\App\Services\ApiService::friendlyExceptionMessage($e));
        }
    }

    /**
     * @param  array<int, array{product_id:int, return_qty:float, purchase_price:float, gross:float, discount:float}>  $fifoRows
     * @param  array<int, float>  $lineDiscountByProduct
     * @return array<int, array{product_id:int, return_qty:float, purchase_price:float, gross:float, discount:float}>
     */
    private function allocateFifoLineDiscounts(array $fifoRows, array $lineDiscountByProduct): array
    {
        $byProduct = [];
        foreach ($fifoRows as $idx => $row) {
            $pid = (int) $row['product_id'];
            $byProduct[$pid][] = $idx;
        }

        foreach ($byProduct as $productId => $indices) {
            $totalGross = 0.0;
            foreach ($indices as $idx) {
                $totalGross += $fifoRows[$idx]['gross'];
            }
            $requested = (float) ($lineDiscountByProduct[$productId] ?? 0);
            $D = min(max(0.0, $requested), $totalGross);
            if ($totalGross <= 0.0000001 || $D <= 0.0000001) {
                continue;
            }

            $remaining = $D;
            $last = count($indices) - 1;
            foreach ($indices as $i => $idx) {
                if ($i === $last) {
                    $fifoRows[$idx]['discount'] = round($remaining, 4);
                } else {
                    $share = $D * ($fifoRows[$idx]['gross'] / $totalGross);
                    $fifoRows[$idx]['discount'] = round($share, 4);
                    $remaining -= $fifoRows[$idx]['discount'];
                }
            }
        }

        return $fifoRows;
    }

    public function show(string $id)
    {
        $ledger = PurchaseReturnLedger::with(['supplier', 'entries' => function ($q) {
            $q->orderBy('id');
        }, 'entries.product'])
            ->findOrFail($id);

        $supplierPayment = SupplierPayment::where('supplier_id', $ledger->supplier_id)
            ->where('type', SupplierPayment::TYPE_PURCHASE_RETURN)
            ->where('reference_no', $ledger->id)
            ->first();

        return view($this->path.'.show', compact('ledger', 'supplierPayment'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $ledger = PurchaseReturnLedger::with('entries')->findOrFail($id);

            foreach ($ledger->entries as $entry) {
                $stock = WareHouseStocks::findMatchingStockRow(
                    (int) $entry->product_id,
                    $entry->purchase_price
                );

                if (! $stock) {
                    throw new \Exception('Stock row not found for return rollback.');
                }

                if ($stock->return_qty < $entry->return_qty) {
                    throw new \Exception('Invalid stock rollback quantity detected.');
                }

                $stock->decrement('return_qty', $entry->return_qty);
            }

            $ledger->entries()->delete();

            SupplierPayment::where('supplier_id', $ledger->supplier_id)
                ->where('type', SupplierPayment::TYPE_PURCHASE_RETURN)
                ->where('reference_no', $ledger->id)
                ->delete();

            $ledger->delete();

            DB::commit();

            return redirect()->route('purchase_return.index')->with('success', 'Purchase return deleted and stock rolled back successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to delete purchase return: '.\App\Services\ApiService::friendlyExceptionMessage($e));
        }
    }
}
