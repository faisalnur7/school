@extends('layouts.master')

@section('styles')
    @include('components.form-styles')
    @include('pages.shareholder-transactions.partials.form-styles')
@endsection

@section('contents')
    @include('pages.shareholder-transactions.partials.form', [
        'transaction' => $transaction,
        'formAction' => route('shareholder-transactions.update', $transaction->id),
        'formMethod' => 'PUT',
        'pageTitle' => 'Edit Capital Transaction',
        'pageIcon' => 'fa-edit',
        'submitLabel' => 'Update Transaction',
        'submitIcon' => 'fa-save',
        'backRoute' => route('shareholder-transactions.index'),
        'accountType' => $accountType ?? null,
        'accountId' => $accountId ?? null,
    ])
@endsection

@section('scripts')
    @include('pages.shareholder-transactions.partials.scripts')
@endsection
