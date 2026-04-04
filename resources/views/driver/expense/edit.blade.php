@extends('driver.layouts.master')

@section('page_title', 'Edit Expense')

@section('body')
@include('driver.expense.form', [
    'route' => route('expense_entry.update', $expenseentry->id),
    'method' => 'PUT',
    'expenses' => $expenses,
    'expenseentry' => $expenseentry,
    'buttonText' => 'Update Expense'
])
@endsection

