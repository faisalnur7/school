@php
    $renderForPdf = $renderForPdf ?? false;
    $studentPages = $students->chunk(4);
    $cardType = $cardType ?? 'id_card';
    $isLibraryCard = $cardType === 'library_card';
    $isAdmitOrSeatCard = in_array($cardType, ['admit_card', 'seat_card'], true);
    $idColor = $setting?->id_card_color ?? '#1e3a5f';
    $secondary = $setting?->secondary_color ?? '#2563eb';
    $frontTitle = match ($cardType) {
        'library_card' => 'LIBRARY CARD',
        'admit_card' => 'ADMIT CARD',
        'seat_card' => 'SEAT CARD',
        default => 'STUDENT ID',
    };
    $backBadge = match ($cardType) {
        'library_card' => 'LIBRARY',
        'admit_card' => 'ADMIT',
        'seat_card' => 'SEAT',
        default => 'BACK',
    };
    $backSectionTitle = $isLibraryCard || $isAdmitOrSeatCard ? 'Student Details' : 'Parent / Guardian';

    $logoPath = null;
    if ($setting?->logo && file_exists(public_path($setting->logo))) {
        $logoPath = $renderForPdf ? public_path($setting->logo) : asset($setting->logo);
    }
@endphp

<div class="id-card-pages">
    @foreach($studentPages as $pageIndex => $pageStudents)
        <div class="id-card-page">
            @foreach($pageStudents as $student)
                @php
                    $ai = $student->academicInformations->first();

                    $placeholder = $student->gender == \App\Models\Student::FEMALE
                        ? 'assets/img/female-placeholder.png'
                        : 'assets/img/male-placeholder.png';

                    $photoPath = null;
                    if ($student->image && file_exists(public_path($student->image))) {
                        $photoPath = $renderForPdf ? public_path($student->image) : asset($student->image);
                    } else {
                        $photoPath = $renderForPdf ? public_path($placeholder) : asset($placeholder);
                    }
                @endphp

                <div class="id-card-pair">
                    <div class="id-card">
                        <div class="id-card__header id-card__header--front">
                            @if($logoPath)
                                <img src="{{ $logoPath }}" class="id-card__logo" alt="Logo">
                            @endif
                            <div class="id-card__school-name">{{ $setting?->name ?? 'School Name' }}</div>
                            @if($setting?->slogan)
                                <div class="id-card__slogan">{{ $setting->slogan }}</div>
                            @endif
                            <div class="id-card__label-badge">{{ $frontTitle }}</div>
                        </div>

                        <div class="id-card__front-body">
                            <img src="{{ $photoPath }}" class="id-card__photo" alt="{{ $student->full_name_en }}">

                            <div class="id-card__info">
                                <div class="id-card__name">{{ $student->full_name_en }}</div>
                                @if($student->full_name_bn)
                                    <div class="id-card__name-bn">{{ $student->full_name_bn }}</div>
                                @endif

                                <div class="id-card__divider"></div>

                                <div class="id-card__rows">
                                    <div class="id-card__row">
                                        <span class="id-card__lbl">ID</span>
                                        <span class="id-card__val">{{ $student->student_cid }}</span>
                                    </div>
                                    @if($ai)
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">Class</span>
                                            <span class="id-card__val">{{ $ai->schoolClass?->name_en ?? '—' }}@if($ai->section) / {{ $ai->section->name_en }}@endif</span>
                                        </div>
                                        @if($ai->roll)
                                            <div class="id-card__row">
                                                <span class="id-card__lbl">Roll</span>
                                                <span class="id-card__val">{{ $ai->roll }}</span>
                                            </div>
                                        @endif
                                        @if($ai->group)
                                            <div class="id-card__row">
                                                <span class="id-card__lbl">Group</span>
                                                <span class="id-card__val">{{ $ai->group->name_en }}</span>
                                            </div>
                                        @endif
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">Session</span>
                                            <span class="id-card__val">{{ $ai->academicSession?->name_en ?? '—' }}</span>
                                        </div>
                                    @endif
                                    @if($student->date_of_birth)
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">DOB</span>
                                            <span class="id-card__val">{{ $student->date_of_birth->format('d M Y') }}</span>
                                        </div>
                                    @endif
                                    @if($student->blood_group)
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">Blood</span>
                                            <span class="id-card__val id-card__blood">{{ $student->blood_group_text }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="id-card__footer" style="background: linear-gradient(135deg, {{ $idColor }}, {{ $secondary }});">
                            @if($setting?->contact_number_1)
                                <span>📞 {{ $setting->contact_number_1 }}</span>
                            @endif
                            @if($setting?->contact_number_2)
                                <span>📞 {{ $setting->contact_number_2 }}</span>
                            @endif
                            @if($setting?->website)
                                <span>🌐 {{ $setting->website }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="id-card id-card--back">
                        <div class="id-card__header id-card__header--back">
                            <div class="id-card__school-name">{{ $setting?->name ?? 'School Name' }}</div>
                            @if($setting?->slogan)
                                <div class="id-card__slogan">{{ $setting->slogan }}</div>
                            @endif
                            <div class="id-card__label-badge">{{ $backBadge }}</div>
                        </div>

                        <div class="id-card__back-body">
                            <div class="id-card__back-section">
                                <div class="id-card__back-title">{{ $backSectionTitle }}</div>
                                @if($isLibraryCard || $isAdmitOrSeatCard)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Name</span>
                                        <span class="id-card__val">{{ $student->full_name_en }}</span>
                                    </div>
                                    @if($student->student_cid)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">ID</span>
                                            <span class="id-card__val">{{ $student->student_cid }}</span>
                                        </div>
                                    @endif
                                    @if($ai)
                                        @if($ai->schoolClass)
                                            <div class="id-card__back-row">
                                                <span class="id-card__lbl">Class</span>
                                                <span class="id-card__val">{{ $ai->schoolClass?->name_en ?? '—' }}@if($ai->section) / {{ $ai->section->name_en }}@endif</span>
                                            </div>
                                        @endif
                                        @if($ai->section)
                                            <div class="id-card__back-row">
                                                <span class="id-card__lbl">Section</span>
                                                <span class="id-card__val">{{ $ai->section->name_en }}</span>
                                            </div>
                                        @endif
                                        @if($ai->roll)
                                            <div class="id-card__back-row">
                                                <span class="id-card__lbl">Roll</span>
                                                <span class="id-card__val">{{ $ai->roll }}</span>
                                            </div>
                                        @endif
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Session</span>
                                            <span class="id-card__val">{{ $ai->academicSession?->name_en ?? '—' }}</span>
                                        </div>
                                    @endif
                                @else
                                    @if($student->father_name)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Father</span>
                                            <span class="id-card__val">{{ $student->father_name }}</span>
                                        </div>
                                    @endif
                                    @if($student->mother_name)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Mother</span>
                                            <span class="id-card__val">{{ $student->mother_name }}</span>
                                        </div>
                                    @endif
                                    @if($student->father_phone || $student->mother_phone)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Contact</span>
                                            <span class="id-card__val">{{ implode(', ', array_filter([$student->father_phone, $student->mother_phone])) }}</span>
                                        </div>
                                    @endif
                                    @if($student->present_address)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Address</span>
                                            <span class="id-card__val">{{ Str::limit($student->present_address, 46) }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="id-card__back-section">
                                <div class="id-card__back-title">{{ $isLibraryCard ? 'Library / School Contact' : ($isAdmitOrSeatCard ? 'School / Exam Contact' : 'School Contact') }}</div>
                                @if($setting?->address)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Address</span>
                                        <span class="id-card__val">{{ Str::limit($setting->address, 52) }}</span>
                                    </div>
                                @endif
                                @if($setting?->contact_number_1 || $setting?->contact_number_2)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Contact</span>
                                        <span class="id-card__val">{{ implode(', ', array_filter([$setting?->contact_number_1, $setting?->contact_number_2])) }}</span>
                                    </div>
                                @endif
                                @if($setting?->whatsapp_number)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">WhatsApp</span>
                                        <span class="id-card__val">{{ $setting->whatsapp_number }}</span>
                                    </div>
                                @endif
                                @if($setting?->email)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Email</span>
                                        <span class="id-card__val">{{ $setting->email }}</span>
                                    </div>
                                @endif
                                @if($setting?->website)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Web</span>
                                        <span class="id-card__val">{{ $setting->website }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($setting?->whatsapp_qr && file_exists(public_path($setting->whatsapp_qr)))
                                <div style="display:flex;justify-content:center;">
                                    <img src="{{ $renderForPdf ? public_path($setting->whatsapp_qr) : asset($setting->whatsapp_qr) }}" class="id-card__qr" alt="WhatsApp QR">
                                </div>
                            @endif

                            <div class="id-card__back-notice">If found, please return to the school.</div>
                        </div>

                        <div class="id-card__footer" style="background:#222;">
                            @if($setting?->eiin)
                                <span>EIIN: {{ $setting->eiin }}</span>
                            @endif
                            @if($setting?->whatsapp_number)
                                <span>📱 {{ $setting->whatsapp_number }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
