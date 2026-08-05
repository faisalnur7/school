@extends('layouts.master')

@section('contents')
    <div class="container-fluid px-3 py-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-plus-circle mr-2"></i>Create Role
                    </h4>
                    <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('roles.store') }}" id="modernForm">
                @csrf

                <div class="card-body p-3">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 mb-2 py-2" role="alert">
                            <i class="fas fa-exclamation-circle mr-1"></i><strong>Errors:</strong>
                            <ul class="mb-0 mt-1 ml-4 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label class="small mb-1">Role Name <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control form-control-sm @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required placeholder="e.g. Teacher, Accountant">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group mb-2">
                                <label class="small mb-1">Description</label>
                                <input type="text" name="description"
                                    class="form-control form-control-sm @error('description') is-invalid @enderror"
                                    value="{{ old('description') }}" placeholder="Optional description">
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0 role-permissions-group">
                        <label class="text-md mb-2 font-weight-bold">Permissions</label>
                        @include('pages.roles._permission-cards')
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

    @section('styles')
        @include('components.form-styles')
        <style>
            .role-permissions-group {
                min-height: 0;
            }

            .permission-card-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 0.85rem;
            }

            .permission-card-tile {
                appearance: none;
                border: 1px solid #dbe4ee;
                border-radius: 1rem;
                background: #fff;
                padding: 1rem 1rem;
                min-height: 86px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.05);
                text-align: left;
                width: 100%;
                transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
            }

            .permission-card-tile:hover,
            .permission-card-tile:focus {
                border-color: #2563eb;
                box-shadow: 0 12px 26px rgba(37, 99, 235, 0.12);
                transform: translateY(-1px);
                outline: none;
            }

            .permission-card-tile__copy {
                min-width: 0;
                display: flex;
                flex-direction: column;
                gap: 0.3rem;
            }

            .permission-card-tile__title {
                font-size: 1rem;
                font-weight: 800;
                color: #0f172a;
                line-height: 1.25;
            }

            .permission-card-tile__subtitle {
                font-size: 0.82rem;
                color: #64748b;
            }

            .permission-card-tile__icon {
                flex: 0 0 auto;
                color: #94a3b8;
                font-size: 0.9rem;
            }

            .permission-modal__content {
                border: 0;
                border-radius: 1rem;
                overflow: hidden;
            }

            .permission-modal__header {
                border-bottom: 1px solid rgba(148, 163, 184, 0.18);
            }

            .permission-modal__body {
                background: #fff;
            }

            .permission-modal__footer {
                border-top: 1px solid rgba(148, 163, 184, 0.18);
                background: #f8fafc;
            }

            .permission-matrix-wrap {
                min-width: 0;
            }

            .permission-matrix {
                min-width: 640px;
                table-layout: fixed;
            }

            .permission-matrix th,
            .permission-matrix td {
                border: 0 !important;
                vertical-align: middle;
                padding: 0.85rem 0.5rem;
            }

            .permission-matrix thead th {
                font-size: 0.82rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: #475569;
            }

            .permission-matrix__module,
            .permission-row-label {
                text-align: left;
                width: 34%;
                font-weight: 700;
                color: #0f172a;
            }

            .permission-matrix__action,
            .permission-cell {
                text-align: center;
                width: 13.2%;
            }

            .permission-checkbox-wrap {
                min-height: 1.5rem;
                min-width: 1.5rem;
            }

            .permission-checkbox {
                width: 1rem;
                height: 1rem;
                margin: 0;
                cursor: pointer;
                accent-color: #2563eb;
            }

            @media (max-width: 1199.98px) {
                .permission-card-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 575.98px) {
                .permission-card-grid {
                    grid-template-columns: 1fr;
                }

                .permission-card-tile {
                    min-height: 78px;
                    padding: 0.9rem 0.95rem;
                }

                .permission-card-tile__title {
                    font-size: 0.96rem;
                }

                .permission-modal__content {
                    border-radius: 0.85rem;
                }

                .permission-matrix {
                    min-width: 560px;
                }

                .permission-matrix__module,
                .permission-row-label {
                    width: 38%;
                }
            }
        </style>
    @endsection

@section('scripts')
    <script>
        $(function() {
            if ($('.is-invalid').length > 0) {
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 50
                }, 300);
            }
        });
    </script>
@endsection
