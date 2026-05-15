<?php

namespace App\Http\Controllers;

use App\Models\DriverIssueItem;
use App\Models\DriverIssues;
use App\Models\SalesEntry;
use App\Models\SalesLedger;
use App\Models\SalesPayment;
use App\Models\SalesReturnEntries;
use App\Models\SalesReturnLedger;
use App\Services\CustomerService;
use App\Services\DriverCashService;
use App\Services\DriverPeriodService;
use App\Services\SalesService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    public function __construct() {}

    public function index(Request $request)
    {
        if (Auth::user()->hasRole('Driver')) {
            $today = now()->toDateString();

            if (! $request->filled('from_date') && ! $request->filled('to_date')) {
                $search['from_date'] = $today;
                $search['to_date'] = $today;
            } else {
                $from = $request->input('from_date');
                $to = $request->input('to_date');
                if (filled($from) && filled($to)) {
                    $search['from_date'] = $from;
                    $search['to_date'] = $to;
                } elseif (filled($from)) {
                    $search['from_date'] = $from;
                    $search['to_date'] = $from;
                } elseif (filled($to)) {
                    $search['from_date'] = $to;
                    $search['to_date'] = $to;
                } else {
                    $search['from_date'] = $today;
                    $search['to_date'] = $today;
                }
            }

            $search['free_text'] = $request->input('free_text');

            $returns = SalesReturnLedger::with(['customer', 'payments'])
                ->where('create_by', Auth::id())
                ->whereBetween('date', [$search['from_date'], $search['to_date']])
                ->when($request->filled('free_text'), function ($q) use ($request) {
                    $term = $request->input('free_text');
                    $q->where(function ($qq) use ($term) {
                        $qq->where('invoice_no', 'like', '%'.$term.'%')
                            ->orWhereHas('customer', function ($c) use ($term) {
                                $c->where('name', 'like', '%'.$term.'%');
                            });
                    });
                })
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();

            return view('driver.sales_return.index', compact('returns', 'search'));
        }

        $returns = SalesReturnLedger::with('customer')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('backend.sales_return.index', compact('returns'));
    }

    public function create(Request $request)
    {
        if (Auth::user()->hasRole('Driver')) {
            $driverId = (int) Auth::user()->driver_id;

            $sales_ledgers = SalesLedger::where('driver_id', $driverId)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->get(['id', 'invoice_no', 'date', 'customer_id']);

            $customers = (new CustomerService())->getrDriverCustomer($driverId)[2] ?? collect();

            [, , $stock] = (new StockService())->getDriverStock($driverId);
            $driverStockJson = collect($stock)->map(function ($row) {
                return [
                    'product_id' => $row->product_id,
                    'name' => $row->product->name ?? 'Product',
                    'sale_price' => (float) ($row->product->sale_price ?? 0),
                    'available_qty' => (float) $row->available_qty,
                ];
            })->values();

            $availableCash = DriverCashService::openPeriodAvailableCash((int) Auth::id(), $driverId);

            $intentInvoice = $request->get('intent') === 'invoice';

            $selectedLedger = null;
            $invoiceLineGroups = collect();

            if ($request->filled('invoice_id')) {
                $selectedLedger = SalesLedger::with([
                    'items.product',
                    'items.subUnit',
                    'customer',
                    'items' => function ($query) {
                        $query->withSum('returnEntries', 'return_qty');
                    },
                ])
                    ->where('driver_id', $driverId)
                    ->find($request->invoice_id);

                if ($selectedLedger) {
                    $invoiceLineGroups = $selectedLedger->items
                        ->sortBy('id')
                        ->groupBy(fn (SalesEntry $e) => $e->sale_line_uid ?: 'legacy:'.$e->id)
                        ->values();
                }
            }

            return view('driver.sales_return.create', compact(
                'sales_ledgers',
                'customers',
                'driverStockJson',
                'availableCash',
                'selectedLedger',
                'invoiceLineGroups',
                'intentInvoice'
            ));
        }
    }

    public function store(Request $request)
    {
        $driverId = (int) Auth::user()->driver_id;
        if (! $driverId) {
            return back()->with('error', 'Driver profile not linked.');
        }

        $validated = $request->validate([
            'return_mode'     => 'required|in:with_invoice,without_invoice',
            'sales_ledger_id' => 'nullable|exists:sales_ledgers,id',
            'customer_id'     => 'nullable|exists:customers,id',
            'settlement_mode' => 'required|in:cash_only,due_first',
            'return_discount' => 'nullable|numeric|min:0',
            'standalone_lines' => 'nullable|array',
            'standalone_lines.*.product_id' => 'nullable|exists:products,id',
            'standalone_lines.*.qty' => 'nullable|numeric|min:0',
            'standalone_lines.*.unit_price' => 'nullable|numeric|min:0',
            'items'           => 'nullable|array',
            'return_line'     => 'nullable|array',
            'return_line.*'   => 'nullable|boolean',
        ]);

        if ($validated['return_mode'] === 'with_invoice') {
            $request->validate(['sales_ledger_id' => 'required|exists:sales_ledgers,id']);
        } else {
            $request->validate(['customer_id' => 'required|exists:customers,id']);
        }

        $headerDiscount = min((float) ($validated['return_discount'] ?? 0), 1e12);

        DB::beginTransaction();

        try {
            if (Auth::user()->hasRole('Driver')) {
                DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                    $driverId,
                    now()->toDateString()
                );
            }

            $period = DriverPeriodService::periodForDriver($driverId);
            $driverIssue = DriverIssues::where('driver_id', $driverId)
                ->where('status', 'accepted')
                ->whereDate('issue_date', '>=', $period['start_date'])
                ->whereDate('issue_date', '<=', $period['end_date'])
                ->orderByDesc('issue_date')
                ->first();

            $linesSubtotal = 0.0;
            $returnMode = $validated['return_mode'];
            $salesLedger = null;
            $customerId = null;
            $invoiceNo = '—';

            if ($returnMode === 'with_invoice') {
                $salesLedger = SalesLedger::with('items')
                    ->where('driver_id', $driverId)
                    ->findOrFail($request->sales_ledger_id);
                $customerId = (int) $salesLedger->customer_id;
                $invoiceNo = $salesLedger->invoice_no ?? '—';
            } else {
                $customerId = (int) $request->customer_id;
                if (! (new CustomerService())->driverCanCollectFromCustomer($driverId, $customerId)) {
                    throw new \Exception('This customer is not assigned to you.');
                }
            }

            $returnLedger = SalesReturnLedger::create([
                'sales_ledger_id' => $salesLedger?->id,
                'invoice_no'      => $invoiceNo,
                'return_mode'     => $returnMode,
                'date'            => now()->toDateString(),
                'customer_id'     => $customerId,
                'lines_subtotal'  => 0,
                'discount'        => 0,
                'subtotal'        => 0,
                'create_by'       => Auth::id(),
            ]);

            if ($returnMode === 'with_invoice') {
                $linesSubtotal += $this->processWithInvoiceLines(
                    $request,
                    $salesLedger,
                    $returnLedger,
                    $driverIssue,
                    $driverId
                );
            } else {
                $linesSubtotal += $this->processWithoutInvoiceLines(
                    $request->input('standalone_lines', []),
                    $returnLedger,
                    $driverIssue,
                    $driverId,
                    $this->stockRowsByProduct($driverId)
                );
            }

            if ($linesSubtotal <= 0.0001) {
                throw new \Exception('Select at least one line with return quantity.');
            }

            $appliedHeaderDiscount = min($headerDiscount, $linesSubtotal);
            $grandTotal = round($linesSubtotal - $appliedHeaderDiscount, 4);

            $returnLedger->update([
                'lines_subtotal' => round($linesSubtotal, 4),
                'discount'       => $appliedHeaderDiscount,
                'subtotal'       => max(0, $grandTotal),
            ]);

            $dueBefore = (new CustomerService())->getCustomerDueById($customerId);
            $settlement = $validated['settlement_mode'];

            if ($dueBefore <= 0.009) {
                if ($settlement !== 'cash_only') {
                    throw new \Exception('This customer has no due — use cash paid only.');
                }
                $cashRefund = $grandTotal;
            } else {
                if ($settlement === 'cash_only') {
                    $cashRefund = $grandTotal;
                } else {
                    $dueCredit = min($dueBefore, $grandTotal);
                    $cashRefund = max(0.0, $grandTotal - $dueCredit);
                }
            }

            $available = DriverCashService::openPeriodAvailableCash((int) Auth::id(), $driverId);
            if ($cashRefund > $available + 0.02) {
                throw new \Exception(
                    'Not enough carrying cash for this return. Available: Tk '.number_format($available, 2)
                    .'; cash part needed: Tk '.number_format($cashRefund, 2)
                );
            }

            if ($grandTotal > 0.0001) {
                SalesPayment::create([
                    'date'           => now()->toDateString(),
                    'time'           => now()->toTimeString(),
                    'ledger_id'      => null,
                    'customer_id'    => $customerId,
                    'amount'         => $cashRefund > 0.0001 ? -$cashRefund : 0,
                    'type'           => SalesPayment::TYPE_RETURN,
                    'reference_type' => 'return',
                    'reference_id'   => $returnLedger->id,
                    'note'           => $settlement === 'due_first' && $dueBefore > 0.009
                        ? 'Sales return — due adjusted; cash portion Tk '.number_format($cashRefund, 2)
                        : 'Sales return — cash paid Tk '.number_format($cashRefund, 2),
                    'create_by'      => Auth::id(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'Sales return saved.');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage())->withInput();
        }
    }

    /**
     * @return float Lines subtotal added
     */
    private function processWithInvoiceLines(
        Request $request,
        SalesLedger $salesLedger,
        SalesReturnLedger $returnLedger,
        ?DriverIssues $driverIssue,
        int $driverId
    ): float {
        $linesSubtotal = 0.0;
        $selected = $request->input('return_line', []);

        foreach ($request->input('items', []) as $bundleKey => $returnQtyRaw) {
            if (empty($selected[$bundleKey])) {
                continue;
            }
            $returnQty = (float) $returnQtyRaw;
            if ($returnQty <= 0) {
                continue;
            }

            if (ctype_digit((string) $bundleKey)) {
                $entryId = (int) $bundleKey;
                $entry = SalesEntry::where('ledger_id', $salesLedger->id)->findOrFail($entryId);

                $alreadyReturned = SalesReturnEntries::where('sales_entry_id', $entryId)->sum('return_qty');
                $remainingLineQty = (float) $entry->quantity - (float) $alreadyReturned;
                if ($returnQty > $remainingLineQty + 0.00001) {
                    throw new \Exception('Return quantity exceeds sold quantity for a line.');
                }

                $unitNet = $this->netValuePerSaleQtyUnit($entry);
                $linesSubtotal += $returnQty * $unitNet;

                SalesReturnEntries::create([
                    'return_ledger_id' => $returnLedger->id,
                    'sales_entry_id'   => $entry->id,
                    'product_id'       => $entry->product_id,
                    'return_qty'       => $returnQty,
                    'sale_price'       => $unitNet,
                    'purchase_price'   => $entry->purchase_price,
                ]);

                if ($driverIssue) {
                    $pieces = SalesService::returnQtyLineUnitToPieces($entry, $returnQty);
                    DriverIssueItem::addReturnQtyAcrossLines(
                        (int) $driverIssue->id,
                        (int) $entry->product_id,
                        $pieces
                    );
                }
            } else {
                $entries = SalesEntry::where('ledger_id', $salesLedger->id)
                    ->where('sale_line_uid', $bundleKey)
                    ->orderBy('id')
                    ->get();

                if ($entries->isEmpty()) {
                    throw new \Exception('Invalid return line.');
                }

                $maxRemain = $entries->sum(function (SalesEntry $e) {
                    $alreadyReturned = SalesReturnEntries::where('sales_entry_id', $e->id)->sum('return_qty');

                    return max(0.0, (float) $e->quantity - (float) $alreadyReturned);
                });

                if ($returnQty > $maxRemain + 0.00001) {
                    throw new \Exception('Return quantity exceeds sold quantity for a bundle.');
                }

                $remaining = $returnQty;
                foreach ($entries as $entry) {
                    if ($remaining <= 0.000001) {
                        break;
                    }
                    $alreadyReturned = SalesReturnEntries::where('sales_entry_id', $entry->id)->sum('return_qty');
                    $cap = max(0.0, (float) $entry->quantity - (float) $alreadyReturned);
                    $take = min($cap, $remaining);
                    if ($take <= 0) {
                        continue;
                    }
                    $unitNet = $this->netValuePerSaleQtyUnit($entry);
                    $linesSubtotal += $take * $unitNet;

                    SalesReturnEntries::create([
                        'return_ledger_id' => $returnLedger->id,
                        'sales_entry_id'   => $entry->id,
                        'product_id'       => $entry->product_id,
                        'return_qty'       => $take,
                        'sale_price'       => $unitNet,
                        'purchase_price'   => $entry->purchase_price,
                    ]);

                    if ($driverIssue) {
                        $pieces = SalesService::returnQtyLineUnitToPieces($entry, $take);
                        DriverIssueItem::addReturnQtyAcrossLines(
                            (int) $driverIssue->id,
                            (int) $entry->product_id,
                            $pieces
                        );
                    }
                    $remaining -= $take;
                }
            }
        }

        return $linesSubtotal;
    }

    /**
     * @param  array<int, array{product_id?:int,qty?:float,unit_price?:float}>  $rows
     */
    private function processWithoutInvoiceLines(
        array $rows,
        SalesReturnLedger $returnLedger,
        ?DriverIssues $driverIssue,
        int $driverId,
        array $availableByProduct
    ): float {
        $byProduct = [];
        foreach ($rows as $row) {
            if (empty($row['product_id']) || empty($row['qty'])) {
                continue;
            }
            $pid = (int) $row['product_id'];
            $qty = (float) $row['qty'];
            $price = (float) ($row['unit_price'] ?? 0);
            if ($qty <= 0 || $price <= 0) {
                continue;
            }
            if (! isset($byProduct[$pid])) {
                $byProduct[$pid] = ['qty' => 0.0, 'unit_price' => $price];
            }
            $byProduct[$pid]['qty'] += $qty;
            // last non-zero price wins if user changed mid-form
            $byProduct[$pid]['unit_price'] = $price;
        }

        $linesSubtotal = 0.0;

        foreach ($byProduct as $productId => $agg) {
            $qty = $agg['qty'];
            $unitPrice = $agg['unit_price'];
            $avail = (float) ($availableByProduct[$productId] ?? 0);
            if ($qty > $avail + 0.0001) {
                throw new \Exception('Return qty exceeds available stock for a product.');
            }

            $linesSubtotal += $qty * $unitPrice;

            SalesReturnEntries::create([
                'return_ledger_id' => $returnLedger->id,
                'sales_entry_id'   => null,
                'product_id'       => (int) $productId,
                'return_qty'       => $qty,
                'sale_price'       => $unitPrice,
                'purchase_price'   => 0,
            ]);

            if ($driverIssue) {
                DriverIssueItem::addReturnQtyAcrossLines(
                    (int) $driverIssue->id,
                    (int) $productId,
                    $qty
                );
            }
        }

        return $linesSubtotal;
    }

    private function netValuePerSaleQtyUnit(SalesEntry $e): float
    {
        $qty = (float) $e->quantity;
        $gross = (float) $e->sale_price * $qty - (float) $e->discount;
        if ($qty > 0.000001) {
            return $gross / $qty;
        }
        $fq = (float) $e->final_quantity;
        if ($fq > 0.000001) {
            return ((float) $e->sale_price * $fq - (float) $e->discount) / $fq;
        }

        return (float) $e->sale_price;
    }

    public function show($id)
    {
        $return = SalesReturnLedger::with([
            'customer',
            'entries.product',
            'payments',
        ])->findOrFail($id);
        if (Auth::user()->hasRole('Driver')) {
            return view('driver.sales_return.show', compact('return'));
        }

        return view('backend.sales_return.show', compact('return'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $returnLedger = SalesReturnLedger::with('entries')
                ->findOrFail($id);

            if (Auth::user()->hasRole('Driver') && Auth::user()->driver_id) {
                if ((int) $returnLedger->create_by !== (int) Auth::id()) {
                    return back()->with('error', 'You can only delete your own return entries.');
                }
                DriverPeriodService::assertDriverPanelDateOpenForTransaction(
                    (int) Auth::user()->driver_id,
                    (string) $returnLedger->date
                );
            }

            foreach ($returnLedger->entries as $entry) {
                $driverIssue = DriverIssues::where('driver_id', Auth::user()->driver_id)
                    ->whereDate('issue_date', $returnLedger->date)
                    ->where('status', 'accepted')
                    ->first();

                if ($driverIssue) {
                    $salesEntry = $entry->sales_entry_id
                        ? SalesEntry::find($entry->sales_entry_id)
                        : null;
                    $pieces = $salesEntry
                        ? SalesService::returnQtyLineUnitToPieces($salesEntry, (float) $entry->return_qty)
                        : (float) $entry->return_qty;
                    DriverIssueItem::removeReturnQtyAcrossLines(
                        (int) $driverIssue->id,
                        (int) $entry->product_id,
                        $pieces
                    );
                }

                $entry->delete();
            }

            SalesPayment::where('reference_type', 'return')
                ->where('reference_id', $returnLedger->id)
                ->delete();

            $returnLedger->delete();

            DB::commit();

            return back()->with('success', 'Sales Return Deleted & Rolled Back Successfully');
        } catch (\Throwable $th) {
            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }

    /** @internal closure substitute */
    private function stockRowsByProduct(int $driverId): array
    {
        [, , $stock] = (new StockService())->getDriverStock($driverId);
        $map = [];
        foreach ($stock as $row) {
            $map[(int) $row->product_id] = (float) $row->available_qty;
        }

        return $map;
    }
}
