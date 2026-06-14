@php
    $renderForPdf = $renderForPdf ?? false;
    $layout = $layout ?? [];
    $cardsPerPage = max(1, min(10, (int) ($layout['cardsPerPage'] ?? 8)));
    $cardsPerRow = max(1, min($cardsPerPage, (int) ($layout['cardsPerRow'] ?? 2)));
    $pageRows = max(1, (int) ($layout['pageRows'] ?? ceil($cardsPerPage / $cardsPerRow)));
    $cardWidthMm = $layout['cardWidthMm'] ?? 80;
    $cardHeightMm = $layout['cardHeightMm'] ?? 46;
    $gapXmm = $layout['gapXmm'] ?? 4;
    $gapYmm = $layout['gapYmm'] ?? 4;
    $marginMm = $layout['marginMm'] ?? 8;

    $studentPages = $students->chunk($cardsPerPage);
    $cardTitle = $cardType ?? 'admit_card';
    $isSeatCard = $cardTitle === 'seat_card';
    $cardLabel = $isSeatCard ? 'SEAT CARD' : 'ADMIT CARD';
    $schoolAddress = $setting?->address;
    $examTypeLabel = $examType ? (strtolower($examType) === 'term' ? 'Terminal Exam' : 'Tutorial Exam') : null;
    $examName = $selectedExam?->name;

    $logoPath = null;
    if ($setting?->logo && file_exists(public_path($setting->logo))) {
        $logoPath = $renderForPdf ? public_path($setting->logo) : asset($setting->logo);
    }
@endphp

<div
    class="admit-card-pages"
>
    @foreach($studentPages as $pageStudents)
        <div
            class="admit-card-page"
            style="grid-template-columns: repeat({{ $cardsPerRow }}, {{ $cardWidthMm }}mm); gap: {{ $gapYmm }}mm {{ $gapXmm }}mm;"
        >
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

                <div class="admit-card" style="width: {{ $cardWidthMm }}mm; height: {{ $cardHeightMm }}mm;">
                    <div class="admit-card__header">
                        <div style="display:flex;align-items:flex-start;gap:1.4mm;justify-content:center;">
                            @if($logoPath)
                                <img src="{{ $logoPath }}" alt="Logo" style="width:6.5mm;height:6.5mm;object-fit:contain;filter:grayscale(1);margin-top:0.3mm;">
                            @endif
                            <div style="flex:1;min-width:0;">
                                <div class="admit-card__school">{{ $setting?->name ?? 'School Name' }}</div>
                                @if($schoolAddress)
                                    <div class="admit-card__meta">{{ $schoolAddress }}</div>
                                @endif
                                @if($examTypeLabel)
                                    <div class="admit-card__meta"><strong>Exam Type:</strong> {{ $examTypeLabel }}</div>
                                @endif
                                @if($examName)
                                    <div class="admit-card__meta"><strong>Exam Name:</strong> {{ $examName }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="admit-card__exam">
                            <div class="admit-card__exam-label">{{ $cardLabel }}</div>
                        </div>
                    </div>

                    <div class="admit-card__body">
                        <div class="admit-card__photo-wrap">
                            <img src="{{ $photoPath }}" class="admit-card__photo" alt="{{ $student->full_name_en }}">
                        </div>

                        <div class="admit-card__info">
                            <div class="admit-card__name">{{ $student->full_name_en }}</div>

                            <div class="admit-card__rows">
                                <div class="admit-card__row">
                                    <span class="admit-card__lbl">ID</span>
                                    <span class="admit-card__val">{{ $student->student_cid }}</span>
                                </div>
                                @if($ai?->roll)
                                    <div class="admit-card__row">
                                        <span class="admit-card__lbl">Roll</span>
                                        <span class="admit-card__val">{{ $ai->roll }}</span>
                                    </div>
                                @endif
                                <div class="admit-card__row">
                                    <span class="admit-card__lbl">Class</span>
                                    <span class="admit-card__val">{{ $ai?->schoolClass?->name_en ?? '—' }}</span>
                                </div>
                                <div class="admit-card__row">
                                    <span class="admit-card__lbl">Section</span>
                                    <span class="admit-card__val">{{ $ai?->section?->name_en ?? '—' }}</span>
                                </div>
                                <div class="admit-card__row">
                                    <span class="admit-card__lbl">Session</span>
                                    <span class="admit-card__val">{{ $ai?->academicSession?->name_en ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="admit-card__footer">
                        @if($setting?->eiin)
                            <span>EIIN: {{ $setting->eiin }}</span>
                        @endif
                        @if($setting?->contact_number_1)
                            <span>{{ $setting->contact_number_1 }}</span>
                        @endif
                        @if($setting?->whatsapp_number)
                            <span>{{ $setting->whatsapp_number }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
