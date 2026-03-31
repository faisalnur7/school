@extends('layouts.master')

@section('contents')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">📦 Edit Asset</h4>
        <a href="{{ route('assets.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="card border-0 shadow-sm" style="max-width:600px">
        <div class="card-body">
            <form action="{{ route('assets.update', $asset) }}" method="POST">
                @csrf @method('PUT')
                @include('pages.assets.assets._form')
                <button type="submit" class="btn btn-primary">Update Asset</button>
            </form>
        </div>
    </div>
</div>
@endsection
