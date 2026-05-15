<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $ledger->invoice_no }} — {{ optional($settings)->title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">
    <style>
        :root {
            --inv-accent: #0d9488;
            --inv-accent-dark: #0f766e;
            --inv-ink: #0f172a;
            --inv-muted: #64748b;
            --inv-border: #e2e8f0;
            --inv-surface: #f8fafc;
            --inv-card: #ffffff;
            --inv-radius: 14px;
            --inv-shadow: 0 1px 2px rgba(15, 23, 42, 0.06), 0 12px 32px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px 16px 48px;
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.55;
            color: var(--inv-ink);
            background: linear-gradient(165deg, #ecfdf5 0%, #f1f5f9 42%, #e0f2fe 100%);
            min-height: 100vh;
        }

        .no-print {
            max-width: 720px;
            margin: 0 auto 16px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .no-print button {
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            background: var(--inv-accent);
            color: #fff;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35);
        }

        .no-print button:hover {
            background: var(--inv-accent-dark);
        }

        .invoice-shell {
            max-width: 210mm;
            margin: 0 auto;
            background: var(--inv-card);
            border-radius: var(--inv-radius);
            box-shadow: var(--inv-shadow);
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.9);
        }

        .inv-accent-bar {
            height: 5px;
            background: linear-gradient(90deg, #0f766e 0%, var(--inv-accent) 52%, #5eead4 100%);
        }

        .inv-header {
            padding: 22px 28px 18px;
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 20px;
            align-items: start;
            border-bottom: 1px solid var(--inv-border);
            background: linear-gradient(180deg, #fafefd 0%, #fff 100%);
        }

        @media (min-width: 640px) {
            .inv-header {
                grid-template-columns: 100px 1fr auto;
                align-items: center;
            }
        }

        .inv-logo-wrap {
            width: 92px;
            height: 92px;
            border-radius: 16px;
            border: 1px solid var(--inv-border);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .inv-logo-wrap img {
            max-width: 88%;
            max-height: 88%;
            object-fit: contain;
        }

        .inv-logo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 700;
            color: var(--inv-accent);
            background: rgba(13, 148, 136, 0.08);
        }

        .inv-brand h1 {
            margin: 0 0 4px;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--inv-ink);
        }

        .inv-brand .slogan {
            margin: 0;
            font-size: 13px;
            color: var(--inv-muted);
            font-weight: 500;
        }

        .inv-contact {
            text-align: left;
            font-size: 12.5px;
            color: var(--inv-muted);
            line-height: 1.65;
        }

        @media (min-width: 640px) {
            .inv-contact {
                text-align: right;
            }
        }

        .inv-contact strong {
            display: block;
            color: var(--inv-ink);
            font-size: 13px;
            margin-bottom: 2px;
        }

        .inv-contact .inv-contact-line {
            margin: 0;
        }

        .inv-contact a {
            color: var(--inv-accent-dark);
            text-decoration: none;
        }

        .inv-body {
            padding: 22px 28px 28px;
        }

        .inv-title-row {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
        }

        .inv-title-row h2 {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--inv-accent-dark);
            letter-spacing: -0.02em;
        }

        .inv-badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(13, 148, 136, 0.12);
            color: var(--inv-accent-dark);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .inv-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-bottom: 22px;
        }

        @media (min-width: 620px) {
            .inv-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .inv-card {
            background: var(--inv-surface);
            border: 1px solid var(--inv-border);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .inv-card h3 {
            margin: 0 0 10px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--inv-accent-dark);
        }

        .inv-card p {
            margin: 4px 0;
            font-size: 13.5px;
            color: var(--inv-ink);
        }

        .inv-meta-table {
            width: 100%;
            font-size: 13px;
        }

        .inv-meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .inv-meta-table td:first-child {
            color: var(--inv-muted);
            width: 38%;
            font-weight: 500;
        }

        .inv-items-wrap {
            border-radius: 12px;
            border: 1px solid var(--inv-border);
            overflow: hidden;
            margin-bottom: 20px;
        }

        table.inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        table.inv-table thead th {
            text-align: left;
            padding: 12px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #fff;
            background: linear-gradient(135deg, var(--inv-accent-dark) 0%, var(--inv-accent) 100%);
            border-bottom: none;
        }

        table.inv-table thead th:last-child,
        table.inv-table tbody td:last-child {
            text-align: right;
        }

        table.inv-table tbody td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--inv-border);
            vertical-align: top;
        }

        table.inv-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        table.inv-table tbody tr:last-child td {
            border-bottom: none;
        }

        .inv-num {
            font-variant-numeric: tabular-nums;
        }

        .inv-totals-wrap {
            display: flex;
            justify-content: flex-end;
        }

        .inv-totals {
            width: 100%;
            max-width: 320px;
            border-radius: 12px;
            border: 1px solid var(--inv-border);
            overflow: hidden;
        }

        .inv-totals table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .inv-totals td {
            padding: 10px 16px;
        }

        .inv-totals tr:not(.grand) td {
            border-bottom: 1px solid var(--inv-border);
        }

        .inv-totals tr td:first-child {
            color: var(--inv-muted);
            font-weight: 500;
        }

        .inv-totals tr td:last-child {
            text-align: right;
            font-variant-numeric: tabular-nums;
            font-weight: 600;
        }

        .inv-totals tr.grand td {
            background: linear-gradient(135deg, var(--inv-accent-dark) 0%, var(--inv-accent) 100%);
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            border: none;
        }

        .inv-note {
            margin-top: 20px;
            padding: 14px 16px;
            border-radius: 12px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            font-size: 13px;
        }

        .inv-note strong {
            color: #b45309;
        }

        .inv-slip {
            margin-top: 18px;
        }

        .inv-slip img {
            max-width: 100%;
            max-height: 220px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid var(--inv-border);
        }

        .inv-footer {
            text-align: center;
            padding: 18px 24px 22px;
            font-size: 12px;
            color: var(--inv-muted);
            border-top: 1px solid var(--inv-border);
            background: var(--inv-surface);
        }

        .inv-footer .brand-line {
            font-weight: 600;
            color: var(--inv-ink);
            margin-bottom: 4px;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .invoice-shell {
                box-shadow: none;
                border: none;
                border-radius: 0;
                max-width: none;
            }
        }
    </style>
</head>

<body>
    @php
        $s = $settings ?? null;
        $logoUrl = $s && !empty($s->logo) ? asset('storage/' . ltrim($s->logo, '/')) : null;
        $brandInitial = strtoupper(substr(optional($s)->title ?? config('app.name'), 0, 1));
    @endphp

    <div class="no-print">
        <button type="button" onclick="window.print()">Print / Save PDF</button>
    </div>

    <article class="invoice-shell" aria-label="Sales invoice">
        <div class="inv-accent-bar" aria-hidden="true"></div>

        <header class="inv-header">
            <div class="inv-logo-wrap" title="{{ optional($s)->title }}">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ optional($s)->title ?? 'Logo' }}">
                @else
                    <span class="inv-logo-placeholder" aria-hidden="true">{{ $brandInitial }}</span>
                @endif
            </div>

            <div class="inv-brand">
                <h1>{{ optional($s)->title ?? config('app.name') }}</h1>
                @if (!empty(optional($s)->slogan))
                    <p class="slogan">{{ $s->slogan }}</p>
                @endif
            </div>

            <div class="inv-contact">
                @if (!empty(optional($s)->name))
                    <strong>{{ $s->name }}</strong>
                @endif
                @if (!empty(optional($s)->designation))
                    <p class="inv-contact-line">{{ $s->designation }}</p>
                @endif
                @if (!empty(optional($s)->phone))
                    <p class="inv-contact-line">Phone: <span class="inv-num">{{ $s->phone }}</span></p>
                @endif
                @if (!empty(optional($s)->email))
                    <p class="inv-contact-line">Email: <a href="mailto:{{ $s->email }}">{{ $s->email }}</a></p>
                @endif
                @if (!empty(optional($s)->address))
                    <div class="inv-contact-line">{!! $s->address !!}</div>
                @endif
            </div>
        </header>

        <div class="inv-body">
            <div class="inv-title-row">
                <h2>Sales invoice</h2>
                <span class="inv-badge"># {{ $ledger->invoice_no }}</span>
            </div>

            <div class="inv-grid">
                <div class="inv-card">
                    <h3>Bill to</h3>
                    <p><strong>{{ $ledger->customer->name ?? '—' }}</strong></p>
                    @if (!empty($ledger->customer->address))
                        <p>{{ $ledger->customer->address }}</p>
                    @endif
                    @if (!empty($ledger->customer->phone ?? null))
                        <p class="inv-num">{{ $ledger->customer->phone }}</p>
                    @endif
                </div>

                <div class="inv-card">
                    <h3>Invoice details</h3>
                    <table class="inv-meta-table">
                        <tr>
                            <td>Invoice no.</td>
                            <td><strong>{{ $ledger->invoice_no }}</strong></td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>{{ $ledger->date }}</td>
                        </tr>
                        <tr>
                            <td>Time</td>
                            <td>{{ $ledger->time }}</td>
                        </tr>
                        @if ($ledger->driver)
                            <tr>
                                <td>Sales rep</td>
                                <td>{{ $ledger->driver->name ?? '' }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="inv-items-wrap">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ledger->invoiceDisplayLines() as $key => $item)
                            <tr>
                                <td class="inv-num">{{ $key + 1 }}</td>
                                <td>{{ $item->product->name ?? '' }}</td>
                                <td class="inv-num">{{ rtrim(rtrim(number_format((float) $item->final_quantity, 4, '.', ''), '0'), '.') }}</td>
                                <td>{{ $item->subUnit->name ?? '' }}</td>
                                <td class="inv-num">{{ number_format($item->sale_price, 2) }}</td>
                                <td class="inv-num">{{ number_format($item->discount, 2) }}</td>
                                <td class="inv-num">{{ number_format($item->final_quantity * $item->sale_price - $item->discount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="inv-totals-wrap">
                <div class="inv-totals">
                    <table>
                        <tr>
                            <td>Subtotal</td>
                            <td>{{ number_format($ledger->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Invoice discount</td>
                            <td>{{ number_format($ledger->discount, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Paid</td>
                            <td>{{ number_format($ledger->paid, 2) }}</td>
                        </tr>
                        <tr class="grand">
                            <td>Balance due</td>
                            <td>Tk {{ number_format($ledger->subtotal - $ledger->discount - $ledger->paid, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if ($ledger->note)
                <div class="inv-note">
                    <strong>Note</strong>
                    <p style="margin:8px 0 0;color:var(--inv-ink)">{{ $ledger->note }}</p>
                </div>
            @endif

            @if (!empty($ledger->slip_image))
                <div class="inv-slip">
                    <h3 style="margin:0 0 10px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--inv-accent-dark)">Payment slip</h3>
                    <img src="{{ asset('storage/' . ltrim($ledger->slip_image, '/')) }}" alt="Payment slip">
                </div>
            @endif
        </div>

        <footer class="inv-footer">
            <div class="brand-line">Thank you for your business</div>
            <div>{{ optional($s)->title ?? config('app.name') }} — computer generated invoice</div>
        </footer>
    </article>
</body>

</html>
