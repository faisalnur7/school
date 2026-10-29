@extends('layouts.master')

@section('title', 'Mobile App Settings')

@section('contents')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1">Mobile App Settings</h1>
            <p class="text-muted mb-0">Control the single global student mobile application experience.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('platform-admins.mobile-app.update') }}">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header"><strong>Global application status</strong></div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="mobile-app-active" name="is_active" value="1" {{ $settings->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="mobile-app-active">Mobile app active</label>
                </div>
                <label class="form-label" for="maintenance-message">Inactive message</label>
                <textarea class="form-control" id="maintenance-message" name="maintenance_message" rows="2" maxlength="1000" placeholder="Shown to students while the app is inactive">{{ old('maintenance_message', $settings->maintenance_message) }}</textarea>
                <small class="text-muted">When inactive, students see this message and all application menus are hidden.</small>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><strong>Mobile application screens</strong></div>
            <div class="card-body p-0">
                @foreach($features as $feature)
                    <div class="d-flex flex-wrap align-items-center gap-3 px-3 py-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $feature->label }}</div>
                            <small class="text-muted">{{ $feature->key }} · {{ $feature->flutter_route }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0" for="feature-{{ $feature->key }}-order">Order</label>
                            <input class="form-control form-control-sm" style="width: 80px" type="number" min="1" id="feature-{{ $feature->key }}-order" name="features[{{ $feature->key }}][sort_order]" value="{{ old('features.'.$feature->key.'.sort_order', $feature->sort_order) }}">
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input type="hidden" name="features[{{ $feature->key }}][is_enabled]" value="0">
                            <input class="form-check-input" type="checkbox" id="feature-{{ $feature->key }}" name="features[{{ $feature->key }}][is_enabled]" value="1" {{ $feature->is_enabled ? 'checked' : '' }} {{ $feature->key === 'dashboard' ? 'disabled' : '' }}>
                            <label class="form-check-label" for="feature-{{ $feature->key }}">Enabled{{ $feature->key === 'dashboard' ? ' (required)' : '' }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save settings</button>
        </div>
    </form>
</div>
@endsection
