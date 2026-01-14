@extends('backend.layouts.master')
@section('title','Warehouse Stock')
@section('content')
 <div class="container">
    <div class="page-inner">
        <div
            class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Warehouse Stock'])
            <div class="ms-md-auto py-2 py-md-0">
                
            </div>
        </div>
        <form method="get" action="{{ route('warehouse_stock.index') }}">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input
                        type="text"
                        id="stockSearch"
                        name="free_text"
                        class="form-control"
                        placeholder="Search product or SKU..."
                    >
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Search</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="stockTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Purchase Qty</th>
                        <th>Sales Qty</th>
                        <th>Sales Return Qty</th>
                        <th>Return Qty</th>
                        <th>Available Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr>
                        <td>1</td>
                        <td class="product-name">{{$stock->product->name}}</td>
                        <td class="sku">{{$stock->product->product_code }}</td>
                        <td>{{$stock->purchase_qty}}</td>
                        <td>{{$stock->sales_qty}}</td>
                        <td>{{$stock->sales_return_qty}}</td>
                        <td>{{$stock->return_qty}}</td>
                        <td>
                            <strong>{{$stock->available_qty}}</strong>
                        </td>
                        <td>
                            <span class="badge bg-success">In Stock</span>
                        </td>
                    </tr>
                    @empty

                    @endforelse

                </tbody>
            </table>
            {{ $stocks->links('pagination::bootstrap-5') }}
        </div>


    </div>
</div>
@endsection
