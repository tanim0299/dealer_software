<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mobile POS</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

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
    </style>
</head>
<body>

<div class="content-wrapper">
    @yield('body')
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav d-flex">
    <a href="#">🏠</a>
    <a href="#">📦</a>
    <a href="#">➕</a>
    <a href="#">📊</a>
    <a href="#">⚙️</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
