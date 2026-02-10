@extends('driver.layouts.master')

@section('body')
<form method="POST" action="{{ route('sales.update', $sale->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('driver.sale.form', ['sale' => $sale, 'customers' => $customers])
</form>
@endsection
