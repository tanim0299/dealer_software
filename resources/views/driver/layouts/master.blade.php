<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Mobile POS</title>

    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- 2. Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f4f6f8;
        }

        .content-wrapper {
            padding-bottom: 130px; /* space for fixed btn + nav */
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: #fff;
            border-top: 1px solid #ddd;
            z-index: 1030;
        }

        .bottom-nav a {
            flex: 1;
            text-align: center;
            padding: 10px 0;
            font-size: 20px;
            text-decoration: none;
            color: #555;
        }

        .fixed-action {
            position: fixed;
            bottom: 56px;
            width: 100%;
            z-index: 1029;
        }
        a{
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="content-wrapper">
    @yield('body')
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav d-flex">
    <a href="{{ route('dashboard.index') }}">🏠</a>
    <a href="#">📦</a>
    <a href="#">➕</a>
    <a href="#">📊</a>
    <a href="#">⚙️</a>
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
