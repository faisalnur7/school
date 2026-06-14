@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-white rounded-2xl shadow p-5 mb-5">
        <div class="flex items-center justify-between gap-4 flex-wrap mb-4">
            <div>
                <h3 class="text-slate-900 text-2xl font-bold mb-1">Edit Certificate Type</h3>
                <p class="text-slate-500 text-sm mb-0">Update the type details, manage templates, and assign the active template.</p>
            </div>
            <a href="{{ route('certificates.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('certificates.update', $certificate) }}" class="mb-5">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Type Name</label>
                    <input type="text" name="name" value="{{ old('name', $certificate->name) }}" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $certificate->slug) }}" class="form-control" required>
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $certificate->description) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Active Template</label>
                    <select name="active_template_id" class="form-control">
                        <option value="">Select active template</option>
                        @foreach($certificate->templates as $template)
                            <option value="{{ $template->id }}" @selected((int) old('active_template_id', $certificate->active_template_id) === $template->id)>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 mb-0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $certificate->is_active))>
                        <span>Active</span>
                    </label>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Save Type
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow p-5 mb-5">
        <div class="flex items-start justify-between gap-4 flex-wrap mb-3">
            <div>
                <h4 class="text-slate-900 text-xl font-bold mb-1">Templates</h4>
                <p class="text-slate-500 text-sm mb-0">Create multiple templates and assign the one that should be active.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4">
            @forelse($certificate->templates as $template)
                <div class="card border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3" style="gap:10px">
                            <div>
                                <div class="font-weight-bold">{{ $template->name }}</div>
                                <div class="text-muted small">
                                    {{ $certificate->active_template_id === $template->id ? 'Active template' : 'Template available for assignment' }}
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                @if($certificate->active_template_id !== $template->id)
                                    <form action="{{ route('certificates.templates.activate', [$certificate, $template]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Make Active</button>
                                    </form>
                                @endif
                                <form action="{{ route('certificates.templates.destroy', [$certificate, $template]) }}" method="POST" onsubmit="return confirm('Delete this template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>

                        <form action="{{ route('certificates.templates.update', [$certificate, $template]) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Template Name</label>
                                    <input type="text" name="name" value="{{ old('name', $template->name) }}" class="form-control" required>
                                </div>
                                <div class="flex items-end">
                                    <label class="inline-flex items-center gap-2 mb-0">
                                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $template->is_active))>
                                        <span>Template Active</span>
                                    </label>
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="form-label">Template Body</label>
                                    <textarea name="body" id="template_body_{{ $template->id }}" rows="10" class="form-control font-monospace" onfocus="setActiveCertificateTemplate('template_body_{{ $template->id }}')">{{ old('body', $template->body) }}</textarea>
                                </div>
                            </div>

                            <div class="mt-3 mb-3">
                                @foreach($placeholders as $groupName => $groupPlaceholders)
                                    <div class="mb-3">
                                        <div class="font-weight-bold mb-2">
                                            {{ ucfirst(str_replace('_', ' ', $groupName)) }} placeholders
                                        </div>
                                        <div class="d-flex flex-wrap" style="gap:8px">
                                            @foreach($groupPlaceholders as $placeholder)
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-secondary js-certificate-placeholder"
                                                    data-target="template_body_{{ $template->id }}"
                                                    data-token="{{ $placeholder['token'] }}">
                                                    {{ $placeholder['label'] }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save mr-1"></i> Update Template
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-muted">No templates assigned yet.</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-5">
        <h4 class="text-slate-900 text-xl font-bold mb-1">Add Template</h4>
        <p class="text-slate-500 text-sm mb-4">Create another template under this certificate type.</p>

        <form action="{{ route('certificates.templates.store', $certificate) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Template Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 mb-0">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span>Template Active</span>
                    </label>
                </div>
                <div class="lg:col-span-2">
                    <label class="form-label">Template Body</label>
                    <textarea name="body" id="new_template_body" rows="10" class="form-control font-monospace"></textarea>
                </div>
            </div>

            <div class="mt-3 mb-3">
                @foreach($placeholders as $groupName => $groupPlaceholders)
                    <div class="mb-3">
                        <div class="font-weight-bold mb-2">
                            {{ ucfirst(str_replace('_', ' ', $groupName)) }} placeholders
                        </div>
                        <div class="d-flex flex-wrap" style="gap:8px">
                            @foreach($groupPlaceholders as $placeholder)
                                <button type="button"
                                    class="btn btn-sm btn-outline-secondary js-certificate-placeholder"
                                    data-target="new_template_body"
                                    data-token="{{ $placeholder['token'] }}">
                                    {{ $placeholder['label'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Add Template
            </button>
        </form>
    </div>
</div>

<script>
    let activeCertificateTemplate = 'new_template_body';

    function setActiveCertificateTemplate(templateId) {
        activeCertificateTemplate = templateId;
    }

    function insertCertificatePlaceholder(templateId, token) {
        const textarea = document.getElementById(templateId || activeCertificateTemplate);
        if (!textarea) return;
        textarea.focus();

        const start = textarea.selectionStart ?? textarea.value.length;
        const end = textarea.selectionEnd ?? textarea.value.length;
        const before = textarea.value.substring(0, start);
        const after = textarea.value.substring(end);
        const needsSpaceBefore = before.length && !/\s$/.test(before) && !/^\s/.test(token);
        const needsSpaceAfter = after.length && !/^\s/.test(after) && !/\s$/.test(token);
        const insert = `${needsSpaceBefore ? ' ' : ''}${token}${needsSpaceAfter ? ' ' : ''}`;
        textarea.value = before + insert + after;
        const cursor = before.length + insert.length;
        textarea.setSelectionRange(cursor, cursor);
        textarea.focus();
        activeCertificateTemplate = textarea.id;
    }

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.js-certificate-placeholder');
        if (!button) return;

        event.preventDefault();
        insertCertificatePlaceholder(button.dataset.target, button.dataset.token);
    });
</script>
@endsection
