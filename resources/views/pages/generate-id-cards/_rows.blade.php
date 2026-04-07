<div class="id-card__rows">
    <div class="id-card__row"><span class="id-card__lbl">ID</span><span class="id-card__val">{{ $student->student_cid }}</span></div>
    @if($ai)
        <div class="id-card__row">
            <span class="id-card__lbl">Class</span>
            <span class="id-card__val">{{ $ai->schoolClass?->name_en ?? '—' }}@if($ai->section) / {{ $ai->section->name_en }}@endif</span>
        </div>
        @if($ai->roll)
            <div class="id-card__row"><span class="id-card__lbl">Roll</span><span class="id-card__val">{{ $ai->roll }}</span></div>
        @endif
        @if($ai->group)
            <div class="id-card__row"><span class="id-card__lbl">Group</span><span class="id-card__val">{{ $ai->group->name_en }}</span></div>
        @endif
        <div class="id-card__row"><span class="id-card__lbl">Session</span><span class="id-card__val">{{ $ai->academicSession?->name_en ?? '—' }}</span></div>
    @endif
    @if($student->date_of_birth)
        <div class="id-card__row"><span class="id-card__lbl">DOB</span><span class="id-card__val">{{ $student->date_of_birth->format('d M Y') }}</span></div>
    @endif
    @if($student->blood_group)
        <div class="id-card__row"><span class="id-card__lbl">Blood</span><span class="id-card__val id-card__blood">{{ $student->blood_group_text }}</span></div>
    @endif
</div>
