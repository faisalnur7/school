@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">

        {{-- Create Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                    <h3 class="card-title">Create Fee Category</h3>
                </div>

                <form method="POST" action="{{ route('fee-categories.store') }}">
                    @csrf

                    <div class="card-body">

                        <div class="form-group">
                            <label>Name (English)</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Name (Bangla)</label>
                            <input type="text" name="bn_name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button class="btn btn-success">Save</button>
                        <a href="{{ route('fee-categories.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="col-md-8">
            @include('pages.fee_categories.table')
        </div>

    </div>
</div>
@endsection
