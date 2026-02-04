<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile POS Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<style>
    * {
    box-sizing: border-box;
    font-family: system-ui, sans-serif;
    margin: 0;
    padding: 0;
}

body {
    background: #f4f6f8;
}

.app {
    padding-bottom: 70px;
}

/* Header */
.header {
    background: #2563eb;
    color: white;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Summary Cards */
.summary {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    padding: 12px;
}

.card {
    background: white;
    border-radius: 12px;
    padding: 14px;
    text-align: center;
}

.card p {
    font-size: 13px;
    color: #666;
}

.card h3 {
    margin-top: 4px;
    color: #111;
}

/* Actions */
.actions {
    padding: 12px;
    display: grid;
    gap: 10px;
}

button {
    border: none;
    border-radius: 10px;
    padding: 14px;
    font-size: 16px;
    cursor: pointer;
}

.primary {
    background: #2563eb;
    color: white;
}

.secondary {
    background: white;
    border: 1px solid #ddd;
}

/* Activity */
.activity {
    padding: 12px;
}

.activity h2 {
    font-size: 16px;
    margin-bottom: 8px;
}

.activity ul li {
    background: white;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 6px;
}

/* Bottom Navigation */
.bottom-nav {
    position: fixed;
    bottom: 0;
    width: 100%;
    background: white;
    display: flex;
    justify-content: space-around;
    padding: 10px 0;
    border-top: 1px solid #ddd;
}

.bottom-nav a {
    text-decoration: none;
    font-size: 20px;
    color: #555;
}

</style>
<body>

<div class="app">

    <!-- Header -->
    <header class="header">
        <h1>My Shop</h1>
        <span class="date">Today</span>
    </header>

    @yield('body')

</div>

<!-- Bottom Navigation -->
<nav class="bottom-nav">
    <a href="#">🏠</a>
    <a href="#">📦</a>
    <a href="#">➕</a>
    <a href="#">📊</a>
    <a href="#">⚙️</a>
</nav>

</body>
</html>
