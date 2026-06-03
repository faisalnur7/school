@extends('layouts.master')
@section('title', 'Create Academic Calendar Item')
@section('contents')
<div class="col-12">
    <form method="POST" action="{{ route('website.academic-calendar.store') }}" class="card">
        <div class="card-header"><h3 class="card-title">Create Academic Calendar Item</h3></div>
        @include('pages.website.academic-calendar.form')
    </form>
</div>
@endsection
