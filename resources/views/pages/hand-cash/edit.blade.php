@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                        <h3 class="card-title">Edit Hand Cash</h3>
                    </div>

                    <form method="POST" action="{{ route('hand-cash.update', $handCash->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            <div class="form-group">
                                <label>Label</label>
                                <input type="text" name="label" class="form-control @error('label') is-invalid @enderror"
                                       value="{{ old('label', $handCash->label) }}" required>
                                @error('label') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Opening Amount (BDT)</label>
                                <input type="number" name="opening_amount" step="0.01" min="0"
                                       class="form-control @error('opening_amount') is-invalid @enderror"
                                       value="{{ old('opening_amount', $handCash->opening_amount) }}" required>
                                @error('opening_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Opening Date</label>
                                <input type="text" name="opening_date" datepicker datepicker-format="dd/mm/yyyy"
                                       class="form-control @error('opening_date') is-invalid @enderror"
                                       value="{{ old('opening_date', $handCash->opening_date->format('d/m/Y')) }}"
                                       placeholder="dd/mm/yyyy" autocomplete="off" required>
                                @error('opening_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Notes <small class="text-muted">(optional)</small></label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $handCash->notes) }}</textarea>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active"
                                           name="is_active" value="1"
                                           {{ old('is_active', $handCash->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer">
                            <button class="btn btn-success">Update</button>
                            <a href="{{ route('hand-cash.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-8">
                @include('pages.hand-cash.table')
            </div>
        </div>
    </div>
@endsection