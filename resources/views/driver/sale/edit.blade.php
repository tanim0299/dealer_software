@extends('driver.layouts.master')

@section('page_title', 'Edit Sale')

@section('body')
    @include('driver.sale.form', ['sale' => $sale, 'customers' => $customers])
@endsection

