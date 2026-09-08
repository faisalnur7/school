@extends('layouts.master')

@section('contents')
@php
    $feeSetsByClass = collect($feeSets ?? [])->groupBy(function ($feeSet) {
        return $feeSet->school_class_id ?? 'all';
    });
    $firstClassId = optional($classes->first())->id;
    $activeFeeSetTab = request('school_class_id') ? (string) request('school_class_id') : (string) $firstClassId;
@endphp
<div class="container-fluid px-3 py-3">
    <div class="row">
        <!-- Create Form on Left -->
        <div class="col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 font-weight-bold text-white">
                            <i class="fas fa-plus-circle mr-2"></i>Create Fee Set
                        </h4>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-outline-light btn-sm mr-2" data-toggle="modal" data-target="#duplicateFeeSetsModal">
                                <i class="fas fa-copy mr-1"></i> Duplicate
                            </button>
                            <a href="{{ route('fee-sets.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('fee-sets.store') }}" id="modernForm">
                    @csrf

                    <div class="card-body p-3">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>Errors:</strong>
                                <ul class="mb-0 mt-1 ml-4">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        {{-- Fee Set Info --}}
                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Name (English) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" required>
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Name (Bangla)</label>
                            <input type="text" name="bn_name" class="form-control form-control-sm @error('bn_name') is-invalid @enderror">
                            @error('bn_name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Academic Session <span class="text-danger">*</span></label>
                            <select name="academic_session_id" class="form-control form-control-sm @error('academic_session_id') is-invalid @enderror" required>
                                <option value="">Select Session</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('academic_session_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small font-weight-600 mb-1">Class</label>
                                <select name="school_class_id" id="schoolClass" class="form-control form-control-sm @error('school_class_id') is-invalid @enderror">
                                    <option value="">Select Class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('school_class_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small font-weight-600 mb-1">Group</label>
                                <select name="group_id" id="groupSelect" class="form-control form-control-sm @error('group_id') is-invalid @enderror">
                                    <option value="">Select Group</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('group_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Frequency <span class="text-danger">*</span></label>
                            <select name="frequency" id="frequencySelect" class="form-control form-control-sm @error('frequency') is-invalid @enderror" required>
                                <option value="monthly" {{ old('frequency', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly (Every Month)</option>
                                <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>Yearly (Once a Year)</option>
                                <option value="others" {{ old('frequency') == 'others' ? 'selected' : '' }}>Others (Specific Month)</option>
                            </select>
                            @error('frequency')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group" id="yearlyDueDateSelector" style="display:none;">
                            <label class="form-label small font-weight-600 mb-1">Yearly Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control form-control-sm @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}">
                            @error('due_date')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group" id="monthSelector" style="display:none;">
                            <label class="form-label small font-weight-600 mb-1">Select Month <span class="text-danger">*</span></label>
                            @php
                                $months = [
                                    1=>'January',  2=>'February', 3=>'March',    4=>'April',
                                    5=>'May',      6=>'June',     7=>'July',      8=>'August',
                                    9=>'September',10=>'October', 11=>'November', 12=>'December'
                                ];
                            @endphp
                            <select name="month" class="form-control form-control-sm">
                                <option value="">Select Month</option>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ old('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label small font-weight-600 mb-1">Description</label>
                            <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" rows="2" placeholder="Enter description...">{{ old('description') }}</textarea>
                            @error('description')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <hr class="my-3">

                        {{-- FEE SET ITEMS --}}
                        <h5 class="mb-3 small font-weight-bold">
                            <i class="fas fa-list mr-2"></i>Fee Categories & Amounts
                        </h5>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" id="feeItemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="small">Fee Category</th>
                                        <th class="small text-center" style="width: 30%;">Amount</th>
                                        <th class="small text-center" style="width: 10%;">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="fee_category_id[]" class="form-control form-control-sm" required>
                                                <option value="">Select Category</option>
                                                @foreach ($feeCategories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <input type="number" step="0.01" name="amount[]" class="form-control form-control-sm" required>
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-success addRow">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-top py-2 px-3">
                        <div class="d-flex justify-content-between gap-2">
                            <a href="{{ route('fee-sets.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save mr-1"></i>Create Fee Set
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal fade" id="duplicateFeeSetsModal" tabindex="-1" role="dialog" aria-labelledby="duplicateFeeSetsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow duplicate-fee-sets-modal-content">
                        <div class="modal-header duplicate-fee-sets-modal-header text-white">
                            <h5 class="modal-title text-white" id="duplicateFeeSetsModalLabel">
                                <span class="duplicate-fee-sets-modal-icon"><i class="fas fa-copy"></i></span>
                                <span>Duplicate Fee Sets</span>
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" action="{{ route('fee-sets.duplicate') }}">
                            @csrf
                            <div class="modal-body duplicate-fee-sets-modal-body">
                                <div class="duplicate-fee-sets-modal-intro mb-4">
                                    <div class="duplicate-fee-sets-modal-intro-icon"><i class="fas fa-layer-group"></i></div>
                                    <div>
                                        <div class="font-weight-bold text-dark mb-1">Start with an existing fee structure</div>
                                        <div class="text-muted small">All fee sets and their categories will be copied to the target session.</div>
                                    </div>
                                </div>
                                <div class="form-group duplicate-fee-sets-field">
                                    <label class="small font-weight-bold">Copy from session <span class="text-danger">*</span></label>
                                    <select name="source_session_id" class="form-control form-control-sm" required>
                                        <option value="">Select source session</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session->id }}">{{ $session->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="duplicate-fee-sets-transfer-arrow"><i class="fas fa-arrow-down"></i></div>
                                <div class="form-group duplicate-fee-sets-field mb-0">
                                    <label class="small font-weight-bold">Copy to session <span class="text-danger">*</span></label>
                                    <select name="target_session_id" class="form-control form-control-sm" required>
                                        <option value="">Select target session</option>
                                        @foreach ($sessions as $session)
                                            <option value="{{ $session->id }}">{{ $session->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer duplicate-fee-sets-modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary duplicate-fee-sets-submit">
                                    <i class="fas fa-copy mr-1"></i>Duplicate Fee Sets
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Sets List on Right -->
        <div class="col-md-7 mb-3">
            <div class="card shadow-sm border-0 classwise-fee-sets-card">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4 class="mb-1 font-weight-bold">Fee Sets by Class</h4>
                            <div class="text-muted small">Switch between classes to review the fee structure.</div>
                        </div>
                        <form method="GET" action="{{ route('fee-sets.index') }}" id="feeSetSessionFilterForm" class="fee-session-filter form-inline mt-2 mt-md-0">
                            @if(request('school_class_id'))<input type="hidden" name="school_class_id" value="{{ request('school_class_id') }}">@endif
                            <label for="feeSetSessionFilter" class="small font-weight-bold text-muted mr-2 mb-0">Session</label>
                            <select id="feeSetSessionFilter" name="academic_session_id" class="form-control form-control-sm">
                                <option value="">All sessions</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" @selected($sessionId == $session->id)>{{ $session->name_en }}</option>
                                @endforeach
                            </select>
                            @if($sessionId)<a href="{{ route('fee-sets.index', request('school_class_id') ? ['school_class_id' => request('school_class_id')] : []) }}" class="btn btn-sm btn-outline-secondary">Clear</a>@endif
                        </form>
                    </div>
                </div>

                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs classwise-tabs mb-3" id="feeSetClassTabs" role="tablist">
                            @foreach ($classes as $class)
                                @php $classId = (string) $class->id; @endphp
                                <li class="nav-item" role="presentation">
                                    <a
                                        class="nav-link {{ $activeFeeSetTab === $classId ? 'active' : '' }}"
                                        id="fee-set-tab-{{ $classId }}"
                                        data-toggle="tab"
                                        href="#fee-set-pane-{{ $classId }}"
                                        role="tab"
                                        aria-controls="fee-set-pane-{{ $classId }}"
                                        aria-selected="{{ $activeFeeSetTab === $classId ? 'true' : 'false' }}"
                                    >
                                        {{ $class->name_en }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach ($classes as $class)
                                @php
                                    $classId = (string) $class->id;
                                    $classFeeSets = $feeSetsByClass->get($class->id, collect());
                                @endphp
                                <div
                                    class="tab-pane fade {{ $activeFeeSetTab === $classId ? 'show active' : '' }}"
                                    id="fee-set-pane-{{ $classId }}"
                                    role="tabpanel"
                                    aria-labelledby="fee-set-tab-{{ $classId }}"
                                >
                                    @include('pages.fee_sets.table', [
                                        'feeSets' => $classFeeSets,
                                        'showHeader' => false,
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection

@section('styles')
@include('components.form-styles')
<style>
    .row.g-2 {
        margin-right: -0.5rem;
        margin-left: -0.5rem;
    }

    .row.g-2 > [class*="col-"] {
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }

    .classwise-fee-sets-card .classwise-tabs {
        display: flex;
        flex-wrap: nowrap;
        gap: 0;
        white-space: normal;
        overflow-x: auto;
        padding-bottom: 0;
        border-bottom: 0;
    }

    .classwise-fee-sets-card .classwise-tabs .nav-item {
        flex: 0 0 auto;
    }

    .classwise-fee-sets-card .classwise-tabs .nav-link {
        border-radius: 0.25rem;
        border: 1px solid #cbd5e1;
        color: #2563eb;
        background: #fff;
        padding: 0.45rem 0.85rem;
        font-size: 0.86rem;
        margin: 0;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }

    .classwise-fee-sets-card .classwise-tabs .nav-item + .nav-item .nav-link {
        margin-left: -1px;
    }

    .classwise-fee-sets-card .classwise-tabs .nav-item:first-child .nav-link {
        border-top-left-radius: 0.3rem;
        border-bottom-left-radius: 0.3rem;
    }

    .classwise-fee-sets-card .classwise-tabs .nav-item:last-child .nav-link {
        border-top-right-radius: 0.3rem;
        border-bottom-right-radius: 0.3rem;
    }

    .classwise-fee-sets-card .classwise-tabs .nav-link.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .classwise-fee-sets-card .classwise-tabs .nav-link:hover {
        border-color: #2563eb;
        color: #fff;
        background: #3b82f6;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
    }

    .classwise-fee-sets-card .fee-session-filter {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.5rem;
    }

    .classwise-fee-sets-card .fee-session-filter .mr-2 {
        margin-right: 0 !important;
    }

    .classwise-fee-sets-card .fee-session-filter select {
        min-width: 130px;
    }

    .classwise-fee-sets-card .fee-set-table-card--embedded {
        border: 1px solid #e2e8f0;
        border-radius: 0.9rem;
        box-shadow: none;
        overflow: hidden;
    }

    .classwise-fee-sets-card .fee-set-table-card--embedded .card-body {
        padding-bottom: 0 !important;
    }

    .classwise-fee-sets-card .fee-set-table-card--embedded .table {
        font-size: 0.9rem;
    }

    .classwise-fee-sets-card .fee-set-table-card--embedded .table th {
        color: #334155;
        font-size: 0.78rem;
        letter-spacing: 0.01em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .classwise-fee-sets-card .fee-set-table-card--embedded .table td,
    .classwise-fee-sets-card .fee-set-table-card--embedded .table th {
        padding: 0.8rem 0.95rem;
        vertical-align: middle;
    }

    .duplicate-fee-sets-modal-content {
        border-radius: 0.85rem;
        overflow: hidden;
    }

    .duplicate-fee-sets-modal-header {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border-bottom: 0;
        padding: 1rem 1.25rem;
    }

    .duplicate-fee-sets-modal-header .modal-title {
        display: flex;
        align-items: center;
        font-size: 1rem;
        letter-spacing: 0.01em;
    }

    .duplicate-fee-sets-modal-icon,
    .duplicate-fee-sets-modal-intro-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .duplicate-fee-sets-modal-icon {
        width: 2rem;
        height: 2rem;
        margin-right: 0.65rem;
        border-radius: 0.55rem;
        background: rgba(255, 255, 255, 0.18);
    }

    .duplicate-fee-sets-modal-header .close {
        opacity: 0.8;
        font-size: 1.35rem;
    }

    .duplicate-fee-sets-modal-header .close:hover {
        opacity: 1;
    }

    .duplicate-fee-sets-modal-body {
        padding: 1.35rem 1.25rem 1.15rem;
    }

    .duplicate-fee-sets-modal-intro {
        display: flex;
        align-items: flex-start;
        padding: 0.85rem;
        border: 1px solid #dbeafe;
        border-radius: 0.65rem;
        background: #eff6ff;
    }

    .duplicate-fee-sets-modal-intro-icon {
        width: 2rem;
        height: 2rem;
        margin-right: 0.7rem;
        border-radius: 50%;
        color: #2563eb;
        background: #dbeafe;
    }

    .duplicate-fee-sets-field label {
        color: #334155;
        margin-bottom: 0.4rem;
    }

    .duplicate-fee-sets-field .form-control {
        height: 2.7rem;
        border-color: #cbd5e1;
        border-radius: 0.5rem;
    }

    .duplicate-fee-sets-field .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.12);
    }

    .duplicate-fee-sets-transfer-arrow {
        width: 1.8rem;
        height: 1.8rem;
        margin: -0.15rem auto 0.35rem;
        border: 1px solid #bfdbfe;
        border-radius: 50%;
        color: #2563eb;
        background: #fff;
        font-size: 0.7rem;
        line-height: 1.65rem;
        text-align: center;
    }

    .duplicate-fee-sets-modal-footer {
        gap: 0.55rem;
        padding: 0.9rem 1.25rem 1.1rem;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .duplicate-fee-sets-submit {
        padding: 0.6rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
    }

    @media (max-width: 767.98px) {
        .classwise-fee-sets-card .card-header > .d-flex {
            align-items: flex-start !important;
        }

        .classwise-fee-sets-card .fee-session-filter {
            width: 100%;
        }

        .classwise-fee-sets-card .fee-session-filter select {
            flex: 1 1 auto;
        }

        .duplicate-fee-sets-modal-body,
        .duplicate-fee-sets-modal-footer {
            padding-right: 1rem;
            padding-left: 1rem;
        }

        .duplicate-fee-sets-modal-footer {
            flex-wrap: wrap;
        }

        .duplicate-fee-sets-submit {
            flex: 1 1 auto;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    $(function () {
        // Frequency selector - Show/hide month field
        $('#frequencySelect').on('change', function() {
            const freq = $(this).val();
            
            // Show month selector only for 'others' frequency
            $('#monthSelector').toggle(freq === 'others');
            $('#yearlyDueDateSelector').toggle(freq === 'yearly');
        }).trigger('change');

        $('#feeSetSessionFilter').on('change', function() {
            $('#feeSetSessionFilterForm').trigger('submit');
        });

        // Add row functionality
        $(document).on('click', '.addRow', function() {
            const newRow = `
                <tr>
                    <td>
                        <select name="fee_category_id[]" class="form-control form-control-sm" required>
                            <option value="">Select Category</option>
                            @foreach ($feeCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="amount[]" class="form-control form-control-sm" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger removeRow">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#feeItemsTable tbody').append(newRow);
        });

        // Remove row functionality
        $(document).on('click', '.removeRow', function() {
            if ($('#feeItemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('At least one fee category is required');
            }
        });

        // Auto-scroll to first error
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection
