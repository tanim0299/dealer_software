@extends('driver.layouts.master')

@section('page_title', 'New Sale')

@section('body')
    @include('driver.sale.form', ['customers' => $customers])

@endsection
