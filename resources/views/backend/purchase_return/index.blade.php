@extends('backend.layouts.master')
@section('title','Purchase Returns')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Purchase Returns'])
        </div>

        {{-- Filter Form --}}
        <form method="GET" class="mb-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" placeholder="From Date">
                </div>
                <div class="col-md-3">
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" placeholder="To Date">
                </div>
                <div class="col-md-3">
                    <select name="supplier_id" class="form-select">
                        <option value="">-- Select Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="return_type" class="form-select">
                        <option value="">-- Return Type --</option>
                        <option value="1" {{ request('return_type') == 1 ? 'selected' : '' }}>Cash</option>
                        <option value="2" {{ request('return_type') == 2 ? 'selected' : '' }}>Minus From Due</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                </div>
            </div>
        </form>

        {{-- Purchase Return Table --}}
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Return Type</th>
                            <th>Subtotal</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ledgers as $ledger)
                            <tr>
                                <td>{{ $ledger->id }}</td>
                                <td>{{ $ledger->supplier->name }}</td>
                                <td>{{ $ledger->return_type == 1 ? 'Cash' : 'Minus From Due' }}</td>
                                <td>{{ number_format($ledger->subtotal,2) }}</td>
                                <td>{{ $ledger->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('purchase_return.show', $ledger->id) }}" class="btn btn-info btn-sm">View</a>
                                    <form action="{{ route('purchase_return.destroy', $ledger->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this purchase return?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Purchase Returns found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $ledgers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection