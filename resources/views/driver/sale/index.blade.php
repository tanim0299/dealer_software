@extends('driver.layouts.master')

@section('body')
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <button class="btn btn-light" onclick="history.back()">← Back</button>
        <span class="navbar-brand mx-auto">Sales List</span>
    </div>
</nav>

<div class="container mx-auto p-2">

    <!-- Filters -->
    <form method="GET" action="{{ route('sales.index') }}" class="mb-4">
        <div class="flex flex-col sm:flex-row gap-2">
            <input type="text" name="free_text" placeholder="Invoice / Customer" 
                   value="{{ $search['free_text'] ?? '' }}" 
                   class="flex-1 border rounded p-2">

            <input type="date" name="from_date" value="{{ $search['from_date'] ?? '' }}" 
                   class="border rounded p-2">

            <input type="date" name="to_date" value="{{ $search['to_date'] ?? '' }}" 
                   class="border rounded p-2">

            <button type="submit" class="btn btn-secondary">
                Search
            </button>
        </div>
    </form>

    <!-- Sales List -->
    <div class="space-y-4">

        @forelse($sales as $ledger)
        <div class="bg-white shadow rounded-lg border p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1 mb-2 sm:mb-0">
                <p class="font-semibold text-blue-700">Invoice: {{ $ledger->invoice_no }}</p>
                <p class="text-gray-600 text-sm">Date: {{ $ledger->date }}</p>
                <p class="text-gray-600 text-sm">Customer: {{ $ledger->customer->name ?? 'N/A' }}</p>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-sm text-gray-700">
                <span>Subtotal: <strong>{{ number_format($ledger->subtotal, 2) }}</strong></span>
                <span>Discount: <strong>{{ number_format($ledger->discount, 2) }}</strong></span>
                <span>Paid: <strong>{{ number_format($ledger->paid, 2) }}</strong></span>
                <span>Balance: <strong class="text-blue-700">{{ number_format($ledger->subtotal - $ledger->discount - $ledger->paid, 2) }}</strong></span>
            </div>

            <div class="flex gap-2 mt-2 sm:mt-0">
                <a href="{{ url('sales_invoice/' . $ledger->id) }}" 
                   class="btn btn-sm btn-success">
                   View
                </a>
                <form action="{{ route('sales.destroy', $ledger->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this sale?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-500 mt-4">No sales found.</p>
        @endforelse

    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center">
        {{ $sales->links() }}
    </div>

</div>
@endsection
