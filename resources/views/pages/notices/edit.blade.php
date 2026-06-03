@extends('layouts.master')
@section('title', 'Edit Notice')
@section('contents')
<div class="col-12">
    <form action="{{ route('notice.update', $notice) }}" method="POST" class="card">
        @method('PUT')
        @include('pages.notices.form')
    </form>
</div>
@endsection
