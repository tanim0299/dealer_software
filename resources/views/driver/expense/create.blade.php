@extends('driver.layouts.master')

@section('page_title', 'Add Expense')

@section('body')
@include('driver.expense.form', [
    'route' => route('expense_entry.store'),
    'expenses' => $expenses,
    'buttonText' => 'Save Expense'
])
@endsection

