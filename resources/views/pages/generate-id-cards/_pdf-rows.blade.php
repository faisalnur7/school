<div class="info-row"><span class="lbl">ID &nbsp;</span><span class="val">{{ $student->student_cid }}</span></div>
@if($ai)
    <div class="info-row"><span class="lbl">Class </span><span class="val">{{ $ai->schoolClass?->name_en ?? '—' }}@if($ai->section) / {{ $ai->section->name_en }}@endif</span></div>
    @if($ai->roll)<div class="info-row"><span class="lbl">Roll &nbsp;</span><span class="val">{{ $ai->roll }}</span></div>@endif
    @if($ai->group)<div class="info-row"><span class="lbl">Group </span><span class="val">{{ $ai->group->name_en }}</span></div>@endif
    <div class="info-row"><span class="lbl">Sess. </span><span class="val">{{ $ai->academicSession?->name_en ?? '—' }}</span></div>
@endif
@if($student->date_of_birth)<div class="info-row"><span class="lbl">DOB &nbsp;</span><span class="val">{{ $student->date_of_birth->format('d M Y') }}</span></div>@endif
@if($student->blood_group)<div class="info-row"><span class="lbl">Blood </span><span class="val blood">{{ $student->blood_group_text }}</span></div>@endif
