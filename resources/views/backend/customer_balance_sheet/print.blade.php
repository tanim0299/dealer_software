<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account statement — {{ $customer->name ?? 'Customer' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --stmt-ink: #0f172a;
            --stmt-muted: #475569;
            --stmt-border: #cbd5e1;
            --stmt-fill: #f8fafc;
            --stmt-accent: #1e3a5f;
            --stmt-accent-soft: #e8eef4;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Source Sans 3', ui-sans-serif, system-ui, sans-serif;
            font-size: 11.5px;
            line-height: 1.45;
            color: var(--stmt-ink);
            background: #e2e8f0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .stmt-wrap {
            max-width: 900px;
            margin: 24px auto;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--stmt-border);
            overflow: hidden;
        }

        .stmt-toolbar {
            padding: 12px 20px;
            background: var(--stmt-fill);
            border-bottom: 1px solid var(--stmt-border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .stmt-toolbar button {
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 6px;
            border: 1px solid var(--stmt-accent);
            background: var(--stmt-accent);
            color: #fff;
            cursor: pointer;
        }

        .stmt-toolbar button:hover {
            filter: brightness(1.08);
        }

        .stmt-inner {
            padding: 28px 32px 32px;
        }

        .stmt-header {
            display: grid;
            grid-template-columns: 88px 1fr;
            gap: 20px;
            align-items: start;
            padding-bottom: 22px;
            border-bottom: 2px solid var(--stmt-accent);
            margin-bottom: 22px;
        }

        .stmt-logo {
            width: 88px;
            height: 88px;
            border: 1px solid var(--stmt-border);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            overflow: hidden;
        }

        .stmt-logo img {
            max-width: 86%;
            max-height: 86%;
            object-fit: contain;
        }

        .stmt-logo-fallback {
            font-family: 'Source Serif 4', Georgia, serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--stmt-accent);
            line-height: 1;
        }

        .stmt-org h1 {
            font-family: 'Source Serif 4', Georgia, serif;
            margin: 0 0 6px;
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--stmt-accent);
        }

        .stmt-org-meta {
            font-size: 10.5px;
            color: var(--stmt-muted);
            line-height: 1.55;
        }

        .stmt-org-meta a {
            color: inherit;
        }

        .stmt-doc-title {
            margin-top: 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--stmt-muted);
        }

        .stmt-doc-sub {
            margin-top: 4px;
            font-size: 13px;
            font-weight: 600;
            color: var(--stmt-ink);
        }

        .stmt-panels {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .stmt-panels { grid-template-columns: 1fr; }
            .stmt-inner { padding: 20px 16px; }
            .stmt-header { grid-template-columns: 1fr; }
        }

        .stmt-panel {
            border: 1px solid var(--stmt-border);
            border-radius: 8px;
            padding: 12px 14px;
            background: var(--stmt-fill);
        }

        .stmt-panel h2 {
            margin: 0 0 10px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--stmt-accent);
        }

        .stmt-kv { margin: 0; font-size: 11px; }
        .stmt-kv dt {
            float: left;
            clear: left;
            width: 38%;
            margin: 0 0 6px;
            color: var(--stmt-muted);
            font-weight: 500;
        }
        .stmt-kv dd {
            margin: 0 0 6px;
            padding-left: 40%;
            font-weight: 600;
        }

        .stmt-table-wrap {
            border: 1px solid var(--stmt-border);
            border-radius: 8px;
            overflow: hidden;
        }

        table.stmt-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        table.stmt-table thead th {
            text-align: left;
            padding: 10px 8px;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            background: var(--stmt-accent);
            color: #fff;
            border-bottom: 2px solid #1e293b;
        }

        table.stmt-table thead th.stmt-num {
            text-align: right;
        }

        table.stmt-table tbody td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        table.stmt-table tbody tr:nth-child(even) td {
            background: #fafbfc;
        }

        table.stmt-table tbody tr.stmt-row-opening td {
            background: var(--stmt-accent-soft);
            font-weight: 600;
            border-bottom: 1px solid var(--stmt-border);
        }

        table.stmt-table .stmt-num {
            text-align: right;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        table.stmt-table .stmt-muted {
            color: var(--stmt-muted);
        }

        table.stmt-table a {
            color: #1d4ed8;
            text-decoration: none;
        }

        table.stmt-table a:hover {
            text-decoration: underline;
        }

        .stmt-desc {
            font-size: 10px;
            line-height: 1.4;
        }

        .stmt-empty {
            text-align: center;
            padding: 24px !important;
            color: var(--stmt-muted);
            font-style: italic;
        }

        .stmt-footer {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid var(--stmt-border);
            font-size: 9.5px;
            color: var(--stmt-muted);
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
        }

        @media print {
            body { background: #fff; }

            .stmt-wrap {
                margin: 0;
                max-width: none;
                border: none;
                border-radius: 0;
                box-shadow: none;
            }

            .stmt-toolbar { display: none !important; }

            .stmt-inner {
                padding: 12mm 14mm;
            }

            @page {
                size: A4;
                margin: 12mm;
            }
        }
    </style>
</head>
<body>
@php
    $s = $settings ?? null;
    $logoUrl = $s && !empty($s->logo) ? asset('storage/' . ltrim($s->logo, '/')) : null;
    $brandInitial = strtoupper(substr(optional($s)->title ?? 'C', 0, 1));
@endphp

<div class="stmt-wrap">
    <div class="stmt-toolbar no-print">
        <button type="button" onclick="window.print()">Print / Save PDF</button>
    </div>

    <div class="stmt-inner">
        <header class="stmt-header">
            <div class="stmt-logo" aria-hidden="{{ $logoUrl ? 'false' : 'true' }}">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ optional($s)->title ?? '' }}">
                @else
                    <span class="stmt-logo-fallback">{{ $brandInitial }}</span>
                @endif
            </div>
            <div class="stmt-org">
                <h1>{{ optional($s)->title ?? config('app.name') }}</h1>
                <div class="stmt-org-meta">
                    @if (!empty(optional($s)->address))
                        <div>{!! $s->address !!}</div>
                    @endif
                    @if (!empty(optional($s)->phone))
                        <div>Tel: {{ $s->phone }}</div>
                    @endif
                    @if (!empty(optional($s)->email))
                        <div>Email: <a href="mailto:{{ $s->email }}">{{ $s->email }}</a></div>
                    @endif
                </div>
                <div class="stmt-doc-title">Statement of account</div>
                <div class="stmt-doc-sub">{{ $report_title ?? 'Customer balance' }}</div>
            </div>
        </header>

        <div class="stmt-panels">
            <section class="stmt-panel" aria-labelledby="stmt-party-label">
                <h2 id="stmt-party-label">Account holder</h2>
                <dl class="stmt-kv">
                    <dt>Name</dt>
                    <dd>{{ $customer->name ?? 'N/A' }}</dd>
                    <dt>Account ID</dt>
                    <dd>#{{ $customer->id ?? '—' }}</dd>
                    @if (!empty($customer->phone ?? null))
                        <dt>Phone</dt>
                        <dd>{{ $customer->phone }}</dd>
                    @endif
                    @if (!empty($customer->customerArea->name ?? null))
                        <dt>Area</dt>
                        <dd>{{ $customer->customerArea->name }}</dd>
                    @endif
                </dl>
            </section>
            <section class="stmt-panel" aria-labelledby="stmt-period-label">
                <h2 id="stmt-period-label">Report details</h2>
                <dl class="stmt-kv">
                    <dt>Generated</dt>
                    <dd>{{ now()->format('d M Y, H:i') }}</dd>
                    @if (!empty($previous_date))
                        <dt>Opening cutoff</dt>
                        <dd>{{ \Carbon\Carbon::parse($previous_date)->format('d M Y') }}</dd>
                    @endif
                    <dt>Report</dt>
                    <dd>{{ $report_title ?? '—' }}</dd>
                </dl>
            </section>
        </div>

        <div class="stmt-table-wrap">
            <table class="stmt-table">
                <thead>
                    <tr>
                        <th style="width:9%;">Date</th>
                        <th style="width:8%;">Type</th>
                        <th style="width:11%;">Reference</th>
                        <th>Particulars</th>
                        <th class="stmt-num" style="width:9%;">Debit / Sale</th>
                        <th class="stmt-num" style="width:9%;">Payment in</th>
                        <th class="stmt-num" style="width:9%;">Due adjusted</th>
                        <th class="stmt-num" style="width:9%;">Return (net)</th>
                        <th class="stmt-num" style="width:10%;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php $balance = $previous_balance; @endphp
                    <tr class="stmt-row-opening">
                        <td colspan="8">Opening balance as at {{ \Carbon\Carbon::parse($previous_date)->format('d M Y') }}</td>
                        <td class="stmt-num"><strong>{{ number_format($previous_balance, 2) }}</strong></td>
                    </tr>
                    @if (count($items) > 0)
                        @foreach ($items as $item)
                            @php
                                $saleAmount = 0;
                                $paidAmount = 0;
                                $returnAmount = 0;
                                $cashRefund = 0.0;
                                $dueAdjusted = 0.0;

                                if ($item->type == 0) {
                                    $saleAmount = ($item->sale->subtotal ?? 0) - ($item->sale->discount ?? 0);
                                    $paidAmount = $item->amount;
                                    $balance += ($saleAmount - $paidAmount);
                                } elseif ($item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE) {
                                    $saleAmount = $item->amount;
                                    $balance += $saleAmount;
                                } elseif ($item->type == 1) {
                                    $paidAmount = $item->amount;
                                    $balance -= $paidAmount;
                                } elseif ($item->type == 2) {
                                    $rl = $item->returnLedger;
                                    $netCredit = (float) (optional($rl)->subtotal ?? 0);
                                    $linesSub = (float) (optional($rl)->lines_subtotal ?? 0);
                                    if ($rl && $linesSub < 0.0001) {
                                        $linesSub = (float) $rl->entries->sum(fn ($e) => (float) $e->return_qty * (float) $e->sale_price);
                                    }
                                    if ((! $rl || $linesSub < 0.0001) && $netCredit > 0) {
                                        $linesSub = $netCredit;
                                    }
                                    $hdrDisc = (float) (optional($rl)->discount ?? 0);
                                    if ($hdrDisc < 0.0001 && $linesSub > $netCredit + 0.0001) {
                                        $hdrDisc = max(0.0, $linesSub - $netCredit);
                                    }
                                    $returnAmount = $netCredit;
                                    $cashRefund = (float) $item->amount < 0 ? abs((float) $item->amount) : 0.0;
                                    $dueAdjusted = max(0.0, $netCredit - $cashRefund);
                                    $balance -= $dueAdjusted;
                                }
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</td>
                                <td>
                                    @if ($item->type == 0)
                                        Sale
                                    @elseif ($item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE)
                                        Prev. due
                                    @elseif ($item->type == 1)
                                        Payment
                                    @elseif ($item->type == 2)
                                        Return
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="stmt-muted">
                                    @if ($item->type == 0)
                                        <a href="{{ route('sales.invoice', $item->ledger_id) }}" target="_blank" rel="noopener">
                                            {{ $item->sale->invoice_no ?? '—' }}
                                        </a>
                                    @elseif ($item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE)
                                        Opening
                                    @elseif ($item->type == 2)
                                        SR #{{ $item->reference_id ?? '—' }}
                                        @if (!empty(optional($item->returnLedger)->salesLedger?->invoice_no))
                                            <span class="stmt-muted"> · Sale {{ $item->returnLedger->salesLedger->invoice_no }}</span>
                                        @endif
                                    @else
                                        {{ \Illuminate\Support\Str::limit(strip_tags($item->note ?? ''), 40, '…') ?: '—' }}
                                    @endif
                                </td>
                                <td class="stmt-desc">
                                    @if ($item->type == 0)
                                        @if (!empty($item->sale) && $item->sale->items->isNotEmpty())
                                            @foreach ($item->sale->invoiceDisplayLines() as $entry)
                                                {{ $entry->product->name ?? 'N/A' }} — {{ $entry->final_quantity }} {{ $entry->subUnit->name ?? '' }}
                                                @ {{ number_format($entry->sale_price, 2) }}; disc {{ number_format($entry->discount, 2) }}
                                                ; line {{ number_format(($entry->final_quantity * $entry->sale_price) - $entry->discount, 2) }}<br>
                                            @endforeach
                                        @else
                                            Sale invoice
                                        @endif
                                    @elseif ($item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE)
                                        Opening balance brought forward
                                    @elseif ($item->type == 1)
                                        Amount received on account
                                    @elseif ($item->type == 2)
                                        @php $rl = $item->returnLedger; @endphp
                                        @if (!empty($rl))
                                            @if (($rl->return_mode ?? '') === 'with_invoice')
                                                <span class="stmt-muted">With invoice return</span>
                                                @if (!empty($rl->invoice_no) && $rl->invoice_no !== '—')
                                                    — return ref. {{ $rl->invoice_no }}
                                                @endif
                                                <br>
                                            @elseif (($rl->return_mode ?? '') === 'without_invoice')
                                                <span class="stmt-muted">Without invoice return</span><br>
                                            @endif
                                            @if ($rl->entries->isNotEmpty())
                                                @foreach ($rl->entries as $entry)
                                                    {{ $entry->product->name ?? 'N/A' }} — ret. {{ $entry->return_qty }} × {{ number_format($entry->sale_price, 2) }}
                                                    (net unit) = {{ number_format($entry->return_qty * $entry->sale_price, 2) }}<br>
                                                @endforeach
                                            @else
                                                Return lines (detail not stored).<br>
                                            @endif
                                            <span class="stmt-muted">Lines subtotal</span> {{ number_format($linesSub, 2) }}<br>
                                            @if ($hdrDisc > 0.0001)
                                                <span class="stmt-muted">Return discount</span> −{{ number_format($hdrDisc, 2) }}<br>
                                            @endif
                                            <strong>Net credit</strong> {{ number_format($netCredit, 2) }}<br>
                                            @if ($cashRefund > 0.0001 && $dueAdjusted > 0.0001)
                                                <span class="stmt-muted">From due:</span> −{{ number_format($dueAdjusted, 2) }}
                                                <span class="stmt-muted"> · Cash paid out (refund):</span> {{ number_format($cashRefund, 2) }}
                                            @elseif ($cashRefund > 0.0001)
                                                <span class="stmt-muted">Cash paid out (refund):</span> {{ number_format($cashRefund, 2) }}
                                            @elseif ($netCredit > 0.0001)
                                                <span class="stmt-muted">Adjusted from due:</span> −{{ number_format($dueAdjusted, 2) }}
                                            @endif
                                            @if (!empty($item->note))
                                                <br><span class="stmt-muted">{{ \Illuminate\Support\Str::limit($item->note, 120) }}</span>
                                            @endif
                                        @else
                                            Sales return (ledger missing).
                                        @endif
                                    @endif
                                </td>
                                <td class="stmt-num">
                                    @if ($item->type == 0 || $item->type == \App\Models\SalesPayment::TYPE_PREVIOUS_DUE)
                                        {{ number_format($saleAmount, 2) }}
                                    @else
                                        <span class="stmt-muted">—</span>
                                    @endif
                                </td>
                                <td class="stmt-num">
                                    @if ($item->type == 0 || $item->type == 1)
                                        {{ number_format($paidAmount, 2) }}
                                    @else
                                        <span class="stmt-muted">—</span>
                                    @endif
                                </td>
                                <td class="stmt-num">
                                    @if ($item->type == 2 && $dueAdjusted > 0.0001)
                                        {{ number_format($dueAdjusted, 2) }}
                                    @else
                                        <span class="stmt-muted">—</span>
                                    @endif
                                </td>
                                <td class="stmt-num">
                                    @if ($item->type == 2 && $returnAmount > 0)
                                        {{ number_format($returnAmount, 2) }}
                                    @else
                                        <span class="stmt-muted">—</span>
                                    @endif
                                </td>
                                <td class="stmt-num"><strong>{{ number_format($balance, 2) }}</strong></td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="stmt-empty">No transactions in the selected period.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <footer class="stmt-footer">
            <span>Balances are shown in company base currency (Tk). This is a computer-generated statement and is valid without signature unless otherwise required.</span>
            <span>{{ optional($s)->title ?? config('app.name') }}</span>
        </footer>
    </div>
</div>
</body>
</html>
