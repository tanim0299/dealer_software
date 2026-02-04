@extends('driver.layouts.master')
@section('body')
<!-- Summary Cards -->
    <section class="summary">
        <div class="card">
            <p>Sales</p>
            <h3>₹12,500</h3>
        </div>
        <div class="card">
            <p>Dues</p>
            <h3>₹3,200</h3>
        </div>
        <div class="card">
            <p>Expenses</p>
            <h3>₹1,100</h3>
        </div>
        <div class="card">
            <p>Stock</p>
            <h3>245</h3>
        </div>
    </section>

    <!-- Quick Actions -->
    <section class="actions">
        <button class="primary">➕ New Sale</button>
        <button class="secondary">💰 Collect Due</button>
        <button class="secondary">🧾 Add Expense</button>
    </section>

    <!-- Recent Activity -->
    <section class="activity">
        <h2>Recent</h2>
        <ul>
            <li>Sale - ₹500</li>
            <li>Expense - ₹120</li>
            <li>Due Collected - ₹300</li>
        </ul>
    </section>

@endsection