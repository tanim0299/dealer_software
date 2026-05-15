<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Balance Sheet — {{ $supplier->name ?? 'Supplier' }}</title>
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --surface: #f8fafc;
            --accent: #0f766e;
            --accent-soft: #ccfbf1;
            --danger: #b91c1c;
            --radius: 8px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fff;
            color: var(--ink);
            font-size: 13px;
            line-height: 1.45;
        }

        .sheet {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px 28px 40px;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .sheet {
                max-width: none;
                padding: 12px 16px;
            }

            .no-print {
                display: none !important;
            }

            a {
                color: inherit;
                text-decoration: none;
            }

            tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group;
            }
        }

        @page {
            margin: 12mm;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-top: 18px;
            margin-bottom: 20px;
        }

        .meta-card {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            padding: 12px 14px;
            background: var(--surface);
        }

        .meta-card .label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .meta-card .value {
            font-size: 15px;
            font-weight: 600;
            color: var(--ink);
        }

        .meta-card .value.sm {
            font-size: 13px;
            font-weight: 500;
        }

        .period-pill {
            display: inline-block;
            margin-top: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 11px;
            font-weight: 600;
        }

        .ledger-table-wrap {
            border: 1px solid var(--line);
            border-radius: var(--radius);
            overflow: hidden;
            margin-top: 8px;
        }

        table.ledger {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table.ledger thead th {
            background: var(--ink);
            color: #fff;
            font-weight: 600;
            text-align: left;
            padding: 10px 10px;
            font-size: 11px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        table.ledger thead th.num {
            text-align: right;
        }

        table.ledger tbody td {
            padding: 9px 10px;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }

        table.ledger tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        table.ledger tbody tr.opening-row td {
            background: #fffbeb;
            font-weight: 600;
            border-bottom: 2px solid #fcd34d;
        }

        table.ledger tbody tr.closing-row td {
            background: var(--accent-soft);
            font-weight: 700;
            border-bottom: none;
            border-top: 2px solid var(--accent);
        }

        .num {
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .type-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .type-badge.purchase { background: #dbeafe; color: #1d4ed8; }
        .type-badge.payment { background: #dcfce7; color: #15803d; }
        .type-badge.return { background: #fee2e2; color: var(--danger); }
        .type-badge.return-due {
            background: #ede9fe;
            color: #5b21b6;
            margin-top: 4px;
        }

        .type-badge.opening { background: #ede9fe; color: #5b21b6; }
            color: var(--muted);
            font-size: 11px;
            margin-top: 4px;
            line-height: 1.4;
        }

        .footer-note {
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            font-size: 11px;
            color: var(--muted);
            text-align: center;
        }

        .empty-state {
            text-align: center;
            padding: 36px 16px;
            color: var(--muted);
        }

        .letterhead .print-btn button {
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid var(--ink);
            background: var(--ink);
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>
<body>

@php
    $periodEndLabel = '';
    if (($report_type ?? '') === 'daily' && !empty($date)) {
        $periodEndLabel = \Carbon\Carbon::parse($date)->format('d M Y');
    } elseif (($report_type ?? '') === 'date_to_date' && !empty($to_date)) {
        $periodEndLabel = \Carbon\Carbon::parse($to_date)->format('d M Y');
    } elseif (($report_type ?? '') === 'monthly' && !empty($month)) {
        $periodEndLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->endOfMonth()->format('d M Y');
    } elseif (($report_type ?? '') === 'yearly' && !empty($year)) {
        $periodEndLabel = '31 Dec '.$year;
    }
    $txnCount = ($items ?? collect())->count();
@endphp

<div class="sheet">

    @include('letterhead', ['report_title' => $report_title])

    <div class="meta-grid">
        <div class="meta-card">
            <div class="label">Supplier</div>
            <div class="value">{{ $supplier->name ?? 'N/A' }}</div>
            @if(!empty($supplier->supplier_id))
                <span class="period-pill">ID: {{ $supplier->supplier_id }}</span>
            @endif
        </div>
        <div class="meta-card">
            <div class="label">Contact</div>
            <div class="value sm">{{ $supplier->phone ?? '—' }}</div>
            @if(!empty($supplier->email))
                <div class="desc-muted">{{ $supplier->email }}</div>
            @endif
        </div>
        <div class="meta-card">
            <div class="label">Report period</div>
            <div class="value sm">{{ $report_title }}</div>
            @if($periodEndLabel !== '')
                <div class="desc-muted">Through {{ $periodEndLabel }}</div>
            @endif
        </div>
        <div class="meta-card">
            <div class="label">Generated</div>
            <div class="value sm">{{ now()->format('d M Y, H:i') }}</div>
            <div class="desc-muted">{{ $txnCount }} transaction line(s) in period</div>
        </div>
    </div>

    <div class="ledger-table-wrap">
        <table class="ledger">
            <thead>
                <tr>
                    <th style="width:9%;">Date</th>
                    <th style="width:11%;">Type</th>
                    <th style="width:13%;">Reference</th>
                    <th>Description</th>
                    <th class="num" style="width:11%;">Bill / Due</th>
                    <th class="num" style="width:10%;">Paid</th>
                    <th class="num" style="width:10%;">Return</th>
                    <th class="num" style="width:11%;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $balance = (float) $previous_balance;
                @endphp
                <tr class="opening-row">
                    <td>—</td>
                    <td><span class="type-badge opening">Opening</span></td>
                    <td colspan="2">Previous balance as of {{ \Carbon\Carbon::parse($previous_date)->format('d M Y') }}</td>
                    <td class="num">—</td>
                    <td class="num">—</td>
                    <td class="num">—</td>
                    <td class="num">{{ number_format($previous_balance, 2) }}</td>
                </tr>

                @if(($items ?? collect())->isEmpty())
                    <tr>
                        <td colspan="8" class="empty-state">No transactions in the selected period.</td>
                    </tr>
                @else
                    @foreach($items as $row)
                        @if($row->kind === 'payment')
                            @php
                                $item = $row->payment;
                                if ((int) $item->type === 1 && $item->purchase) {
                                    $balance += ($item->purchase->total_amount - $item->purchase->discount) - (float) $item->amount;
                                } elseif ((int) $item->type === \App\Models\SupplierPayment::TYPE_PREVIOUS_DUE) {
                                    $balance += (float) $item->amount;
                                } elseif ((int) $item->type === 2) {
                                    $balance -= (float) $item->amount;
                                }
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->payment_date)->format('d M Y') }}</td>
                                <td>
                                    @if((int) $item->type === 1)
                                        <span class="type-badge purchase">Purchase</span>
                                    @elseif((int) $item->type === \App\Models\SupplierPayment::TYPE_PREVIOUS_DUE)
                                        <span class="type-badge opening">Opening</span>
                                    @elseif((int) $item->type === 2)
                                        <span class="type-badge payment">Payment</span>
                                    @else
                                        <span class="type-badge">Other</span>
                                    @endif
                                </td>
                                <td>
                                    @if((int) $item->type === 1 && $item->purchase)
                                        <a href="{{ route('purchase.invoice', $item->purchase->id) }}" target="_blank" rel="noopener">
                                            {{ $item->purchase->invoice_no ?? 'INV #'.$item->purchase->id }}
                                        </a>
                                    @elseif((int) $item->type === \App\Models\SupplierPayment::TYPE_PREVIOUS_DUE)
                                        <span class="desc-muted">Brought forward</span>
                                    @elseif((int) $item->type === 2)
                                        <span class="desc-muted">{{ \Illuminate\Support\Str::limit($item->note ?? 'Due payment', 48) }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if((int) $item->type === 1 && $item->purchase)
                                        <div class="desc-muted">
                                            @foreach($item->purchase->entries ?? [] as $entry)
                                                {{ $entry->product->name ?? 'Item' }} — {{ number_format($entry->final_quantity, 2) }} × {{ number_format($entry->unit_price, 2) }}
                                                @if((float) ($entry->discount ?? 0) > 0)
                                                    ; disc. {{ number_format($entry->discount, 2) }}
                                                @endif
                                                <br>
                                            @endforeach
                                        </div>
                                    @elseif((int) $item->type === \App\Models\SupplierPayment::TYPE_PREVIOUS_DUE)
                                        <span class="desc-muted">Opening supplier due</span>
                                    @else
                                        <span class="desc-muted">—</span>
                                    @endif
                                </td>
                                <td class="num">
                                    @if((int) $item->type === 1 && $item->purchase)
                                        {{ number_format($item->purchase->total_amount - $item->purchase->discount, 2) }}
                                    @elseif((int) $item->type === \App\Models\SupplierPayment::TYPE_PREVIOUS_DUE)
                                        {{ number_format($item->amount, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="num">
                                    @if((int) $item->type === 1 || (int) $item->type === 2)
                                        {{ number_format($item->amount, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="num">—</td>
                                <td class="num"><strong>{{ number_format($balance, 2) }}</strong></td>
                            </tr>
                        @else
                            @php
                                $ret = $row->return;
                                $grand = (float) ((($ret->grand_total ?? 0) > 0.0001) ? $ret->grand_total : $ret->subtotal);
                                $dueAdj = (float) ($ret->due_adjustment ?? 0);
                                $cashPortion = (float) ($ret->cash_portion ?? 0);
                                $balance -= $grand;
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($ret->date)->format('d M Y') }}</td>
                                <td>
                                    <span class="type-badge return">Purchase return</span>
                                    @if((int) $ret->return_type === 2)
                                        <div><span class="type-badge return-due">Minus from due</span></div>
                                    @else
                                        <div class="desc-muted" style="margin-top:6px;">Cash settlement</div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('purchase_return.show', $ret->id) }}" target="_blank" rel="noopener">PR #{{ $ret->id }}</a>
                                </td>
                                <td>
                                    <div class="desc-muted">
                                        @if((int) $ret->return_type === 2)
                                            Due adjusted: {{ number_format($dueAdj, 2) }}
                                            @if($cashPortion > 0.0001)
                                                · Cash: {{ number_format($cashPortion, 2) }}
                                            @endif
                                        @else
                                            Full return value settled in cash.
                                        @endif
                                    </div>
                                    @if($ret->entries && $ret->entries->isNotEmpty())
                                        <div class="desc-muted" style="margin-top:6px;">
                                            @foreach($ret->entries as $entry)
                                                {{ $entry->product->name ?? 'Item' }} — {{ number_format($entry->return_qty, 4) }} @ {{ number_format($entry->purchase_price, 4) }}<br>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="num">—</td>
                                <td class="num">
                                    @if($cashPortion > 0.0001)
                                        {{ number_format($cashPortion, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="num">{{ number_format($grand, 2) }}</td>
                                <td class="num"><strong>{{ number_format($balance, 2) }}</strong></td>
                            </tr>
                        @endif
                    @endforeach
                @endif

                @if($txnCount > 0)
                    <tr class="closing-row">
                        <td colspan="5" class="num" style="text-align:right;padding-right:14px;">Closing balance (payable)</td>
                        <td class="num">—</td>
                        <td class="num">—</td>
                        <td class="num">{{ number_format($balance, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer-note">
        This statement is system-generated. Amounts are shown in Tk. Verify with original vouchers.
    </div>
</div>

</body>
</html>
