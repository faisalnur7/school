@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-white rounded-2xl shadow p-5">
        <div class="flex items-center justify-between gap-4 flex-wrap mb-4">
            <div>
                <h3 class="text-slate-900 text-2xl font-bold mb-1">Create Certificate Type</h3>
                <p class="text-slate-500 text-sm mb-0">Add the certificate type and seed its first template.</p>
            </div>
            <a href="{{ route('certificates.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('certificates.store') }}">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Type Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="optional-custom-slug">
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Initial Template Name</label>
                    <input type="text" name="template_name" value="{{ old('template_name', 'Default Template') }}" class="form-control" required>
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">Initial Template Body</label>
                    <textarea name="template_body" id="template_body" rows="12" class="form-control font-monospace" required>{{ old('template_body') }}</textarea>
                </div>
            </div>

            <div class="mt-4 mb-3">
                <div class="font-weight-bold mb-2">Shared placeholders</div>
                <div class="d-flex flex-wrap" style="gap:8px">
                    @foreach(\App\Models\Certificate::placeholderGroups()['shared'] as $placeholder)
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="insertPlaceholder('{{ $placeholder['token'] }}')">
                            {{ $placeholder['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Save Type
            </button>
        </form>
    </div>
</div>

<script>
    function insertPlaceholder(token) {
        const textarea = document.getElementById('template_body');
        const start = textarea.selectionStart ?? textarea.value.length;
        const end = textarea.selectionEnd ?? textarea.value.length;
        const before = textarea.value.substring(0, start);
        const after = textarea.value.substring(end);
        const needsSpaceBefore = before.length && !/\s$/.test(before) && !/^\s/.test(token);
        const needsSpaceAfter = after.length && !/^\s/.test(after) && !/\s$/.test(token);
        const insert = `${needsSpaceBefore ? ' ' : ''}${token}${needsSpaceAfter ? ' ' : ''}`;
        textarea.value = before + insert + after;
        textarea.focus();
        textarea.setSelectionRange(before.length + insert.length, before.length + insert.length);
    }
</script>
@endsection
