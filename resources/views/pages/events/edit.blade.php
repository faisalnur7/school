@extends('layouts.master')
@section('title', 'Edit Event')
@section('contents')
<div class="col-12">
    <form method="POST" action="{{ route('events.update', $event) }}" class="card" enctype="multipart/form-data">
        <div class="card-header"><h3 class="card-title">Edit Event</h3></div>
        @include('pages.events.form')
    </form>
</div>
@endsection
