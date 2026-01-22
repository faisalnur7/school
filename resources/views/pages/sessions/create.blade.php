@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3>{{ isset($session) ? 'Edit' : 'Create' }} Academic Session</h3>
                    </div>

                    <form method="POST"
                        action="{{ isset($session) ? route('sessions.update', $session->id) : route('sessions.store') }}">
                        @csrf

                        <div class="card-body">
                            <div class="form-group">
                                <label>Name (English)</label>
                                <input type="text" name="name_en" class="form-control"
                                    value="{{ old('name_en', $session->name_en ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Name (Bangla)</label>
                                <input type="text" name="name_bn" class="form-control"
                                    value="{{ old('name_bn', $session->name_bn ?? '') }}" required>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Save</button>
                            <a href="{{ route('sessions.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                @include('pages.sessions.table')
            </div>
        </div>
    </div>
@endsection
