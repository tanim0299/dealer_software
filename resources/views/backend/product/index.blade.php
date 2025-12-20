@extends('backend.layouts.master')
@section('title','Product List')

@section('content')
<div class="container">
    <div class="page-inner">

        {{-- Page Header --}}
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            @include('backend.layouts.partials.breadcrumb',['page_title'=>'Product List'])
            <div class="ms-md-auto py-2 py-md-0">
                @if(Auth::user()->can('Product create'))
                    <a href="{{ route('product.create') }}" class="btn btn-label-primary btn-round">Create Product</a>
                @endif
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('product.index') }}">
                    <div class="row">

                        {{-- Free Text Search --}}
                        <div class="col-md-3 mb-2">
                            <input type="text" name="free_text" class="form-control"
                                value="{{ request('free_text') }}"
                                placeholder="Search product, item, category, brand">
                        </div>

                        <div class="col-md-2 mb-2">
                            <select name="item_id" class="form-select">
                                <option value="">All Items</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-2">
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-2">
                            <select name="brand_id" class="form-select">
                                <option value="">All Brands</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-2">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                @foreach(App\Models\Product::STATUS as $key=>$value)
                                    <option value="{{ $key }}" {{ request('status') == (string)$key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1 mb-2">
                            <button class="btn btn-primary w-100"><i class="fa fa-filter"></i></button>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        {{-- Product Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Unit</th>
                                <th>Purchase Price</th>
                                <th>Sale Price</th>
                                <th>Status</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $key => $product)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->item->name ?? '-' }}</td>
                                    <td>{{ $product->category->name ?? '-' }}</td>
                                    <td>{{ $product->brand->name ?? '-' }}</td>
                                    <td>{{ $product->unit->name ?? '-' }}</td>
                                    <td>{{ number_format($product->purchase_price, 2) }}</td>
                                    <td>{{ number_format($product->sale_price, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $product->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ App\Models\Product::STATUS[$product->status] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(Auth::user()->can('Product edit'))
                                            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-sm btn-info">Edit</a>
                                        @endif
                                        @if(auth()->user()->can('Product destroy'))
                                        <form action="{{ route('product.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No products found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection
