@extends('layouts.master')

@section('contents')
@php
    $existingItems = $routine?->items?->keyBy('subject_id') ?? collect();
    $timeValue = fn ($value, $fallback) => $value ? $value->format('H:i') : $fallback;
    $exportQuery = array_filter([
        'academic_session_id' => $sessionId,
        'exam_type' => $examType,
        'exam_id' => $examId,
        'school_class_id' => $classId,
        'group_id' => $groupId,
    ], fn ($value) => filled($value));
@endphp
<div class="container-fluid exam-routine-page">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h3 class="mt-2 mb-1"><i class="fas fa-calendar-alt text-orange mr-2"></i>{{ __('Exam Routine') }}</h3>
            <p class="text-muted mb-0">{{ __('Arrange every subject by exam date and time slot.') }}</p>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><strong>{{ __('Please correct the highlighted information.') }}</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <div class="card routine-panel mb-4">
        <div class="card-body">
            <div class="panel-heading">
                <a href="{{ route('results.hub') }}" class="step-back" aria-label="{{ __('Back to Results Hub') }}">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i><span>{{ __('Back') }}</span>
                </a>
                <div><h5>{{ __('Choose exam context') }}</h5><p>{{ __('Select the academic session, exam, and class. Group is optional.') }}</p></div>
            </div>
            <form method="GET" action="{{ route('exam-routines.index') }}" class="row g-3 filter-form" id="routine-filter-form">
                <div class="col-xl-2 col-md-4"><label>{{ __('Academic Session') }}</label><select name="academic_session_id" id="academic_session_id" class="form-control" required><option value="">{{ __('Select session') }}</option>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected($sessionId == $session->id)>{{ $session->name_en }}</option>@endforeach</select></div>
                <div class="col-xl-2 col-md-4"><label>{{ __('Exam Type') }}</label><select name="exam_type" id="exam_type" class="form-control" required><option value="">{{ __('Select type') }}</option>@foreach($examTypes as $value => $label)<option value="{{ $value }}" @selected($examType === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-xl-3 col-md-4"><label>{{ __('Exam Name') }}</label><select name="exam_id" id="exam_id" class="form-control" required><option value="">{{ __('Select exam name') }}</option>@foreach($exams as $exam)<option value="{{ $exam->id }}" @selected($examId == $exam->id)>{{ $exam->name }}</option>@endforeach</select></div>
                <div class="col-xl-2 col-md-4"><label>{{ __('Class') }}</label><select name="school_class_id" id="school_class_id" class="form-control" required><option value="">{{ __('Select class') }}</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name_en }}</option>@endforeach</select></div>
                <div class="col-xl-2 col-md-4"><label>{{ __('Group') }} <span class="text-muted">({{ __('optional') }})</span></label><select name="group_id" id="group_id" class="form-control"><option value="">{{ $classId ? __('All groups') : __('Select class first') }}</option>@foreach($groups as $group)<option value="{{ $group->id }}" @selected($groupId == $group->id)>{{ $group->name_en }}</option>@endforeach</select></div>
                <div class="col-xl-1 col-md-4 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit" title="{{ __('Load subjects') }}"><i class="fas fa-arrow-right"></i></button></div>
            </form>
        </div>
    </div>

    @if($subjects->isNotEmpty())
    <form method="POST" action="{{ route('exam-routines.store') }}" id="routine-editor-form">
        @csrf
        <input type="hidden" name="academic_session_id" value="{{ $sessionId }}"><input type="hidden" name="exam_type" value="{{ $examType }}"><input type="hidden" name="exam_id" value="{{ $examId }}"><input type="hidden" name="school_class_id" value="{{ $classId }}"><input type="hidden" name="group_id" value="{{ $groupId }}">
        <div class="card routine-panel mb-4">
            <div class="card-body">
                <div class="panel-heading"><span class="step-number">2</span><div><h5>{{ __('Configure slot times') }}</h5><p>{{ __('These are time inputs and apply to every subject using the selected slot.') }}</p></div></div>
                <div class="slot-grid">
                    <div class="slot-box morning"><div class="slot-title"><span class="slot-dot"></span>{{ __('Morning') }}</div><div class="row"><div class="col-6"><label>{{ __('Starts') }}</label><input type="time" name="morning_start_time" class="form-control" value="{{ $timeValue($routine?->morning_start_time, '09:00') }}" required></div><div class="col-6"><label>{{ __('Ends') }}</label><input type="time" name="morning_end_time" class="form-control" value="{{ $timeValue($routine?->morning_end_time, '12:00') }}" required></div></div></div>
                    <div class="slot-box noon"><div class="slot-title"><span class="slot-dot"></span>{{ __('Noon') }}</div><div class="row"><div class="col-6"><label>{{ __('Starts') }}</label><input type="time" name="noon_start_time" class="form-control" value="{{ $timeValue($routine?->noon_start_time, '13:00') }}" required></div><div class="col-6"><label>{{ __('Ends') }}</label><input type="time" name="noon_end_time" class="form-control" value="{{ $timeValue($routine?->noon_end_time, '16:00') }}" required></div></div></div>
                </div>
            </div>
        </div>

        <div class="card routine-panel">
            <div class="card-body p-0">
                <div class="editor-header"><div class="panel-heading mb-0"><span class="step-number">3</span><div><h5>{{ __('Arrange subjects') }}</h5><p>{{ __('Drag subjects up or down, choose a slot, then select a date.') }}</p></div></div><div class="editor-actions">@if($routine)<a href="{{ route('exam-routines.print', $exportQuery) }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener"><i class="fas fa-print mr-1"></i>{{ __('Print') }}</a><a href="{{ route('exam-routines.pdf', $exportQuery) }}" class="btn btn-primary btn-sm"><i class="fas fa-file-pdf mr-1"></i>{{ __('PDF') }}</a>@else<span class="export-hint">{{ __('Save the routine to enable print and PDF export.') }}</span>@endif<span class="subject-count"><span id="subject-count">{{ $subjects->count() }}</span> {{ __('subjects') }}</span></div></div>
                <div class="routine-table-head"><span>{{ __('Order') }}</span><span>{{ __('Subject') }}</span><span>{{ __('Slot') }}</span><span>{{ __('Date') }}</span><span>{{ __('Day') }}</span></div>
                <div id="subject-list" class="subject-list">
                    @foreach($subjects as $index => $subject)
                        @php $item = $existingItems->get($subject->id); @endphp
                        <div class="subject-row" draggable="true" data-exam-date="{{ $item?->exam_date?->format('Y-m-d') ?? '' }}">
                            <div class="drag-order"><i class="fas fa-grip-vertical drag-handle" title="{{ __('Drag to reorder') }}"></i><span class="row-number">{{ $index + 1 }}</span></div>
                            <div class="subject-name"><input type="hidden" name="items[{{ $index }}][subject_id]" value="{{ $subject->id }}"><strong>{{ $subject->name }}</strong>@if($subject->code)<small>{{ $subject->code }}</small>@endif</div>
                            <div class="slot-options"><label><input type="radio" name="items[{{ $index }}][slot]" value="morning" @checked(($item?->slot ?? 'morning') === 'morning')> <span>{{ __('Morning') }}</span></label><label><input type="radio" name="items[{{ $index }}][slot]" value="noon" @checked($item?->slot === 'noon')> <span>{{ __('Noon') }}</span></label></div>
                            <div><input type="date" name="items[{{ $index }}][exam_date]" class="form-control exam-date" value="{{ $item?->exam_date?->format('Y-m-d') }}" required></div>
                            <div class="day-display"><span>{{ $item?->day_name ?? __('Select date') }}</span></div>
                        </div>
                    @endforeach
                </div>
                <div class="editor-footer"><span class="text-muted small"><i class="fas fa-info-circle mr-1"></i>{{ __('The weekday is calculated automatically from the selected date.') }}</span><button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-2"></i>{{ __('Save changes') }}</button></div>
            </div>
        </div>
    </form>
    @elseif($sessionId || $examType || $examId || $classId || $groupId)
        <div class="empty-routine"><i class="fas fa-layer-group"></i><h5>{{ __('Complete the filters to load subjects') }}</h5><p>{{ __('Choose a valid exam, class, and group, then submit the filter.') }}</p></div>
    @endif
</div>

<style>
    .exam-routine-page{--orange:#ea580c;--ink:#0f172a;width:100%;max-width:none}.text-orange{color:var(--orange)}.routine-panel{border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 8px 28px rgba(15,23,42,.05)}.panel-heading{display:flex;align-items:flex-start;gap:12px}.panel-heading h5{margin:2px 0 3px;font-weight:700;color:var(--ink)}.panel-heading p{margin:0;color:#64748b;font-size:.88rem}.step-number,.step-back{display:inline-flex;align-items:center;justify-content:center;flex:0 0 30px;height:30px;border-radius:10px;font-weight:800}.step-number{background:#fff1eb;color:var(--orange)}.step-back{flex-basis:auto;min-width:68px;padding:0 11px;gap:6px;background:var(--orange);border:1px solid var(--orange);color:#fff;font-size:.76rem;text-decoration:none;transition:background .15s ease,border-color .15s ease,box-shadow .15s ease}.step-back:hover,.step-back:focus{background:#c2410c;border-color:#c2410c;box-shadow:0 4px 10px rgba(234,88,12,.22);color:#fff;text-decoration:none}.filter-form label,.slot-box label{display:block;font-size:.75rem;font-weight:700;color:#475569;margin-bottom:5px}.filter-form select,.slot-box input{min-height:42px}.slot-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-top:22px}.slot-box{border:1px solid #fed7aa;border-radius:14px;padding:16px;background:#fffaf7}.slot-box.noon{border-color:#bfdbfe;background:#f8fbff}.slot-title{font-weight:800;color:#334155;margin-bottom:12px}.slot-dot{display:inline-block;width:9px;height:9px;border-radius:50%;background:#f97316;margin-right:7px}.noon .slot-dot{background:#2563eb}.editor-header{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:20px 22px;border-bottom:1px solid #e2e8f0}.editor-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap}.export-hint{color:#64748b;font-size:.75rem}.subject-count{font-size:.8rem;font-weight:700;color:#64748b;background:#f8fafc;padding:7px 11px;border-radius:999px;white-space:nowrap}.routine-table-head,.subject-row{display:grid;grid-template-columns:90px minmax(180px,1.5fr) minmax(190px,1.2fr) minmax(160px,1fr) 140px;gap:16px;align-items:center}.routine-table-head{padding:11px 22px;background:#f8fafc;color:#64748b;font-size:.7rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em}.subject-row{padding:14px 22px;border-bottom:1px solid #f1f5f9;background:#fff;transition:box-shadow .15s,background .15s}.subject-row.dragging{opacity:.55;background:#fff7ed}.subject-row.over{box-shadow:inset 0 3px 0 var(--orange)}.drag-order{display:flex;align-items:center;gap:12px;color:#94a3b8}.drag-handle{cursor:grab}.row-number{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:8px;background:#f1f5f9;color:#334155;font-size:.8rem;font-weight:800}.subject-name small{display:block;color:#94a3b8;font-size:.72rem;margin-top:2px}.slot-options{display:flex;gap:13px;flex-wrap:wrap}.slot-options label{font-size:.8rem;color:#475569;white-space:nowrap}.slot-options input{accent-color:var(--orange);margin-right:3px}.day-display{font-weight:700;color:#334155}.day-display span{display:inline-flex;min-height:36px;align-items:center;padding:0 11px;border-radius:9px;background:#f8fafc;color:#64748b;font-size:.82rem}.editor-footer{display:flex;justify-content:space-between;align-items:center;gap:15px;padding:18px 22px}.empty-routine{text-align:center;border:1px dashed #cbd5e1;border-radius:16px;padding:55px 20px;color:#64748b}.empty-routine i{font-size:2rem;color:#94a3b8;margin-bottom:12px}.empty-routine h5{color:#334155}
    @media(max-width:900px){.routine-table-head{display:none}.subject-row{grid-template-columns:48px 1fr;gap:10px;padding:16px}.subject-row>div:nth-child(n+3){grid-column:2}.subject-row:before{content:'{{ __('Slot') }}';display:block;grid-column:1;color:#64748b;font-size:.72rem;font-weight:700}.slot-options{margin-top:-2px}.subject-row>div:nth-child(4):before{content:'{{ __('Date') }}';display:block;color:#64748b;font-size:.72rem;font-weight:700;margin-bottom:5px}.subject-row>div:nth-child(5):before{content:'{{ __('Day') }}';display:block;color:#64748b;font-size:.72rem;font-weight:700;margin-bottom:5px}.slot-grid{grid-template-columns:1fr}.editor-footer{align-items:flex-start;flex-direction:column}.editor-footer .btn{width:100%}}
</style>
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
window.addEventListener('load', () => {
    const session = document.getElementById('academic_session_id'), type = document.getElementById('exam_type'), exam = document.getElementById('exam_id'), cls = document.getElementById('school_class_id'), group = document.getElementById('group_id');
    const selectedExam = @json($examId), selectedGroup = @json($groupId);
    const weekendDays = @json($weekendDays).map(Number);
    const holidayDates = new Set(@json($holidayDates));
    const holidayNames = @json($holidayNames);
    const holidayMessage = @json(__("You can't select a holiday for an exam."));
    const refreshSelect = select => { if (window.jQuery && window.jQuery(select).hasClass('select2-hidden-accessible')) { window.jQuery(select).trigger('change'); } else if (window.refreshSelect2) { window.refreshSelect2(select); } };
    const fill = (select, rows, placeholder, selected = null) => { select.innerHTML = `<option value="">${placeholder}</option>`; rows.forEach(row => { const option = new Option(row.name || row.name_en, row.id); if (String(row.id) === String(selected)) option.selected = true; select.add(option); }); refreshSelect(select); };
    const loadExams = async () => { fill(exam, [], '{{ __('Loading...') }}'); if (!session.value || !type.value) { fill(exam, [], '{{ __('Select session and exam type') }}'); return; } try { const response = await fetch(`{{ route('exam-routines.exams') }}?academic_session_id=${encodeURIComponent(session.value)}&exam_type=${encodeURIComponent(type.value)}`, {headers:{Accept:'application/json', 'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'}); if (!response.ok) throw new Error(`Exam lookup failed (${response.status})`); const payload = await response.json(); fill(exam, payload.exams || [], '{{ __('Select exam name') }}', selectedExam); } catch (error) { console.error(error); fill(exam, [], '{{ __('Unable to load exam names') }}'); } };
    const loadGroups = async () => { fill(group, [], '{{ __('Loading...') }}'); if (!session.value || !cls.value) { fill(group, [], '{{ __('Select class first') }}'); return; } try { const response = await fetch(`{{ route('exam-routines.groups') }}?academic_session_id=${encodeURIComponent(session.value)}&school_class_id=${encodeURIComponent(cls.value)}`, {headers:{Accept:'application/json', 'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'}); if (!response.ok) throw new Error(`Group lookup failed (${response.status})`); fill(group, (await response.json()).groups || [], '{{ __('All groups') }}', selectedGroup); } catch (error) { console.error(error); fill(group, [], '{{ __('All groups') }}'); } };
    if (window.jQuery) {
        const $document = window.jQuery(document);
        $document.off('.examRoutineFilters');
        $document.on('change.examRoutineFilters select2:select.examRoutineFilters select2:clear.examRoutineFilters', '#academic_session_id', function () { loadExams(); if (cls?.value) loadGroups(); });
        $document.on('change.examRoutineFilters select2:select.examRoutineFilters select2:clear.examRoutineFilters', '#exam_type', function () { loadExams(); });
        $document.on('change.examRoutineFilters select2:select.examRoutineFilters select2:clear.examRoutineFilters', '#school_class_id', function () { loadGroups(); });
    } else {
        session?.addEventListener('change', loadExams); type?.addEventListener('change', loadExams); cls?.addEventListener('change', loadGroups);
    }
    if (session?.value && type?.value) loadExams();
    if (session?.value && cls?.value) loadGroups();
    const list = document.getElementById('subject-list'); let dragged;
    list?.querySelectorAll('.subject-row').forEach(row => { row.addEventListener('dragstart', () => { dragged = row; row.classList.add('dragging'); }); row.addEventListener('dragend', () => { row.classList.remove('dragging'); list.querySelectorAll('.subject-row').forEach(item => item.classList.remove('over')); renumber(); }); row.addEventListener('dragover', event => { event.preventDefault(); if (row !== dragged) row.classList.add('over'); }); row.addEventListener('dragleave', () => row.classList.remove('over')); row.addEventListener('drop', event => { event.preventDefault(); row.classList.remove('over'); if (dragged && dragged !== row) { const rect = row.getBoundingClientRect(); list.insertBefore(dragged, event.clientY > rect.top + rect.height / 2 ? row.nextSibling : row); } }); });
    const renumber = () => {
        if (!list) return;
        const rows = [...list.querySelectorAll('.subject-row')];

        // Rename through unique temporary groups first. Renaming rows directly
        // can make radio names collide and cause the browser to uncheck a slot.
        rows.forEach((row, index) => {
            row.querySelector('.row-number').textContent = index + 1;
            row.querySelectorAll('[name]').forEach((input, fieldIndex) => {
                input.name = input.name.replace(/^items\[\d+\]/, `items[__reorder_${index}_${fieldIndex}]`);
            });
        });

        rows.forEach((row, index) => {
            row.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/^items\[__reorder_\d+_\d+\]/, `items[${index}]`);
            });
        });
    };
    const sortByDate = () => {
        if (!list) return;
        const rows = [...list.querySelectorAll('.subject-row')];
        rows.sort((left, right) => {
            const leftDate = getRowDate(left);
            const rightDate = getRowDate(right);
            if (!leftDate && !rightDate) return 0;
            if (!leftDate) return 1;
            if (!rightDate) return -1;
            return leftDate.localeCompare(rightDate);
        });
        rows.forEach(row => list.appendChild(row));
        renumber();
    };
    const getDateFields = row => ({
        hidden: row.querySelector('input[type="hidden"][name*="[exam_date]"]'),
        visible: row.querySelector('input.exam-date:not([type="date"])') || row.querySelector('input.exam-date'),
    });
    const toCanonicalDate = value => {
        if (!value) return '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(value)) return value;
        const match = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        return match ? `${match[3]}-${match[2]}-${match[1]}` : '';
    };
    const getRowDate = row => {
        const fields = getDateFields(row);
        const rawValue = fields.visible ? fields.visible.value : (fields.hidden?.value || row.dataset.examDate || '');
        return toCanonicalDate(rawValue);
    };
    const blockedDateMessage = value => {
        if (!value) return null;
        const date = new Date(`${value}T00:00:00`);
        if (weekendDays.includes(date.getDay())) return @json(__("You can't select an off day for an exam."));
        if (holidayDates.has(value)) return `${holidayMessage} (${holidayNames[value] || @json(__('Holiday'))})`;
        return null;
    };
    const showBlockedDateAlert = message => {
        if (window.Swal) {
            Swal.fire({ icon: 'warning', title: '{{ __('Date unavailable') }}', text: message, confirmButtonText: '{{ __('Okay') }}' });
        } else {
            window.alert(message);
        }
    };
    const updateDateRow = row => {
        const value = getRowDate(row);
        const day = row.querySelector('.day-display span');
        day.textContent = value ? new Date(`${value}T00:00:00`).toLocaleDateString(undefined, {weekday:'long'}) : '{{ __('Select date') }}';
    };
    list?.querySelectorAll('.subject-row').forEach(row => {
        const fields = getDateFields(row);
        const handleDateChange = () => window.setTimeout(() => {
            const value = getRowDate(row);
            const message = blockedDateMessage(value);
            if (message) {
                if (fields.hidden) fields.hidden.value = '';
                if (fields.visible) fields.visible.value = '';
                row.dataset.examDate = '';
                showBlockedDateAlert(message);
            } else {
                row.dataset.examDate = value;
            }
            updateDateRow(row);
            sortByDate();
        }, 0);
        fields.visible?.addEventListener('change', handleDateChange);
        fields.visible?.addEventListener('blur', handleDateChange);
        if (window.jQuery && fields.visible) window.jQuery(fields.visible).on('changeDate.examRoutine', handleDateChange);
        updateDateRow(row);
    });
    sortByDate();
});
</script>
@endsection
