@extends('layouts.master')

@section('styles')
    @include('components.form-styles')
    @include('pages.expenses.partials.form-styles')
    @include('components.dropzone-attachment-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
@endsection

@section('contents')
    @include('pages.expenses.partials.form', [
        'formAction' => route('expenses.store'),
        'formMethod' => 'POST',
        'pageTitle' => 'Record Expense',
        'pageIcon' => 'fa-plus-circle',
        'submitLabel' => 'Create Expense',
        'submitIcon' => 'fa-save',
        'backRoute' => route('expenses.index'),
    ])
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    @include('components.dropzone-attachment-script')
    @include('pages.expenses.partials.scripts')
@endsection
