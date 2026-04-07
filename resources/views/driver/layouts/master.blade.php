<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $settings->title ?? 'Driver App' }}</title>

    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- 2. Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background: #eef2f7;
            font-family: "Inter", "Segoe UI", sans-serif;
            color: #1f2937;
        }

        .content-wrapper {
            padding-top: 84px;
            padding-bottom: 98px;
        }

        .app-topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(13, 110, 253, .25);
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            padding: 12px 14px;
        }

        .topbar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .topbar-title {
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
        }

        .topbar-sub {
            font-size: .78rem;
            opacity: .9;
        }

        .profile-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 999px;
            padding: 6px 10px;
            color: #fff;
            cursor: pointer;
        }

        .profile-chip:hover {
            background: rgba(255, 255, 255, .24);
        }

        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, .5);
            background: #fff;
        }

        .avatar-fallback {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #fff;
            color: #0d6efd;
            font-weight: 700;
            font-size: .82rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, .5);
        }

        .page-shell {
            padding: 12px;
        }

        .page-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e6ebf2;
            box-shadow: 0 4px 14px rgba(17, 24, 39, .05);
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #e4e8ee;
            box-shadow: 0 -4px 18px rgba(17, 24, 39, .08);
            z-index: 1030;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            padding: 6px 4px;
        }

        .bottom-nav a {
            flex: 1;
            text-align: center;
            padding: 6px 0;
            text-decoration: none;
            color: #6b7280;
            font-size: .72rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }

        .bottom-nav a i {
            font-size: 1.05rem;
        }

        .bottom-nav a.active {
            color: #0d6efd;
            font-weight: 600;
        }

        .fixed-action {
            position: fixed;
            bottom: 74px;
            width: 100%;
            z-index: 1029;
        }
        a{
            text-decoration: none;
        }

        .driver-page-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .driver-page-subtitle {
            margin: 0;
            font-size: .8rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
@php
    $driverUser = auth()->user();
    $currentRoute = Route::currentRouteName() ?? '';
    $userImage = !empty($driverUser?->image) ? asset('storage' . $driverUser->image) : null;
    $userInitials = collect(explode(' ', trim($driverUser?->name ?? 'D')))
        ->filter()
        ->take(2)
        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
        ->join('');
@endphp

<div class="app-topbar">
    <div class="topbar-row">
        <div>
            <h6 class="topbar-title">@yield('page_title', 'Driver Panel')</h6>
            <div class="topbar-sub">{{ $settings->title ?? 'Fresh Foods' }}</div>
        </div>

        <button class="profile-chip border-0" data-bs-toggle="offcanvas" data-bs-target="#driverAccountCanvas" aria-controls="driverAccountCanvas">
            @if($userImage)
                <img src="{{ $userImage }}" alt="profile" class="profile-avatar">
            @else
                <span class="avatar-fallback">{{ $userInitials ?: 'DR' }}</span>
            @endif
            <span class="d-none d-sm-inline">{{ $driverUser->name ?? 'Driver' }}</span>
            <i class="bi bi-chevron-down small"></i>
        </button>
    </div>
</div>

<div class="content-wrapper">
    <div class="page-shell">
        @yield('body')
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="driverAccountCanvas" aria-labelledby="driverAccountCanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="driverAccountCanvasLabel">Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="d-flex align-items-center gap-3 mb-3">
            @if($userImage)
                <img src="{{ $userImage }}" alt="profile" class="profile-avatar" style="width:54px;height:54px;">
            @else
                <span class="avatar-fallback" style="width:54px;height:54px;font-size:1rem;">{{ $userInitials ?: 'DR' }}</span>
            @endif
            <div>
                <div class="fw-bold">{{ $driverUser->name ?? 'Driver' }}</div>
                <small class="text-muted">{{ $driverUser->email ?? '' }}</small>
            </div>
        </div>

        <a href="{{ route('driver.profile') }}" class="btn btn-outline-primary w-100 mb-2">
            <i class="bi bi-person-circle me-1"></i> Profile & Change Password
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav d-flex">
    <a href="{{ route('dashboard.index') }}" class="{{ Route::is('') ? 'active' : '' }}">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('sales.index') }}" class="{{ Route::is('') ? 'active' : '' }}">
        <i class="bi bi-receipt"></i>
        <span>Sales</span>
    </a>
    <a href="{{ route('sales.create') }}" class="{{ Route::is('') ? 'active' : '' }}">
        <i class="bi bi-plus-circle"></i>
        <span>New</span>
    </a>
    <a href="{{ route('driver_stock.index') }}" class="{{ Route::is('') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i>
        <span>Stock</span>
    </a>
    <a href="{{ route('driver_cash_distribution.index') }}" class="{{ Route::is('') ? 'active' : '' }}">
        <i class="bi bi-cash-coin"></i>
        <span>Cash</span>
    </a>
    <a href="{{ route('driver.profile') }}" class="{{ Route::is('') ? 'active' : '' }}">
        <i class="bi bi-person"></i>
        <span>Account</span>
    </a>
</div>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
@stack('scripts')
<script>
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif

    @if(session('info'))
        toastr.info("{{ session('info') }}");
    @endif

    @if(session('warning'))
        toastr.warning("{{ session('warning') }}");
    @endif
</script>
</body>
</html>

