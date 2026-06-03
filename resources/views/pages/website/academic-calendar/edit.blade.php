@extends('layouts.master')
@section('title', 'Edit Academic Calendar Item')
@section('contents')
<div class="col-12">
    <form method="POST" action="{{ route('website.academic-calendar.update', $item) }}" class="card">
        @method('PUT')
        <div class="card-header"><h3 class="card-title">Edit Academic Calendar Item</h3></div>
        @include('pages.website.academic-calendar.form')
    </form>
</div>
@endsection
