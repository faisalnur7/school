@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">

        {{-- Edit Form --}}
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                    <h3 class="card-title">Edit Fee Category</h3>
                </div>

                <form method="POST" action="{{ route('fee-categories.update', $feeCategory->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        <div class="form-group">
                            <label>Name (English)</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ $feeCategory->name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Name (Bangla)</label>
                            <input type="text" name="bn_name" class="form-control"
                                   value="{{ $feeCategory->bn_name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $feeCategory->description }}</textarea>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button class="btn btn-success">Update</button>
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
