@extends('layouts.master')

@section('contents')
<div class="container-fluid">

    {{-- Filter Card --}}
    <div class="card no-print">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Generate ID Cards</h3>
        </div>
        <div class="card-body pb-2">
            <form method="GET" action="{{ route('students.id-cards') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="font-weight-bold">Academic Year <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-control form-control-sm" required onchange="this.form.submit()">
                                <option value="">— Select Year —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="font-weight-bold">Template <span class="text-danger">*</span></label>
                            <select name="template_id" class="form-control form-control-sm" required>
                                <option value="">— Select Template —</option>
                                @foreach($templates as $t)
                                    <option value="{{ $t->id }}" {{ request('template_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ ucfirst($t->orientation) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Class</label>
                            <select name="class_id" class="form-control form-control-sm" id="classSelect">
                                <option value="">All Classes</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Section</label>
                            <select name="section_id" class="form-control form-control-sm">
                                <option value="">All Sections</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Group</label>
                            <select name="group_id" class="form-control form-control-sm">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-group mb-0 d-flex" style="gap:6px">
                            <button type="submit" class="btn btn-primary btn-sm" title="Generate"><i class="fas fa-id-card"></i></button>
                            <a href="{{ route('students.id-cards') }}" class="btn btn-secondary btn-sm" title="Reset"><i class="fas fa-times"></i></a>
                            @if($students->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm" onclick="window.print()" title="Print"><i class="fas fa-print"></i></button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!request('session_id') || !request('template_id'))
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p class="mb-1">Select Academic Year and Template to generate ID cards.</p>
            @if($templates->isEmpty())
                <a href="{{ route('id-card-templates.create') }}" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-plus"></i> Create a Template first
                </a>
            @endif
        </div>
    @elseif($students->isEmpty())
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
            <p>No students found for the selected filters.</p>
        </div>
    @else
        @php
            $isLandscape = $template->orientation === 'landscape';
            $cardW       = $isLandscape ? '360px' : '220px';
            $cardH       = $isLandscape ? '220px' : '360px';
            $primary     = $setting?->primary_color   ?? '#1e3a5f';
            $secondary   = $setting?->secondary_color ?? '#2563eb';
            $idColor     = $setting?->id_card_color   ?? '#1e3a5f';
        @endphp

        <div class="no-print mb-3 d-flex align-items-center" style="gap:8px">
            <span class="badge badge-primary px-3 py-2" style="font-size:13px">{{ $students->count() }} Students</span>
            <span class="badge badge-secondary px-3 py-2" style="font-size:13px">{{ ucfirst($template->orientation) }}</span>
            <span class="badge badge-info px-3 py-2" style="font-size:13px">{{ $template->name }}</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">Front + Back per student</span>
        </div>

        {{-- Card pairs grid --}}
        <div class="id-card-grid">
            @foreach($students as $student)
                @php $ai = $student->academicInformations->first(); @endphp

                {{-- ── PAIR WRAPPER ── --}}
                <div class="id-card-pair">

                    {{-- ── FRONT CARD ── --}}
                    <div class="id-card {{ $isLandscape ? 'id-card--landscape' : 'id-card--portrait' }}">
                        @if($template->front_bg_image)
                            <img src="{{ asset($template->front_bg_image) }}" class="id-card__bg">
                        @elseif($template->background_image)
                            <img src="{{ asset($template->background_image) }}" class="id-card__bg">
                        @endif

                        {{-- Header --}}
                        <div class="id-card__header {{ $isLandscape ? 'id-card__header--landscape' : 'id-card__header--portrait' }}"
                             style="background: linear-gradient(135deg, {{ $idColor }}, {{ $secondary }})">
                            @if($setting?->logo)
                                <img src="{{ asset($setting->logo) }}" class="id-card__logo">
                            @endif
                            <div>
                                <div class="id-card__school-name">{{ $setting?->name ?? 'School Name' }}</div>
                                @if($setting?->slogan)<div class="id-card__slogan">{{ $setting->slogan }}</div>@endif
                            </div>
                            <div class="id-card__label-badge">STUDENT ID</div>
                        </div>

                        @if($isLandscape)
                            <div class="id-card__body id-card__body--landscape">
                                <div class="id-card__photo-wrap">
                                    <img src="{{ $student->photo_url }}" class="id-card__photo">
                                </div>
                                <div class="id-card__info">
                                    <div class="id-card__name">{{ $student->full_name_en }}</div>
                                    @if($student->full_name_bn)<div class="id-card__name-bn">{{ $student->full_name_bn }}</div>@endif
                                    <div class="id-card__divider" style="background:linear-gradient(90deg,{{ $idColor }},transparent)"></div>
                                    <div class="id-card__info-row">
                                        @include('pages.generate-id-cards._rows', compact('student','ai'))
                                        @if($setting?->whatsapp_qr)
                                            <div class="id-card__qr-wrap">
                                                <img src="{{ asset($setting->whatsapp_qr) }}" class="id-card__qr">
                                                <span style="font-size:6.5px;color:#94a3b8;margin-top:2px">Scan</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="id-card__photo-center">
                                <img src="{{ $student->photo_url }}" class="id-card__photo id-card__photo--portrait">
                            </div>
                            <div class="id-card__info id-card__info--portrait">
                                <div class="id-card__name text-center">{{ $student->full_name_en }}</div>
                                @if($student->full_name_bn)<div class="id-card__name-bn text-center">{{ $student->full_name_bn }}</div>@endif
                                <div class="id-card__divider" style="background:linear-gradient(90deg,{{ $idColor }},transparent)"></div>
                                @include('pages.generate-id-cards._rows', compact('student','ai'))
                            </div>
                        @endif

                        <div class="id-card__footer" style="background:linear-gradient(135deg,{{ $idColor }},{{ $secondary }})">
                            @if($setting?->contact_number_1)<span>📞 {{ $setting->contact_number_1 }}</span>@endif
                            @if($setting?->contact_number_2)<span>📞 {{ $setting->contact_number_2 }}</span>@endif
                            @if($setting?->website)<span>🌐 {{ $setting->website }}</span>@endif
                        </div>
                    </div>

                    {{-- ── BACK CARD ── --}}
                    <div class="id-card id-card--back {{ $isLandscape ? 'id-card--landscape' : 'id-card--portrait' }}" style="background:#fff;border:1.5px solid #333">
                        @if($template->back_bg_image)
                            <img src="{{ asset($template->back_bg_image) }}" class="id-card__bg" style="opacity:.04">
                        @endif

                        {{-- Back header - B&W no logo --}}
                        <div class="id-card__header {{ $isLandscape ? 'id-card__header--landscape' : 'id-card__header--portrait' }}"
                             style="background:#222;color:#fff">
                            <div>
                                <div class="id-card__school-name" style="font-size:10px">{{ $setting?->name ?? 'School Name' }}</div>
                                @if($setting?->slogan)<div class="id-card__slogan">{{ $setting->slogan }}</div>@endif
                            </div>
                        </div>

                        {{-- Back body --}}
                        <div class="id-card__back-body">

                            @if($isLandscape)
                                {{-- Landscape: two columns side by side --}}
                                <div class="id-card__back-cols">
                                    <div class="id-card__back-section">
                                        <div class="id-card__back-title">Parent / Guardian</div>
                                        @if($student->father_name)
                                            <div class="id-card__back-row"><span class="id-card__lbl">Father</span><span class="id-card__val">{{ $student->father_name }}</span></div>
                                        @endif
                                        @if($student->mother_name)
                                            <div class="id-card__back-row"><span class="id-card__lbl">Mother</span><span class="id-card__val">{{ $student->mother_name }}</span></div>
                                        @endif
                                        @if($student->father_phone || $student->mother_phone)
                                            <div class="id-card__back-row"><span class="id-card__lbl">Contact</span><span class="id-card__val">{{ implode(', ', array_filter([$student->father_phone, $student->mother_phone])) }}</span></div>
                                        @endif
                                        @if($student->present_address)
                                            <div class="id-card__back-row"><span class="id-card__lbl">Address</span><span class="id-card__val">{{ Str::limit($student->present_address, 35) }}</span></div>
                                        @endif
                                    </div>
                                    <div class="id-card__back-section">
                                        <div class="id-card__back-title">School Contact</div>
                                        @if($setting?->address)<div class="id-card__back-row"><span class="id-card__lbl">Address</span><span class="id-card__val">{{ Str::limit($setting->address, 40) }}</span></div>@endif
                                        @if($setting?->contact_number_1 || $setting?->contact_number_2)
                                            <div class="id-card__back-row"><span class="id-card__lbl">Contact</span><span class="id-card__val">{{ implode(', ', array_filter([$setting?->contact_number_1, $setting?->contact_number_2])) }}</span></div>
                                        @endif
                                        @if($setting?->whatsapp_number)<div class="id-card__back-row"><span class="id-card__lbl">WhatsApp</span><span class="id-card__val">{{ $setting->whatsapp_number }}</span></div>@endif
                                        @if($setting?->email)<div class="id-card__back-row"><span class="id-card__lbl">Email</span><span class="id-card__val">{{ $setting->email }}</span></div>@endif
                                        @if($setting?->website)<div class="id-card__back-row"><span class="id-card__lbl">Web</span><span class="id-card__val">{{ $setting->website }}</span></div>@endif
                                    </div>
                                </div>
                            @else
                                <div class="id-card__back-section">
                                    <div class="id-card__back-title">Parent / Guardian</div>
                                    @if($student->father_name)
                                        <div class="id-card__back-row"><span class="id-card__lbl">Father</span><span class="id-card__val">{{ $student->father_name }}</span></div>
                                    @endif
                                    @if($student->mother_name)
                                        <div class="id-card__back-row"><span class="id-card__lbl">Mother</span><span class="id-card__val">{{ $student->mother_name }}</span></div>
                                    @endif
                                    @if($student->father_phone || $student->mother_phone)
                                        <div class="id-card__back-row"><span class="id-card__lbl">Contact</span><span class="id-card__val">{{ implode(', ', array_filter([$student->father_phone, $student->mother_phone])) }}</span></div>
                                    @endif
                                    @if($student->present_address)
                                        <div class="id-card__back-row"><span class="id-card__lbl">Address</span><span class="id-card__val">{{ Str::limit($student->present_address, 40) }}</span></div>
                                    @endif
                                </div>

                                <div class="id-card__back-section">
                                    <div class="id-card__back-title">School Contact</div>
                                    @if($setting?->address)<div class="id-card__back-row"><span class="id-card__lbl">Address</span><span class="id-card__val">{{ Str::limit($setting->address, 80) }}</span></div>@endif
                                    @if($setting?->contact_number_1 || $setting?->contact_number_2)
                                        <div class="id-card__back-row"><span class="id-card__lbl">Contact</span><span class="id-card__val">{{ implode(', ', array_filter([$setting?->contact_number_1, $setting?->contact_number_2])) }}</span></div>
                                    @endif
                                    @if($setting?->whatsapp_number)<div class="id-card__back-row"><span class="id-card__lbl">WhatsApp</span><span class="id-card__val">{{ $setting->whatsapp_number }}</span></div>@endif
                                    @if($setting?->email)<div class="id-card__back-row"><span class="id-card__lbl">Email</span><span class="id-card__val">{{ $setting->email }}</span></div>@endif
                                    @if($setting?->website)<div class="id-card__back-row"><span class="id-card__lbl">Web</span><span class="id-card__val">{{ $setting->website }}</span></div>@endif
                                </div>

                                @if($setting?->whatsapp_qr)
                                    <div class="id-card__back-section" style="text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;width:100%">
                                        <div class="id-card__back-title">Scan to WhatsApp</div>
                                        <img src="{{ asset($setting->whatsapp_qr) }}" style="width:80px;height:80px;object-fit:contain;border:1px solid #ddd;border-radius:4px;padding:2px">
                                    </div>
                                @endif
                            @endif

                            <div class="id-card__back-notice">If found, please return to the school.</div>
                        </div>

                        {{-- Back footer - B&W --}}
                        <div class="id-card__footer" style="background:#222;color:rgba(255,255,255,.8)">
                            @if($setting?->eiin)<span>EIIN: {{ $setting->eiin }}</span>@endif
                            @if($setting?->whatsapp_number)<span>📱 {{ $setting->whatsapp_number }}</span>@endif
                        </div>
                    </div>

                </div>{{-- /.id-card-pair --}}
            @endforeach
        </div>
    @endif
</div>

<script>
document.getElementById('classSelect')?.addEventListener('change', function () {
    const url = new URL(window.location.href);
    url.searchParams.set('class_id', this.value);
    url.searchParams.delete('section_id');
    url.searchParams.delete('group_id');
    ['session_id','template_id'].forEach(k => {
        const v = document.querySelector(`[name="${k}"]`)?.value;
        if (v) url.searchParams.set(k, v);
    });
    window.location.href = url.toString();
});
</script>

<style>
/* ── Pair wrapper ── */
.id-card-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    padding: 8px 0;
}

.id-card-pair {
    display: flex;
    gap: 6px;
    align-items: stretch;
    page-break-inside: avoid;
    break-inside: avoid;
}

.id-card-pair > .id-card {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.id-card-pair::after {
    content: '';
    display: block;
    width: 1px;
    background: repeating-linear-gradient(to bottom, #ccc 0, #ccc 4px, transparent 4px, transparent 8px);
    align-self: stretch;
    margin: 0 4px;
}

/* ── Base Card ── */
.id-card {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    font-family: 'Segoe UI', Arial, sans-serif;
    box-shadow: 0 6px 24px rgba(0,0,0,.15);
    transition: transform .2s, box-shadow .2s;
    background: #fff;
    break-inside: avoid;
}
.id-card:hover { transform: translateY(-3px); box-shadow: 0 12px 36px rgba(0,0,0,.2); }

/* ── Background ── */
.id-card__bg {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover; opacity: .08;
    pointer-events: none;
}

/* ── Sizes ── */
.id-card--portrait  { width: 220px; min-height: 360px; display: flex; flex-direction: column; }
.id-card--landscape { width: 355px; min-height: 185px; max-height: 200px; display: flex; flex-direction: column; }

/* ── Header ── */
.id-card__header {
    position: relative; z-index: 1;
    color: #fff; display: flex;
    align-items: center; gap: 8px; padding: 10px 12px;
}
.id-card__header--portrait  { flex-direction: column; text-align: center; padding: 12px 10px 8px; gap: 3px; }
.id-card__header--landscape { flex-direction: row; }
.id-card__logo { height: 40px; width: auto; object-fit: contain; }
.id-card__school-name { font-size: 10px; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; line-height: 1.3; }
.id-card__slogan { font-size: 7.5px; opacity: .8; }
.id-card__label-badge {
    margin-left: auto;
    background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.4);
    border-radius: 20px; font-size: 7.5px; font-weight: 700;
    letter-spacing: .1em; padding: 2px 7px; white-space: nowrap;
}
.id-card__header--portrait .id-card__label-badge { margin-left: 0; }

/* ── Body landscape ── */
.id-card__body--landscape {
    position: relative; z-index: 1;
    display: flex; flex: 1; gap: 10px;
    padding: 10px; align-items: flex-start;
}

/* ── Photo ── */
.id-card__photo-wrap { flex-shrink: 0; }
.id-card__photo { width: 78px; height: 92px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,.12); }
.id-card__photo--portrait { width: 72px; height: 85px; }
.id-card__avatar { width: 78px; height: 92px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 30px; font-weight: 800; color: #fff; border: 2px solid #e2e8f0; }
.id-card__avatar--portrait { width: 72px; height: 85px; font-size: 26px; }
.id-card__photo-center { position: relative; z-index: 1; display: flex; justify-content: center; padding: 8px 0 4px; }

/* ── Info ── */
.id-card__info { position: relative; z-index: 1; flex: 1; }
.id-card__info--portrait { padding: 4px 10px 6px; }
.id-card__info-row { display: flex; gap: 6px; align-items: flex-start; }
.id-card__info-row > .id-card__rows { flex: 1; }
.id-card__qr-wrap { flex-shrink: 0; display: flex; flex-direction: column; align-items: center; }
.id-card__qr { width: 52px; height: 52px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; }
.id-card__name { font-size: 12px; font-weight: 800; color: #1e3a5f; line-height: 1.3; }
.id-card__name-bn { font-size: 10px; color: #475569; margin-top: 1px; }
.id-card__divider { height: 2px; border-radius: 2px; margin: 5px 0; }
.id-card__rows { display: flex; flex-direction: column; gap: 2px; }
.id-card__row  { display: flex; align-items: baseline; gap: 4px; font-size: 9px; }
.id-card__lbl  { color: #94a3b8; font-weight: 600; min-width: 42px; font-size: 8px; text-transform: uppercase; letter-spacing: .04em; }
.id-card__val  { color: #1e293b; font-weight: 700; }
.id-card__blood { color: #dc2626 !important; }

/* ── Back body ── */
.id-card__back-body { position: relative; z-index: 1; flex: 1; padding: 6px 10px 4px; }
.id-card__back-section { margin-bottom: 4px; }
.id-card__back-cols { display: flex; gap: 8px; align-items: flex-start; }
.id-card__back-cols > .id-card__back-section { flex: 1; margin-bottom: 0; }
.id-card__back-title { font-size: 8.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color:#222; margin-bottom: 3px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
.id-card__back-row { display: flex; gap: 4px; font-size: 8.5px; margin-bottom: 2px; }
.id-card__back-notice { font-size: 7.5px; color: #94a3b8; text-align: center; font-style: italic; margin-top: 6px; padding-top: 4px; border-top: 1px dashed #e2e8f0; }

/* ── Footer ── */
.id-card__footer {
    position: relative; z-index: 1;
    color: rgba(255,255,255,.85); font-size: 7.5px;
    padding: 4px 8px; display: flex; gap: 8px;
    flex-wrap: wrap; justify-content: center; margin-top: auto;
}

/* ── Print ── */
@media print {
    .no-print, .main-sidebar, .main-header, .content-header { display: none !important; }
    .content-wrapper { margin-left: 0 !important; padding: 4px !important; }
    .id-card-grid { gap: 12px !important; }
    .id-card-pair { gap: 4px !important; break-inside: avoid; page-break-inside: avoid; }
    .id-card { box-shadow: none !important; border: 1px solid #ccc !important; break-inside: avoid; }
    .id-card:hover { transform: none !important; }
    body { background: #fff !important; }
}
</style>
@endsection
