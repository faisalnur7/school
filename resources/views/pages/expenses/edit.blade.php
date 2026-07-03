@extends('layouts.master')

@section('styles')
    @include('components.form-styles')
    @include('pages.expenses.partials.form-styles')
    @include('components.dropzone-attachment-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
@endsection

@section('contents')
    @include('pages.expenses.partials.form', [
        'expense' => $expense,
        'formAction' => route('expenses.update', $expense->id),
        'formMethod' => 'PUT',
        'pageTitle' => 'Edit Expense',
        'pageIcon' => 'fa-edit',
        'submitLabel' => 'Update Expense',
        'submitIcon' => 'fa-save',
        'backRoute' => route('expenses.index'),
    ])
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    @include('components.dropzone-attachment-script')
    @include('pages.expenses.partials.scripts')
@endsection
