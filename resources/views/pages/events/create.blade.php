@extends('layouts.master')
@section('title', 'Create Event')
@section('contents')
<div class="col-12">
    <form method="POST" action="{{ route('events.store') }}" class="card" enctype="multipart/form-data">
        <div class="card-header"><h3 class="card-title">Create Event</h3></div>
        @include('pages.events.form')
    </form>
</div>
@endsection
