@extends('layouts.master')
@section('title', 'Edit Gallery Item')
@section('contents')
<div class="col-12">
    <form action="{{ route('website.gallery.update', $item) }}" method="POST" enctype="multipart/form-data" class="card">
        @method('PUT')
        @include('pages.website.gallery.form')
    </form>
</div>
@endsection
