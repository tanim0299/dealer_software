@extends('driver.layouts.master')

@section('body')
<form method="POST" action="{{ route('expense_entry.store') }}" enctype="multipart/form-data">
    @csrf
    @include('driver.expense.form', ['expenses' => $expenses])
</form>
@endsection
