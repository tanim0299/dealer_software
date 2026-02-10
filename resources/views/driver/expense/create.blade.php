@extends('driver.layouts.master')

@section('body')
<form method="POST" action="{{ route('sales.store') }}" enctype="multipart/form-data">
    @csrf
    @include('driver.sale.form', ['customers' => $customers])
</form>
@endsection
