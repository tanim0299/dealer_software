<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1b6ef3">
    <meta name="color-scheme" content="light">

    <title>@yield('page_title', 'DSR') — {{ $settings->title ?? 'Fresh Foods' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        :root {
            --drv-primary: #1b6ef3;
            --drv-primary-dark: #1557c7;
            --drv-on-primary: #ffffff;
            --drv-bg: #e8eaed;
            --drv-surface: #ffffff;
            --drv-surface-variant: #f1f3f4;
            --drv-on-surface: #202124;
            --drv-on-surface-variant: #5f6368;
            --drv-outline: #dadce0;
            --drv-success: #1e8e3e;
            --drv-warning: #f9ab00;
            --drv-error: #d93025;
            --drv-radius-xl: 20px;
            --drv-radius-lg: 16px;
            --drv-radius-md: 12px;
            --drv-elev-nav: 0 -2px 8px rgba(60, 64, 67, .12), 0 -4px 16px rgba(60, 64, 67, .08);
            --drv-elev-card: 0 1px 2px rgba(60, 64, 67, .28), 0 2px 6px rgba(60, 64, 67, .12);
            --driver-topbar-h: 56px;
            --driver-bottom-nav-h: 68px;
        }

        .driver-app {
            font-family: 'Roboto', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: var(--drv-bg);
            color: var(--drv-on-surface);
            min-height: 100dvh;
            -webkit-tap-highlight-color: transparent;
        }

        .driver-app a {
            text-decoration: none;
        }

        .driver-app .content-wrapper {
            padding-top: calc(var(--driver-topbar-h) + env(safe-area-inset-top, 0px) + 10px);
            padding-bottom: calc(var(--driver-bottom-nav-h) + env(safe-area-inset-bottom, 0px) + 20px);
            min-height: 100dvh;
        }

        .driver-app .page-shell {
            padding: 12px 14px 8px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Top app bar — Android / Material style */
        .driver-app-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            min-height: var(--driver-topbar-h);
            padding: 8px 12px calc(8px + env(safe-area-inset-top, 0px));
            padding-top: calc(8px + env(safe-area-inset-top, 0px));
            background: linear-gradient(180deg, var(--drv-primary) 0%, var(--drv-primary-dark) 100%);
            color: var(--drv-on-primary);
            box-shadow: 0 2px 6px rgba(0, 0, 0, .18);
        }

        .driver-app-bar__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .driver-app-bar__titles .driver-app-bar__title {
            font-weight: 500;
            font-size: 1.125rem;
            letter-spacing: .01em;
            margin: 0;
            line-height: 1.25;
        }

        .driver-app-bar__titles .driver-app-bar__subtitle {
            font-size: .75rem;
            opacity: .88;
            margin: 2px 0 0;
        }

        .driver-profile-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, .2);
            border: 0;
            border-radius: 999px;
            padding: 4px 10px 4px 4px;
            color: var(--drv-on-primary);
            cursor: pointer;
            transition: background .15s ease, transform .1s ease;
        }

        .driver-profile-chip:active {
            transform: scale(.97);
            background: rgba(255, 255, 255, .28);
        }

        .driver-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, .5);
            background: var(--drv-surface);
        }

        .driver-avatar-fallback {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--drv-surface);
            color: var(--drv-primary);
            font-weight: 700;
            font-size: .8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, .5);
        }

        /* Surfaces & cards */
        .driver-app .page-card {
            background: var(--drv-surface);
            border-radius: var(--drv-radius-lg);
            border: 1px solid var(--drv-outline);
            box-shadow: var(--drv-elev-card);
        }

        .driver-tile {
            background: var(--drv-surface);
            border-radius: var(--drv-radius-md);
            border: 1px solid var(--drv-outline);
            box-shadow: var(--drv-elev-card);
            padding: 14px 16px;
            margin-bottom: 10px;
        }

        .driver-tile:active {
            background: var(--drv-surface-variant);
        }

        .driver-metric-card {
            background: var(--drv-surface);
            border-radius: var(--drv-radius-lg);
            border: 1px solid var(--drv-outline);
            box-shadow: var(--drv-elev-card);
            padding: 14px 12px;
            text-align: center;
            height: 100%;
            transition: transform .12s ease, box-shadow .12s ease;
        }

        a .driver-metric-card:active {
            transform: scale(.98);
        }

        .driver-metric-card .driver-metric-label {
            font-size: .72rem;
            font-weight: 500;
            color: var(--drv-on-surface-variant);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .driver-metric-card .driver-metric-value {
            font-size: 1.15rem;
            font-weight: 700;
            margin-top: 6px;
            color: var(--drv-on-surface);
            word-break: break-word;
        }

        .driver-banner {
            border-radius: var(--drv-radius-md);
            padding: 12px 14px;
            background: #e8f0fe;
            border: 1px solid #c6dafc;
            color: #174ea6;
            font-size: .875rem;
        }

        .driver-banner strong {
            font-weight: 700;
        }

        /* Forms — large touch targets */
        .driver-app .form-control,
        .driver-app .form-select {
            border-radius: var(--drv-radius-md);
            min-height: 48px;
            border-color: var(--drv-outline);
            font-size: 1rem;
        }

        .driver-app .form-control:focus,
        .driver-app .form-select:focus {
            border-color: var(--drv-primary);
            box-shadow: 0 0 0 3px rgba(27, 110, 243, .2);
        }

        .driver-app .form-label {
            font-weight: 500;
            font-size: .875rem;
            color: var(--drv-on-surface-variant);
        }

        .driver-app .btn {
            border-radius: 999px;
            font-weight: 500;
            min-height: 48px;
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }

        .driver-app .btn-sm {
            min-height: 40px;
            border-radius: 999px;
        }

        .driver-app .btn-primary {
            background: var(--drv-primary);
            border-color: var(--drv-primary);
        }

        .driver-app .btn-primary:active {
            background: var(--drv-primary-dark);
            border-color: var(--drv-primary-dark);
        }

        .driver-app .table {
            font-size: .875rem;
        }

        .driver-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: var(--drv-radius-md);
            border: 1px solid var(--drv-outline);
            background: var(--drv-surface);
        }

        .driver-app .pagination {
            justify-content: center;
            flex-wrap: wrap;
            gap: 4px;
        }

        /* Bottom navigation */
        .driver-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            background: var(--drv-surface);
            padding-bottom: env(safe-area-inset-bottom, 0px);
            box-shadow: var(--drv-elev-nav);
            border-top: 1px solid var(--drv-outline);
            border-radius: var(--drv-radius-xl) var(--drv-radius-xl) 0 0;
        }

        .driver-bottom-nav__track {
            display: flex;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            gap: 2px;
            padding: 6px 6px 8px;
        }

        .driver-bottom-nav__track::-webkit-scrollbar {
            display: none;
        }

        .driver-bottom-nav a {
            flex: 1 0 auto;
            min-width: 64px;
            max-width: 96px;
            text-align: center;
            padding: 6px 4px 4px;
            text-decoration: none;
            color: var(--drv-on-surface-variant);
            font-size: .65rem;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            border-radius: 12px;
            transition: color .15s, background .15s;
        }

        .driver-bottom-nav a i {
            font-size: 1.25rem;
            line-height: 1;
        }

        .driver-bottom-nav a span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .driver-bottom-nav a.active {
            color: var(--drv-primary);
            background: rgba(27, 110, 243, .1);
        }

        .driver-bottom-nav a:active {
            background: rgba(27, 110, 243, .16);
        }

        .driver-bottom-nav a.nav-fab,
        .driver-bottom-nav span.nav-fab {
            flex: 1 0 auto;
            min-width: 56px;
            max-width: 56px;
            margin-top: -18px;
            align-self: flex-end;
            background: var(--drv-primary);
            color: var(--drv-on-primary) !important;
            border-radius: 18px;
            box-shadow: 0 4px 12px rgba(27, 110, 243, .45);
            padding: 12px 0 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            text-align: center;
            font-size: .65rem;
            font-weight: 500;
        }

        .driver-bottom-nav a.nav-fab i,
        .driver-bottom-nav span.nav-fab i {
            font-size: 1.5rem;
        }

        .driver-bottom-nav a.nav-fab span,
        .driver-bottom-nav span.nav-fab span {
            color: var(--drv-on-primary);
            font-size: .62rem;
        }

        .driver-bottom-nav a.nav-fab.active {
            background: var(--drv-primary-dark);
            color: var(--drv-on-primary) !important;
        }

        .driver-bottom-nav span.nav-fab.driver-nav-link--disabled {
            background: #94a3b8;
            box-shadow: none;
        }

        .driver-fixed-action {
            position: fixed;
            bottom: calc(var(--driver-bottom-nav-h) + env(safe-area-inset-bottom, 0px) + 8px);
            left: 0;
            right: 0;
            z-index: 1029;
            padding: 0 14px;
            max-width: 600px;
            margin: 0 auto;
            pointer-events: none;
        }

        .driver-fixed-action > * {
            pointer-events: auto;
        }

        .driver-app .fixed-action {
            position: fixed;
            bottom: calc(var(--driver-bottom-nav-h) + env(safe-area-inset-bottom, 0px) + 8px);
            left: 0;
            right: 0;
            z-index: 1029;
            max-width: 600px;
            margin: 0 auto;
            padding: 0 14px;
        }

        /* Offcanvas sheet */
        .driver-app .offcanvas {
            border-radius: var(--drv-radius-xl) 0 0 var(--drv-radius-xl);
        }

        .driver-app .offcanvas-header {
            border-bottom: 1px solid var(--drv-outline);
        }

        /* Select2 match app */
        .driver-app .select2-container--default .select2-selection--single,
        .driver-app .select2-container--default .select2-selection--multiple {
            min-height: 48px !important;
            border-radius: var(--drv-radius-md) !important;
            border-color: var(--drv-outline) !important;
            padding-top: 6px;
        }

        .driver-app .select2-container {
            max-width: 100% !important;
        }

        .driver-empty {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--drv-on-surface-variant);
            font-size: .9rem;
        }

        /* List row actions — icon buttons (DSR lists) */
        .driver-actions {
            display: flex;
            gap: 10px;
            padding-top: 12px;
            align-items: stretch;
        }

        .driver-actions form {
            flex: 1;
            margin: 0;
        }

        .driver-actions--end {
            justify-content: flex-end;
        }

        .driver-actions--single form {
            flex: 0 0 auto;
            min-width: 52px;
        }

        .driver-actions--single .driver-icon-action--delete {
            width: auto !important;
            min-width: 46px;
        }

        a.driver-icon-action {
            flex: 1;
            text-decoration: none !important;
        }

        .driver-icon-action {
            min-height: 46px;
            border-radius: 14px !important;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            line-height: 1;
            transition: transform .1s ease, box-shadow .1s ease, opacity .1s ease;
            box-shadow: 0 1px 3px rgba(60, 64, 67, .2);
        }

        .driver-icon-action:active {
            transform: scale(.97);
        }

        .driver-icon-action--edit {
            background: linear-gradient(135deg, #1e8e3e 0%, #148f5c 100%);
            color: #fff !important;
        }

        .driver-icon-action--edit:focus-visible {
            outline: 2px solid var(--drv-primary);
            outline-offset: 2px;
        }

        .driver-icon-action--delete {
            background: var(--drv-surface);
            color: var(--drv-error) !important;
            border: 2px solid rgba(217, 48, 37, .35) !important;
            box-shadow: none;
            width: 100%;
        }

        .driver-icon-action--success {
            background: linear-gradient(135deg, #1e8e3e 0%, #148f5c 100%);
            color: #fff !important;
            width: 100%;
        }

        .driver-icon-action--reject {
            background: var(--drv-surface);
            color: var(--drv-error) !important;
            border: 2px solid rgba(217, 48, 37, .35) !important;
            box-shadow: none;
            width: 100%;
        }

        .driver-icon-action--print {
            background: linear-gradient(180deg, var(--drv-primary) 0%, var(--drv-primary-dark) 100%);
            color: #fff !important;
            width: 100%;
            min-height: 48px;
            font-size: 1.35rem;
        }

        .driver-section-title {
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--drv-on-surface-variant);
            margin: 16px 0 8px;
        }

        .driver-page-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .driver-issue-banner {
            display: block;
            background: linear-gradient(135deg, #e8f0fe 0%, #d2e3fc 100%);
            border: 1px solid #aecbfa;
            color: #174ea6;
            border-radius: var(--drv-radius-md);
            padding: 12px 14px;
            margin-bottom: 10px;
            box-shadow: var(--drv-elev-card);
        }

        .driver-issue-banner:active {
            transform: scale(0.99);
        }

        .driver-issue-banner .driver-issue-banner__title {
            font-weight: 700;
            font-size: .95rem;
        }

        .driver-issue-banner .driver-issue-banner__hint {
            font-size: .78rem;
            opacity: .9;
        }

        .driver-nav-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            font-size: 10px;
            font-weight: 700;
            line-height: 18px;
            border-radius: 999px;
            background: #ea4335;
            color: #fff;
        }

        .driver-bottom-nav a.driver-nav-link--disabled,
        .driver-bottom-nav span.driver-nav-link--disabled {
            opacity: .45;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>
    @stack('styles')
</head>
<body class="driver-app">
@php
    $driverUser = auth()->user();
    $userImage = !empty($driverUser?->image) ? asset('storage' . $driverUser->image) : null;
    $userInitials = collect(explode(' ', trim($driverUser?->name ?? 'D')))
        ->filter()
        ->take(2)
        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
        ->join('');
@endphp

<header class="driver-app-bar">
    <div class="driver-app-bar__row">
        <div class="driver-app-bar__titles">
            <h1 class="driver-app-bar__title">@yield('page_title', 'DSR')</h1>
            <p class="driver-app-bar__subtitle">{{ $settings->title ?? 'Fresh Foods' }}</p>
        </div>

        <button type="button" class="driver-profile-chip position-relative" data-bs-toggle="offcanvas"
                data-bs-target="#driverAccountCanvas" aria-controls="driverAccountCanvas">
            @if(($pendingDriverIssueCount ?? 0) > 0)
                <span class="driver-nav-badge" aria-label="Pending stock issues">{{ $pendingDriverIssueCount > 9 ? '9+' : $pendingDriverIssueCount }}</span>
            @endif
            @if($userImage)
                <img src="{{ $userImage }}" alt="" class="driver-avatar" width="36" height="36">
            @else
                <span class="driver-avatar-fallback">{{ $userInitials ?: 'D' }}</span>
            @endif
            <i class="bi bi-chevron-expand small opacity-75" aria-hidden="true"></i>
        </button>
    </div>
</header>

@if(!empty($driverPanelDayClosed))
    <div class="px-3 pt-2" style="max-width: 600px; margin: 0 auto;">
        <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-0 small" role="status">
            <div class="fw-bold mb-1"><i class="bi bi-lock-fill me-1"></i> Closing completed for today</div>
            <div class="text-dark">For <strong>{{ now()->format('d M Y') }}</strong>, you cannot add new sales, expenses, returns, due collections, or cash distributions from this panel. Use list screens to view history only.</div>
        </div>
    </div>
@endif

<div class="content-wrapper">
    @if(($pendingDriverIssueCount ?? 0) > 0)
        <div class="px-3" style="max-width: 600px; margin: 0 auto;">
            <a href="{{ route('driver-issues.index') }}" class="driver-issue-banner text-decoration-none">
                <div class="driver-issue-banner__title">
                    <i class="bi bi-inbox-fill me-1"></i> New warehouse stock issue
                </div>
                <div class="driver-issue-banner__hint">Open the issue list to accept or reject. Warehouse stock is reserved until you accept.</div>
            </a>
        </div>
    @endif
    <div class="page-shell">
        @include('components.flash-messages')
        @yield('body')
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="driverAccountCanvas" aria-labelledby="driverAccountCanvasLabel">
    <div class="offcanvas-header">
        <h2 class="h5 offcanvas-title" id="driverAccountCanvasLabel">Account</h2>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-flex align-items-center gap-3 mb-4">
            @if($userImage)
                <img src="{{ $userImage }}" alt="" class="driver-avatar" style="width:52px;height:52px;">
            @else
                <span class="driver-avatar-fallback" style="width:52px;height:52px;font-size:1rem;">{{ $userInitials ?: 'D' }}</span>
            @endif
            <div>
                <div class="fw-bold">{{ $driverUser->name ?? 'DSR' }}</div>
                <small class="text-muted">{{ $driverUser->email ?? '' }}</small>
            </div>
        </div>

        <a href="{{ route('driver.profile') }}" class="btn btn-outline-primary w-100 mb-2 rounded-4">
            <i class="bi bi-person-circle me-1"></i> Profile & password
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100 rounded-4">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</div>

<nav class="driver-bottom-nav" aria-label="Main navigation">
    <div class="driver-bottom-nav__track">
        <a href="{{ route('dashboard.index') }}"
           class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('sales.index') }}"
           class="{{ request()->routeIs('sales.index') || request()->routeIs('sales.edit') || request()->routeIs('sales.invoice') ? 'active' : '' }}">
            <i class="bi bi-receipt-cutoff"></i>
            <span>Sales</span>
        </a>
        @if(!empty($driverPanelDayClosed))
            <span class="nav-fab driver-nav-link--disabled" title="Closing completed for today — new sale is disabled">
                <i class="bi bi-plus-lg"></i>
                <span>New</span>
            </span>
        @else
            <a href="{{ route('sales.create') }}"
               class="nav-fab {{ request()->routeIs('sales.create') ? 'active' : '' }}">
                <i class="bi bi-plus-lg"></i>
                <span>New</span>
            </a>
        @endif
        <a href="{{ route('driver_stock.index') }}"
           class="{{ request()->routeIs('driver_stock.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i>
            <span>Stock</span>
        </a>
        <a href="{{ route('driver_cash_distribution.index') }}"
           class="{{ request()->routeIs('driver_cash_distribution.*') ? 'active' : '' }}">
            <i class="bi bi-cash-stack"></i>
            <span>Cash</span>
        </a>
        <a href="{{ route('expense_entry.index') }}"
           class="{{ request()->routeIs('expense_entry.*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i>
            <span>Expense</span>
        </a>
        <a href="{{ route('driver.profile') }}"
           class="{{ request()->routeIs('driver.profile') ? 'active' : '' }}">
            <i class="bi bi-person"></i>
            <span>Account</span>
        </a>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@stack('scripts')
@include('components.fresh-select2-script')
@include('components.flash-toasts')
</body>
</html>
