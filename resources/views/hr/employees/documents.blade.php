@extends('layouts.master')

@section('contents')
@php
    $documentTypes = [
        'nid' => 'National ID',
        'passport' => 'Passport',
        'certificate' => 'Certificate',
        'contract' => 'Contract',
        'photo_id' => 'Photo ID',
        'other' => 'Other',
    ];
@endphp
<div class="container-fluid py-3">
    @include('hr._alerts')

    <div class="card">
        <div class="card-header">
            {{-- Inner wrapper: AdminLTE's .card-header::after clearfix would otherwise be a third flex item. --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: .5rem;">
                <div>
                    <h1 class="h5 mb-0">Documents</h1>
                    <div class="small text-muted">{{ $employee->name }} · {{ $employee->employee_id }}</div>
                </div>
                <a href="{{ route('hr.employees.show', $employee->id) }}" class="btn btn-light btn-sm border">
                    <i class="fas fa-arrow-left mr-1"></i> Back to profile
                </a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('hr.employees.documents.store', $employee->id) }}" enctype="multipart/form-data" class="row align-items-end mb-4">
                @csrf
                <div class="col-md-4 form-group mb-md-0">
                    <label for="document_type" class="small font-weight-bold mb-1">Document type</label>
                    <select name="document_type" id="document_type" class="form-control form-control-sm" required>
                        @foreach($documentTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('document_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 form-group mb-md-0">
                    <label for="file" class="small font-weight-bold mb-1">File</label>
                    <input type="file" name="file" id="file" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                    <small class="form-text text-muted">JPG, PNG, PDF or Word, up to 100 KB.</small>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                        <i class="fas fa-upload mr-1"></i> Upload
                    </button>
                </div>
            </form>

            @if($documents->isEmpty())
                <p class="text-muted text-center py-4 mb-0">No documents uploaded yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>File</th>
                                <th>Size</th>
                                <th>Uploaded</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>{{ $documentTypes[$document->document_type] ?? ucfirst($document->document_type) }}</td>
                                    <td><a href="{{ $document->file_url }}" target="_blank" rel="noopener">{{ $document->original_name }}</a></td>
                                    <td>{{ $document->file_size }}</td>
                                    <td>{{ $document->uploaded_at?->format('d M Y') ?? '—' }}</td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('hr.documents.destroy', $document) }}" class="d-inline" data-confirm="{{ __('Delete this document?') }}" data-confirm-action="{{ __('Delete document') }}" data-confirm-cancel="{{ __('Keep document') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
