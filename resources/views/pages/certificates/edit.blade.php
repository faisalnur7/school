@extends('layouts.master')

@section('contents')
@php($layout = $certificate->layoutSettings())
@php($principalPhone = trim((string) ($setting?->principal_phone ?? '')) ?: (trim((string) ($setting?->contact_number_1 ?? '')) ?: trim((string) ($setting?->contact_number_2 ?? ''))))
<div class="container-fluid certificate-editor-page">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;700;800&display=swap');
        .certificate-editor-page .workspace-tab { border: 0; border-bottom: 3px solid transparent; background: transparent; color: #64748b; font-weight: 600; padding: .85rem 1rem; }
        .certificate-editor-page .workspace-tab:hover, .certificate-editor-page .workspace-tab.is-active { color: #4f46e5; border-bottom-color: #4f46e5; }
        .certificate-editor-page .template-card.is-default { border-color: #818cf8 !important; box-shadow: 0 0 0 1px rgba(129, 140, 248, .2); }
        .certificate-editor-page .placeholder-chip { border: 1px solid #cbd5e1; background: #fff; color: #334155; border-radius: 999px; font-size: .78rem; padding: .35rem .65rem; }
        .certificate-editor-page .placeholder-chip:hover { border-color: #6366f1; color: #4338ca; background: #eef2ff; }
        .certificate-editor-page .preview-paper { position: relative; overflow: hidden; width: min(100%, 780px); min-height: 1100px; margin: 0 auto; padding: 132px 70px 70px; background: #fff; border: 1px solid #cbd5e1; box-shadow: 0 12px 30px rgba(15, 23, 42, .12); font-family: Georgia, 'Times New Roman', serif; color: #111827; }
        .certificate-editor-page .preview-paper > * { position: relative; z-index: 1; }
        .certificate-editor-page .preview-watermark { position: absolute !important; z-index: 0 !important; top: 42%; left: 50%; width: 55%; max-width: none; max-height: none; transform: translate(-50%, -50%); object-fit: contain; opacity: .12; }
        .certificate-editor-page .preview-paper .preview-heading { display: table; margin: 0 auto 76px; padding: 9px 42px; border: 1px solid #4b5563; background: #eef2e3; color: #111827; font-family: Arial, sans-serif; font-size: 1.5rem; font-weight: 500; letter-spacing: .01em; text-transform: uppercase; }
        .certificate-editor-page .preview-paper .preview-school { display: none; }
        .certificate-editor-page .preview-body { position: relative; min-height: 550px; font-size: 1.22rem; line-height: 1.9; text-align: justify; text-justify: inter-word; }
        .certificate-editor-page .preview-body[contenteditable="true"] { outline: 2px dashed #6366f1; outline-offset: 5px; cursor: text; }
        .certificate-editor-page .preview-body p { margin: 0 0 1rem; }
        .certificate-editor-page .preview-bottom { display: flex; align-items: flex-end; justify-content: space-between; gap: 2rem; margin-top: 56px; font-family: Arial, sans-serif; font-size: .95rem; line-height: 1.45; }
        .certificate-editor-page .preview-reason { max-width: 46%; }
        .certificate-editor-page .preview-reason-title { margin-bottom: .45rem; font-weight: 600; }
        .certificate-editor-page .preview-principal { min-width: 44%; text-align: center; }
        .certificate-editor-page .preview-principal-line { width: 170px; margin: 0 auto .5rem; border-top: 1px solid #4b5563; }
        .certificate-editor-page .preview-principal-name { font-size: 1.35rem; font-weight: 700; }
        .certificate-editor-page .certificate-draggable { cursor: move; touch-action: none; transition: outline .15s, box-shadow .15s; }
        .certificate-editor-page .certificate-draggable:hover, .certificate-editor-page .certificate-draggable.is-dragging { outline: 2px dashed #6366f1; outline-offset: 5px; box-shadow: 0 0 0 5px rgba(99, 102, 241, .1); }
        .certificate-editor-page .certificate-settings-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: .75rem; }
        .certificate-editor-page .preview-placeholder { color: #b45309; background: #fef3c7; border-radius: 3px; padding: 0 .15rem; }
        .certificate-editor-page .template-body-editor { min-height: 260px; resize: vertical; }
        .certificate-editor-page { padding-bottom: 3rem; }
        .certificate-editor-page > .d-flex:first-of-type { padding: .5rem .25rem .25rem; }
        .certificate-editor-page > .d-flex:first-of-type h3 { letter-spacing: -.02em; }
        .certificate-editor-page > .d-flex:first-of-type .btn { min-width: 132px; white-space: nowrap; }
        .certificate-editor-page > .bg-white.rounded-xl { border-radius: 1rem !important; overflow: hidden; }
        .certificate-editor-page .workspace-tab { min-height: 56px; }
        .certificate-editor-page [data-certificate-panel="preview"] > .row { margin-left: -.5rem; margin-right: -.5rem; }
        .certificate-editor-page [data-certificate-panel="preview"] > .row > [class*="col-"] { padding-left: .5rem; padding-right: .5rem; }
        .certificate-editor-page [data-certificate-panel="preview"] .col-lg-8 > .bg-white { padding: 1.75rem !important; border-radius: 1rem !important; }
        .certificate-editor-page [data-certificate-panel="preview"] .col-lg-8 > .bg-white > .d-flex.flex-column { padding-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; }
        .certificate-editor-page [data-certificate-panel="preview"] .col-lg-8 > .bg-white > .d-flex.flex-column > div:first-child { min-width: 0; }
        .certificate-editor-page [data-certificate-panel="preview"] .col-lg-8 > .bg-white > .d-flex.flex-column > div:first-child p { max-width: 620px; line-height: 1.55; }
        .certificate-editor-page [data-certificate-panel="preview"] .col-lg-8 > .bg-white > .d-flex.flex-column > div:last-child { gap: .6rem !important; flex-shrink: 0; }
    .certificate-editor-page [data-certificate-panel="preview"] #certificatePreviewTemplate,
    .certificate-editor-page [data-certificate-panel="preview"] #certificatePreviewTemplate + .select2-container { display: none !important; }
        .certificate-editor-page [data-certificate-panel="preview"] #saveCertificateLayout,
        .certificate-editor-page [data-certificate-panel="preview"] #saveCertificatePreviewText { min-width: 112px; white-space: nowrap; }
        .certificate-editor-page [data-certificate-panel="preview"] #saveCertificateLayout,
        .certificate-editor-page [data-certificate-panel="preview"] #saveCertificatePreviewText { width: 124px; min-height: 42px; line-height: 1.25; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card { position: sticky; top: 1rem; padding: 1.35rem !important; border-radius: 1rem; box-shadow: 0 6px 18px rgba(15, 23, 42, .04); }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card > .d-flex { padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card > .row { margin-left: -.4rem; margin-right: -.4rem; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card > .row > [class*="col-"] { padding-left: .4rem; padding-right: .4rem; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card .form-label { color: #334155; font-weight: 600; margin-bottom: .4rem; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card .form-control { min-height: 38px; border-color: #cbd5e1; border-radius: .5rem; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card h6 { padding-top: .65rem; margin-top: .35rem; border-top: 1px solid #e2e8f0; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card .small.text-muted { line-height: 1.45; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card .mb-3 { margin-bottom: .9rem !important; }
        .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card .btn-block { margin-top: .4rem; min-height: 40px; }
        .certificate-editor-page [data-certificate-panel="preview"] .preview-paper { border-radius: .35rem; }
        .certificate-editor-page [data-certificate-panel="preview"] .preview-paper { margin-top: 1.75rem; }
        @media (min-width: 992px) { .certificate-editor-page [data-certificate-panel="preview"] .col-lg-8 { flex: 0 0 70%; max-width: 70%; } .certificate-editor-page [data-certificate-panel="preview"] .col-lg-4 { flex: 0 0 30%; max-width: 30%; } }
        @media (max-width: 767.98px) { .certificate-editor-page > .d-flex:first-of-type .btn { min-width: 0; } .certificate-editor-page [data-certificate-panel="preview"] .col-lg-8 > .bg-white { padding: 1rem !important; } .certificate-editor-page [data-certificate-panel="preview"] .certificate-settings-card { position: static; } .certificate-editor-page [data-certificate-panel="preview"] #certificatePreviewTemplate { width: 100%; } }
        @media (max-width: 767.98px) { .certificate-editor-page .preview-paper { min-height: 900px; padding: 90px 28px 45px; } .certificate-editor-page .preview-paper .preview-heading { margin-bottom: 48px; padding: 8px 20px; font-size: 1.05rem; } .certificate-editor-page .preview-body { min-height: 430px; font-size: .98rem; line-height: 1.7; } .certificate-editor-page .preview-bottom { flex-direction: column; align-items: stretch; margin-top: 30px; font-size: .8rem; } .certificate-editor-page .preview-reason, .certificate-editor-page .preview-principal { max-width: none; min-width: 0; } }
    </style>

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4" style="gap:1rem">
        <div>
            <div class="d-flex align-items-center flex-wrap" style="gap:.6rem">
                <i class="fas fa-certificate text-primary"></i>
                <h3 class="mb-0 font-weight-bold text-slate-900">{{ $certificate->name }}</h3>
                <span class="badge badge-{{ $certificate->is_active ? 'success' : 'secondary' }}">{{ $certificate->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <p class="text-muted mb-0 mt-1">Preview the certificate first, then manage its details and single template in one workspace.</p>
        </div>
        <div class="d-flex flex-wrap" style="gap:.5rem">
            <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left mr-1"></i> Back to types</a>
            <button type="button" class="btn btn-outline-primary btn-sm" data-certificate-tab="preview"><i class="fas fa-eye mr-1"></i> Preview</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center" role="alert"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-warning d-flex align-items-center" role="alert"><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="font-weight-bold mb-1">Please check the following:</div>
            <ul class="mb-0 pl-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border mb-4">
        <nav class="d-flex flex-wrap px-2" aria-label="Certificate editor sections">
            <button type="button" class="workspace-tab is-active" data-certificate-tab="general"><i class="fas fa-sliders-h mr-1"></i> General settings</button>
            <button type="button" class="workspace-tab" data-certificate-tab="templates"><i class="fas fa-layer-group mr-1"></i> Templates <span class="badge badge-light ml-1">{{ $certificate->templates->count() }}</span></button>
            <button type="button" class="workspace-tab" data-certificate-tab="preview"><i class="fas fa-eye mr-1"></i> Preview</button>
        </nav>
    </div>

    <section data-certificate-panel="general">
        <div class="bg-white rounded-2xl shadow-sm border p-4 p-lg-5">
            <div class="mb-4"><h4 class="text-slate-900 font-weight-bold mb-1">General settings</h4><p class="text-muted mb-0">These settings identify the certificate and control whether it is available for issuing.</p></div>
            <form id="certificateSettingsForm" method="POST" action="{{ route('certificates.update', $certificate) }}">
                @csrf
                <div class="row">
                    <div class="col-lg-6 mb-3"><label class="form-label">Certificate name</label><input type="text" name="name" value="{{ old('name', $certificate->name) }}" class="form-control" required></div>
                    <div class="col-lg-6 mb-3"><label class="form-label">Slug <span class="text-muted font-weight-normal">(URL identifier)</span></label><input type="text" name="slug" value="{{ old('slug', $certificate->slug) }}" class="form-control" required></div>
                    <div class="col-12 mb-3"><label class="form-label">Description</label><textarea name="description" rows="3" class="form-control" placeholder="Explain when this certificate should be used.">{{ old('description', $certificate->description) }}</textarea></div>
                    <div class="col-lg-6 mb-3"><label class="form-label">Default template</label><select name="active_template_id" class="form-control"><option value="">Select default template</option>@foreach($certificate->templates as $template)<option value="{{ $template->id }}" @selected((int) old('active_template_id', $certificate->active_template_id) === $template->id)>{{ $template->name }}</option>@endforeach</select><small class="form-text text-muted">This template is used when issuing this certificate.</small></div>
                    <div class="col-lg-6 mb-3 d-flex align-items-center"><label class="custom-control custom-switch mb-0"><input type="checkbox" name="is_active" value="1" class="custom-control-input" @checked(old('is_active', $certificate->is_active))><span class="custom-control-label">Certificate available for issuing</span></label></div>
                </div>
            </form>
        </div>
    </section>

    <section data-certificate-panel="templates" class="d-none">
        <div class="bg-white rounded-2xl shadow-sm border p-4 p-lg-5 mb-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-3" style="gap:1rem">
                <div><h4 class="text-slate-900 font-weight-bold mb-1">Placeholder library</h4><p class="text-muted mb-0">Click a placeholder to insert it into the template editor you last used.</p></div>
                <div style="min-width:260px"><div class="input-group input-group-sm"><div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-search"></i></span></div><input type="search" id="certificatePlaceholderSearch" class="form-control" placeholder="Search placeholders"></div></div>
            </div>
            <div id="certificatePlaceholderLibrary">
                @foreach($placeholders as $groupName => $groupPlaceholders)
                    <div class="placeholder-group mb-3" data-placeholder-group><div class="small text-uppercase text-muted font-weight-bold mb-2">{{ ucfirst(str_replace('_', ' ', $groupName)) }}</div><div class="d-flex flex-wrap" style="gap:.45rem">
                        @foreach($groupPlaceholders as $placeholder)<button type="button" class="placeholder-chip js-certificate-placeholder" data-token="{{ $placeholder['token'] }}" data-search="{{ strtolower($placeholder['label'].' '.$placeholder['token'].' '.$placeholder['note']) }}" title="{{ $placeholder['note'] }}">{{ $placeholder['label'] }}</button>@endforeach
                    </div></div>
                @endforeach
                <div id="noCertificatePlaceholders" class="text-muted small d-none">No matching placeholders found.</div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3"><div><h4 class="text-slate-900 font-weight-bold mb-1">Templates</h4><p class="text-muted mb-0">Keep variations here and choose one default template for issuing.</p></div><button type="button" class="btn btn-outline-primary btn-sm" data-scroll-to="new-certificate-template"><i class="fas fa-plus mr-1"></i> Add template</button></div>

        @forelse($certificate->templates as $template)
            <div class="template-card card border mb-4 {{ $certificate->active_template_id === $template->id ? 'is-default' : '' }}">
                <div class="card-header bg-white d-flex flex-column flex-lg-row align-items-lg-center justify-content-between" style="gap:.75rem">
                    <div><div class="d-flex align-items-center" style="gap:.5rem"><span class="font-weight-bold text-slate-900">{{ $template->name }}</span>@if($certificate->active_template_id === $template->id)<span class="badge badge-primary">Default template</span>@endif</div><div class="small text-muted mt-1">Edit the wording below, then save this template.</div></div>
                    <div class="d-flex flex-wrap" style="gap:.4rem">
                        @if($certificate->active_template_id !== $template->id)<form action="{{ route('certificates.templates.activate', [$certificate, $template]) }}" method="POST">@csrf<button type="submit" class="btn btn-sm btn-outline-primary"><i class="fas fa-star mr-1"></i> Make default</button></form>@endif
                        @if($certificate->templates->count() > 1)
                            <form action="{{ route('certificates.templates.destroy', [$certificate, $template]) }}" method="POST" class="js-delete-certificate-template" data-template-name="{{ $template->name }}" data-template-default="{{ $certificate->active_template_id === $template->id ? '1' : '0' }}">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt mr-1"></i> Delete</button></form>
                        @else
                            <span class="small text-muted align-self-center" title="Add another template before deleting this one"><i class="fas fa-lock mr-1"></i> Keep one template</span>
                        @endif
                    </div>
                </div>
                <div class="card-body"><form action="{{ route('certificates.templates.update', [$certificate, $template]) }}" method="POST">@csrf
                    <div class="row"><div class="col-lg-8 mb-3"><label class="form-label">Template name</label><input type="text" name="name" value="{{ old('name', $template->name) }}" class="form-control" required></div><div class="col-lg-4 mb-3 d-flex align-items-end"><label class="custom-control custom-switch mb-2"><input type="checkbox" name="is_active" value="1" class="custom-control-input" @checked(old('is_active', $template->is_active))><span class="custom-control-label">Available for use</span></label></div><div class="col-12"><label class="form-label">Template body</label><textarea name="body" id="template_body_{{ $template->id }}" rows="10" class="form-control font-monospace template-body-editor js-certificate-editor" data-template-id="{{ $template->id }}" required>{{ old('body', $template->body) }}</textarea><small class="form-text text-muted">Use the placeholder library above to insert student and school information.</small></div></div>
                </form></div>
            </div>
        @empty
            <div class="bg-white rounded-xl border p-4 text-muted">No templates assigned yet. Add the first template below.</div>
        @endforelse

        <div id="new-certificate-template" class="bg-white rounded-2xl shadow-sm border p-4 p-lg-5"><div class="mb-4"><h4 class="text-slate-900 font-weight-bold mb-1">Add a template</h4><p class="text-muted mb-0">Create a separate variation without changing the existing templates.</p></div><form action="{{ route('certificates.templates.store', $certificate) }}" method="POST">@csrf
            <div class="row"><div class="col-lg-8 mb-3"><label class="form-label">Template name</label><input type="text" name="name" class="form-control" placeholder="e.g. Bengali Transfer Certificate" required></div><div class="col-lg-4 mb-3 d-flex align-items-end"><label class="custom-control custom-switch mb-2"><input type="checkbox" name="is_active" value="1" class="custom-control-input" checked><span class="custom-control-label">Available for use</span></label></div><div class="col-12 mb-3"><label class="form-label">Template body</label><textarea name="body" id="new_template_body" rows="10" class="form-control font-monospace template-body-editor js-certificate-editor" required></textarea></div></div><button type="submit" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Add template</button>
        </form></div>
    </section>

    <section data-certificate-panel="preview" class="d-none"><div class="row"><div class="col-lg-8 mb-4"><div class="bg-white rounded-2xl shadow-sm border p-4 p-lg-5"><div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4" style="gap:1rem"><div><h4 class="text-slate-900 font-weight-bold mb-1">Certificate preview</h4><p class="text-muted mb-0">Drag the title, body, watermark, or footer to position it. Changes are saved with the layout settings.</p></div><div class="d-flex align-items-center" style="gap:.5rem"><select id="certificatePreviewTemplate" class="form-control form-control-sm" style="max-width:210px">@foreach($certificate->templates as $template)<option value="template_body_{{ $template->id }}" @selected($certificate->active_template_id === $template->id)>{{ $template->name }}</option>@endforeach</select><button type="button" class="btn btn-primary btn-sm" id="saveCertificateLayout"><i class="fas fa-save mr-1"></i> Save layout</button><button type="button" class="btn btn-outline-primary btn-sm" id="saveCertificatePreviewText"><i class="fas fa-edit mr-1"></i> Save text</button></div></div><div class="preview-paper rounded-lg" id="certificatePreviewPaper">@if(!empty($setting?->logo))<img class="preview-watermark certificate-draggable" data-certificate-draggable="watermark" src="{{ asset($setting->logo) }}" alt="">@endif<div class="preview-heading certificate-draggable" data-certificate-draggable="title">{{ $certificate->name }}</div><div class="preview-body certificate-draggable" data-certificate-draggable="body" id="certificatePreviewBody" contenteditable="true" spellcheck="true">Select a template to preview it.</div><div class="preview-bottom certificate-draggable" data-certificate-draggable="bottom"><div class="preview-reason"><div class="preview-reason-title">Reason for Leaving School:</div><div>1. Guardian's desire</div><div>2. Transfer of Guardian</div><div>3. Transfer of Residence</div></div><div class="preview-principal"><div class="preview-principal-line"></div><div class="preview-principal-name">{{ $setting?->principal_designation ?: 'Principal' }}</div><div>{{ $setting?->principal_name ? '(' . $setting->principal_name . ')' : '' }}</div><div>{{ $setting?->principal_school_name ?: $setting?->name ?: config('app.name', 'School') }}</div><div>{{ $setting?->principal_phone }}</div></div></div></div></div></div><div class="col-lg-4 mb-4"><div class="certificate-settings-card p-3 p-lg-4 mb-3"><div class="d-flex justify-content-between align-items-start mb-3" style="gap:.75rem"><div><h5 class="font-weight-bold text-slate-900 mb-1">Certificate layout</h5><p class="text-muted small mb-0">Adjust print-safe settings here while you preview.</p></div><i class="fas fa-arrows-alt text-primary mt-1" title="Drag enabled in preview"></i></div><div class="row"><div class="col-6 mb-3"><label class="form-label small">Page width (mm)</label><input form="certificateSettingsForm" type="number" step="0.1" min="50" max="1000" name="layout_settings[page][width]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.page.width', data_get($layout, 'page.width')) }}"></div><div class="col-6 mb-3"><label class="form-label small">Page height (mm)</label><input form="certificateSettingsForm" type="number" step="0.1" min="50" max="1400" name="layout_settings[page][height]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.page.height', data_get($layout, 'page.height')) }}"></div>@foreach(['top' => 'Top', 'right' => 'Right', 'bottom' => 'Bottom', 'left' => 'Left'] as $side => $label)<div class="col-6 mb-3"><label class="form-label small">{{ $label }} margin (mm)</label><input form="certificateSettingsForm" type="number" step="0.1" min="0" max="100" name="layout_settings[margins][{{ $side }}]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.margins.'.$side, data_get($layout, 'margins.'.$side)) }}"></div>@endforeach<div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">Global font</h6><div class="small text-muted">Controls for the certificate title, body, alignment, color, and overall weight.</div></div><div class="col-6 mb-3"><label class="form-label small">Title size</label><input form="certificateSettingsForm" type="number" step="1" min="8" max="72" name="layout_settings[typography][title_size]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.typography.title_size', data_get($layout, 'typography.title_size')) }}"></div><div class="col-6 mb-3"><label class="form-label small">Body size</label><input form="certificateSettingsForm" type="number" step="1" min="8" max="48" name="layout_settings[typography][body_size]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.typography.body_size', data_get($layout, 'typography.body_size')) }}"></div><div class="col-6 mb-3"><label class="form-label small">Font family</label><select form="certificateSettingsForm" name="layout_settings[typography][font_family]" class="form-control form-control-sm js-certificate-layout-input"><option value="Georgia, Times New Roman, serif" @selected(data_get($layout, 'typography.font_family') === 'Georgia, Times New Roman, serif')>Georgia</option><option value="Arial, Helvetica, sans-serif" @selected(data_get($layout, 'typography.font_family') === 'Arial, Helvetica, sans-serif')>Arial</option><option value="Times New Roman, Times, serif" @selected(data_get($layout, 'typography.font_family') === 'Times New Roman, Times, serif')>Times New Roman</option><option value="Verdana, Geneva, sans-serif" @selected(data_get($layout, 'typography.font_family') === 'Verdana, Geneva, sans-serif')>Verdana</option><option value="DejaVu Serif, serif" @selected(data_get($layout, 'typography.font_family') === 'DejaVu Serif, serif')>DejaVu Serif</option><option value="DejaVu Sans, sans-serif" @selected(data_get($layout, 'typography.font_family') === 'DejaVu Sans, sans-serif')>DejaVu Sans</option></select></div><div class="col-6 mb-3"><label class="form-label small">Text align</label><select form="certificateSettingsForm" name="layout_settings[typography][text_align]" class="form-control form-control-sm js-certificate-layout-input"><option value="left" @selected(data_get($layout, 'typography.text_align') === 'left')>Left</option><option value="center" @selected(data_get($layout, 'typography.text_align') === 'center')>Center</option><option value="right" @selected(data_get($layout, 'typography.text_align') === 'right')>Right</option><option value="justify" @selected(data_get($layout, 'typography.text_align') === 'justify')>Justify</option></select></div><div class="col-6 mb-3"><label class="form-label small">Font color</label><input form="certificateSettingsForm" type="color" name="layout_settings[typography][font_color]" class="form-control form-control-sm p-1 js-certificate-layout-input" value="{{ data_get($layout, 'typography.font_color', '#111827') }}"></div><div class="col-6 mb-3"><label class="form-label small">Whole font weight</label><select form="certificateSettingsForm" name="layout_settings[typography][font_weight]" class="form-control form-control-sm js-certificate-layout-input">@foreach(range(300, 900, 100) as $weight)<option value="{{ $weight }}" @selected((int) data_get($layout, 'typography.font_weight', 400) === $weight)>{{ $weight }}</option>@endforeach</select></div><div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">Placeholder font</h6><div class="small text-muted">Controls for substituted student and school values in the certificate text.</div></div><div class="col-6 mb-3"><label class="form-label small">Placeholder font</label><select form="certificateSettingsForm" name="layout_settings[typography][placeholder_font_family]" class="form-control form-control-sm js-certificate-layout-input"><option value="Georgia, Times New Roman, serif" @selected(data_get($layout, 'typography.placeholder_font_family') === 'Georgia, Times New Roman, serif')>Georgia</option><option value="Arial, Helvetica, sans-serif" @selected(data_get($layout, 'typography.placeholder_font_family') === 'Arial, Helvetica, sans-serif')>Arial</option><option value="Times New Roman, Times, serif" @selected(data_get($layout, 'typography.placeholder_font_family') === 'Times New Roman, Times, serif')>Times New Roman</option><option value="Verdana, Geneva, sans-serif" @selected(data_get($layout, 'typography.placeholder_font_family') === 'Verdana, Geneva, sans-serif')>Verdana</option><option value="DejaVu Serif, serif" @selected(data_get($layout, 'typography.placeholder_font_family') === 'DejaVu Serif, serif')>DejaVu Serif</option><option value="DejaVu Sans, sans-serif" @selected(data_get($layout, 'typography.placeholder_font_family') === 'DejaVu Sans, sans-serif')>DejaVu Sans</option></select></div><div class="col-6 mb-3"><label class="form-label small">Placeholder size</label><input form="certificateSettingsForm" type="number" step="1" min="8" max="48" name="layout_settings[typography][placeholder_font_size]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.typography.placeholder_font_size', data_get($layout, 'typography.placeholder_font_size')) }}"></div><div class="col-6 mb-3"><label class="form-label small">Placeholder weight</label><select form="certificateSettingsForm" name="layout_settings[typography][placeholder_font_weight]" class="form-control form-control-sm js-certificate-layout-input">@foreach(range(300, 900, 100) as $weight)<option value="{{ $weight }}" @selected((int) data_get($layout, 'typography.placeholder_font_weight', 700) === $weight)>{{ $weight }}</option>@endforeach</select></div><div class="col-6 mb-3"><label class="form-label small">Placeholder color</label><input form="certificateSettingsForm" type="color" name="layout_settings[typography][placeholder_color]" class="form-control form-control-sm p-1 js-certificate-layout-input" value="{{ data_get($layout, 'typography.placeholder_color', '#b45309') }}"></div><div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">Reason font</h6><div class="small text-muted">Typography for the reason block.</div></div><div class="col-6 mb-3"><label class="form-label small">Reason family</label><select form="certificateSettingsForm" name="layout_settings[footer][reason][font_family]" class="form-control form-control-sm js-certificate-layout-input"><option value="Arial, Helvetica, sans-serif" @selected(data_get($layout, 'footer.reason.font_family') === 'Arial, Helvetica, sans-serif')>Arial</option><option value="Georgia, Times New Roman, serif" @selected(data_get($layout, 'footer.reason.font_family') === 'Georgia, Times New Roman, serif')>Georgia</option><option value="Times New Roman, Times, serif" @selected(data_get($layout, 'footer.reason.font_family') === 'Times New Roman, Times, serif')>Times New Roman</option><option value="Verdana, Geneva, sans-serif" @selected(data_get($layout, 'footer.reason.font_family') === 'Verdana, Geneva, sans-serif')>Verdana</option></select></div><div class="col-6 mb-3"><label class="form-label small">Reason size</label><input form="certificateSettingsForm" type="number" step="1" min="8" max="48" name="layout_settings[footer][reason][font_size]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.footer.reason.font_size', data_get($layout, 'footer.reason.font_size', 15)) }}"></div><div class="col-6 mb-3"><label class="form-label small">Reason weight</label><select form="certificateSettingsForm" name="layout_settings[footer][reason][font_weight]" class="form-control form-control-sm js-certificate-layout-input">@foreach(range(300, 900, 100) as $weight)<option value="{{ $weight }}" @selected((int) data_get($layout, 'footer.reason.font_weight', 400) === $weight)>{{ $weight }}</option>@endforeach</select></div><div class="col-6 mb-3"><label class="form-label small">Reason color</label><input form="certificateSettingsForm" type="color" name="layout_settings[footer][reason][color]" class="form-control form-control-sm p-1 js-certificate-layout-input" value="{{ data_get($layout, 'footer.reason.color', '#111827') }}"></div><div class="col-6 mb-3"><label class="form-label small">Reason align</label><select form="certificateSettingsForm" name="layout_settings[footer][reason][text_align]" class="form-control form-control-sm js-certificate-layout-input"><option value="left" @selected(data_get($layout, 'footer.reason.text_align', 'left') === 'left')>Left</option><option value="center" @selected(data_get($layout, 'footer.reason.text_align', 'left') === 'center')>Center</option><option value="right" @selected(data_get($layout, 'footer.reason.text_align', 'left') === 'right')>Right</option></select></div><div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">Principal font</h6><div class="small text-muted">Typography for the principal block.</div></div><div class="col-6 mb-3"><label class="form-label small">Principal family</label><select form="certificateSettingsForm" name="layout_settings[footer][principal][font_family]" class="form-control form-control-sm js-certificate-layout-input"><option value="Arial, Helvetica, sans-serif" @selected(data_get($layout, 'footer.principal.font_family') === 'Arial, Helvetica, sans-serif')>Arial</option><option value="Georgia, Times New Roman, serif" @selected(data_get($layout, 'footer.principal.font_family') === 'Georgia, Times New Roman, serif')>Georgia</option><option value="Times New Roman, Times, serif" @selected(data_get($layout, 'footer.principal.font_family') === 'Times New Roman, Times, serif')>Times New Roman</option><option value="Verdana, Geneva, sans-serif" @selected(data_get($layout, 'footer.principal.font_family') === 'Verdana, Geneva, sans-serif')>Verdana</option></select></div><div class="col-6 mb-3"><label class="form-label small">Principal size</label><input form="certificateSettingsForm" type="number" step="1" min="8" max="48" name="layout_settings[footer][principal][font_size]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.footer.principal.font_size', data_get($layout, 'footer.principal.font_size', 16)) }}"></div><div class="col-6 mb-3"><label class="form-label small">Principal weight</label><select form="certificateSettingsForm" name="layout_settings[footer][principal][font_weight]" class="form-control form-control-sm js-certificate-layout-input">@foreach(range(300, 900, 100) as $weight)<option value="{{ $weight }}" @selected((int) data_get($layout, 'footer.principal.font_weight', 400) === $weight)>{{ $weight }}</option>@endforeach</select></div><div class="col-6 mb-3"><label class="form-label small">Principal color</label><input form="certificateSettingsForm" type="color" name="layout_settings[footer][principal][color]" class="form-control form-control-sm p-1 js-certificate-layout-input" value="{{ data_get($layout, 'footer.principal.color', '#111827') }}"></div><div class="col-6 mb-3"><label class="form-label small">Principal align</label><select form="certificateSettingsForm" name="layout_settings[footer][principal][text_align]" class="form-control form-control-sm js-certificate-layout-input"><option value="left" @selected(data_get($layout, 'footer.principal.text_align', 'center') === 'left')>Left</option><option value="center" @selected(data_get($layout, 'footer.principal.text_align', 'center') === 'center')>Center</option><option value="right" @selected(data_get($layout, 'footer.principal.text_align', 'center') === 'right')>Right</option></select></div><div class="col-6 mb-3"><label class="form-label small">Line height</label><input form="certificateSettingsForm" type="number" step="0.05" min="1" max="3" name="layout_settings[typography][line_height]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.typography.line_height', data_get($layout, 'typography.line_height')) }}"></div><div class="col-6 mb-3"><label class="form-label small">Watermark opacity</label><input form="certificateSettingsForm" type="number" step="0.01" min="0" max="1" name="layout_settings[watermark][opacity]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.watermark.opacity', data_get($layout, 'watermark.opacity')) }}"></div><div class="col-6 mb-3"><label class="form-label small">Watermark size (%)</label><input form="certificateSettingsForm" type="number" step="1" min="10" max="100" name="layout_settings[watermark][size]" class="form-control form-control-sm js-certificate-layout-input" value="{{ old('layout_settings.watermark.size', data_get($layout, 'watermark.size')) }}"></div></div>@foreach(['title', 'body', 'bottom', 'watermark'] as $positionKey)<input form="certificateSettingsForm" type="hidden" name="layout_settings[positions][{{ $positionKey }}][x]" data-certificate-position="{{ $positionKey }}" data-axis="x" value="{{ data_get($layout, 'positions.'.$positionKey.'.x') }}"><input form="certificateSettingsForm" type="hidden" name="layout_settings[positions][{{ $positionKey }}][y]" data-certificate-position="{{ $positionKey }}" data-axis="y" value="{{ data_get($layout, 'positions.'.$positionKey.'.y') }}">@endforeach</div><button type="button" class="btn btn-primary btn-block btn-sm mb-3" id="saveCertificateLayoutSidebar"><i class="fas fa-save mr-1"></i> Save layout settings</button><div class="bg-white rounded-2xl shadow-sm border p-4"><h5 class="font-weight-bold text-slate-900 mb-3">Drag guide</h5><div class="small text-muted mb-3">Drag highlighted blocks directly on the page. Positions are measured in millimetres and are included in browser and PDF output after saving.</div><dl class="row small mb-0"><dt class="col-7">Paper</dt><dd class="col-5 text-right">A4 portrait</dd><dt class="col-7">Movable blocks</dt><dd class="col-5 text-right">4</dd><dt class="col-7">Watermark</dt><dd class="col-5 text-right">Logo only</dd></dl></div></div></div></section>
</div>

<script>
    let activeCertificateTemplate = 'new_template_body';
    const certificatePlaceholder = (name) => String.fromCharCode(123, 123) + ' ' + name + ' ' + String.fromCharCode(125, 125);
    let certificateSampleValues = {
        [certificatePlaceholder('student.full_name_en')]: 'Ayesha Rahman', [certificatePlaceholder('student.full_name_bn')]: 'আয়েশা রহমান', [certificatePlaceholder('student.student_cid')]: 'STD-2026-0012', [certificatePlaceholder('student.father_name')]: 'Abdul Rahman', [certificatePlaceholder('student.mother_name')]: 'Nasrin Begum', [certificatePlaceholder('student.date_of_birth')]: '15 March 2012', [certificatePlaceholder('student.birth_certificate_number')]: '20124567890123456', [certificatePlaceholder('student.present_address')]: '12 Green Road, Dhaka', [certificatePlaceholder('student.present_division.name')]: 'Dhaka', [certificatePlaceholder('student.present_district.name')]: 'Dhaka', [certificatePlaceholder('student.present_police_station.name')]: 'Dhanmondi', [certificatePlaceholder('student.present_post_office.name')]: 'Dhanmondi', [certificatePlaceholder('student.permanent_address')]: 'Village Road, Cumilla', [certificatePlaceholder('academicInfo.schoolClass.name_en')]: 'Class Eight', [certificatePlaceholder('academicInfo.section.name_en')]: 'A', [certificatePlaceholder('academicInfo.academicSession.name_en')]: '2026', [certificatePlaceholder('academicInfo.roll')]: '12', [certificatePlaceholder('academicInfo.checkout_date')]: '30 September 2026', [certificatePlaceholder('academicInfo.academic_status')]: 'Transferred', [certificatePlaceholder('academicInfo.notes')]: 'No additional notes', [certificatePlaceholder('student.previous_school')]: 'Green Valley School', [certificatePlaceholder('student.previous_class_appeared')]: 'Class Seven', [certificatePlaceholder('student.tc_number')]: 'TC-2026-0012', [certificatePlaceholder('issueYear')]: '2026', [certificatePlaceholder('issueDate')]: '04 October 2026', [certificatePlaceholder('setting.name')]: @json(config('app.name', 'School')), [certificatePlaceholder('setting.address')]: 'School campus address'
    };
    function setActiveCertificateTemplate(id) { activeCertificateTemplate = id; }
    function insertCertificatePlaceholder(token) { const textarea=document.getElementById(activeCertificateTemplate); if(!textarea)return; textarea.focus(); const start=textarea.selectionStart??textarea.value.length,end=textarea.selectionEnd??textarea.value.length,before=textarea.value.substring(0,start),after=textarea.value.substring(end),spaceBefore=before.length&&!/\s$/.test(before)&&!/^\s/.test(token),spaceAfter=after.length&&!/^\s/.test(after)&&!/\s$/.test(token),insert=`${spaceBefore?' ':''}${token}${spaceAfter?' ':''}`; textarea.value=before+insert+after; const cursor=before.length+insert.length; textarea.setSelectionRange(cursor,cursor); textarea.dispatchEvent(new Event('input',{bubbles:true})); }
    function updateCertificatePreview() { const selector=document.getElementById('certificatePreviewTemplate'),preview=document.getElementById('certificatePreviewBody'); if(!selector||!preview)return; const textarea=document.getElementById(selector.value); if(!textarea){preview.textContent='Add a template to preview it.';return;} let html=textarea.value||'This template has no content yet.'; html=html.replace(/[&<>"']/g,character=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[character])); Object.keys(certificateSampleValues).forEach(token=>{html=html.split(token).join('<span class="preview-placeholder">'+certificateSampleValues[token]+'</span>');}); html=html.replace(/\{\{[^}]+\}\}/g,token=>'<span class="preview-placeholder">'+token+'</span>'); preview.innerHTML=html.replace(/\n/g,'<br>'); applyCertificateLayout(); }
    function certificateLayoutInput(path) { return document.querySelector('[name="layout_settings['+path.join('][')+']"]'); }
    function certificateLayoutNumber(path, fallback) { const input=certificateLayoutInput(path), value=parseFloat(input?.value ?? ''); return Number.isFinite(value) ? value : fallback; }
    function certificatePosition(key, axis) { const input=document.querySelector('[data-certificate-position="'+key+'"][data-axis="'+axis+'"]'), value=parseFloat(input?.value ?? ''); return Number.isFinite(value) ? value : 0; }
    function applyCertificateLayout() { const paper=document.getElementById('certificatePreviewPaper'); if(!paper)return; const width=certificateLayoutNumber(['page','width'],210),height=certificateLayoutNumber(['page','height'],297),top=certificateLayoutNumber(['margins','top'],28),right=certificateLayoutNumber(['margins','right'],16),bottom=certificateLayoutNumber(['margins','bottom'],18),left=certificateLayoutNumber(['margins','left'],16),titleSize=certificateLayoutNumber(['typography','title_size'],18),bodySize=certificateLayoutNumber(['typography','body_size'],17),lineHeight=certificateLayoutNumber(['typography','line_height'],2.05),placeholderFontSize=certificateLayoutNumber(['typography','placeholder_font_size'],17),watermarkOpacity=certificateLayoutNumber(['watermark','opacity'],.12),watermarkSize=certificateLayoutNumber(['watermark','size'],55),fontFamily=certificateLayoutInput(['typography','font_family'])?.value||'Georgia, Times New Roman, serif',fontColor=certificateLayoutInput(['typography','font_color'])?.value||'#111827',fontWeight=certificateLayoutNumber(['typography','font_weight'],400),placeholderFontFamily=certificateLayoutInput(['typography','placeholder_font_family'])?.value||fontFamily,placeholderFontWeight=certificateLayoutNumber(['typography','placeholder_font_weight'],700),placeholderColor=certificateLayoutInput(['typography','placeholder_color'])?.value||'#b45309',textAlign=certificateLayoutInput(['typography','text_align'])?.value||'justify',reasonFontFamily=certificateLayoutInput(['footer','reason','font_family'])?.value||'Arial, Helvetica, sans-serif',reasonFontSize=certificateLayoutNumber(['footer','reason','font_size'],15),reasonFontWeight=certificateLayoutNumber(['footer','reason','font_weight'],400),reasonColor=certificateLayoutInput(['footer','reason','color'])?.value||'#111827',reasonAlign=certificateLayoutInput(['footer','reason','text_align'])?.value||'left',principalFontFamily=certificateLayoutInput(['footer','principal','font_family'])?.value||'Arial, Helvetica, sans-serif',principalFontSize=certificateLayoutNumber(['footer','principal','font_size'],16),principalFontWeight=certificateLayoutNumber(['footer','principal','font_weight'],400),principalColor=certificateLayoutInput(['footer','principal','color'])?.value||'#111827',principalAlign=certificateLayoutInput(['footer','principal','text_align'])?.value||'center'; paper.style.width='min(100%, '+width+'mm)'; paper.style.height=height+'mm'; paper.style.minHeight='0'; paper.style.padding=top+'mm '+right+'mm '+bottom+'mm '+left+'mm'; paper.style.color=fontColor;paper.style.fontWeight=fontWeight; const title=paper.querySelector('[data-certificate-draggable="title"]'),body=paper.querySelector('[data-certificate-draggable="body"]'),bottomBlock=paper.querySelector('[data-certificate-draggable="bottom"]'),watermark=paper.querySelector('[data-certificate-draggable="watermark"]'); if(title){title.style.fontSize=titleSize+'px';title.style.fontFamily=fontFamily;title.style.color=fontColor;title.style.fontWeight=fontWeight;title.style.transform='translate('+certificatePosition('title','x')+'mm, '+certificatePosition('title','y')+'mm)';} if(body){body.style.fontSize=bodySize+'px';body.style.fontFamily=fontFamily;body.style.color=fontColor;body.style.fontWeight=fontWeight;body.style.lineHeight=lineHeight;body.style.textAlign=textAlign;body.style.transform='translate('+certificatePosition('body','x')+'mm, '+certificatePosition('body','y')+'mm)';} if(bottomBlock){const reason=bottomBlock.querySelector('.preview-reason'),principal=bottomBlock.querySelector('.preview-principal'); if(reason){reason.style.fontFamily=reasonFontFamily;reason.style.fontSize=reasonFontSize+'px';reason.style.fontWeight=reasonFontWeight;reason.style.color=reasonColor;reason.style.textAlign=reasonAlign;reason.querySelector('.preview-reason-title')?.style.setProperty('font-weight', reasonFontWeight); reason.querySelector('.font-bold')?.style.setProperty('font-weight', reasonFontWeight);} if(principal){principal.style.fontFamily=principalFontFamily;principal.style.fontSize=principalFontSize+'px';principal.style.fontWeight=principalFontWeight;principal.style.color=principalColor;principal.style.textAlign=principalAlign;principal.querySelector('.preview-principal-name')?.style.setProperty('font-size', principalFontSize+'px');principal.querySelector('.preview-principal-name')?.style.setProperty('font-weight', principalFontWeight); principal.querySelectorAll('div:not(.preview-principal-line)').forEach((element)=>{element.style.fontFamily=principalFontFamily;element.style.fontSize=principalFontSize+'px';element.style.fontWeight=principalFontWeight;element.style.color=principalColor;element.style.textAlign=principalAlign;});} bottomBlock.style.color=fontColor;bottomBlock.style.transform='translate('+certificatePosition('bottom','x')+'mm, '+certificatePosition('bottom','y')+'mm)';} paper.querySelectorAll('.preview-placeholder').forEach(element=>{element.style.color=placeholderColor;element.style.fontFamily=placeholderFontFamily;element.style.fontSize=placeholderFontSize+'px';element.style.fontWeight=placeholderFontWeight;}); if(watermark){watermark.style.opacity=watermarkOpacity;watermark.style.width=watermarkSize+'%';watermark.style.top='calc(42% + '+certificatePosition('watermark','y')+'mm)';watermark.style.left='calc(50% + '+certificatePosition('watermark','x')+'mm)';} }
    document.addEventListener('DOMContentLoaded',function(){ const tabs=document.querySelectorAll('[data-certificate-tab]'),panels=document.querySelectorAll('[data-certificate-panel]'); function showCertificateTab(name){tabs.forEach(tab=>tab.classList.toggle('is-active',tab.dataset.certificateTab===name));panels.forEach(panel=>panel.classList.toggle('d-none',panel.dataset.certificatePanel!==name));if(name==='preview')updateCertificatePreview();window.history.replaceState(null,'','#'+name);} tabs.forEach(tab=>tab.addEventListener('click',()=>showCertificateTab(tab.dataset.certificateTab))); document.querySelectorAll('.js-certificate-editor').forEach(editor=>{editor.addEventListener('focus',()=>setActiveCertificateTemplate(editor.id));editor.addEventListener('input',updateCertificatePreview);}); document.querySelectorAll('.js-certificate-layout-input').forEach(input=>input.addEventListener('input',applyCertificateLayout)); document.querySelectorAll('.js-certificate-placeholder').forEach(button=>button.addEventListener('click',()=>insertCertificatePlaceholder(button.dataset.token))); const search=document.getElementById('certificatePlaceholderSearch'); search?.addEventListener('input',function(){const query=search.value.trim().toLowerCase();let visible=0;document.querySelectorAll('.js-certificate-placeholder').forEach(button=>{const match=!query||button.dataset.search.includes(query);button.classList.toggle('d-none',!match);if(match)visible++;});document.querySelectorAll('[data-placeholder-group]').forEach(group=>group.classList.toggle('d-none',!group.querySelector('.js-certificate-placeholder:not(.d-none)')));document.getElementById('noCertificatePlaceholders')?.classList.toggle('d-none',visible>0);}); document.querySelectorAll('[data-scroll-to]').forEach(button=>button.addEventListener('click',()=>document.getElementById(button.dataset.scrollTo)?.scrollIntoView({behavior:'smooth',block:'start'}))); document.querySelectorAll('.js-preview-template').forEach(button=>button.addEventListener('click',function(){const selector=document.getElementById('certificatePreviewTemplate');if(selector)selector.value=button.dataset.target;showCertificateTab('preview');})); document.getElementById('certificatePreviewTemplate')?.addEventListener('change',updateCertificatePreview); const saveLayout=()=>document.getElementById('certificateSettingsForm')?.requestSubmit(); document.getElementById('saveCertificateLayout')?.addEventListener('click',saveLayout); document.getElementById('saveCertificateLayoutSidebar')?.addEventListener('click',saveLayout); const paper=document.getElementById('certificatePreviewPaper'); let dragState=null; paper?.addEventListener('pointerdown',function(event){const element=event.target.closest('[data-certificate-draggable]');if(!element||event.button!==0)return;event.preventDefault();const rect=paper.getBoundingClientRect();dragState={element,key:element.dataset.certificateDraggable,lastX:event.clientX,lastY:event.clientY,rect,moved:false};element.classList.add('is-dragging');element.setPointerCapture?.(event.pointerId);}); paper?.addEventListener('pointermove',function(event){if(!dragState)return;event.preventDefault();const mmPerPixel=certificateLayoutNumber(['page','width'],210)/Math.max(dragState.rect.width,1),x=event.clientX-dragState.lastX,y=event.clientY-dragState.lastY;const xInput=document.querySelector('[data-certificate-position="'+dragState.key+'"][data-axis="x"]'),yInput=document.querySelector('[data-certificate-position="'+dragState.key+'"][data-axis="y"]');if(xInput)xInput.value=(certificatePosition(dragState.key,'x')+x*mmPerPixel).toFixed(2);if(yInput)yInput.value=(certificatePosition(dragState.key,'y')+y*mmPerPixel).toFixed(2);if(Math.abs(x)>0||Math.abs(y)>0)dragState.moved=true;dragState.lastX=event.clientX;dragState.lastY=event.clientY;applyCertificateLayout();}); function finishCertificateDrag(event){if(!dragState)return;dragState.element.classList.remove('is-dragging');dragState.element.releasePointerCapture?.(event.pointerId);dragState=null;} paper?.addEventListener('pointerup',finishCertificateDrag);paper?.addEventListener('pointercancel',finishCertificateDrag); const initialTab=window.location.hash.replace('#','');showCertificateTab(['general','templates','preview'].includes(initialTab)?initialTab:'general'); });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const settingsCard = document.querySelector('[data-certificate-panel="preview"] .certificate-settings-card');
        if (!settingsCard || settingsCard.querySelector('[data-certificate-title-settings]')) return;

        const titleSettings = document.createElement('div');
        titleSettings.setAttribute('data-certificate-title-settings', '');
        titleSettings.className = 'row mt-2';
        titleSettings.innerHTML = `
            <div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">Title style</h6><div class="small text-muted">Customize the title box, typography, border, colors, spacing, and alignment.</div></div>
            <div class="col-6 mb-3"><label class="form-label small">Title font</label><select form="certificateSettingsForm" name="layout_settings[typography][title_font_family]" class="form-control form-control-sm js-certificate-title-input"><option value="Arial, Helvetica, sans-serif" @selected(data_get($layout, 'typography.title_font_family', 'Arial, Helvetica, sans-serif') === 'Arial, Helvetica, sans-serif')>Arial</option><option value="Georgia, Times New Roman, serif" @selected(data_get($layout, 'typography.title_font_family') === 'Georgia, Times New Roman, serif')>Georgia</option><option value="Times New Roman, Times, serif" @selected(data_get($layout, 'typography.title_font_family') === 'Times New Roman, Times, serif')>Times New Roman</option><option value="Verdana, Geneva, sans-serif" @selected(data_get($layout, 'typography.title_font_family') === 'Verdana, Geneva, sans-serif')>Verdana</option><option value="DejaVu Serif, serif" @selected(data_get($layout, 'typography.title_font_family') === 'DejaVu Serif, serif')>DejaVu Serif</option><option value="DejaVu Sans, sans-serif" @selected(data_get($layout, 'typography.title_font_family') === 'DejaVu Sans, sans-serif')>DejaVu Sans</option></select></div>
            <div class="col-6 mb-3"><label class="form-label small">Title size</label><input form="certificateSettingsForm" type="number" min="8" max="96" step="1" name="layout_settings[typography][title_size]" class="form-control form-control-sm js-certificate-title-input" value="{{ data_get($layout, 'typography.title_size', 18) }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Title weight</label><select form="certificateSettingsForm" name="layout_settings[typography][title_font_weight]" class="form-control form-control-sm js-certificate-title-input">@foreach(range(300, 900, 100) as $weight)<option value="{{ $weight }}" @selected((int) data_get($layout, 'typography.title_font_weight', 500) === $weight)>{{ $weight }}</option>@endforeach</select></div>
            <div class="col-6 mb-3"><label class="form-label small">Title color</label><input form="certificateSettingsForm" type="color" name="layout_settings[typography][title_color]" class="form-control form-control-sm p-1 js-certificate-title-input" value="{{ data_get($layout, 'typography.title_color', '#111827') }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Background color</label><input form="certificateSettingsForm" type="color" name="layout_settings[typography][title_background]" class="form-control form-control-sm p-1 js-certificate-title-input" value="{{ data_get($layout, 'typography.title_background', '#eef2e3') }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Border color</label><input form="certificateSettingsForm" type="color" name="layout_settings[typography][title_border_color]" class="form-control form-control-sm p-1 js-certificate-title-input" value="{{ data_get($layout, 'typography.title_border_color', '#4b5563') }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Border width (px)</label><input form="certificateSettingsForm" type="number" min="0" max="10" step="0.5" name="layout_settings[typography][title_border_width]" class="form-control form-control-sm js-certificate-title-input" value="{{ data_get($layout, 'typography.title_border_width', 1) }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Border style</label><select form="certificateSettingsForm" name="layout_settings[typography][title_border_style]" class="form-control form-control-sm js-certificate-title-input"><option value="none" @selected(data_get($layout, 'typography.title_border_style', 'solid') === 'none')>None</option><option value="solid" @selected(data_get($layout, 'typography.title_border_style', 'solid') === 'solid')>Solid</option><option value="dashed" @selected(data_get($layout, 'typography.title_border_style') === 'dashed')>Dashed</option><option value="dotted" @selected(data_get($layout, 'typography.title_border_style') === 'dotted')>Dotted</option><option value="double" @selected(data_get($layout, 'typography.title_border_style') === 'double')>Double</option></select></div>
            <div class="col-6 mb-3"><label class="form-label small">Text align</label><select form="certificateSettingsForm" name="layout_settings[typography][title_text_align]" class="form-control form-control-sm js-certificate-title-input"><option value="left" @selected(data_get($layout, 'typography.title_text_align') === 'left')>Left</option><option value="center" @selected(data_get($layout, 'typography.title_text_align', 'center') === 'center')>Center</option><option value="right" @selected(data_get($layout, 'typography.title_text_align') === 'right')>Right</option></select></div>
            <div class="col-6 mb-3"><label class="form-label small">Letter spacing (px)</label><input form="certificateSettingsForm" type="number" min="-5" max="20" step="0.01" name="layout_settings[typography][title_letter_spacing]" class="form-control form-control-sm js-certificate-title-input" value="{{ data_get($layout, 'typography.title_letter_spacing', 0.01) }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Text transform</label><select form="certificateSettingsForm" name="layout_settings[typography][title_text_transform]" class="form-control form-control-sm js-certificate-title-input"><option value="none" @selected(data_get($layout, 'typography.title_text_transform') === 'none')>None</option><option value="uppercase" @selected(data_get($layout, 'typography.title_text_transform', 'uppercase') === 'uppercase')>Uppercase</option><option value="lowercase" @selected(data_get($layout, 'typography.title_text_transform') === 'lowercase')>Lowercase</option><option value="capitalize" @selected(data_get($layout, 'typography.title_text_transform') === 'capitalize')>Capitalize</option></select></div>
            <div class="col-6 mb-3"><label class="form-label small">Padding top (px)</label><input form="certificateSettingsForm" type="number" min="0" max="100" step="1" name="layout_settings[typography][title_padding_top]" class="form-control form-control-sm js-certificate-title-input" value="{{ data_get($layout, 'typography.title_padding_top', 9) }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Padding right (px)</label><input form="certificateSettingsForm" type="number" min="0" max="100" step="1" name="layout_settings[typography][title_padding_right]" class="form-control form-control-sm js-certificate-title-input" value="{{ data_get($layout, 'typography.title_padding_right', 42) }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Padding bottom (px)</label><input form="certificateSettingsForm" type="number" min="0" max="100" step="1" name="layout_settings[typography][title_padding_bottom]" class="form-control form-control-sm js-certificate-title-input" value="{{ data_get($layout, 'typography.title_padding_bottom', 9) }}"></div>
            <div class="col-6 mb-3"><label class="form-label small">Padding left (px)</label><input form="certificateSettingsForm" type="number" min="0" max="100" step="1" name="layout_settings[typography][title_padding_left]" class="form-control form-control-sm js-certificate-title-input" value="{{ data_get($layout, 'typography.title_padding_left', 42) }}"></div>`;

        settingsCard.appendChild(titleSettings);
        const legacyTitleSize = Array.from(settingsCard.querySelectorAll('[name="layout_settings[typography][title_size]"]')).find((element) => !element.closest('[data-certificate-title-settings]'));
        if (legacyTitleSize) {
            legacyTitleSize.closest('.col-6')?.classList.add('d-none');
            legacyTitleSize.disabled = true;
        }
        const input = (name) => titleSettings.querySelector('[name="layout_settings[typography][' + name + ']"]');
        const number = (name, fallback) => { const value = parseFloat(input(name)?.value ?? ''); return Number.isFinite(value) ? value : fallback; };
        const applyTitleSettings = () => {
            const title = document.querySelector('#certificatePreviewPaper [data-certificate-draggable="title"]');
            if (!title) return;
            title.style.fontFamily = input('title_font_family')?.value || 'Arial, Helvetica, sans-serif';
            title.style.fontSize = number('title_size', 18) + 'px';
            title.style.fontWeight = number('title_font_weight', 500);
            title.style.color = input('title_color')?.value || '#111827';
            title.style.backgroundColor = input('title_background')?.value || '#eef2e3';
            title.style.borderColor = input('title_border_color')?.value || '#4b5563';
            title.style.borderWidth = number('title_border_width', 1) + 'px';
            title.style.borderStyle = input('title_border_style')?.value || 'solid';
            title.style.padding = number('title_padding_top', 9) + 'px ' + number('title_padding_right', 42) + 'px ' + number('title_padding_bottom', 9) + 'px ' + number('title_padding_left', 42) + 'px';
            title.style.textAlign = input('title_text_align')?.value || 'center';
            title.style.letterSpacing = number('title_letter_spacing', 0.01) + 'px';
            title.style.textTransform = input('title_text_transform')?.value || 'uppercase';
        };
        titleSettings.querySelectorAll('.js-certificate-title-input').forEach((element) => { element.addEventListener('input', applyTitleSettings); element.addEventListener('change', applyTitleSettings); });
        applyTitleSettings();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const preview = document.getElementById('certificatePreviewBody');
        let previewEdited = false;
        const syncPreviewText = function () {
            const selector = document.getElementById('certificatePreviewTemplate');
            const textarea = selector ? document.getElementById(selector.value) : null;
            if (!preview || !textarea || !previewEdited) return;

            const clone = preview.cloneNode(true);
            clone.querySelectorAll('.preview-placeholder').forEach((element) => {
                element.replaceWith(document.createTextNode(element.textContent || ''));
            });
            let text = clone.innerText.replace(/\u00a0/g, ' ').replace(/\r\n?/g, '\n');
            Object.keys(certificateSampleValues).forEach((token) => {
                const sample = String(certificateSampleValues[token]);
                if (sample) text = text.split(sample).join(token);
            });
            textarea.value = text;
            previewEdited = false;
        };
        window.syncCertificatePreviewText = syncPreviewText;

        preview?.addEventListener('input', () => {
            previewEdited = true;
            syncPreviewText();
        });
        document.querySelectorAll('.js-certificate-editor').forEach((editor) => {
            editor.addEventListener('input', () => { previewEdited = false; });
        });
        document.getElementById('saveCertificatePreviewText')?.addEventListener('click', function () {
            syncPreviewText();
            const selector = document.getElementById('certificatePreviewTemplate');
            const textarea = selector ? document.getElementById(selector.value) : null;
            textarea?.closest('form')?.requestSubmit();
        });
    });
</script>
<style>
    .certificate-editor-page .preview-principal,
    .certificate-editor-page .preview-principal > div:not(.preview-principal-line) {
        text-align: center !important;
    }
    .certificate-editor-page .certificate-enhancement-status {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        color: #64748b;
        font-size: .8rem;
        font-weight: 600;
    }
    .certificate-editor-page .certificate-enhancement-status.is-dirty { color: #b45309; }
    .certificate-editor-page .certificate-enhancement-status.is-saved { color: #047857; }
    .certificate-editor-page .certificate-settings-card .certificate-settings-section {
        margin: .65rem 0;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        background: #fff;
        overflow: hidden;
    }
    .certificate-editor-page .certificate-settings-section > summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        cursor: pointer;
        list-style: none;
        padding: .85rem .95rem;
        color: #1e293b;
        font-weight: 700;
    }
    .certificate-editor-page .certificate-settings-section > summary::-webkit-details-marker { display: none; }
    .certificate-editor-page .certificate-settings-section > summary::after { content: '\f078'; font-family: 'Font Awesome 5 Free'; font-weight: 900; color: #64748b; font-size: .72rem; }
    .certificate-editor-page .certificate-settings-section[open] > summary::after { content: '\f077'; }
    .certificate-editor-page .certificate-settings-section > summary small { display: block; margin-top: .2rem; color: #64748b; font-weight: 400; line-height: 1.4; }
    .certificate-editor-page .certificate-settings-section-body { padding: .25rem .95rem .55rem; }
    .certificate-editor-page .certificate-settings-presets,
    .certificate-editor-page .certificate-position-toolbar,
    .certificate-editor-page .certificate-zoom-toolbar {
        border: 1px solid #dbeafe;
        border-radius: .75rem;
        background: #eff6ff;
        padding: .8rem;
    }
    .certificate-editor-page .certificate-settings-presets label,
    .certificate-editor-page .certificate-position-toolbar label { font-size: .75rem; color: #334155; font-weight: 700; margin-bottom: .3rem; }
    .certificate-editor-page .certificate-zoom-toolbar { display: inline-flex; align-items: center; gap: .45rem; background: #f8fafc; border-color: #e2e8f0; padding: .35rem .5rem; }
    .certificate-editor-page .certificate-zoom-toolbar .form-control { width: 92px; min-height: 32px; }
    .certificate-editor-page .certificate-sample-toolbar { display: inline-flex; align-items: center; gap: .45rem; border: 1px solid #e2e8f0; border-radius: .75rem; background: #f8fafc; padding: .35rem .5rem; }
    .certificate-editor-page .certificate-sample-toolbar .form-control { width: 132px; min-height: 32px; }
    .certificate-editor-page .preview-paper { zoom: var(--certificate-preview-zoom, 1); }
    .certificate-editor-page .preview-paper .certificate-draggable.is-selected { outline: 2px solid #2563eb; outline-offset: 5px; box-shadow: 0 0 0 5px rgba(37, 99, 235, .12); }
    .certificate-editor-page .preview-paper .certificate-draggable:focus { outline: 2px solid #2563eb; outline-offset: 5px; }
    .certificate-editor-page .template-card .card-body.is-collapsed { display: none; }
    .certificate-editor-page .template-toggle { border: 0; background: transparent; color: #475569; padding: .35rem .55rem; font-weight: 700; }
    .certificate-editor-page .template-toggle:hover { color: #4f46e5; background: #eef2ff; border-radius: .45rem; }
    .certificate-editor-page .certificate-confirm-modal { position: fixed; inset: 0; z-index: 2050; display: none; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, .48); }
    .certificate-editor-page .certificate-confirm-modal.is-open { display: flex; }
    .certificate-editor-page .certificate-confirm-dialog { width: min(100%, 460px); border-radius: 1rem; background: #fff; box-shadow: 0 24px 70px rgba(15, 23, 42, .3); padding: 1.4rem; }
    .certificate-editor-page .certificate-confirm-dialog h5 { color: #0f172a; font-weight: 800; }
    .certificate-editor-page .certificate-confirm-dialog p { color: #64748b; line-height: 1.55; }
    .certificate-editor-page .certificate-editor-actionbar { position: sticky; bottom: .75rem; z-index: 20; display: flex; align-items: center; justify-content: space-between; gap: .75rem; margin-top: 1rem; padding: .7rem .9rem; border: 1px solid #cbd5e1; border-radius: .85rem; background: rgba(255,255,255,.95); box-shadow: 0 10px 28px rgba(15,23,42,.12); backdrop-filter: blur(10px); }
    @media (max-width: 767.98px) {
        .certificate-editor-page .certificate-editor-actionbar { align-items: stretch; flex-direction: column; }
        .certificate-editor-page .certificate-editor-actionbar .btn { width: 100%; }
        .certificate-editor-page .certificate-zoom-toolbar { display: flex; width: 100%; }
        .certificate-editor-page .certificate-zoom-toolbar .form-control { flex: 1; }
        .certificate-editor-page .certificate-sample-toolbar { display: flex; width: 100%; }
        .certificate-editor-page .certificate-sample-toolbar .form-control { flex: 1; width: auto; }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const page = document.querySelector('.certificate-editor-page');
        if (!page) return;

        const settingsForm = document.getElementById('certificateSettingsForm');
        const previewPanel = page.querySelector('[data-certificate-panel="preview"]');
        const settingsCard = page.querySelector('.certificate-settings-card');
        const previewPaper = document.getElementById('certificatePreviewPaper');

        const status = document.createElement('span');
        status.className = 'certificate-enhancement-status is-saved';
        status.innerHTML = '<i class="fas fa-check-circle" aria-hidden="true"></i><span>All changes saved</span>';
        page.querySelector('.certificate-editor-page > .d-flex:first-of-type > div:first-child')?.appendChild(status);

        let dirty = false;
        const setStatus = (isDirty, message) => {
            dirty = isDirty;
            status.classList.toggle('is-dirty', isDirty);
            status.classList.toggle('is-saved', !isDirty);
            status.innerHTML = `<i class="fas ${isDirty ? 'fa-circle' : 'fa-check-circle'}" aria-hidden="true"></i><span>${message || (isDirty ? 'Unsaved changes' : 'All changes saved')}</span>`;
        };
        page.addEventListener('input', event => {
            if (event.target.matches('input, textarea, select, [contenteditable="true"]')) setStatus(true);
        });
        page.querySelectorAll('form').forEach(form => form.addEventListener('submit', () => setStatus(false, 'Saving changes…')));
        window.addEventListener('beforeunload', event => {
            if (!dirty) return;
            event.preventDefault();
            event.returnValue = '';
        });

        const actionbar = document.createElement('div');
        actionbar.className = 'certificate-editor-actionbar';
        actionbar.innerHTML = '<span class="small text-muted"><i class="fas fa-shield-alt mr-1 text-primary" aria-hidden="true"></i>Your changes stay in this certificate until you save them.</span><div class="d-flex flex-wrap" style="gap:.5rem"><button type="button" class="btn btn-outline-primary btn-sm" data-certificate-action-preview><i class="fas fa-eye mr-1"></i> Preview</button><button type="button" class="btn btn-primary btn-sm" data-certificate-action-save><i class="fas fa-save mr-1"></i> Save changes</button></div>';
        page.appendChild(actionbar);
        actionbar.querySelector('[data-certificate-action-preview]')?.addEventListener('click', () => page.querySelector('[data-certificate-tab="preview"]')?.click());
        actionbar.querySelector('[data-certificate-action-save]')?.addEventListener('click', () => {
            const activePanel = page.querySelector('[data-certificate-panel]:not(.d-none)')?.dataset.certificatePanel;
            if (activePanel === 'templates') {
                const expanded = page.querySelector('.template-card .card-body:not(.is-collapsed) form');
                expanded?.requestSubmit();
                return;
            }
            if (activePanel === 'preview') {
                document.getElementById('saveCertificateLayout')?.click();
                return;
            }
            settingsForm?.requestSubmit();
        });

        const tabs = page.querySelectorAll('[data-certificate-tab]');
        tabs.forEach(tab => {
            tab.setAttribute('role', 'tab');
            tab.setAttribute('aria-selected', tab.classList.contains('is-active') ? 'true' : 'false');
            tab.addEventListener('click', () => {
                tabs.forEach(item => item.setAttribute('aria-selected', item === tab ? 'true' : 'false'));
            });
        });
        page.querySelectorAll('[data-certificate-panel]').forEach(panel => {
            panel.setAttribute('role', 'tabpanel');
            panel.setAttribute('aria-label', `${panel.dataset.certificatePanel} certificate workspace`);
        });

        const groupSettings = (row, fallbackTitle) => {
            if (!row || row.dataset.grouped === '1') return;
            row.dataset.grouped = '1';
            const children = Array.from(row.children);
            const groups = [];
            let current = null;
            children.forEach(child => {
                const heading = child.querySelector('h6');
                if (heading) {
                    current = { title: heading.innerText.trim(), description: child.querySelector('.small')?.innerText.trim() || '', children: [] };
                    groups.push(current);
                    return;
                }
                if (!current) {
                    current = { title: fallbackTitle, description: '', children: [] };
                    groups.push(current);
                }
                current.children.push(child);
            });
            row.replaceChildren(...groups.map((group, index) => {
                const details = document.createElement('details');
                details.className = 'certificate-settings-section';
                details.open = index === 0 || /global|title/i.test(group.title);
                const summary = document.createElement('summary');
                summary.innerHTML = `<span>${group.title}${group.description ? `<small>${group.description}</small>` : ''}</span>`;
                const body = document.createElement('div');
                body.className = 'certificate-settings-section-body row';
                group.children.forEach(child => body.appendChild(child));
                details.append(summary, body);
                return details;
            }));
        };
        if (settingsCard) {
            const mainRow = settingsCard.querySelector(':scope > .row');
            if (mainRow && !mainRow.querySelector('[data-certificate-additional-layout-heading]')) {
                const lineHeightField = mainRow.querySelector('[name="layout_settings[typography][line_height]"]')?.closest('[class*="col-"]');
                if (lineHeightField) {
                    const heading = document.createElement('div');
                    heading.className = 'col-12 mt-2 mb-2';
                    heading.dataset.certificateAdditionalLayoutHeading = '1';
                    heading.innerHTML = '<h6 class="font-weight-bold text-slate-800 mb-1">Additional layout</h6><div class="small text-muted">Fine-tune line spacing and watermark presentation.</div>';
                    mainRow.insertBefore(heading, lineHeightField);
                }
            }
            groupSettings(mainRow, 'Page and margins');
            groupSettings(settingsCard.querySelector('[data-certificate-title-settings]'), 'Title style');

        }

        page.querySelectorAll('.template-card').forEach((card, index) => {
            const header = card.querySelector('.card-header');
            const body = card.querySelector('.card-body');
            if (!header || !body || header.querySelector('.template-toggle')) return;
            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'template-toggle';
            toggle.setAttribute('aria-expanded', index === 0 ? 'true' : 'false');
            toggle.innerHTML = `<i class="fas ${index === 0 ? 'fa-chevron-up' : 'fa-chevron-down'} mr-1" aria-hidden="true"></i>${index === 0 ? 'Collapse' : 'Edit template'}`;
            header.querySelector('.d-flex.flex-wrap')?.prepend(toggle);
            if (index !== 0) body.classList.add('is-collapsed');
            toggle.addEventListener('click', () => {
                const collapsed = body.classList.toggle('is-collapsed');
                toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                toggle.innerHTML = `<i class="fas ${collapsed ? 'fa-chevron-down' : 'fa-chevron-up'} mr-1" aria-hidden="true"></i>${collapsed ? 'Edit template' : 'Collapse'}`;
            });
        });

        if (settingsCard && !settingsCard.querySelector('[data-certificate-principal-settings]')) {
            const principalSettings = document.createElement('div');
            principalSettings.dataset.certificatePrincipalSettings = '';
            principalSettings.className = 'row mt-2';
            principalSettings.innerHTML = `
                <div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">Principal text</h6><div class="small text-muted">Control the designation, such as Principal or Head Teacher.</div></div>
                <div class="col-6 mb-3"><label class="form-label small">Font family</label><select form="certificateSettingsForm" name="layout_settings[footer][principal_label][font_family]" class="form-control form-control-sm js-certificate-principal-input"><option value="Arial, Helvetica, sans-serif" @selected(data_get($layout, 'footer.principal_label.font_family', 'Arial, Helvetica, sans-serif') === 'Arial, Helvetica, sans-serif')>Arial</option><option value="Georgia, Times New Roman, serif" @selected(data_get($layout, 'footer.principal_label.font_family') === 'Georgia, Times New Roman, serif')>Georgia</option><option value="Times New Roman, Times, serif" @selected(data_get($layout, 'footer.principal_label.font_family') === 'Times New Roman, Times, serif')>Times New Roman</option><option value="Verdana, Geneva, sans-serif" @selected(data_get($layout, 'footer.principal_label.font_family') === 'Verdana, Geneva, sans-serif')>Verdana</option></select></div>
                <div class="col-6 mb-3"><label class="form-label small">Font size (px)</label><input form="certificateSettingsForm" type="number" min="8" max="48" step="1" name="layout_settings[footer][principal_label][font_size]" class="form-control form-control-sm js-certificate-principal-input" value="{{ data_get($layout, 'footer.principal_label.font_size', 16) }}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Weight</label><select form="certificateSettingsForm" name="layout_settings[footer][principal_label][font_weight]" class="form-control form-control-sm js-certificate-principal-input">@foreach(range(300, 900, 100) as $weight)<option value="{{ $weight }}" @selected((int) data_get($layout, 'footer.principal_label.font_weight', 700) === $weight)>{{ $weight }}</option>@endforeach</select></div>
                <div class="col-6 mb-3"><label class="form-label small">Color</label><input form="certificateSettingsForm" type="color" name="layout_settings[footer][principal_label][color]" class="form-control form-control-sm p-1 js-certificate-principal-input" value="{{ data_get($layout, 'footer.principal_label.color', '#111827') }}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Alignment</label><select form="certificateSettingsForm" name="layout_settings[footer][principal_label][text_align]" class="form-control form-control-sm js-certificate-principal-input"><option value="left">Left</option><option value="center" @selected(data_get($layout, 'footer.principal_label.text_align', 'center') === 'center')>Center</option><option value="right">Right</option></select></div>
                <div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">School name</h6><div class="small text-muted">Control the school or college name shown under the principal text.</div></div>
                <div class="col-6 mb-3"><label class="form-label small">Font family</label><select form="certificateSettingsForm" name="layout_settings[footer][school_name][font_family]" class="form-control form-control-sm js-certificate-principal-input"><option value="Arial, Helvetica, sans-serif" @selected(data_get($layout, 'footer.school_name.font_family', 'Arial, Helvetica, sans-serif') === 'Arial, Helvetica, sans-serif')>Arial</option><option value="Georgia, Times New Roman, serif" @selected(data_get($layout, 'footer.school_name.font_family') === 'Georgia, Times New Roman, serif')>Georgia</option><option value="Times New Roman, Times, serif" @selected(data_get($layout, 'footer.school_name.font_family') === 'Times New Roman, Times, serif')>Times New Roman</option><option value="Verdana, Geneva, sans-serif" @selected(data_get($layout, 'footer.school_name.font_family') === 'Verdana, Geneva, sans-serif')>Verdana</option></select></div>
                <div class="col-6 mb-3"><label class="form-label small">Font size (px)</label><input form="certificateSettingsForm" type="number" min="8" max="48" step="1" name="layout_settings[footer][school_name][font_size]" class="form-control form-control-sm js-certificate-principal-input" value="{{ data_get($layout, 'footer.school_name.font_size', 16) }}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Weight</label><select form="certificateSettingsForm" name="layout_settings[footer][school_name][font_weight]" class="form-control form-control-sm js-certificate-principal-input">@foreach(range(300, 900, 100) as $weight)<option value="{{ $weight }}" @selected((int) data_get($layout, 'footer.school_name.font_weight', 700) === $weight)>{{ $weight }}</option>@endforeach</select></div>
                <div class="col-6 mb-3"><label class="form-label small">Color</label><input form="certificateSettingsForm" type="color" name="layout_settings[footer][school_name][color]" class="form-control form-control-sm p-1 js-certificate-principal-input" value="{{ data_get($layout, 'footer.school_name.color', '#111827') }}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Alignment</label><select form="certificateSettingsForm" name="layout_settings[footer][school_name][text_align]" class="form-control form-control-sm js-certificate-principal-input"><option value="left">Left</option><option value="center" @selected(data_get($layout, 'footer.school_name.text_align', 'center') === 'center')>Center</option><option value="right">Right</option></select></div>
                <div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">Contact number</h6><div class="small text-muted">Style the single contact number shown below the school name.</div></div>
                <div class="col-6 mb-3"><label class="form-label small">Font size (px)</label><input form="certificateSettingsForm" type="number" min="8" max="48" step="1" name="layout_settings[footer][contact_number][font_size]" class="form-control form-control-sm js-certificate-principal-input" value="{{ data_get($layout, 'footer.contact_number.font_size', 14) }}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Weight</label><select form="certificateSettingsForm" name="layout_settings[footer][contact_number][font_weight]" class="form-control form-control-sm js-certificate-principal-input">@foreach(range(300, 900, 100) as $weight)<option value="{{ $weight }}" @selected((int) data_get($layout, 'footer.contact_number.font_weight', 400) === $weight)>{{ $weight }}</option>@endforeach</select></div>
                <div class="col-6 mb-3"><label class="form-label small">Color</label><input form="certificateSettingsForm" type="color" name="layout_settings[footer][contact_number][color]" class="form-control form-control-sm p-1 js-certificate-principal-input" value="{{ data_get($layout, 'footer.contact_number.color', '#111827') }}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Alignment</label><select form="certificateSettingsForm" name="layout_settings[footer][contact_number][text_align]" class="form-control form-control-sm js-certificate-principal-input"><option value="left">Left</option><option value="center" @selected(data_get($layout, 'footer.contact_number.text_align', 'center') === 'center')>Center</option><option value="right">Right</option></select></div>`;
            settingsCard.appendChild(principalSettings);
            groupSettings(principalSettings, 'Principal and school typography');
            const principalBlock = previewPaper?.querySelector('.preview-principal');
            const applyPrincipalTypography = () => {
                if (!principalBlock) return;
                const read = (group, key, fallback) => page.querySelector(`[name="layout_settings[footer][${group}][${key}]"]`)?.value || fallback;
                const apply = (element, group, defaults) => {
                    if (!element) return;
                    element.style.fontFamily = read(group, 'font_family', defaults.fontFamily);
                    element.style.fontSize = read(group, 'font_size', defaults.fontSize) + 'px';
                    element.style.fontWeight = read(group, 'font_weight', defaults.fontWeight);
                    element.style.color = read(group, 'color', defaults.color);
                    element.style.textAlign = read(group, 'text_align', defaults.textAlign);
                };
                apply(principalBlock.querySelector('.preview-principal-name'), 'principal_label', { fontFamily: 'Arial, Helvetica, sans-serif', fontSize: 16, fontWeight: 700, color: '#111827', textAlign: 'center' });
                apply(principalBlock.children[3], 'school_name', { fontFamily: 'Arial, Helvetica, sans-serif', fontSize: 16, fontWeight: 700, color: '#111827', textAlign: 'center' });
            };
            principalSettings.querySelectorAll('.js-certificate-principal-input').forEach(input => input.addEventListener('input', applyPrincipalTypography));
            applyPrincipalTypography();
        }

        if (previewPanel && previewPaper) {
            const previewHeader = previewPanel.querySelector('.col-lg-8 > .bg-white > .d-flex');
            const zoom = document.createElement('div');
            zoom.className = 'certificate-zoom-toolbar';
            zoom.innerHTML = '<label class="sr-only" for="certificatePreviewZoom">Preview zoom</label><i class="fas fa-search-minus text-muted" aria-hidden="true"></i><select id="certificatePreviewZoom" class="form-control form-control-sm" aria-label="Preview zoom"><option value="0.75">75%</option><option value="0.9">90%</option><option value="1" selected>100%</option><option value="1.1">110%</option></select><button type="button" class="btn btn-link btn-sm p-0" data-certificate-fit>Fit</button>';
            previewHeader?.querySelector('.d-flex.align-items-center')?.prepend(zoom);
            const applyZoom = value => { previewPaper.style.setProperty('--certificate-preview-zoom', value); };
            zoom.querySelector('select')?.addEventListener('change', event => applyZoom(event.target.value));
            zoom.querySelector('[data-certificate-fit]')?.addEventListener('click', () => { zoom.querySelector('select').value = '0.9'; applyZoom('0.9'); });

            const movable = ['title', 'body', 'bottom', 'watermark'];
            const positionToolbar = document.createElement('div');
            positionToolbar.className = 'certificate-position-toolbar mb-3';
            positionToolbar.innerHTML = '<div class="certificate-position-heading"><div><span class="certificate-position-kicker">Position controls</span><strong>Move selected element</strong><small>Use millimetres to fine-tune placement on the certificate.</small></div><button type="button" class="btn btn-link btn-sm certificate-position-reset" data-certificate-reset-position><i class="fas fa-undo-alt mr-1"></i>Reset</button></div><div class="certificate-position-grid"><div class="certificate-position-field certificate-position-element"><label for="certificateSelectedElement">Element</label><select id="certificateSelectedElement" class="form-control form-control-sm"><option value="title">Title</option><option value="body">Body</option><option value="bottom">Footer</option><option value="watermark">Watermark</option></select></div><div class="certificate-position-field"><label for="certificateSelectedX">Horizontal position (X)</label><div class="certificate-position-input"><input id="certificateSelectedX" type="number" step="0.5" class="form-control form-control-sm"><span>mm</span></div></div><div class="certificate-position-field"><label for="certificateSelectedY">Vertical position (Y)</label><div class="certificate-position-input"><input id="certificateSelectedY" type="number" step="0.5" class="form-control form-control-sm"><span>mm</span></div></div></div>';
            const toolbarInsertTarget = settingsCard?.querySelector(':scope > .d-flex');
            toolbarInsertTarget?.after(positionToolbar);
            let selected = 'title';
            const hiddenPosition = (key, axis) => page.querySelector(`[data-certificate-position="${key}"][data-axis="${axis}"]`);
            const selectElement = key => {
                selected = movable.includes(key) ? key : 'title';
                positionToolbar.querySelector('#certificateSelectedElement').value = selected;
                previewPaper.querySelectorAll('[data-certificate-draggable]').forEach(element => element.classList.toggle('is-selected', element.dataset.certificateDraggable === selected));
                ['x', 'y'].forEach(axis => positionToolbar.querySelector(`#certificateSelected${axis.toUpperCase()}`).value = hiddenPosition(selected, axis)?.value || 0);
            };
            positionToolbar.querySelector('#certificateSelectedElement')?.addEventListener('change', event => selectElement(event.target.value));
            ['x', 'y'].forEach(axis => positionToolbar.querySelector(`#certificateSelected${axis.toUpperCase()}`)?.addEventListener('input', event => { const input = hiddenPosition(selected, axis); if (input) { input.value = event.target.value; input.dispatchEvent(new Event('input', { bubbles: true })); } if (typeof applyCertificateLayout === 'function') applyCertificateLayout(); }));
            positionToolbar.querySelector('[data-certificate-reset-position]')?.addEventListener('click', () => { ['x', 'y'].forEach(axis => { const input = hiddenPosition(selected, axis); if (input) input.value = 0; }); selectElement(selected); if (typeof applyCertificateLayout === 'function') applyCertificateLayout(); setStatus(true, 'Position reset — save to keep it'); });
            previewPaper.querySelectorAll('[data-certificate-draggable]').forEach(element => {
                element.setAttribute('tabindex', '0');
                element.setAttribute('role', 'button');
                element.setAttribute('aria-label', `Select ${element.dataset.certificateDraggable} block`);
                element.addEventListener('click', () => selectElement(element.dataset.certificateDraggable));
                element.addEventListener('keydown', event => {
                    if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(event.key)) return;
                    event.preventDefault();
                    selectElement(element.dataset.certificateDraggable);
                    const axis = event.key.includes('Left') || event.key.includes('Right') ? 'x' : 'y';
                    const delta = event.key === 'ArrowLeft' || event.key === 'ArrowUp' ? -1 : 1;
                    const input = hiddenPosition(selected, axis);
                    if (input) { input.value = (parseFloat(input.value || 0) + delta * (event.shiftKey ? 5 : 1)).toFixed(1); input.dispatchEvent(new Event('input', { bubbles: true })); }
                    if (typeof applyCertificateLayout === 'function') applyCertificateLayout();
                });
            });
            selectElement('title');
        }

        const modal = document.createElement('div');
        modal.className = 'certificate-confirm-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.innerHTML = '<div class="certificate-confirm-dialog"><h5>Delete template?</h5><p data-certificate-delete-message></p><div class="d-flex justify-content-end" style="gap:.5rem"><button type="button" class="btn btn-outline-secondary" data-certificate-delete-cancel>Cancel</button><button type="button" class="btn btn-danger" data-certificate-delete-confirm>Delete template</button></div></div>';
        page.appendChild(modal);
        let pendingDelete = null;
        const closeModal = () => { modal.classList.remove('is-open'); pendingDelete = null; };
        page.querySelectorAll('.js-delete-certificate-template').forEach(form => form.addEventListener('submit', event => {
            event.preventDefault();
            if (page.querySelectorAll('.template-card').length <= 1) {
                setStatus(true, 'Add another template before deleting this one');
                return;
            }
            pendingDelete = form;
            modal.querySelector('[data-certificate-delete-message]').textContent = `“${form.dataset.templateName}” will be removed. If it is the default, another available template will be selected automatically.`;
            modal.classList.add('is-open');
            modal.querySelector('[data-certificate-delete-cancel]').focus();
        }));
        modal.querySelector('[data-certificate-delete-cancel]')?.addEventListener('click', closeModal);
        modal.querySelector('[data-certificate-delete-confirm]')?.addEventListener('click', () => { if (pendingDelete) pendingDelete.submit(); });
        modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
        document.addEventListener('keydown', event => { if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal(); });
    });
</script>
<style>
    /* One focused workspace: the preview is the anchor and every control follows it. */
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-nav { display: none !important; }
    .certificate-editor-page.certificate-single-workspace [data-certificate-panel="general"],
    .certificate-editor-page.certificate-single-workspace [data-certificate-panel="templates"] { display: none !important; }
    .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] { display: block !important; }
    .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] > .row { display: flex; flex-direction: column; }
    .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] > .row > .col-lg-8 { order: 1; width: 100%; max-width: none; flex: 0 0 auto; }
    .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] > .row > .col-lg-4 { order: 2; width: 100%; max-width: none; flex: 0 0 auto; }
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section { order: 3; }
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section > .bg-white,
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section > .card { border-radius: 1rem !important; box-shadow: 0 6px 18px rgba(15, 23, 42, .04); }
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section .template-card:not(:first-of-type) { display: none; }
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section #new-certificate-template,
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section .template-card .card-header .d-flex:last-child { display: none !important; }
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section .template-card .card-header { border-bottom: 1px solid #e2e8f0; }
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section .template-card .card-header .small { color: #64748b; }
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section .template-card .card-body { padding: 1.25rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-workspace-section .template-card .card-body .custom-switch { display: none; }
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tabs { margin-top: 1rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tablist { display: flex; flex-wrap: wrap; gap: .35rem; padding: .35rem; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: .85rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tab { flex: 1 1 180px; min-height: 46px; padding: .65rem .9rem; border: 0; border-radius: .65rem; background: transparent; color: #64748b; font-weight: 600; text-align: center; }
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tab:hover { color: #4338ca; background: #e0e7ff; }
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tab.is-active { color: #3730a3; background: #fff; box-shadow: 0 2px 8px rgba(15, 23, 42, .08); }
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel { padding-top: 1rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel > .bg-white,
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel > .template-card { margin-bottom: 1rem !important; }
    .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel[data-editor-panel="content"] > .template-card { margin-bottom: 0 !important; }
    .certificate-editor-page #saveCertificateLayout,
    .certificate-editor-page #saveCertificatePreviewText,
    .certificate-editor-page #saveCertificateLayoutSidebar,
    .certificate-editor-page #certificateSettingsForm button[type="submit"],
    .certificate-editor-page .template-card form button[type="submit"] { display: none !important; }
        .certificate-editor-page #saveCertificateLayout,
        .certificate-editor-page #saveCertificatePreviewText,
        .certificate-editor-page #saveCertificateLayoutSidebar,
        .certificate-editor-page #certificateSettingsForm > button[type="submit"],
        .certificate-editor-page .template-card form button[type="submit"] { display: none !important; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel { padding: 1rem; border: 1px solid #e2e8f0; border-radius: 1rem; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-card { margin: 0 0 1rem !important; padding: 0 !important; border: 0; background: transparent; box-shadow: none; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-card > .d-flex { margin: 0 0 1rem !important; padding: 1rem 1.1rem; border: 1px solid #c7d2fe; border-radius: .85rem; background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 100%); }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-card > .d-flex h5 { color: #312e81 !important; letter-spacing: -.01em; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-card > .d-flex p { color: #4f46e5 !important; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-card > .d-flex > i { display: grid; place-items: center; width: 2.25rem; height: 2.25rem; border-radius: .7rem; color: #fff !important; background: #4f46e5; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-card > .row { display: block; margin: 0 !important; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section { display: block; width: 100%; clear: both; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section { margin-bottom: .7rem; border-color: #e2e8f0; border-radius: .75rem; box-shadow: 0 2px 7px rgba(15, 23, 42, .035); }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section > summary { min-height: 48px; padding: .7rem .85rem; color: #1e293b; font-size: .88rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section > summary small { font-size: .72rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section[open] > summary { color: #3730a3; background: #fafaff; border-bottom: 1px solid #eef2ff; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body { display: grid !important; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0 .8rem; padding: .7rem .8rem .2rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body > .col-6 { grid-column: span 6; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body > .col-12 { grid-column: 1 / -1; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .form-label { margin-bottom: .25rem; color: #475569; font-size: .72rem; font-weight: 700; letter-spacing: .01em; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .form-control { min-height: 34px; padding: .35rem .55rem; border-color: #cbd5e1; border-radius: .45rem; font-size: .82rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .form-control:focus { border-color: #818cf8; box-shadow: 0 0 0 .18rem rgba(99, 102, 241, .12); }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body > [class*="col-"] { margin-bottom: .6rem !important; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body > [class*="col-"] {
        min-width: 0;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .form-label {
        display: block;
        min-height: 2.1em;
        line-height: 1.25;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .form-control {
        display: block;
        width: 100%;
        max-width: none;
        min-width: 0;
        min-height: 38px;
        padding: .45rem .65rem;
        color: #1e293b;
        background-color: #fff;
        border: 1px solid #cbd5e1;
        border-radius: .55rem;
        font-size: .82rem;
        line-height: 1.35;
        transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel input.form-control,
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel select.form-control,
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel textarea.form-control {
        width: 100% !important;
        max-width: none !important;
        flex: 1 1 auto;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel select.form-control {
        padding-right: 2rem;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .form-control:hover {
        border-color: #a5b4fc;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .form-control:focus {
        color: #1e293b;
        background-color: #fff;
        border-color: #6366f1;
        outline: 0;
        box-shadow: 0 0 0 .2rem rgba(99, 102, 241, .14);
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel input[type="number"].form-control {
        appearance: textfield;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel input[type="number"].form-control::-webkit-inner-spin-button,
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel input[type="number"].form-control::-webkit-outer-spin-button {
        margin: 0;
        appearance: none;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel input[type="color"].form-control {
        min-height: 38px;
        padding: .25rem;
        cursor: pointer;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
        padding: .85rem;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] {
        grid-column: span 1 !important;
        margin-bottom: 0 !important;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body h6 { margin-top: .15rem; padding-top: .55rem; border-top: 1px solid #eef2ff; color: #4338ca !important; font-size: .78rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel > .bg-white { border: 1px solid #e2e8f0; border-radius: .85rem !important; box-shadow: 0 2px 8px rgba(15, 23, 42, .035); }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-position-toolbar { margin-bottom: .75rem; border-color: #c7d2fe; background: #eef2ff; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-grid { display: grid; grid-template-columns: minmax(0, 1fr); gap: .85rem; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card { min-width: 0; overflow: hidden; border: 1px solid #e2e8f0; border-radius: .85rem; background: #fff; box-shadow: 0 3px 12px rgba(15, 23, 42, .045); }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card.is-wide { grid-column: 1; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-header { display: flex; align-items: flex-start; justify-content: space-between; gap: .75rem; padding: .85rem 1rem; border-bottom: 1px solid #eef2ff; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-header h6 { margin: 0; color: #1e293b; font-size: .9rem; font-weight: 800; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-header p { margin: .15rem 0 0; color: #64748b; font-size: .7rem; line-height: 1.35; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-icon { display: grid; place-items: center; flex: 0 0 auto; width: 2rem; height: 2rem; border-radius: .6rem; color: #4338ca; background: #eef2ff; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body { padding: .75rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-subtabs { display: flex; gap: .25rem; margin: 0 0 .75rem; padding: .3rem; overflow-x: auto; border: 1px solid #e2e8f0; border-radius: .75rem; background: #f1f5f9; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-subtab { flex: 1 0 auto; min-height: 36px; padding: .45rem .7rem; border: 0; border-radius: .55rem; background: transparent; color: #64748b; font-size: .76rem; font-weight: 700; white-space: nowrap; transition: color .15s ease, background .15s ease, box-shadow .15s ease; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-subtab:hover { color: #4338ca; background: #e0e7ff; }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-subtab.is-active { color: #3730a3; background: #fff; box-shadow: 0 2px 8px rgba(15, 23, 42, .08); }
    .certificate-editor-page.certificate-single-workspace .layout-settings-grid.layout-settings-tabs { display: block; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-grid.layout-settings-tabs > .layout-settings-card[hidden] { display: none !important; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row { display: grid !important; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0 .7rem; margin: 0 !important; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row > .col-6 { grid-column: span 4; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row > .col-12 { grid-column: 1 / -1; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .certificate-settings-section { margin-bottom: .65rem; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .certificate-settings-section:last-child { margin-bottom: 0; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row { display: grid !important; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 0 .7rem; margin: 0 !important; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row > .col-6 { grid-column: span 4; }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row > .col-12 { grid-column: 1 / -1; }
    @media (min-width: 1200px) {
        .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body > .col-6 { grid-column: span 2; }
        .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body > .col-12 { grid-column: 1 / -1; }
        .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row > .col-6 { grid-column: span 2; }
        .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row > .col-6 { grid-column: span 2; }
    }
    @media (min-width: 992px) {
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] .certificate-settings-card { position: static; }
    }
    @media (max-width: 575.98px) {
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tab { flex-basis: 100%; text-align: left; }
        .certificate-editor-page.certificate-single-workspace .layout-settings-grid { grid-template-columns: 1fr; }
        .certificate-editor-page.certificate-single-workspace .layout-settings-card.is-wide { grid-column: auto; }
        .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row { grid-template-columns: 1fr; }
        .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row > [class*="col-"] { grid-column: 1 / -1; }
        .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row { grid-template-columns: 1fr; }
        .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row > [class*="col-"] { grid-column: 1 / -1; }
        .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body { grid-template-columns: 1fr; }
        .certificate-editor-page.certificate-single-workspace .certificate-layout-panel .certificate-settings-section-body > [class*="col-"] { grid-column: 1 / -1; }
        .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] { grid-column: span 1 !important; }
    }
    /* Keep every settings form readable: two controls per row instead of compressed six-column fields. */
    .certificate-editor-page.certificate-single-workspace .layout-settings-card .certificate-settings-section-body,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card .certificate-settings-section-body > [class*="col-"],
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row > [class*="col-"],
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row > [class*="col-"] {
        grid-column: span 1 !important;
        width: auto !important;
        max-width: 100% !important;
        flex: none !important;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card [class*="col-"] {
        box-sizing: border-box;
        min-width: 0;
        width: auto !important;
        max-width: 100% !important;
        flex: none !important;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card input.form-control,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card select.form-control,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card textarea.form-control {
        box-sizing: border-box;
        display: block;
        width: 100% !important;
        inline-size: 100% !important;
        min-width: 0;
        min-inline-size: 0;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card .certificate-settings-section-body > [class*="col-"] > .form-control,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row > [class*="col-"] > .form-control,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row > [class*="col-"] > .form-control {
        width: 100% !important;
        inline-size: 100% !important;
        max-width: none !important;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-title [data-certificate-title-settings] {
        display: block !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
    }
    .certificate-editor-page.certificate-single-workspace .title-settings-stack {
        display: block !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-title [data-certificate-title-settings] > .certificate-settings-section {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
    }
    /* Stable, full-width controls: prevent legacy Bootstrap columns from collapsing inputs. */
    .certificate-editor-page.certificate-single-workspace .layout-settings-card .certificate-settings-section-body > [class*="col-"],
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row > [class*="col-"],
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row > [class*="col-"] {
        min-width: 0 !important;
        width: auto !important;
        max-width: none !important;
        padding-left: .3rem !important;
        padding-right: .3rem !important;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card .form-group,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card label {
        min-width: 0;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card .form-control {
        box-sizing: border-box !important;
        display: block !important;
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        margin: 0;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card input[type="number"].form-control {
        appearance: textfield;
        text-align: center;
        font-variant-numeric: tabular-nums;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card input[type="color"].form-control {
        min-height: 38px !important;
        padding: .2rem !important;
        cursor: pointer;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: .55rem !important;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] {
        grid-column: span 1 !important;
        padding: 0 !important;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .form-label {
        min-height: 3.75em;
        margin-bottom: .35rem;
        color: #475569;
        font-size: .67rem;
        line-height: 1.25;
        text-align: left;
    }
    /* Certificate designer reference layout: controls sidebar + live preview stage. */
    @media (min-width: 992px) {
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] { display: grid !important; grid-template-columns: minmax(500px, 560px) minmax(0, 1fr); align-items: stretch; gap: 0; margin: 0 -1rem; border: 1px solid #e3e6ec; background: #fff; }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] > .row { grid-column: 2; grid-row: 1; min-width: 0; min-height: 760px; margin: 0 !important; padding: 1.75rem; background: #dfe2ea; }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] > .row > .col-lg-8 { width: 100%; max-width: none; padding: 0 !important; }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] .col-lg-8 > .bg-white { padding: 0 !important; border: 0 !important; border-radius: 0 !important; background: transparent !important; box-shadow: none !important; }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] .col-lg-8 > .bg-white > .d-flex.flex-column { padding: 0 0 1rem; border-bottom: 0; color: #6b7585; }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] .col-lg-8 > .bg-white > .d-flex.flex-column h4 { color: #6b7585 !important; font-size: .82rem; }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] .col-lg-8 > .bg-white > .d-flex.flex-column p { font-size: .72rem; }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] .preview-paper { margin: 0 auto; box-shadow: 0 8px 30px rgba(0, 0, 0, .18); }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabs { grid-column: 1; grid-row: 1; min-width: 0; display: flex; flex-direction: column; margin: 0; border-right: 1px solid #e3e6ec; background: #fff; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tablist { flex: 0 0 auto; display: flex; gap: .25rem; padding: .65rem .8rem; border: 0; border-bottom: 1px solid #e3e6ec; border-radius: 0; background: #fff; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tab { min-height: 38px; padding: .45rem .5rem; font-size: .78rem; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tab i { display: none; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel { min-height: 0; overflow: auto; padding: .45rem 1rem 1rem; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel[data-editor-panel="content"] { flex: 1; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel[data-editor-panel="layout"] { flex: 1; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabs > .certificate-editor-actionbar { flex: 0 0 auto; position: sticky; bottom: 0; z-index: 25; margin: 0; padding: .7rem .8rem; border: 0; border-top: 1px solid #e3e6ec; border-radius: 0; box-shadow: 0 -6px 16px rgba(15, 23, 42, .08); }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabs > .certificate-editor-actionbar > span { display: none; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabs > .certificate-editor-actionbar > div { width: 100%; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabs > .certificate-editor-actionbar [data-certificate-action-save] { width: 100%; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel > .bg-white { border: 0 !important; border-radius: 0 !important; box-shadow: none !important; padding: .65rem 0 !important; background: transparent !important; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel > .template-card { border-radius: .7rem; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabpanel .certificate-layout-panel { margin: 0 -.35rem; padding: .35rem; border: 0; background: transparent; }
    }
    @media (max-width: 991.98px) {
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] { display: flex !important; flex-direction: column; }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] > .row { order: 2; }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabs { order: 1; }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const page = document.querySelector('.certificate-editor-page');
        const previewPanel = page?.querySelector('[data-certificate-panel="preview"]');
        if (!page || !previewPanel) return;

        page.classList.add('certificate-single-workspace');
        page.querySelector('nav[aria-label="Certificate editor sections"]')?.classList.add('certificate-workspace-nav');
        page.querySelectorAll('[data-certificate-tab]').forEach(tab => tab.setAttribute('aria-hidden', 'true'));

        const row = previewPanel.querySelector(':scope > .row');
        const generalCard = page.querySelector('[data-certificate-panel="general"] > div');
        const templatePanel = page.querySelector('[data-certificate-panel="templates"]');
        const placeholderCard = templatePanel?.querySelector(':scope > .bg-white');
        const templateCards = templatePanel ? Array.from(templatePanel.querySelectorAll('.template-card')) : [];
        const templateHeading = templatePanel?.querySelector(':scope > .d-flex');
        const addTemplate = templatePanel?.querySelector('#new-certificate-template');

        if (row && generalCard && templatePanel && placeholderCard && templateCards.length) {
            const content = document.createElement('div');
            content.className = 'certificate-workspace-section';
            content.innerHTML = '<div class="mb-3"><h4 class="text-slate-900 font-weight-bold mb-1">Certificate content and details</h4><p class="text-muted mb-0">Preview the final certificate first, then manage its details, placeholders, and single template below.</p></div>';
            content.append(generalCard, placeholderCard, templateCards[0]);
            previewPanel.appendChild(content);

            if (templateHeading) templateHeading.remove();
            if (addTemplate) addTemplate.remove();
            templateCards.slice(1).forEach(card => card.remove());

            const activeSelect = generalCard.querySelector('select[name="active_template_id"]');
            if (activeSelect) {
                const selected = activeSelect.options[activeSelect.selectedIndex];
                const wrapper = activeSelect.closest('.col-lg-6');
                wrapper.innerHTML = '<label class="form-label">Template</label><div class="form-control bg-light d-flex align-items-center"><i class="fas fa-file-alt text-primary mr-2"></i><span></span></div><small class="form-text text-muted">Each certificate type has one editable template.</small>';
                wrapper.querySelector('span').textContent = selected?.textContent?.trim() || 'Single certificate template';
            }

            const templateCard = templateCards[0];
            templateCard.querySelector('.card-header .d-flex:last-child')?.remove();
            templateCard.querySelector('.card-header .small')?.replaceChildren(document.createTextNode('This is the only template used when issuing this certificate.'));
            templateCard.querySelector('.js-preview-template')?.remove();
            templateCard.querySelector('input[name="is_active"]')?.closest('.custom-switch')?.remove();

            const previewSelector = document.getElementById('certificatePreviewTemplate');
            previewSelector?.nextElementSibling?.classList.contains('select2-container') && previewSelector.nextElementSibling.remove();
            const textarea = templateCard.querySelector('textarea.js-certificate-editor');
            if (previewSelector && textarea) {
                previewSelector.innerHTML = `<option value="${textarea.id}">${templateCard.querySelector('input[name="name"]')?.value || 'Certificate template'}</option>`;
                previewSelector.value = textarea.id;
                if (typeof setActiveCertificateTemplate === 'function') setActiveCertificateTemplate(textarea.id);
                if (typeof updateCertificatePreview === 'function') updateCertificatePreview();
            }
        }

        page.querySelector('[data-certificate-action-preview]')?.remove();
        page.querySelectorAll('[data-certificate-tab]').forEach(tab => tab.remove());
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const page = document.querySelector('.certificate-editor-page');
        const previewPanel = page?.querySelector('[data-certificate-panel="preview"]');
        const workspace = page?.querySelector('.certificate-workspace-section');
        if (!page || !previewPanel || !workspace) return;

        const row = previewPanel.querySelector(':scope > .row');
        const previewColumn = row?.querySelector(':scope > .col-lg-8');
        const settingsColumn = row?.querySelector(':scope > .col-lg-4');
        const generalCard = workspace.querySelector('#certificateSettingsForm')?.closest('.bg-white');
        const placeholderCard = workspace.querySelector('#certificatePlaceholderLibrary')?.closest('.bg-white');
        const templateCard = workspace.querySelector('.template-card');
        if (!row || !previewColumn || !settingsColumn || !generalCard || !placeholderCard || !templateCard) return;

        const layoutContent = document.createElement('div');
        Array.from(settingsColumn.children).forEach(child => layoutContent.appendChild(child));
        settingsColumn.remove();

        const tabShell = document.createElement('section');
        tabShell.className = 'certificate-editor-tabs';
        tabShell.setAttribute('aria-label', 'Certificate settings');
        tabShell.innerHTML = '<div class="certificate-editor-tablist" role="tablist"><button type="button" class="certificate-editor-tab is-active" role="tab" aria-selected="true" aria-controls="certificate-tab-layout" data-editor-tab="layout"><i class="fas fa-ruler-combined mr-1"></i> Certificate layout</button><button type="button" class="certificate-editor-tab" role="tab" aria-selected="false" aria-controls="certificate-tab-content" data-editor-tab="content"><i class="fas fa-file-alt mr-1"></i> Certificate content</button></div><div class="certificate-editor-tabpanel is-active" id="certificate-tab-layout" role="tabpanel" data-editor-panel="layout"></div><div class="certificate-editor-tabpanel" id="certificate-tab-content" role="tabpanel" data-editor-panel="content" hidden></div>';

        previewPanel.insertBefore(tabShell, workspace);
        const actionbar = page.querySelector('.certificate-editor-actionbar');
        if (actionbar) tabShell.appendChild(actionbar);
        const contentPanel = tabShell.querySelector('[data-editor-panel="content"]');
        contentPanel.append(generalCard, placeholderCard, templateCard);
        generalCard.querySelector('h4')?.replaceChildren(document.createTextNode('Certificate details and template'));
        generalCard.querySelector('p')?.replaceChildren(document.createTextNode('Manage the certificate identity, availability, single template, and placeholders in one place.'));
        const layoutPanel = tabShell.querySelector('[data-editor-panel="layout"]');
        layoutPanel.classList.add('certificate-layout-panel');
        layoutPanel.appendChild(layoutContent);

        const settingsCard = layoutContent.querySelector('.certificate-settings-card');
        const settingsRow = settingsCard?.querySelector(':scope > .row');
        const titleSettings = settingsCard?.querySelector('[data-certificate-title-settings]');
        const principalSettings = settingsCard?.querySelector('[data-certificate-principal-settings]');
        const positionToolbar = layoutContent.querySelector('.certificate-position-toolbar');
        const dragGuide = layoutContent.querySelector(':scope > .bg-white:not(.certificate-settings-card)');
        const layoutDetails = settingsRow ? Array.from(settingsRow.querySelectorAll(':scope > .certificate-settings-section')) : [];
        const makeLayoutCard = (title, description, icon, wide = false) => {
            const card = document.createElement('section');
            card.className = `layout-settings-card${wide ? ' is-wide' : ''}`;
            card.innerHTML = `<div class="layout-settings-card-header"><div><h6>${title}</h6><p>${description}</p></div><span class="layout-settings-card-icon"><i class="fas ${icon}" aria-hidden="true"></i></span></div><div class="layout-settings-card-body"></div>`;
            return card;
        };
        if (settingsCard && settingsRow) {
            const pageCard = makeLayoutCard('Page settings', 'Define the certificate paper size and print margins.', 'fa-file-alt');
            const colorCard = makeLayoutCard('Color settings', 'Control certificate, placeholder, footer, and border colors.', 'fa-palette');
            const typographyCard = makeLayoutCard('Typography settings', 'Manage fonts, sizing, weight, alignment, and spacing.', 'fa-font');
            const titleCard = makeLayoutCard('Title settings', 'Customize the certificate title styling and spacing.', 'fa-heading');
            const positionCard = makeLayoutCard('Element position settings', 'Move certificate elements precisely in millimetres.', 'fa-arrows-alt', true);
            const alignmentCard = makeLayoutCard('Element alignment', 'Align the selected certificate element to the printable area.', 'fa-align-center', true);
            const pageBody = pageCard.querySelector('.layout-settings-card-body');
            const colorBody = colorCard.querySelector('.layout-settings-card-body');
            const typographyBody = typographyCard.querySelector('.layout-settings-card-body');
            const titleBody = titleCard.querySelector('.layout-settings-card-body');
            const positionBody = positionCard.querySelector('.layout-settings-card-body');
            const alignmentBody = alignmentCard.querySelector('.layout-settings-card-body');
            alignmentBody.innerHTML = '<div class="certificate-element-alignment"><div class="certificate-element-alignment-selected"><span class="certificate-position-kicker">Selected element</span><strong data-certificate-alignment-selected>Title</strong><small>Click an element in the preview, then choose an alignment.</small></div><div class="certificate-element-alignment-group"><label>Horizontal alignment</label><div class="certificate-align-tabs" data-certificate-alignment-axis="horizontal" role="group" aria-label="Horizontal alignment"><button type="button" class="certificate-align-tab" data-certificate-alignment="left">Left</button><button type="button" class="certificate-align-tab" data-certificate-alignment="center">Center</button><button type="button" class="certificate-align-tab" data-certificate-alignment="right">Right</button></div></div><div class="certificate-element-alignment-group"><label>Vertical alignment</label><div class="certificate-align-tabs" data-certificate-alignment-axis="vertical" role="group" aria-label="Vertical alignment"><button type="button" class="certificate-align-tab" data-certificate-alignment="top">Top</button><button type="button" class="certificate-align-tab" data-certificate-alignment="center">Center</button><button type="button" class="certificate-align-tab" data-certificate-alignment="bottom">Bottom</button></div></div></div>';
            const visibilityCard = makeLayoutCard('Visibility', 'Choose which certificate elements are shown in the final certificate.', 'fa-eye', true);
            const visibilityBody = visibilityCard.querySelector('.layout-settings-card-body');
            const visibilitySettings = @json(data_get($layout, 'visibility', []));
            const visibilityFields = [
                ['title', 'Certificate title', 'Show the certificate heading.'],
                ['body', 'Certificate body', 'Show the main certificate text.'],
                ['watermark', 'Watermark', 'Show the school watermark/logo.'],
                ['reason', 'Leaving reason', 'Show the reason block.'],
                ['principal', 'Principal block', 'Show the complete principal signature block.'],
                ['principal_name', 'Principal name', 'Show the principal name line.'],
                ['school_name', 'School name', 'Show the school name line.'],
                ['contact_number', 'Contact number', 'Show the single contact number.'],
            ];
            visibilityBody.innerHTML = `<div class="certificate-visibility-grid">${visibilityFields.map(([key, label, description]) => `<label class="certificate-visibility-item"><input type="hidden" form="certificateSettingsForm" name="layout_settings[visibility][${key}]" value="0"><input type="checkbox" form="certificateSettingsForm" name="layout_settings[visibility][${key}]" value="1" data-certificate-visibility="${key}" ${visibilitySettings[key] === false ? '' : 'checked'}><span class="certificate-visibility-switch" aria-hidden="true"></span><span><strong>${label}</strong><small>${description}</small></span></label>`).join('')}</div>`;
            colorBody.classList.add('row');
            const pageDetails = layoutDetails.filter(detail => /page and margins/i.test(detail.querySelector('summary')?.innerText || ''));
            pageDetails.forEach(detail => pageBody.appendChild(detail));

            layoutDetails.forEach(detail => {
                if (pageDetails.includes(detail)) return;
                const detailBody = detail.querySelector('.certificate-settings-section-body');
                if (!detailBody) return;
                Array.from(detailBody.children).forEach(field => {
                    if (field.querySelector('input[name*="color"], input[name*="background"]')) colorBody.appendChild(field);
                });
                if (detailBody.querySelector('input, select')) typographyBody.appendChild(detail);
            });

            if (titleSettings) {
                titleSettings.classList.remove('row', 'mt-2');
                titleSettings.classList.add('title-settings-stack');
                titleBody.appendChild(titleSettings);
            }

            const moveColorField = (source, name, label) => {
                const input = source?.querySelector(`[name="${name}"]`);
                const field = input?.closest('[class*="col-"]');
                if (!field) return;
                field.querySelector('label')?.replaceChildren(document.createTextNode(label));
                colorBody.appendChild(field);
            };
            moveColorField(titleSettings, 'layout_settings[typography][title_color]', 'Title color');
            moveColorField(titleSettings, 'layout_settings[typography][title_background]', 'Title background color');
            moveColorField(titleSettings, 'layout_settings[typography][title_border_color]', 'Title border color');

            if (principalSettings) typographyBody.appendChild(principalSettings);
            const moveTypographyColor = (source, name, label) => {
                const input = source?.querySelector(`[name="layout_settings[${name}][color]"]`);
                const field = input?.closest('[class*="col-"]');
                if (!field) return;
                field.querySelector('label')?.replaceChildren(document.createTextNode(label));
                colorBody.appendChild(field);
            };
            moveTypographyColor(principalSettings, 'footer][principal_label', 'Principal text color');
            moveTypographyColor(principalSettings, 'footer][school_name', 'School name color');
            moveTypographyColor(principalSettings, 'footer][contact_number', 'Contact number color');
            const headerSettings = document.createElement('div');
            headerSettings.className = 'certificate-header-settings row mt-2';
            const headerTypography = @json(data_get($layout, 'header', []));
            headerSettings.innerHTML = `
                <div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">School header typography</h6><div class="small text-muted">Style the Bangla and English school-name headings separately.</div></div>
                <div class="col-6 mb-3"><label class="form-label small">Logo width (px)</label><input form="certificateSettingsForm" type="number" min="16" max="200" step="1" name="layout_settings[header][logo][width]" class="form-control form-control-sm js-certificate-header-input" value="${headerTypography?.logo?.width ?? 48}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Logo height (px)</label><input form="certificateSettingsForm" type="number" min="16" max="200" step="1" name="layout_settings[header][logo][height]" class="form-control form-control-sm js-certificate-header-input" value="${headerTypography?.logo?.height ?? 48}"></div>
                <div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">Bangla heading</h6></div>
                <div class="col-6 mb-3"><label class="form-label small">Size (px)</label><input form="certificateSettingsForm" type="number" min="8" max="72" step="1" name="layout_settings[header][bangla][font_size]" class="form-control form-control-sm js-certificate-header-input" value="${headerTypography?.bangla?.font_size ?? 29}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Weight</label><select form="certificateSettingsForm" name="layout_settings[header][bangla][font_weight]" class="form-control form-control-sm js-certificate-header-input">${[300,400,500,600,700,800,900].map(weight => `<option value="${weight}" ${Number(headerTypography?.bangla?.font_weight ?? 800) === weight ? 'selected' : ''}>${weight}</option>`).join('')}</select></div>
                <div class="col-6 mb-3"><label class="form-label small">Color</label><input form="certificateSettingsForm" type="color" name="layout_settings[header][bangla][color]" class="form-control form-control-sm p-1 js-certificate-header-input" value="${headerTypography?.bangla?.color ?? '#111827'}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Alignment</label><select form="certificateSettingsForm" name="layout_settings[header][bangla][text_align]" class="form-control form-control-sm js-certificate-header-input"><option value="left" ${(headerTypography?.bangla?.text_align ?? 'left') === 'left' ? 'selected' : ''}>Left</option><option value="center" ${(headerTypography?.bangla?.text_align ?? 'left') === 'center' ? 'selected' : ''}>Center</option><option value="right" ${(headerTypography?.bangla?.text_align ?? 'left') === 'right' ? 'selected' : ''}>Right</option></select></div>
                <div class="col-12 mt-2 mb-2"><h6 class="font-weight-bold text-slate-800 mb-1">English heading</h6></div>
                <div class="col-6 mb-3"><label class="form-label small">Size (px)</label><input form="certificateSettingsForm" type="number" min="8" max="72" step="1" name="layout_settings[header][english][font_size]" class="form-control form-control-sm js-certificate-header-input" value="${headerTypography?.english?.font_size ?? 17}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Weight</label><select form="certificateSettingsForm" name="layout_settings[header][english][font_weight]" class="form-control form-control-sm js-certificate-header-input">${[300,400,500,600,700,800,900].map(weight => `<option value="${weight}" ${Number(headerTypography?.english?.font_weight ?? 800) === weight ? 'selected' : ''}>${weight}</option>`).join('')}</select></div>
                <div class="col-6 mb-3"><label class="form-label small">Color</label><input form="certificateSettingsForm" type="color" name="layout_settings[header][english][color]" class="form-control form-control-sm p-1 js-certificate-header-input" value="${headerTypography?.english?.color ?? '#111827'}"></div>
                <div class="col-6 mb-3"><label class="form-label small">Alignment</label><select form="certificateSettingsForm" name="layout_settings[header][english][text_align]" class="form-control form-control-sm js-certificate-header-input"><option value="left" ${(headerTypography?.english?.text_align ?? 'left') === 'left' ? 'selected' : ''}>Left</option><option value="center" ${(headerTypography?.english?.text_align ?? 'left') === 'center' ? 'selected' : ''}>Center</option><option value="right" ${(headerTypography?.english?.text_align ?? 'left') === 'right' ? 'selected' : ''}>Right</option></select></div>`;
            const headerGroups = [];
            const headerIntroFields = [];
            let activeHeaderGroup = null;
            Array.from(headerSettings.children).slice(1).forEach((child) => {
                const heading = child.querySelector('h6');
                if (heading) {
                    activeHeaderGroup = { title: heading.textContent.trim(), description: '', fields: [] };
                    headerGroups.push(activeHeaderGroup);
                    return;
                }
                if (activeHeaderGroup) activeHeaderGroup.fields.push(child);
                else headerIntroFields.push(child);
            });
            const headerParent = document.createElement('details');
            headerParent.className = 'certificate-settings-section';
            headerParent.open = false;
            const headerSummary = document.createElement('summary');
            headerSummary.innerHTML = '<span>School header typography<small>Style the Bangla and English school-name headings separately.</small></span>';
            const headerParentBody = document.createElement('div');
            headerParentBody.className = 'certificate-settings-section-body row';
            if (headerIntroFields.length) {
                const logoDetail = document.createElement('details');
                logoDetail.className = 'certificate-settings-section';
                logoDetail.open = true;
                const logoSummary = document.createElement('summary');
                logoSummary.innerHTML = '<span>School logo</span>';
                const logoBody = document.createElement('div');
                logoBody.className = 'certificate-settings-section-body row';
                headerIntroFields.forEach((field) => logoBody.appendChild(field));
                logoDetail.append(logoSummary, logoBody);
                headerParentBody.appendChild(logoDetail);
            }
            headerGroups.forEach((group) => {
                const detail = document.createElement('details');
                detail.className = 'certificate-settings-section';
                detail.open = false;
                const summary = document.createElement('summary');
                summary.innerHTML = `<span>${group.title}</span>`;
                const body = document.createElement('div');
                body.className = 'certificate-settings-section-body row';
                group.fields.forEach((field) => body.appendChild(field));
                detail.append(summary, body);
                headerParentBody.appendChild(detail);
            });
            headerParent.append(headerSummary, headerParentBody);
            headerSettings.replaceChildren(headerParent);
            moveTypographyColor(headerSettings, 'header][bangla', 'Bangla heading color');
            moveTypographyColor(headerSettings, 'header][english', 'English heading color');
            typographyBody.appendChild(headerSettings);

            const colorFields = new Map();
            colorBody.querySelectorAll('[name]').forEach((input) => {
                const field = input.closest('[class*="col-"]');
                if (field) colorFields.set(input.name, field);
            });
            const arrangeColorSection = (title, names) => {
                const fields = names.map((name) => colorFields.get(name)).filter(Boolean);
                if (!fields.length) return;
                const heading = document.createElement('div');
                heading.className = 'certificate-color-group-title';
                heading.textContent = title;
                colorBody.appendChild(heading);
                fields.forEach((field) => colorBody.appendChild(field));
            };
            colorBody.replaceChildren();
            arrangeColorSection('Global colors', [
                'layout_settings[typography][font_color]',
                'layout_settings[typography][placeholder_color]',
            ]);
            arrangeColorSection('Title colors', [
                'layout_settings[typography][title_color]',
                'layout_settings[typography][title_background]',
                'layout_settings[typography][title_border_color]',
            ]);
            arrangeColorSection('Footer colors', [
                'layout_settings[footer][reason][color]',
                'layout_settings[footer][principal][color]',
                'layout_settings[footer][principal_label][color]',
                'layout_settings[footer][school_name][color]',
                'layout_settings[footer][contact_number][color]',
            ]);
            arrangeColorSection('School header colors', [
                'layout_settings[header][bangla][color]',
                'layout_settings[header][english][color]',
            ]);

            if (positionToolbar) positionBody.appendChild(positionToolbar);
            settingsCard.querySelectorAll('input[type="hidden"][name*="[positions]"]').forEach(input => positionBody.appendChild(input));
            if (dragGuide) positionBody.appendChild(dragGuide);
            layoutContent.classList.add('layout-settings-grid');
            layoutContent.replaceChildren(pageCard, colorCard, typographyCard, titleCard, positionCard, alignmentCard, visibilityCard);

            const layoutSubtabs = document.createElement('div');
            layoutSubtabs.className = 'certificate-layout-subtabs';
            layoutSubtabs.setAttribute('role', 'tablist');
            layoutSubtabs.setAttribute('aria-label', 'Certificate layout settings');
            const layoutTabDefinitions = [
                ['page', 'Page', 'fa-file-alt', pageCard],
                ['colors', 'Colors', 'fa-palette', colorCard],
                ['typography', 'Type', 'fa-font', typographyCard],
                ['title', 'Title', 'fa-heading', titleCard],
                ['position', 'Position', 'fa-arrows-alt', positionCard],
                ['alignment', 'Align', 'fa-align-center', alignmentCard],
                ['visibility', 'Visibility', 'fa-eye', visibilityCard],
            ];
            layoutSubtabs.innerHTML = layoutTabDefinitions.map(([key, label, icon]) => `<button type="button" class="certificate-layout-subtab" role="tab" aria-selected="false" aria-controls="certificate-layout-settings-${key}" data-layout-subtab="${key}"><i class="fas ${icon} mr-1" aria-hidden="true"></i>${label}</button>`).join('');
            layoutTabDefinitions.forEach(([key, , , card]) => {
                card.id = `certificate-layout-settings-${key}`;
                card.setAttribute('role', 'tabpanel');
                card.dataset.layoutPanel = key;
            });
            const alignmentPaper = previewPanel.querySelector('#certificatePreviewPaper');
            const alignmentSelected = alignmentBody.querySelector('[data-certificate-alignment-selected]');
            const alignmentLabels = { title: 'Title', body: 'Body', bottom: 'Footer', watermark: 'Watermark' };
            const selectedAlignmentElement = () => alignmentPaper?.querySelector('[data-certificate-draggable].is-selected') || alignmentPaper?.querySelector(`[data-certificate-draggable="${positionToolbar?.querySelector('#certificateSelectedElement')?.value || 'title'}"]`);
            const refreshAlignmentSelection = () => {
                const element = selectedAlignmentElement();
                if (alignmentSelected) alignmentSelected.textContent = alignmentLabels[element?.dataset.certificateDraggable] || 'Title';
            };
            const applyElementAlignment = (axis, value) => {
                const element = selectedAlignmentElement();
                if (!element || !alignmentPaper) return;
                const key = element.dataset.certificateDraggable;
                const paperRect = alignmentPaper.getBoundingClientRect();
                const styles = getComputedStyle(alignmentPaper);
                const paddingLeft = parseFloat(styles.paddingLeft) || 0;
                const paddingRight = parseFloat(styles.paddingRight) || 0;
                const paddingTop = parseFloat(styles.paddingTop) || 0;
                const paddingBottom = parseFloat(styles.paddingBottom) || 0;
                const contentLeft = paperRect.left + paddingLeft;
                const contentRight = paperRect.right - paddingRight;
                const contentTop = paperRect.top + paddingTop;
                const contentBottom = paperRect.bottom - paddingBottom;
                const pageWidth = typeof certificateLayoutNumber === 'function' ? certificateLayoutNumber(['page', 'width'], 210) : 210;
                const pageHeight = typeof certificateLayoutNumber === 'function' ? certificateLayoutNumber(['page', 'height'], 297) : 297;
                const pxPerMmX = paperRect.width / Math.max(pageWidth, 1);
                const pxPerMmY = paperRect.height / Math.max(pageHeight, 1);
                const currentX = parseFloat(page.querySelector(`[data-certificate-position="${key}"][data-axis="x"]`)?.value || 0) || 0;
                const currentY = parseFloat(page.querySelector(`[data-certificate-position="${key}"][data-axis="y"]`)?.value || 0) || 0;
                const rect = element.getBoundingClientRect();
                const baseLeft = rect.left - currentX * pxPerMmX;
                const baseTop = rect.top - currentY * pxPerMmY;
                const xTarget = value === 'left' ? contentLeft : value === 'right' ? contentRight - rect.width : contentLeft + (contentRight - contentLeft - rect.width) / 2;
                const yTarget = value === 'top' ? contentTop : value === 'bottom' ? contentBottom - rect.height : contentTop + (contentBottom - contentTop - rect.height) / 2;
                const input = page.querySelector(`[data-certificate-position="${key}"][data-axis="${axis === 'horizontal' ? 'x' : 'y'}"]`);
                if (!input) return;
                input.value = ((axis === 'horizontal' ? xTarget - baseLeft : yTarget - baseTop) / (axis === 'horizontal' ? pxPerMmX : pxPerMmY)).toFixed(2);
                input.dispatchEvent(new Event('input', { bubbles: true }));
                if (typeof applyCertificateLayout === 'function') applyCertificateLayout();
                refreshAlignmentSelection();
                if (typeof setStatus === 'function') setStatus(true, 'Alignment changed — save to keep it');
            };
            alignmentBody.querySelectorAll('[data-certificate-alignment]').forEach(button => {
                button.addEventListener('click', () => applyElementAlignment(button.closest('[data-certificate-alignment-axis]')?.dataset.certificateAlignmentAxis || 'horizontal', button.dataset.certificateAlignment));
            });
            alignmentPaper?.addEventListener('click', refreshAlignmentSelection);
            refreshAlignmentSelection();
            layoutPanel.insertBefore(layoutSubtabs, layoutContent);
            layoutContent.classList.add('layout-settings-tabs');
            const activateLayoutSubtab = key => {
                layoutSubtabs.querySelectorAll('[data-layout-subtab]').forEach(tab => {
                    const active = tab.dataset.layoutSubtab === key;
                    tab.classList.toggle('is-active', active);
                    tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    tab.tabIndex = active ? 0 : -1;
                });
                layoutContent.querySelectorAll('[data-layout-panel]').forEach(panel => {
                    panel.hidden = panel.dataset.layoutPanel !== key;
                });
            };
            layoutSubtabs.querySelectorAll('[data-layout-subtab]').forEach(tab => {
                tab.addEventListener('click', () => activateLayoutSubtab(tab.dataset.layoutSubtab));
                tab.addEventListener('keydown', event => {
                    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
                    event.preventDefault();
                    const tabs = Array.from(layoutSubtabs.querySelectorAll('[data-layout-subtab]'));
                    const currentIndex = tabs.indexOf(tab);
                    const nextIndex = event.key === 'ArrowRight'
                        ? (currentIndex + 1) % tabs.length
                        : (currentIndex - 1 + tabs.length) % tabs.length;
                    tabs[nextIndex].focus();
                    activateLayoutSubtab(tabs[nextIndex].dataset.layoutSubtab);
                });
            });
            activateLayoutSubtab('typography');
        }
        workspace.remove();

        const activateTab = name => {
            tabShell.querySelectorAll('[data-editor-tab]').forEach(tab => {
                const active = tab.dataset.editorTab === name;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            tabShell.querySelectorAll('[data-editor-panel]').forEach(panel => {
                const active = panel.dataset.editorPanel === name;
                panel.classList.toggle('is-active', active);
                panel.hidden = !active;
            });
        };
        tabShell.querySelectorAll('[data-editor-tab]').forEach(tab => tab.addEventListener('click', () => activateTab(tab.dataset.editorTab)));

        const saveButton = page.querySelector('[data-certificate-action-save]');
        page.querySelectorAll('#saveCertificateLayout, #saveCertificatePreviewText, #saveCertificateLayoutSidebar, #certificateSettingsForm button[type="submit"], [data-editor-panel="content"] form button[type="submit"]').forEach(button => {
            button.classList.add('certificate-secondary-save');
            button.setAttribute('aria-hidden', 'true');
            button.tabIndex = -1;
        });
        if (saveButton) {
            const replacement = saveButton.cloneNode(true);
            saveButton.replaceWith(replacement);
            page.querySelector('[data-certificate-action-preview]')?.remove();
            replacement.innerHTML = '<i class="fas fa-save mr-1" aria-hidden="true"></i> Save changes';
            replacement.addEventListener('click', () => {
                const active = tabShell.querySelector('[data-editor-tab].is-active')?.dataset.editorTab;
                if (active === 'content') {
                    window.syncCertificatePreviewText?.();
                    const generalForm = document.getElementById('certificateSettingsForm');
                    const templateForm = templateCard.querySelector('form');
                    const syncHidden = (name, value) => {
                        let hidden = generalForm?.querySelector(`[data-certificate-template-sync="${name}"]`);
                        if (!hidden && generalForm) {
                            hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = name;
                            hidden.dataset.certificateTemplateSync = name;
                            generalForm.appendChild(hidden);
                        }
                        if (hidden) hidden.value = value || '';
                    };
                    syncHidden('template_name', templateForm?.querySelector('[name="name"]')?.value);
                    syncHidden('template_body', templateForm?.querySelector('[name="body"]')?.value);
                    generalForm?.requestSubmit();
                }
                else document.getElementById('certificateSettingsForm')?.requestSubmit();
            });
        }
    });
</script>
<style>
    .certificate-element-settings-modal { position: fixed; inset: 0; z-index: 2060; display: none; align-items: center; justify-content: center; padding: 1rem; background: rgba(15, 23, 42, .5); }
    .certificate-element-settings-modal.is-open { display: flex; }
    .certificate-element-settings-dialog { width: min(100%, 560px); max-height: min(720px, calc(100vh - 2rem)); overflow: auto; border: 1px solid #c7d2fe; border-radius: 1rem; background: #fff; box-shadow: 0 24px 70px rgba(15, 23, 42, .3); }
    .certificate-element-settings-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1rem 1.15rem; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #eef2ff 0%, #fff 100%); }
    .certificate-element-settings-header h5 { margin: 0; color: #312e81; font-weight: 800; }
    .certificate-element-settings-header p { margin: .2rem 0 0; color: #64748b; font-size: .78rem; }
    .certificate-element-settings-close { border: 0; background: transparent; color: #64748b; font-size: 1.1rem; }
    .certificate-element-settings-body { padding: 1rem 1.15rem; }
    .certificate-element-settings-content { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; }
    .certificate-element-settings-content h6 { margin: 0 0 .55rem; color: #1e293b; font-size: .82rem; font-weight: 800; }
    .certificate-element-settings-content .form-control { border-color: #cbd5e1; border-radius: .55rem; }
    .certificate-element-settings-content textarea { min-height: 150px; resize: vertical; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: .78rem; line-height: 1.55; }
    .certificate-element-placeholder-toolbar { display: flex; align-items: center; justify-content: space-between; gap: .6rem; margin: 1rem 0 .6rem; }
    .certificate-element-placeholder-toolbar .form-control { min-height: 34px; font-size: .78rem; }
    .certificate-element-placeholder-group { margin-bottom: .7rem; }
    .certificate-element-placeholder-group-title { margin-bottom: .35rem; color: #64748b; font-size: .68rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .certificate-element-placeholder-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .45rem; max-height: 260px; overflow: auto; padding: .5rem; border: 1px solid #e2e8f0; border-radius: .55rem; background: #f8fafc; }
    .certificate-element-placeholder-chip { display: flex; min-width: 0; flex-direction: column; align-items: flex-start; gap: .15rem; border: 1px solid #cbd5e1; border-radius: .6rem; background: #fff; color: #334155; padding: .45rem .55rem; text-align: left; }
    .certificate-element-placeholder-chip:hover { border-color: #6366f1; background: #eef2ff; }
    .certificate-element-placeholder-chip strong { max-width: 100%; overflow: hidden; color: #334155; font-size: .72rem; text-overflow: ellipsis; white-space: nowrap; }
    .certificate-element-placeholder-chip code { max-width: 100%; overflow: hidden; color: #4338ca; font-size: .65rem; text-overflow: ellipsis; white-space: nowrap; }
    .certificate-element-placeholder-chip small { max-width: 100%; overflow: hidden; color: #64748b; font-size: .65rem; text-overflow: ellipsis; white-space: nowrap; }
    .certificate-element-settings-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; }
    .certificate-element-settings-field label { display: block; margin-bottom: .25rem; color: #475569; font-size: .72rem; font-weight: 700; }
    .certificate-element-settings-field .form-control { min-height: 36px; border-color: #cbd5e1; border-radius: .5rem; font-size: .84rem; }
    .certificate-element-settings-field .form-control:focus { border-color: #818cf8; box-shadow: 0 0 0 .18rem rgba(99, 102, 241, .12); }
    .certificate-element-settings-footer { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .8rem 1.15rem; border-top: 1px solid #e2e8f0; background: #f8fafc; }
    .certificate-element-settings-footer small { color: #64748b; }
    @media (max-width: 575.98px) { .certificate-element-settings-grid, .certificate-element-placeholder-list { grid-template-columns: 1fr; } .certificate-element-settings-footer { align-items: stretch; flex-direction: column; } .certificate-element-settings-footer .btn { width: 100%; } }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const page = document.querySelector('.certificate-editor-page');
        const paper = document.getElementById('certificatePreviewPaper');
        if (!page || !paper) return;
        return;

        const modal = document.createElement('div');
        modal.className = 'certificate-element-settings-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'certificateElementSettingsTitle');
        modal.innerHTML = '<div class="certificate-element-settings-dialog"><div class="certificate-element-settings-header"><div><h5 id="certificateElementSettingsTitle">Element settings</h5><p data-element-settings-description>Adjust this preview element without leaving the certificate.</p></div><button type="button" class="certificate-element-settings-close" aria-label="Close element settings"><i class="fas fa-times"></i></button></div><div class="certificate-element-settings-body"><div class="certificate-element-settings-grid" data-element-settings-fields></div><div class="certificate-element-settings-content" data-element-settings-content></div></div><div class="certificate-element-settings-footer"><small><i class="fas fa-info-circle mr-1"></i>Changes are live and saved with the sticky Save changes button.</small><button type="button" class="btn btn-primary btn-sm" data-element-settings-done>Done</button></div></div>';
        page.appendChild(modal);

        const definitions = {
            title: { title: 'Title settings', description: 'Style the certificate title and move it precisely.', fields: [
                ['Background color', 'color', 'layout_settings[typography][title_background]'], ['Font color', 'color', 'layout_settings[typography][title_color]'],
                ['Font family', 'select', 'layout_settings[typography][title_font_family]', ['Arial, Helvetica, sans-serif', 'Georgia, Times New Roman, serif', 'Times New Roman, Times, serif', 'Verdana, Geneva, sans-serif']],
                ['Font size (px)', 'number', 'layout_settings[typography][title_size]', null, '8', '72'], ['Weight', 'select', 'layout_settings[typography][title_font_weight]', ['300', '400', '500', '600', '700', '800', '900']],
                ['Alignment', 'select', 'layout_settings[typography][title_text_align]', ['left', 'center', 'right']], ['X position (mm)', 'number', 'layout_settings[positions][title][x]', null, '-100', '100', '.5'], ['Y position (mm)', 'number', 'layout_settings[positions][title][y]', null, '-100', '100', '.5']
            ]},
            body: { title: 'Body text settings', description: 'Adjust the certificate wording typography and placement.', fields: [
                ['Font color', 'color', 'layout_settings[typography][font_color]'], ['Font family', 'select', 'layout_settings[typography][font_family]', ['Georgia, Times New Roman, serif', 'Arial, Helvetica, sans-serif', 'Times New Roman, Times, serif', 'Verdana, Geneva, sans-serif', 'DejaVu Serif, serif', 'DejaVu Sans, sans-serif']],
                ['Font size (px)', 'number', 'layout_settings[typography][body_size]', null, '8', '48'], ['Weight', 'select', 'layout_settings[typography][font_weight]', ['300', '400', '500', '600', '700', '800', '900']], ['Alignment', 'select', 'layout_settings[typography][text_align]', ['left', 'center', 'right', 'justify']], ['Line height', 'number', 'layout_settings[typography][line_height]', null, '1', '3', '.05'], ['X position (mm)', 'number', 'layout_settings[positions][body][x]', null, '-100', '100', '.5'], ['Y position (mm)', 'number', 'layout_settings[positions][body][y]', null, '-100', '100', '.5']
            ]},
            bottom: { title: 'Footer settings', description: 'Style the principal footer block and adjust its placement.', fields: [
                ['Font color', 'color', 'layout_settings[footer][principal][color]'], ['Font family', 'select', 'layout_settings[footer][principal][font_family]', ['Arial, Helvetica, sans-serif', 'Georgia, Times New Roman, serif', 'Times New Roman, Times, serif', 'Verdana, Geneva, sans-serif']],
                ['Font size (px)', 'number', 'layout_settings[footer][principal][font_size]', null, '8', '48'], ['Weight', 'select', 'layout_settings[footer][principal][font_weight]', ['300', '400', '500', '600', '700', '800', '900']], ['Alignment', 'select', 'layout_settings[footer][principal][text_align]', ['left', 'center', 'right']], ['X position (mm)', 'number', 'layout_settings[positions][bottom][x]', null, '-100', '100', '.5'], ['Y position (mm)', 'number', 'layout_settings[positions][bottom][y]', null, '-100', '100', '.5']
            ]},
            watermark: { title: 'Watermark settings', description: 'Adjust watermark visibility, scale, and position.', fields: [
                ['Opacity', 'number', 'layout_settings[watermark][opacity]', null, '0', '1', '.01'], ['Size (%)', 'number', 'layout_settings[watermark][size]', null, '10', '100', '1'], ['X position (mm)', 'number', 'layout_settings[positions][watermark][x]', null, '-100', '100', '.5'], ['Y position (mm)', 'number', 'layout_settings[positions][watermark][y]', null, '-100', '100', '.5']
            ]}
        };

        const sourceInput = name => document.getElementsByName(name)[0];
        const fieldsContainer = modal.querySelector('[data-element-settings-fields]');
        const contentContainer = modal.querySelector('[data-element-settings-content]');
        const close = () => { modal.classList.remove('is-open'); document.body.classList.remove('certificate-element-settings-open'); };
        const open = key => {
            const definition = definitions[key];
            if (!definition) return;
            modal.querySelector('#certificateElementSettingsTitle').textContent = definition.title;
            modal.querySelector('[data-element-settings-description]').textContent = definition.description;
            fieldsContainer.replaceChildren();
            contentContainer.replaceChildren();
            definition.fields.forEach(([label, type, name, options, min, max, step]) => {
                const source = sourceInput(name);
                if (!source) return;
                const field = document.createElement('div');
                field.className = 'certificate-element-settings-field';
                const labelElement = document.createElement('label');
                labelElement.textContent = label;
                const control = type === 'select' ? document.createElement('select') : document.createElement('input');
                control.className = 'form-control form-control-sm';
                control.dataset.elementSettingsSource = name;
                control.value = source.value;
                if (type === 'select') options.forEach(option => { const optionElement = document.createElement('option'); optionElement.value = option; optionElement.textContent = option.split(',')[0]; control.appendChild(optionElement); });
                else { control.type = type; if (min !== undefined) control.min = min; if (max !== undefined) control.max = max; if (step !== undefined) control.step = step; }
                control.addEventListener('input', () => { source.value = control.value; source.dispatchEvent(new Event('input', { bubbles: true })); if (typeof applyCertificateLayout === 'function') applyCertificateLayout(); });
                field.append(labelElement, control);
                fieldsContainer.appendChild(field);
            });
            if (key === 'body') {
                const selector = document.getElementById('certificatePreviewTemplate');
                const bodySource = selector ? document.getElementById(selector.value) : document.querySelector('.js-certificate-editor');
                if (bodySource) {
                    const heading = document.createElement('h6');
                    heading.textContent = 'Existing template text';
                    const editor = document.createElement('textarea');
                    editor.className = 'form-control';
                    editor.value = bodySource.value;
                    editor.setAttribute('aria-label', 'Existing template text');
                    editor.addEventListener('input', () => {
                        bodySource.value = editor.value;
                        bodySource.dispatchEvent(new Event('input', { bubbles: true }));
                        if (typeof updateCertificatePreview === 'function') updateCertificatePreview();
                    });
                    contentContainer.append(heading, editor);

                    const toolbar = document.createElement('div');
                    toolbar.className = 'certificate-element-placeholder-toolbar';
                    toolbar.innerHTML = '<h6 class="mb-0">Placeholders by type</h6><input type="search" class="form-control form-control-sm" placeholder="Search placeholders" aria-label="Search placeholders" style="max-width:220px">';
                    contentContainer.appendChild(toolbar);
                    const library = document.createElement('div');
                    library.className = 'certificate-element-placeholder-library';
                    const sourceGroups = document.querySelectorAll('[data-editor-panel="content"] [data-placeholder-group]');
                    sourceGroups.forEach(sourceGroup => {
                        const sourceButtons = sourceGroup.querySelectorAll('.js-certificate-placeholder');
                        if (!sourceButtons.length) return;
                        const group = document.createElement('div');
                        group.className = 'certificate-element-placeholder-group';
                        const title = document.createElement('div');
                        title.className = 'certificate-element-placeholder-group-title';
                        title.textContent = sourceGroup.querySelector('.small')?.textContent.trim() || 'Placeholders';
                        const list = document.createElement('div');
                        list.className = 'certificate-element-placeholder-list';
                        sourceButtons.forEach(sourceButton => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'certificate-element-placeholder-chip';
                            const token = sourceButton.dataset.token || '';
                            const label = sourceButton.textContent.trim();
                            const sample = typeof certificateSampleValues !== 'undefined' ? (certificateSampleValues[token] || 'Sample value not available') : 'Click to insert';
                            const labelElement = document.createElement('strong');
                            const tokenElement = document.createElement('code');
                            const sampleElement = document.createElement('small');
                            labelElement.textContent = label;
                            tokenElement.textContent = token;
                            sampleElement.textContent = sample;
                            button.append(labelElement, tokenElement, sampleElement);
                            button.dataset.search = `${sourceButton.dataset.search || label.toLowerCase()} ${token} ${sample}`.toLowerCase();
                            button.title = `${label}: ${token}`;
                            button.addEventListener('click', () => {
                                const start = editor.selectionStart ?? editor.value.length;
                                const end = editor.selectionEnd ?? editor.value.length;
                                const before = editor.value.substring(0, start);
                                const after = editor.value.substring(end);
                                const insert = `${before.length && !/\s$/.test(before) ? ' ' : ''}${token}${after.length && !/^\s/.test(after) ? ' ' : ''}`;
                                editor.value = before + insert + after;
                                editor.focus();
                                editor.setSelectionRange(before.length + insert.length, before.length + insert.length);
                                editor.dispatchEvent(new Event('input', { bubbles: true }));
                            });
                            list.appendChild(button);
                        });
                        group.append(title, list);
                        library.appendChild(group);
                    });
                    contentContainer.appendChild(library);
                    toolbar.querySelector('input')?.addEventListener('input', event => {
                        const query = event.target.value.trim().toLowerCase();
                        library.querySelectorAll('.certificate-element-placeholder-group').forEach(group => {
                            let visible = 0;
                            group.querySelectorAll('.certificate-element-placeholder-chip').forEach(button => {
                                const match = !query || button.dataset.search.includes(query);
                                button.hidden = !match;
                                if (match) visible++;
                            });
                            group.hidden = visible === 0;
                        });
                    });
                }
            }
            modal.classList.add('is-open');
            document.body.classList.add('certificate-element-settings-open');
            fieldsContainer.querySelector('input, select')?.focus();
        };
        paper.querySelectorAll('[data-certificate-draggable]').forEach(element => {
            const key = element.dataset.certificateDraggable;
            element.setAttribute('title', 'Click to edit element settings');
            element.addEventListener('click', () => open(key));
        });
        const watermark = paper.querySelector('[data-certificate-draggable="watermark"]');
        paper.addEventListener('click', event => {
            if (!watermark) return;
            const rect = watermark.getBoundingClientRect();
            const insideWatermark = event.clientX >= rect.left && event.clientX <= rect.right && event.clientY >= rect.top && event.clientY <= rect.bottom;
            const clickedTitle = event.target.closest('[data-certificate-draggable="title"]');
            const clickedFooter = event.target.closest('[data-certificate-draggable="bottom"]');
            if (insideWatermark && !clickedTitle && !clickedFooter) {
                event.stopPropagation();
                open('watermark');
            }
        }, true);
        modal.querySelector('.certificate-element-settings-close')?.addEventListener('click', close);
        modal.querySelector('[data-element-settings-done]')?.addEventListener('click', close);
        modal.addEventListener('click', event => { if (event.target === modal) close(); });
        document.addEventListener('keydown', event => { if (event.key === 'Escape' && modal.classList.contains('is-open')) close(); });
    });
</script>
<style>
    .certificate-editor-page.certificate-single-workspace .certificate-placeholder-card > div:first-child {
        display: block !important;
        width: 100%;
        padding-bottom: .9rem;
        border-bottom: 1px solid #eef2ff;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-placeholder-card > div:first-child > div:first-child {
        width: 100%;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-placeholder-card > div:first-child > div[style*="min-width"] {
        width: 100%;
        min-width: 0 !important;
        margin-top: .85rem;
    }
    .certificate-editor-page.certificate-single-workspace #certificatePlaceholderSearch,
    .certificate-editor-page.certificate-single-workspace #certificatePlaceholderSearch + .form-control {
        width: 100%;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-placeholder-card .input-group {
        display: flex !important;
        flex-wrap: nowrap !important;
        align-items: stretch;
        width: 100%;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-placeholder-card .input-group-prepend {
        display: flex;
        flex: 0 0 auto;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-placeholder-card .input-group .form-control {
        flex: 1 1 auto;
        min-width: 0;
        width: auto !important;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-placeholder-card .input-group-text,
    .certificate-editor-page.certificate-single-workspace .certificate-placeholder-card #certificatePlaceholderSearch {
        min-height: 40px;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card .certificate-settings-section-body,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row,
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row {
        grid-template-columns: minmax(0, 1fr) !important;
    }
    .certificate-editor-page.certificate-single-workspace .layout-settings-card .certificate-settings-section-body > [class*="col-"],
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body.row > [class*="col-"],
    .certificate-editor-page.certificate-single-workspace .layout-settings-card-body > .row > [class*="col-"] {
        grid-column: 1 / -1 !important;
        width: auto !important;
        max-width: 100% !important;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: .85rem .75rem !important;
        padding: 1rem !important;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] {
        grid-column: span 1 !important;
        padding: 0 !important;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-page-group-title {
        grid-column: 1 / -1;
        margin: .15rem 0 -.1rem;
        padding-bottom: .35rem;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] .form-label {
        min-height: 1.35rem;
        margin-bottom: .35rem;
        color: #475569;
        font-size: .7rem;
        line-height: 1.25;
    }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body input.form-control {
        min-height: 42px;
        border-radius: .65rem;
        background: #fff;
        text-align: left;
    }
    .certificate-editor-page .certificate-align-tabs {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .2rem;
        width: 100%;
        padding: .2rem;
        border: 1px solid #cbd5e1;
        border-radius: .55rem;
        background: #f1f5f9;
    }
    .certificate-editor-page .certificate-align-tabs[data-option-count="3"] {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .certificate-editor-page .certificate-align-tab {
        min-width: 0;
        min-height: 32px;
        padding: .3rem .25rem;
        overflow: hidden;
        border: 0;
        border-radius: .38rem;
        color: #64748b;
        background: transparent;
        font-size: .68rem;
        font-weight: 700;
        line-height: 1.15;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: color .15s ease, background-color .15s ease, box-shadow .15s ease;
    }
    .certificate-editor-page .certificate-align-tab:hover {
        color: #4338ca;
        background: #e0e7ff;
    }
    .certificate-editor-page .certificate-align-tab.is-active {
        color: #3730a3;
        background: #fff;
        box-shadow: 0 1px 4px rgba(30, 41, 59, .12);
    }
    .certificate-editor-page .certificate-alignment-source {
        display: none !important;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel select.certificate-alignment-source {
        display: none !important;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-layout-panel select.certificate-alignment-source + .select2-container {
        display: none !important;
    }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .65rem; }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-item { display: flex; align-items: flex-start; gap: .6rem; min-height: 58px; margin: 0; padding: .7rem .75rem; border: 1px solid #e2e8f0; border-radius: .7rem; background: #f8fafc; cursor: pointer; }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-item:hover { border-color: #a5b4fc; background: #eef2ff; }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-item input[type="checkbox"] { position: absolute; opacity: 0; pointer-events: none; }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-switch { position: relative; flex: 0 0 auto; width: 2rem; height: 1.15rem; margin-top: .05rem; border-radius: 999px; background: #cbd5e1; transition: background .15s ease; }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-switch::after { content: ''; position: absolute; top: .15rem; left: .15rem; width: .85rem; height: .85rem; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(15, 23, 42, .2); transition: transform .15s ease; }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-item input[type="checkbox"]:checked + .certificate-visibility-switch { background: #2563eb; }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-item input[type="checkbox"]:checked + .certificate-visibility-switch::after { transform: translateX(.85rem); }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-item strong { display: block; color: #1e293b; font-size: .78rem; line-height: 1.25; }
    .certificate-editor-page.certificate-single-workspace .certificate-visibility-item small { display: block; margin-top: .2rem; color: #64748b; font-size: .68rem; line-height: 1.35; }
    @media (max-width: 575.98px) { .certificate-editor-page.certificate-single-workspace .certificate-visibility-grid { grid-template-columns: 1fr; } }
    .certificate-editor-page .certificate-header { position: relative !important; z-index: 2 !important; margin: 0 0 24px; color: #111827; font-family: Arial, Helvetica, sans-serif; line-height: 1.15; }
    .certificate-editor-page .certificate-header-slogan { min-height: 18px; margin-bottom: 5px; text-align: right; font-family: 'Patrick Hand', cursive; font-size: 18px; font-weight: 400; letter-spacing: .04em; }
    .certificate-editor-page .certificate-header-main { display: flex; align-items: flex-end; gap: 10px; padding: 5px 0 8px; border-bottom: 1px solid #111827; }
    .certificate-editor-page .certificate-header-logo { flex: 0 0 auto; width: 48px; height: 48px; object-fit: contain; }
    .certificate-editor-page .certificate-header-names { flex: 1 1 auto; min-width: 0; }
    .certificate-editor-page .certificate-header-bangla { font-family: 'Noto Sans Bengali', sans-serif; font-size: clamp(18px, 2.2vw, 29px); font-weight: 800; line-height: 1.05; white-space: nowrap; }
    .certificate-editor-page .certificate-header-english { margin-top: 3px; font-size: clamp(11px, 1.25vw, 17px); font-weight: 800; letter-spacing: .04em; white-space: nowrap; }
    .certificate-editor-page .certificate-header-meta { flex: 0 0 31%; min-width: 145px; padding-left: 10px; border-left: 1px solid #111827; font-size: 9px; line-height: 1.35; }
    .certificate-editor-page .certificate-header-meta div { overflow-wrap: anywhere; }
    .certificate-editor-page .certificate-position-toolbar { padding: 1rem; border: 1px solid #bfdbfe; border-radius: .9rem; background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%); }
    .certificate-editor-page .certificate-position-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: .9rem; }
    .certificate-editor-page .certificate-position-heading strong,
    .certificate-editor-page .certificate-position-heading small { display: block; }
    .certificate-editor-page .certificate-position-heading strong { color: #1e3a8a; font-size: .88rem; font-weight: 800; }
    .certificate-editor-page .certificate-position-heading small { margin-top: .2rem; color: #64748b; font-size: .7rem; }
    .certificate-editor-page .certificate-position-kicker { display: block; margin-bottom: .2rem; color: #2563eb; font-size: .65rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .certificate-editor-page .certificate-position-reset { flex: 0 0 auto; padding: .3rem .55rem !important; border: 1px solid #bfdbfe; border-radius: .5rem; color: #1d4ed8; background: #fff; font-weight: 700; }
    .certificate-editor-page .certificate-position-reset:hover { color: #1e3a8a; background: #dbeafe; }
    .certificate-editor-page .certificate-position-grid { display: grid !important; grid-template-columns: minmax(150px, 1.15fr) minmax(120px, 1fr) minmax(120px, 1fr) !important; gap: .75rem; margin: 0 !important; }
    .certificate-editor-page .certificate-position-field { min-width: 0; }
    .certificate-editor-page .certificate-position-field label { display: block; min-height: 2.1em; margin-bottom: .35rem; color: #334155; font-size: .7rem; font-weight: 800; line-height: 1.25; }
    .certificate-editor-page .certificate-position-input { position: relative; }
    .certificate-editor-page .certificate-position-input .form-control { padding-right: 2rem; text-align: center; }
    .certificate-editor-page .certificate-position-input span { position: absolute; top: 50%; right: .7rem; transform: translateY(-50%); color: #64748b; font-size: .7rem; font-weight: 700; pointer-events: none; }
    .certificate-editor-page .certificate-element-alignment { display: grid; gap: .9rem; padding: .85rem; border: 1px solid #c7d2fe; border-radius: .8rem; background: linear-gradient(135deg, #eef2ff 0%, #f8fafc 100%); }
    .certificate-editor-page .certificate-element-alignment-selected { display: flex; flex-direction: column; gap: .15rem; padding-bottom: .7rem; border-bottom: 1px solid #dbeafe; }
    .certificate-editor-page .certificate-element-alignment-selected strong { color: #1e3a8a; font-size: .9rem; }
    .certificate-editor-page .certificate-element-alignment-selected small { color: #64748b; font-size: .7rem; }
    .certificate-editor-page .certificate-element-alignment-group { display: grid; gap: .35rem; }
    .certificate-editor-page .certificate-element-alignment-group > label { margin: 0; color: #334155; font-size: .72rem; font-weight: 800; }
    .certificate-editor-page .certificate-element-alignment .certificate-align-tabs { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    @media (max-width: 575.98px) { .certificate-editor-page .certificate-position-grid { grid-template-columns: 1fr !important; } }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body { gap: .55rem .7rem !important; padding: .75rem !important; background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%); }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-page-group-title { display: flex; align-items: center; gap: .5rem; margin: .15rem 0 -.05rem !important; padding: .35rem 0 .3rem !important; border: 0 !important; color: #4338ca; font-size: .64rem; font-weight: 900; letter-spacing: .1em; }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-page-group-title::after { content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, #c7d2fe, transparent); }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] { padding: 0 !important; }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] .form-label { min-height: 0; margin-bottom: .28rem; color: #475569; font-size: .67rem; font-weight: 800; }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body input.form-control { min-height: 38px; padding: .38rem .65rem; border-color: #cbd5e1; border-radius: .58rem; font-size: .78rem; box-shadow: 0 1px 2px rgba(15, 23, 42, .02); }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body input.form-control:focus { border-color: #818cf8; box-shadow: 0 0 0 .16rem rgba(99, 102, 241, .12); }
    .certificate-editor-page.certificate-single-workspace .certificate-color-group-title { grid-column: 1 / -1 !important; margin: .45rem 0 .6rem !important; padding: .35rem 0 .55rem; border-bottom: 1px solid #e0e7ff; color: #4338ca; font-size: .68rem; font-weight: 900; letter-spacing: .04em; }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] { grid-column: span 1 !important; }
    @media (max-width: 380px) {
        .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body { grid-template-columns: 1fr !important; }
        .certificate-editor-page.certificate-single-workspace #certificate-layout-settings-page .certificate-settings-section-body > [class*="col-"] { grid-column: 1 / -1 !important; }
    }
    .certificate-editor-page .certificate-editor-tabs {
        margin-left: 10px !important;
        margin-right: 10px !important;
    }
    @media (min-width: 992px) {
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] {
            width: calc(100% - 20px) !important;
            margin-left: 10px !important;
            margin-right: 10px !important;
            border: 1px solid #dbe3ef !important;
            border-radius: 1rem !important;
            overflow: hidden;
        }
        .certificate-editor-page.certificate-single-workspace [data-certificate-panel="preview"] > .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .certificate-editor-page.certificate-single-workspace .certificate-editor-tabs {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const page = document.querySelector('.certificate-editor-page');
        if (!page) return;

        const layoutRoot = page.querySelector('.certificate-layout-panel');
        if (!layoutRoot) return;

        const previewPaper = page.querySelector('#certificatePreviewPaper');
        if (previewPaper && !previewPaper.querySelector('.certificate-header')) {
            const headerData = {
                logo: @json($setting?->logo ? asset($setting->logo) : ''),
                slogan: @json($setting?->slogan ?? ''),
                nameBn: @json($setting?->name_bn ?? ''),
                nameEn: @json($setting?->name ?? ''),
                addressBn: @json($setting?->address_bn ?? ''),
                addressEn: @json($setting?->address ?? ''),
                schoolCode: @json($setting?->school_code ?? ''),
                ipemisCode: @json($setting?->ipemis_code ?? ''),
            };
            const header = document.createElement('header');
            header.className = 'certificate-header';
            const meta = [headerData.addressBn, headerData.addressEn].filter(Boolean);
            if (headerData.schoolCode) meta.push(`School Code: ${headerData.schoolCode}`);
            if (headerData.ipemisCode) meta.push(`IPEMIS Code: ${headerData.ipemisCode}`);
            header.innerHTML = `<div class="certificate-header-slogan">${headerData.slogan || ''}</div><div class="certificate-header-main">${headerData.logo ? `<img class="certificate-header-logo" src="${headerData.logo}" alt="">` : ''}<div class="certificate-header-names">${headerData.nameBn ? `<div class="certificate-header-bangla"></div>` : ''}${headerData.nameEn ? `<div class="certificate-header-english"></div>` : ''}</div><div class="certificate-header-meta">${meta.map((line) => `<div></div>`).join('')}</div></div>`;
            const bangla = header.querySelector('.certificate-header-bangla');
            const english = header.querySelector('.certificate-header-english');
            const metaLines = header.querySelectorAll('.certificate-header-meta > div');
            if (bangla) bangla.textContent = headerData.nameBn;
            if (english) english.textContent = headerData.nameEn;
            meta.forEach((line, index) => { if (metaLines[index]) metaLines[index].textContent = line; });
            previewPaper.insertBefore(header, previewPaper.firstChild);
        }

        page.querySelector('#certificatePlaceholderSearch')?.closest('.bg-white')?.classList.add('certificate-placeholder-card');

        layoutRoot.querySelectorAll('.layout-settings-card .certificate-settings-section-body, .layout-settings-card-body.row, .layout-settings-card-body > .row').forEach((grid) => {
            const isTwoColumnSettingsGrid = (grid.closest('#certificate-layout-settings-page') && grid.classList.contains('certificate-settings-section-body')) || grid.closest('#certificate-layout-settings-colors') || grid.closest('#certificate-layout-settings-typography') || grid.closest('#certificate-layout-settings-title');
            grid.style.setProperty('display', 'grid', 'important');
            grid.style.setProperty('grid-template-columns', isTwoColumnSettingsGrid ? 'repeat(2, minmax(0, 1fr))' : 'minmax(0, 1fr)', 'important');
            grid.querySelectorAll(':scope > [class*="col-"]').forEach((field) => {
                const isAlignmentField = field.querySelector('select[name*="text_align"], .certificate-align-tabs');
                field.style.setProperty('grid-column', isAlignmentField || !isTwoColumnSettingsGrid ? '1 / -1' : 'span 1', 'important');
                field.style.setProperty('width', 'auto', 'important');
                field.style.setProperty('max-width', '100%', 'important');
            });
        });
        layoutRoot.querySelectorAll('#certificate-layout-settings-typography select[name*="text_align"]').forEach((select) => {
            const field = select.closest('[class*="col-"]') || select.parentElement;
            field?.style.setProperty('grid-column', '1 / -1', 'important');
        });
        const globalTypography = Array.from(layoutRoot.querySelectorAll('#certificate-layout-settings-typography .certificate-settings-section'))
            .find((section) => /^Global font/i.test(section.querySelector('summary')?.innerText || ''));
        if (globalTypography) {
            const globalBody = globalTypography.querySelector('.certificate-settings-section-body');
            const globalField = (selector) => globalBody?.querySelector(selector)?.closest('[class*="col-"]');
            const bodySizeField = globalField('[name="layout_settings[typography][body_size]"]');
            const familyField = globalField('[name="layout_settings[typography][font_family]"]');
            const weightField = globalField('[name="layout_settings[typography][font_weight]"]');
            const alignField = globalField('[name="layout_settings[typography][text_align]"]');
            bodySizeField?.style.setProperty('grid-column', '1 / -1', 'important');
            bodySizeField?.style.setProperty('order', '1', 'important');
            familyField?.style.setProperty('order', '2', 'important');
            weightField?.style.setProperty('order', '3', 'important');
            alignField?.style.setProperty('order', '4', 'important');
        }
        layoutRoot.querySelector('[data-certificate-principal-settings]')?.querySelectorAll(':scope > .certificate-settings-section').forEach((section) => {
            section.style.setProperty('grid-column', '1 / -1', 'important');
        });
        const headerSettingsPanel = layoutRoot.querySelector('.certificate-header-settings');
        headerSettingsPanel?.style.setProperty('grid-column', '1 / -1', 'important');
        headerSettingsPanel?.querySelectorAll(':scope > .certificate-settings-section, :scope > .certificate-settings-section > .certificate-settings-section-body > .certificate-settings-section').forEach((section) => {
            section.style.setProperty('grid-column', '1 / -1', 'important');
        });

        layoutRoot.querySelectorAll('select[name*="text_align"]').forEach((select) => {
            if (select.dataset.certificateAlignmentEnhanced === 'true') return;

            const field = select.closest('[class*="col-"]') || select.parentElement;
            const label = field?.querySelector('label');
            if (!field || !label) return;

            const tabs = document.createElement('div');
            tabs.className = 'certificate-align-tabs';
            tabs.dataset.optionCount = String(select.options.length);
            tabs.setAttribute('role', 'tablist');
            tabs.setAttribute('aria-label', label.textContent.trim());

            Array.from(select.options).forEach((option) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'certificate-align-tab';
                button.textContent = option.textContent.trim();
                button.dataset.value = option.value;
                button.setAttribute('role', 'tab');
                button.setAttribute('aria-selected', option.value === select.value ? 'true' : 'false');
                button.classList.toggle('is-active', option.value === select.value);
                button.addEventListener('click', () => {
                    select.value = option.value;
                    tabs.querySelectorAll('.certificate-align-tab').forEach((tab) => {
                        const active = tab === button;
                        tab.classList.toggle('is-active', active);
                        tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    });
                    select.dispatchEvent(new Event('input', { bubbles: true }));
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                });
                tabs.appendChild(button);
            });

            select.classList.add('certificate-alignment-source');
            select.dataset.certificateAlignmentEnhanced = 'true';
            select.parentElement.insertBefore(tabs, select);
        });

        const pageBody = layoutRoot.querySelector('#certificate-layout-settings-page .certificate-settings-section-body');
        if (pageBody && !pageBody.querySelector('.certificate-page-group-title')) {
            const fields = Array.from(pageBody.children).filter((child) => child.matches('[class*="col-"]'));
            const addGroupTitle = (text, before) => {
                const heading = document.createElement('div');
                heading.className = 'certificate-page-group-title';
                heading.textContent = text;
                pageBody.insertBefore(heading, before || null);
            };
            addGroupTitle('Paper size', fields[0]);
            addGroupTitle('Print margins', fields[2]);
        }

        layoutRoot.querySelectorAll('select[name*="font_family"]').forEach((select) => {
            if (select.name === 'layout_settings[typography][font_family]') return;
            (select.closest('[class*="col-"]') || select.parentElement)?.remove();
        });

        const applyGlobalTypography = () => {
            const value = (name, fallback) => page.querySelector(`[name="${name}"]`)?.value || fallback;
            const family = value('layout_settings[typography][font_family]', 'Georgia, Times New Roman, serif');
            const preview = page.querySelector('#certificatePreviewPaper');
            if (!preview) return;
            const configuredPrincipalPhone = @json($principalPhone);
            const principalPhone = preview.querySelector('.preview-principal > div:last-child');
            if (principalPhone && !principalPhone.textContent.trim() && configuredPrincipalPhone) {
                principalPhone.textContent = configuredPrincipalPhone;
            }

            preview.querySelectorAll('.preview-heading, .preview-placeholder, .preview-bottom, .preview-reason, .preview-principal, .preview-principal > div:not(.preview-principal-line)').forEach((element) => {
                element.style.fontFamily = family;
            });
        };
        const applyVisibility = () => {
            const preview = page.querySelector('#certificatePreviewPaper');
            if (!preview) return;
            const visible = (key) => page.querySelector(`[data-certificate-visibility="${key}"]`)?.checked !== false;
            const setVisible = (selector, key) => {
                const element = preview.querySelector(selector);
                if (element) element.style.display = visible(key) ? '' : 'none';
            };
            setVisible('[data-certificate-draggable="title"]', 'title');
            setVisible('[data-certificate-draggable="body"]', 'body');
            setVisible('[data-certificate-draggable="watermark"]', 'watermark');
            setVisible('.preview-reason', 'reason');
            setVisible('.preview-principal', 'principal');
            const principal = preview.querySelector('.preview-principal');
            if (principal && visible('principal')) {
                const name = principal.children[2];
                const school = principal.children[3];
                const phone = principal.children[4];
                if (name) name.style.display = visible('principal_name') ? '' : 'none';
                if (school) school.style.display = visible('school_name') ? '' : 'none';
                if (phone) phone.style.display = visible('contact_number') ? '' : 'none';
            }
        };
        const applyPrincipalDetailTypography = () => {
            const previewPrincipal = page.querySelector('#certificatePreviewPaper .preview-principal');
            if (!previewPrincipal) return;
            const value = (group, key, fallback) => page.querySelector(`[name="layout_settings[footer][${group}][${key}]"]`)?.value || fallback;
            const family = page.querySelector('[name="layout_settings[typography][font_family]"]')?.value || 'Georgia, Times New Roman, serif';
            const principal = {
                fontFamily: family,
                fontSize: `${value('principal', 'font_size', 16)}px`,
                fontWeight: value('principal', 'font_weight', 400),
                color: value('principal', 'color', '#111827'),
                textAlign: value('principal', 'text_align', 'center'),
            };
            previewPrincipal.querySelectorAll('div:not(.preview-principal-line)').forEach((element) => Object.assign(element.style, principal));
            const applyGroup = (element, group, defaults) => {
                if (!element) return;
                Object.assign(element.style, {
                    fontFamily: family,
                    fontSize: `${value(group, 'font_size', defaults.fontSize)}px`,
                    fontWeight: value(group, 'font_weight', defaults.fontWeight),
                    color: value(group, 'color', defaults.color),
                    textAlign: value(group, 'text_align', defaults.textAlign),
                });
            };
            applyGroup(previewPrincipal.querySelector('.preview-principal-name'), 'principal_label', { fontSize: 16, fontWeight: 700, color: '#111827', textAlign: 'center' });
            applyGroup(previewPrincipal.children[3], 'school_name', { fontSize: 16, fontWeight: 700, color: '#111827', textAlign: 'center' });
            applyGroup(previewPrincipal.children[4], 'contact_number', { fontSize: 14, fontWeight: 400, color: '#111827', textAlign: 'center' });
        };
        const applyHeaderTypography = () => {
            const header = page.querySelector('#certificatePreviewPaper .certificate-header');
            if (!header) return;
            const value = (group, key, fallback) => page.querySelector(`[name="layout_settings[header][${group}][${key}]"]`)?.value || fallback;
            const family = page.querySelector('[name="layout_settings[typography][font_family]"]')?.value || 'Arial, Helvetica, sans-serif';
            const logo = header.querySelector('.certificate-header-logo');
            if (logo) {
                logo.style.width = `${value('logo', 'width', 48)}px`;
                logo.style.height = `${value('logo', 'height', 48)}px`;
            }
            [['bangla', '.certificate-header-bangla', 29, 800], ['english', '.certificate-header-english', 17, 800]].forEach(([group, selector, fallbackSize, fallbackWeight]) => {
                const element = header.querySelector(selector);
                if (!element) return;
                Object.assign(element.style, {
                    fontFamily: group === 'bangla' ? "'Noto Sans Bengali', sans-serif" : family,
                    fontSize: `${value(group, 'font_size', fallbackSize)}px`,
                    fontWeight: value(group, 'font_weight', fallbackWeight),
                    color: value(group, 'color', '#111827'),
                    textAlign: value(group, 'text_align', 'left'),
                });
            });
        };
        page.querySelectorAll('.js-certificate-layout-input, .js-certificate-title-input, .js-certificate-principal-input').forEach((input) => {
            input.addEventListener('input', applyGlobalTypography);
            input.addEventListener('change', applyGlobalTypography);
            input.addEventListener('input', applyPrincipalDetailTypography);
            input.addEventListener('change', applyPrincipalDetailTypography);
        });
        page.querySelectorAll('.js-certificate-header-input').forEach((input) => {
            input.addEventListener('input', applyHeaderTypography);
            input.addEventListener('change', applyHeaderTypography);
        });
        applyGlobalTypography();
        applyPrincipalDetailTypography();
        applyHeaderTypography();
        layoutRoot.querySelectorAll('[data-certificate-visibility]').forEach((input) => input.addEventListener('change', applyVisibility));
        applyVisibility();

        const titleField = (name) => Array.from(page.querySelectorAll(`[name="layout_settings[typography][${name}]"]`)).find((input) => !input.disabled) || null;
        const titleNumber = (name, fallback) => {
            const value = Number.parseFloat(titleField(name)?.value ?? '');
            return Number.isFinite(value) ? value : fallback;
        };
        const applyTitlePreview = () => {
            const title = page.querySelector('#certificatePreviewPaper [data-certificate-draggable="title"]');
            if (!title) return;
            const family = page.querySelector('[name="layout_settings[typography][font_family]"]')?.value || 'Georgia, Times New Roman, serif';
            title.style.fontFamily = family;
            title.style.fontSize = `${titleNumber('title_size', 18)}px`;
            title.style.fontWeight = titleNumber('title_font_weight', 500);
            title.style.color = titleField('title_color')?.value || '#111827';
            title.style.backgroundColor = titleField('title_background')?.value || '#eef2e3';
            title.style.borderColor = titleField('title_border_color')?.value || '#4b5563';
            title.style.borderWidth = `${titleNumber('title_border_width', 1)}px`;
            title.style.borderStyle = titleField('title_border_style')?.value || 'solid';
            title.style.textAlign = titleField('title_text_align')?.value || 'center';
            title.style.letterSpacing = `${titleNumber('title_letter_spacing', 0.01)}px`;
            title.style.textTransform = titleField('title_text_transform')?.value || 'uppercase';
            title.style.padding = `${titleNumber('title_padding_top', 9)}px ${titleNumber('title_padding_right', 42)}px ${titleNumber('title_padding_bottom', 9)}px ${titleNumber('title_padding_left', 42)}px`;
        };
        page.querySelectorAll('.js-certificate-title-input').forEach((input) => {
            input.addEventListener('input', applyTitlePreview);
            input.addEventListener('change', applyTitlePreview);
        });
        applyTitlePreview();
    });
</script>
@endsection
