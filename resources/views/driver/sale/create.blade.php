@extends('driver.layouts.master')

@section('body')

    @csrf
    @include('driver.sale.form', ['customers' => $customers])

@endsection
