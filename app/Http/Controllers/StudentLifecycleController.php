<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Certificate;
use App\Models\Division;
use App\Models\District;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\Group;
use App\Models\PoliceStation;
use App\Models\PostOffice;
use App\Models\Profession;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\StudentSubject;
use App\Models\CertificateTemplate;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class StudentLifecycleController extends Controller
{
    private const CERTIFICATE_STYLES = ['classic', 'modern'];

    private function baseData(): array
    {
        return [
            'sessions' => AcademicSession::orderByDesc('id')->get(),
            'classes'  => SchoolClass::get(),
            'sections' => Section::orderBy('name_en')->get(),
            'groups'   => Group::orderBy('name_en')->get(),
        ];
    }

    private function certificateStyles(): array
    {
        return self::CERTIFICATE_STYLES;
    }

    private function normalizeCertificateStyle(?string $style, string $fallback = 'modern'): string
    {
        $style = strtolower(trim((string) $style));

        return in_array($style, $this->certificateStyles(), true) ? $style : $fallback;
    }

    private function certificateTemplateCatalog(): array
    {
        return [
            'transfer_certificate' => [
                'title' => 'Transfer Certificate',
                'subtitle' => 'Print a transfer certificate in classic or modern style.',
                'icon' => 'fa-scroll',
                'route' => 'students.tc',
                'pdf_route' => 'students.tc.pdf',
                'default_style' => 'modern',
                'placeholder_group' => 'transfer_certificate',
                'accent' => ['from' => '#0f766e', 'to' => '#0d9488'],
            ],
            'testimonial' => [
                'title' => 'Testimonial',
                'subtitle' => 'Print a testimonial with school and student placeholders.',
                'icon' => 'fa-certificate',
                'route' => 'students.testimonial',
                'pdf_route' => 'students.testimonial.pdf',
                'default_style' => 'modern',
                'placeholder_group' => 'testimonial',
                'accent' => ['from' => '#7c3aed', 'to' => '#5b21b6'],
            ],
        ];
    }

    private function certificatePlaceholderGroups(): array
    {
        return Certificate::placeholderGroups();
    }

    private function certificateTemplateBodyForSlug(string $slug): string
    {
        $definition = collect(Certificate::defaultTypeDefinitions())->firstWhere('slug', $slug);

        return $definition['templates'][0]['body'] ?? $this->schoolCertificateFallback($slug);
    }

    private function schoolCertificateFallback(string $slug): string
    {
        return match ($slug) {
            'transfer-certificate' => SchoolSetting::current()->transfer_certificate_template
                ?? Certificate::defaultTypeDefinitions()[0]['templates'][0]['body'],
            'testimonial' => SchoolSetting::current()->testimonial_template
                ?? Certificate::defaultTypeDefinitions()[1]['templates'][0]['body'],
            default => '',
        };
    }

    private function activeCertificateTemplate(Certificate $certificate): ?CertificateTemplate
    {
        $certificate->loadMissing(['templates', 'activeTemplate']);

        return $certificate->activeTemplate ?? $certificate->templates->first();
    }

    private function certificateBySlug(string $slug): Certificate
    {
        Certificate::ensureDefaults();

        return Certificate::with(['templates', 'activeTemplate'])->where('slug', $slug)->firstOrFail();
    }

    private function renderTemplateText(?string $template, array $replacements): string
    {
        $template = trim((string) $template);
        $template = $template !== '' ? $template : '';

        $tokenMap = [];
        foreach (array_values($replacements) as $index => $value) {
            $tokenMap['__CERT_TOKEN_' . $index . '__'] = '<strong>' . e((string) $value) . '</strong>';
        }

        $rendered = str_replace(array_keys($replacements), array_keys($tokenMap), $template);
        $paragraphs = preg_split('/\R{2,}/', trim($rendered)) ?: [];

        return collect($paragraphs)
            ->filter(fn ($paragraph) => trim((string) $paragraph) !== '')
            ->map(function ($paragraph) use ($tokenMap) {
                $escaped = nl2br(e(trim((string) $paragraph)));

                return '<p>' . str_replace(array_keys($tokenMap), array_values($tokenMap), $escaped) . '</p>';
            })
            ->implode("\n");
    }

    private function pdfDownloadResponse(\Mpdf\Mpdf $mpdf, string $filename)
    {
        return response($mpdf->Output($filename, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function certificateTemplateHtml(string $type, Student $student, ?StudentAcademicInformation $academicInfo, SchoolSetting $setting): string
    {
        $defaults = $this->certificateTemplateDefaults();
        $settingText = $type === 'testimonial'
            ? ($setting->testimonial_template ?? null)
            : ($setting->transfer_certificate_template ?? null);

        $template = trim((string) $settingText) !== '' ? $settingText : $defaults[$type];

        $replacements = [
            '{{ student.full_name_en }}' => $student->full_name_en ?? '',
            '{{ student.full_name_bn }}' => $student->full_name_bn ?? '',
            '{{ student.student_cid }}' => $student->student_cid ?? '',
            '{{ student.father_name }}' => $student->father_name ?? '',
            '{{ student.mother_name }}' => $student->mother_name ?? '',
            '{{ student.date_of_birth }}' => $student->date_of_birth ? $student->date_of_birth->format('d F Y') : '',
            '{{ student.birth_certificate_number }}' => $student->birth_certificate_number ?? '',
            '{{ academicInfo.schoolClass.name_en }}' => $academicInfo?->schoolClass?->name_en ?? '',
            '{{ academicInfo.section.name_en }}' => $academicInfo?->section?->name_en ?? '',
            '{{ academicInfo.academicSession.name_en }}' => $academicInfo?->academicSession?->name_en ?? '',
            '{{ academicInfo.roll }}' => (string) ($academicInfo?->roll ?? ''),
            '{{ academicInfo.checkout_date }}' => $academicInfo?->checkout_date ? $academicInfo->checkout_date->format('d F Y') : '',
            '{{ academicInfo.academic_status }}' => $academicInfo?->academic_status ? ucfirst($academicInfo->academic_status) : '',
            '{{ issueDate }}' => now()->format('d F Y'),
            '{{ setting.name }}' => $setting->name ?? config('app.name'),
            '{{ setting.address }}' => $setting->address ?? '',
        ];

        return $this->renderTemplateText($template, $replacements);
    }

    private const PROMOTION_MODE_MERIT = 'final_term_merit_list';
    private const PROMOTION_MODE_FAIL = 'n_subjects_fail';
    private const PROMOTION_MODE_CUSTOM = 'custom';

    private function promotionModes(): array
    {
        return [
            self::PROMOTION_MODE_MERIT => 'Final Term Merit List',
            self::PROMOTION_MODE_FAIL => 'N Subjects Fail',
            self::PROMOTION_MODE_CUSTOM => 'Custom',
        ];
    }

    private function normalizePromotionMode(?string $mode): string
    {
        $mode = trim((string) $mode);

        return match ($mode) {
            'merit', 'merit_list' => self::PROMOTION_MODE_MERIT,
            'fail', 'fail_based' => self::PROMOTION_MODE_FAIL,
            self::PROMOTION_MODE_CUSTOM => self::PROMOTION_MODE_CUSTOM,
            default => self::PROMOTION_MODE_MERIT,
        };
    }

    private function promotionFilterDefaults(Request $request): array
    {
        $sourceSessionId = $request->input('source_session_id', $request->input('academic_session_id'));
        $sourceClassId = $request->input('source_class_id', $request->input('school_class_id'));
        $targetSessionId = $request->input('target_session_id');
        $targetClassId = $request->input('target_class_id');
        $studentId = trim((string) $request->input('student_id', $request->input('student_cid', '')));
        $mode = $this->normalizePromotionMode($request->input('promotion_mode'));
        $failThreshold = $request->input('fail_threshold', 1);

        return [
            'source_session_id' => $sourceSessionId !== null && $sourceSessionId !== '' ? (string) $sourceSessionId : null,
            'source_class_id' => $sourceClassId !== null && $sourceClassId !== '' ? (string) $sourceClassId : null,
            'target_session_id' => $targetSessionId !== null && $targetSessionId !== '' ? (string) $targetSessionId : null,
            'target_class_id' => $targetClassId !== null && $targetClassId !== '' ? (string) $targetClassId : null,
            'student_id' => $studentId !== '' ? $studentId : null,
            'promotion_mode' => $mode,
            'fail_threshold' => $failThreshold !== null && $failThreshold !== '' ? (int) $failThreshold : 1,
        ];
    }

    private function resolvePromotionStudent(?string $studentId): ?Student
    {
        $studentId = trim((string) $studentId);

        if ($studentId === '') {
            return null;
        }

        return Student::query()
            ->where('student_cid', $studentId)
            ->orWhere('id', is_numeric($studentId) ? (int) $studentId : -1)
            ->first();
    }

    private function promotionSubjectPool(int $classId): \Illuminate\Support\Collection
    {
        return SubjectClassAssignment::query()
            ->with(['subject' => fn ($query) => $query->with('papers')])
            ->where('school_class_id', $classId)
            ->where('is_active', true)
            ->get();
    }

    private function promotionSubjectsForAcademicInfo(StudentAcademicInformation $info, \Illuminate\Support\Collection $assignments): \Illuminate\Support\Collection
    {
        $student = $info->student;

        return $assignments
            ->filter(function (SubjectClassAssignment $assignment) use ($info, $student) {
                if ($assignment->group_id !== null && (int) $assignment->group_id !== (int) $info->group_id) {
                    return false;
                }

                return $assignment->appliesToStudent(
                    $student?->gender,
                    $student?->religion
                );
            })
            ->flatMap(function (SubjectClassAssignment $assignment) {
                $subject = $assignment->subject;

                if (! $subject) {
                    return [];
                }

                if ($subject->is_parent && $subject->papers->isNotEmpty()) {
                    return $subject->papers;
                }

                return [$subject];
            })
            ->unique('id')
            ->values();
    }

    private function promotionCurrentAcademicInfos(array $filters): \Illuminate\Support\Collection
    {
        $student = $this->resolvePromotionStudent($filters['student_id'] ?? null);

        $query = StudentAcademicInformation::query()
            ->with(['student', 'academicSession', 'schoolClass', 'section', 'group'])
            ->where('academic_session_id', $filters['source_session_id'])
            ->where('school_class_id', $filters['source_class_id'])
            ->where('is_current', true)
            ->where('academic_status', 'active')
            ->orderByDesc('id');

        if ($student) {
            $query->where('student_id', $student->id);
        }

        return $query->get()
            ->unique('student_id')
            ->values();
    }

    private function promotionMetrics(int $sourceSessionId, int $sourceClassId, \Illuminate\Support\Collection $infos): array
    {
        $assignments = $this->promotionSubjectPool($sourceClassId);
        $exam = Exam::query()
            ->where('academic_session_id', $sourceSessionId)
            ->where('type', Exam::TYPE_TERMINAL)
            ->where('status', Exam::STATUS_PUBLISHED)
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->first();

        if (! $exam) {
            return [
                'exam' => null,
                'metrics' => $infos->mapWithKeys(function (StudentAcademicInformation $info, int $index) {
                    return [$info->student_id => [
                        'source_rank' => $index + 1,
                        'source_total' => 0,
                        'failed_subjects' => 0,
                        'source_fail_status' => 'Pending result data',
                    ]];
                })->all(),
            ];
        }

        $studentIds = $infos->pluck('student_id')->all();
        $marks = ExamMark::query()
            ->where('exam_id', $exam->id)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id');

        $metrics = [];
        foreach ($infos as $info) {
            $subjects = $this->promotionSubjectsForAcademicInfo($info, $assignments);
            $studentMarks = $marks->get($info->student_id, collect());

            $total = 0.0;
            $failedSubjects = 0;
            $subjectCount = 0;

            foreach ($subjects as $subject) {
                $subjectCount++;
                $config = $subject->getEffectiveMarksForClass($sourceClassId);
                $passMark = (float) ($config['pass_mark'] ?? 33);
                $mark = $studentMarks->firstWhere('subject_id', $subject->id);
                $obtained = $mark ? (float) $mark->total : 0.0;
                $isAbsent = ! $mark || (bool) $mark->is_absent;
                $isFailed = $isAbsent || $obtained < $passMark || (($mark->letter_grade ?? null) === 'F');

                $total += $obtained;
                if ($isFailed) {
                    $failedSubjects++;
                }
            }

            $metrics[$info->student_id] = [
                'source_rank' => 0,
                'source_total' => round($total, 2),
                'failed_subjects' => $failedSubjects,
                'subject_count' => $subjectCount,
                'source_fail_status' => $failedSubjects > 0 ? 'Failed in ' . $failedSubjects . ' subject(s)' : 'Passed',
            ];
        }

        $sorted = $infos->sort(function (StudentAcademicInformation $a, StudentAcademicInformation $b) use ($metrics) {
            $aMetrics = $metrics[$a->student_id];
            $bMetrics = $metrics[$b->student_id];

            if ($aMetrics['source_total'] !== $bMetrics['source_total']) {
                return $bMetrics['source_total'] <=> $aMetrics['source_total'];
            }

            if ($aMetrics['failed_subjects'] !== $bMetrics['failed_subjects']) {
                return $aMetrics['failed_subjects'] <=> $bMetrics['failed_subjects'];
            }

            return strcasecmp($a->student?->full_name_en ?? '', $b->student?->full_name_en ?? '');
        })->values();

        $rank = 1;
        $prevTotal = null;
        $sameCount = 0;
        foreach ($sorted as $info) {
            $total = $metrics[$info->student_id]['source_total'];

            if ($prevTotal !== null && $total === $prevTotal) {
                $metrics[$info->student_id]['source_rank'] = $rank - $sameCount;
                $sameCount++;
            } else {
                $metrics[$info->student_id]['source_rank'] = $rank;
                $sameCount = 1;
            }

            $prevTotal = $total;
            $rank++;
        }

        return [
            'exam' => $exam,
            'metrics' => $metrics,
        ];
    }

    private function promotionTargetRollBase(array $row): ?int
    {
        if (empty($row['target_session_id']) || empty($row['target_class_id'])) {
            return null;
        }

        return StudentAcademicInformation::getNextRoll(
            (int) $row['target_session_id'],
            (int) $row['target_class_id'],
            $row['target_section_id'] !== null ? (int) $row['target_section_id'] : null,
            $row['target_group_id'] !== null ? (int) $row['target_group_id'] : null
        );
    }

    private function promotionRowsForView(array $filters): \Illuminate\Support\Collection
    {
        $infos = $this->promotionCurrentAcademicInfos($filters);
        $metricBundle = $this->promotionMetrics((int) $filters['source_session_id'], (int) $filters['source_class_id'], $infos);
        $metrics = $metricBundle['metrics'];
        $mode = $filters['promotion_mode'];
        $targetSessionId = $filters['target_session_id'] ? (int) $filters['target_session_id'] : null;
        $targetClassId = $filters['target_class_id'] ? (int) $filters['target_class_id'] : null;

        $rows = $infos->map(function (StudentAcademicInformation $info) use ($metrics, $mode, $targetSessionId, $targetClassId) {
            $metric = $metrics[$info->student_id] ?? [
                'source_rank' => null,
                'source_total' => 0,
                'failed_subjects' => 0,
                'source_fail_status' => '—',
            ];

            $row = [
                'academic_info' => $info,
                'student' => $info->student,
                'source_rank' => $metric['source_rank'],
                'source_total' => $metric['source_total'],
                'failed_subjects' => $metric['failed_subjects'],
                'source_fail_status' => $metric['source_fail_status'],
                'selected' => true,
                'target_session_id' => $targetSessionId,
                'target_class_id' => $targetClassId,
                'target_section_id' => $info->section_id,
                'target_group_id' => $info->group_id,
            ];

            $row['target_roll'] = $this->promotionTargetRollBase($row);

            return $row;
        });

        if ($mode === self::PROMOTION_MODE_FAIL) {
            $threshold = max(1, (int) ($filters['fail_threshold'] ?? 1));

            $rows = $rows->filter(fn (array $row) => ($row['failed_subjects'] ?? 0) >= $threshold)->values();
        }

        return match ($mode) {
            self::PROMOTION_MODE_FAIL => $rows->sort(function (array $a, array $b) {
                if ($a['failed_subjects'] !== $b['failed_subjects']) {
                    return $b['failed_subjects'] <=> $a['failed_subjects'];
                }

                if ($a['source_rank'] !== $b['source_rank']) {
                    return $a['source_rank'] <=> $b['source_rank'];
                }

                return strcasecmp($a['student']?->full_name_en ?? '', $b['student']?->full_name_en ?? '');
            })->values(),
            default => $rows->sort(function (array $a, array $b) {
                if ($a['source_rank'] !== $b['source_rank']) {
                    return $a['source_rank'] <=> $b['source_rank'];
                }

                if ($a['source_total'] !== $b['source_total']) {
                    return $b['source_total'] <=> $a['source_total'];
                }

                return strcasecmp($a['student']?->full_name_en ?? '', $b['student']?->full_name_en ?? '');
            })->values(),
        };
    }

    private function applySequentialTargetRolls(\Illuminate\Support\Collection $rows): \Illuminate\Support\Collection
    {
        $nextRolls = [];

        return $rows->values()->map(function (array $row) use (&$nextRolls) {
            $bucketKey = implode(':', [
                $row['target_session_id'] ?? 'null',
                $row['target_class_id'] ?? 'null',
                $row['target_section_id'] ?? 'null',
                $row['target_group_id'] ?? 'null',
            ]);

            if (! array_key_exists($bucketKey, $nextRolls)) {
                $nextRolls[$bucketKey] = $row['target_roll'] ?? $this->promotionTargetRollBase($row);
            }

            $row['target_roll'] = $nextRolls[$bucketKey];
            $nextRolls[$bucketKey] = ((int) $nextRolls[$bucketKey]) + 1;

            return $row;
        });
    }

    private function applyPromotedStudentFees(StudentAcademicInformation $academicInfo): void
    {
        $student = $academicInfo->student;
        if (! $student) {
            return;
        }

        $feeSets = FeeSet::with('items.category')
            ->where('school_class_id', $academicInfo->school_class_id)
            ->where('academic_session_id', $academicInfo->academic_session_id)
            ->get();

        $studentType = $student->academicInformations()->count() > 1 ? 'old' : 'new';

        foreach ($feeSets as $feeSet) {
            $applicableAmount = $feeSet->items->filter(fn ($item) =>
                in_array($item->category->student_type ?? 'both', ['both', $studentType], true)
            )->sum('amount');

            if ($applicableAmount <= 0) {
                continue;
            }

            foreach ($this->generateFeeDueDates($feeSet->frequency, $feeSet->month, $feeSet->due_date) as $dueDate) {
                Fee::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'fee_set_id' => $feeSet->id,
                        'due_date' => $dueDate,
                    ],
                    [
                        'amount' => $applicableAmount,
                        'status' => 'pending',
                    ]
                );
            }
        }
    }

    private function applyPromotedStudentSubjects(StudentAcademicInformation $academicInfo): void
    {
        $student = $academicInfo->student;
        if (! $student) {
            return;
        }

        $gender = $student->gender == 1 ? 'male' : 'female';
        $religion = match ((int) $student->religion) {
            1 => 'islam',
            2 => 'hinduism',
            3 => 'christianity',
            4 => 'buddhism',
            default => 'other',
        };

        $assignments = SubjectClassAssignment::query()
            ->where('school_class_id', $academicInfo->school_class_id)
            ->where(function ($query) use ($academicInfo) {
                $query->whereNull('group_id')
                    ->orWhere('group_id', $academicInfo->group_id);
            })
            ->where(function ($query) use ($gender) {
                $query->where('gender', 'all')
                    ->orWhere('gender', $gender);
            })
            ->where(function ($query) use ($religion) {
                $query->where('religion', 'all')
                    ->orWhere('religion', $religion);
            })
            ->where('is_compulsory', true)
            ->where('is_active', true)
            ->with('subject')
            ->get();

        foreach ($assignments as $assignment) {
            $subject = $assignment->subject;
            if (! $subject) {
                continue;
            }

            StudentSubject::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'academic_session_id' => $academicInfo->academic_session_id,
                ],
                [
                    'school_class_id' => $academicInfo->school_class_id,
                    'is_optional' => false,
                    'is_mandatory' => true,
                ]
            );
        }
    }

    private function generateFeeDueDates(string $frequency, $month = null, $dueDate = null): array
    {
        $year = now()->year;
        $dates = [];

        switch ($frequency) {
            case 'monthly':
                for ($m = 1; $m <= 12; $m++) {
                    $dates[] = Carbon::create($year, $m, 1)->endOfMonth()->toDateString();
                }
                break;
            case 'yearly':
                $dates[] = $dueDate ? Carbon::parse($dueDate)->toDateString() : Carbon::create($year, 12, 31)->toDateString();
                break;
            case 'others':
                if (! empty($month)) {
                    $dates[] = Carbon::create($year, (int) $month, 1)->endOfMonth()->toDateString();
                }
                break;
        }

        return $dates;
    }

    private function applyPromotedStudentLifecycleData(StudentAcademicInformation $academicInfo): void
    {
        $this->applyPromotedStudentFees($academicInfo);
        $this->applyPromotedStudentSubjects($academicInfo);
    }

    private function promotionIndexValidationRules(): array
    {
        return [
            'source_session_id' => ['required_with:source_class_id', 'exists:academic_sessions,id'],
            'source_class_id'   => ['required_with:source_session_id', 'exists:school_classes,id'],
            'target_session_id' => ['nullable', 'exists:academic_sessions,id'],
            'target_class_id'   => ['nullable', 'exists:school_classes,id'],
            'student_id'        => ['nullable', 'string', 'max:255'],
            'promotion_mode'    => ['nullable'],
            'fail_threshold'    => ['nullable', 'integer', 'min:1'],
        ];
    }

    private function selectedPromotionRows(array $rows): array
    {
        return array_values(array_filter($rows, fn (array $row) => ! empty($row['selected'])));
    }

    private function preparePromotionStorePayload(Request $request): array
    {
        return [
            'source_session_id' => $request->input('source_session_id', $request->input('academic_session_id')),
            'source_class_id' => $request->input('source_class_id', $request->input('school_class_id')),
            'target_session_id' => $request->input('target_session_id'),
            'target_class_id' => $request->input('target_class_id'),
            'student_id' => $request->input('student_id', $request->input('student_cid')),
            'promotion_mode' => $this->normalizePromotionMode($request->input('promotion_mode')),
            'fail_threshold' => $request->input('fail_threshold', 1),
            'promotions' => $request->input('promotions', []),
        ];
    }

    // ─── A. New Admission ────────────────────────────────────────────────────

    public function admissionForm()
    {
        return view('pages.students.lifecycle.admission', [
            'academicSessions' => AcademicSession::all(),
            'classes'          => SchoolClass::all(),
            'sections'         => Section::all(),
            'groups'           => Group::all(),
            'divisions'        => Division::all(),
            'districts'        => District::all(),
            'policeStations'   => PoliceStation::all(),
            'postOffices'      => PostOffice::all(),
            'feeSets'          => FeeSet::all(),
            'professions'      => Profession::orderBy('name')->get(),
            'nextStudentCid'   => Student::generateNextCid(),
        ]);
    }

    public function admissionStore(Request $request)
    {
        return app(StudentController::class)->store($request);
    }

    // ─── B. Promote Students ─────────────────────────────────────────────────

    public function promoteIndex(Request $request)
    {
        $filters = $this->promotionFilterDefaults($request);

        if (filled($filters['source_session_id']) || filled($filters['source_class_id'])) {
            $validator = Validator::make($filters, $this->promotionIndexValidationRules());
            $validator->validate();
        }

        $students = collect();
        if (! empty($filters['source_session_id']) && ! empty($filters['source_class_id'])) {
            $students = $this->promotionRowsForView($filters);

            if ($students->isNotEmpty() && ! empty($filters['target_session_id']) && ! empty($filters['target_class_id'])) {
                $students = $this->applySequentialTargetRolls($students);
            }
        }

        return view('pages.students.lifecycle.promote', array_merge($this->baseData(), [
            'students' => $students,
            'filters' => $filters,
            'promotionModes' => $this->promotionModes(),
        ]));
    }

    public function promoteStore(Request $request)
    {
        $payload = $this->preparePromotionStorePayload($request);

        $validated = Validator::make($payload, [
            'source_session_id' => ['required', 'exists:academic_sessions,id'],
            'source_class_id'   => ['required', 'exists:school_classes,id'],
            'target_session_id' => ['required', 'exists:academic_sessions,id', 'different:source_session_id'],
            'target_class_id'   => ['required', 'exists:school_classes,id'],
            'student_id'        => ['nullable', 'string', 'max:255'],
            'promotion_mode'    => ['required', 'in:' . implode(',', array_keys($this->promotionModes()))],
            'fail_threshold'    => ['nullable', 'integer', 'min:1'],
            'promotions'        => ['required', 'array', 'min:1'],
        ])->validate();

        $selectedRows = $this->selectedPromotionRows($validated['promotions']);
        if (empty($selectedRows)) {
            return back()->withErrors(['promotions' => 'Select at least one student to promote.'])->withInput();
        }

        foreach ($selectedRows as $index => $row) {
            Validator::make($row, [
                'selected' => ['nullable'],
                'source_academic_information_id' => ['required', 'exists:student_academic_information,id'],
                'student_id' => ['required'],
                'target_roll' => ['required', 'integer', 'min:1'],
                'target_section_id' => ['nullable', 'exists:sections,id'],
                'target_group_id' => ['nullable', 'exists:groups,id'],
            ])->validate();

            if ($validated['promotion_mode'] === self::PROMOTION_MODE_CUSTOM && (int) $row['target_roll'] < 1) {
                throw ValidationException::withMessages([
                    'promotions' => 'Target rolls must be positive integers.',
                ]);
            }
        }

        if ($validated['promotion_mode'] === self::PROMOTION_MODE_CUSTOM) {
            $rolls = collect($selectedRows)->pluck('target_roll')->map(fn ($value) => (string) $value);
            if ($rolls->contains(fn ($value) => trim($value) === '') || $rolls->unique()->count() !== $rolls->count()) {
                return back()->withErrors(['promotions' => 'Target rolls must be filled in and unique for custom promotion.'])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($validated, $selectedRows) {
                foreach ($selectedRows as $row) {
                    $source = StudentAcademicInformation::query()
                        ->with('student')
                        ->whereKey($row['source_academic_information_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ((int) $source->academic_session_id !== (int) $validated['source_session_id'] || (int) $source->school_class_id !== (int) $validated['source_class_id']) {
                        throw ValidationException::withMessages([
                            'promotions' => 'One or more selected rows do not belong to the chosen source session/class.',
                        ]);
                    }

                    if (! $source->is_current) {
                        throw ValidationException::withMessages([
                            'promotions' => 'Only current academic information records can be promoted.',
                        ]);
                    }

                    $existingTarget = StudentAcademicInformation::query()
                        ->where('student_id', $source->student_id)
                        ->where('academic_session_id', $validated['target_session_id'])
                        ->lockForUpdate()
                        ->exists();

                    if ($existingTarget) {
                        throw ValidationException::withMessages([
                            'target_session_id' => 'One or more students already have a record for the target session.',
                        ]);
                    }

                    StudentAcademicInformation::query()
                        ->where('student_id', $source->student_id)
                        ->where('is_current', true)
                        ->lockForUpdate()
                        ->update(['is_current' => false]);

                    $promotionStatus = ((int) $validated['target_class_id'] === (int) $source->school_class_id)
                        ? 'retained'
                        : 'promoted';

                    $newAcademicInfo = StudentAcademicInformation::create([
                        'student_id' => $source->student_id,
                        'academic_session_id' => $validated['target_session_id'],
                        'school_class_id' => $validated['target_class_id'],
                        'section_id' => filled($row['target_section_id'] ?? null) ? $row['target_section_id'] : $source->section_id,
                        'group_id' => filled($row['target_group_id'] ?? null) ? $row['target_group_id'] : $source->group_id,
                        'roll' => $row['target_roll'],
                        'academic_status' => 'active',
                        'promotion_status' => $promotionStatus,
                        'is_current' => true,
                        'previous_academic_information_id' => $source->id,
                    ]);

                    $newAcademicInfo->loadMissing('student');
                    $this->applyPromotedStudentLifecycleData($newAcademicInfo);
                }
            });
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors(['target_session_id' => 'One or more students already have a record for the target session.'])->withInput();
        }

        return redirect()->route('students.promote', array_filter([
            'source_session_id' => $validated['source_session_id'],
            'source_class_id' => $validated['source_class_id'],
            'target_session_id' => $validated['target_session_id'],
            'target_class_id' => $validated['target_class_id'],
            'student_id' => $validated['student_id'] ?? null,
            'promotion_mode' => $validated['promotion_mode'],
            'fail_threshold' => $validated['fail_threshold'] ?? null,
        ], fn ($value) => $value !== null && $value !== ''))
            ->with('success', count($selectedRows) . ' student(s) promoted successfully.');
    }

    // ─── C. Mid-Year Correction ──────────────────────────────────────────────

    public function correctionIndex(Request $request)
    {
        $students = collect();
        if ($request->filled(['academic_session_id', 'school_class_id'])) {
            $students = StudentAcademicInformation::with(['student', 'section', 'group'])
                ->where('academic_session_id', $request->academic_session_id)
                ->where('school_class_id', $request->school_class_id)
                ->where('is_current', true)
                ->get();
        }

        return view('pages.students.lifecycle.correction', array_merge($this->baseData(), compact('students')));
    }

    public function correctionUpdate(Request $request, $id)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'section_id'      => 'required|exists:sections,id',
        ]);

        $record = StudentAcademicInformation::findOrFail($id);
        $record->update($request->only(['school_class_id', 'section_id', 'group_id', 'roll']));

        return back()->with('success', 'Record updated. No new session record was created.');
    }

    // ─── D. Student Checkout ─────────────────────────────────────────────────

    public function checkoutIndex(Request $request)
    {
        $students = collect();

        $studentCid = trim((string) $request->input('student_cid', ''));

        if ($studentCid !== '') {
            $students = StudentAcademicInformation::with(['student', 'section'])
                ->whereHas('student', function ($query) use ($studentCid) {
                    $query->where('student_cid', $studentCid);
                })
                ->where('is_current', true)
                ->where('academic_status', 'active')
                ->when($request->filled('academic_session_id'), function ($query) use ($request) {
                    $query->where('academic_session_id', $request->academic_session_id);
                })
                ->when($request->filled('school_class_id'), function ($query) use ($request) {
                    $query->where('school_class_id', $request->school_class_id);
                })
                ->get();
        } elseif ($request->filled(['academic_session_id', 'school_class_id'])) {
            $students = StudentAcademicInformation::with(['student', 'section'])
                ->where('academic_session_id', $request->academic_session_id)
                ->where('school_class_id', $request->school_class_id)
                ->where('is_current', true)
                ->where('academic_status', 'active')
                ->get();
        }

        $pendingFeesByStudent = Fee::with('feeSet')
            ->whereIn('student_id', $students->pluck('student_id')->all())
            ->where('is_active', 1)
            ->where('status', '!=', 'paid')
            ->whereRaw('(amount - COALESCE(scholarship_discount,0) - COALESCE(paid_amount,0)) > 0')
            ->orderBy('due_date')
            ->get()
            ->groupBy('student_id');

        $students = $students->map(function ($rec) use ($pendingFeesByStudent) {
            $rec->pendingFees = $pendingFeesByStudent->get($rec->student_id, collect());
            $rec->totalDue = $rec->pendingFees->sum(
                fn($f) => max(0, $f->amount - ($f->scholarship_discount ?? 0) - ($f->paid_amount ?? 0))
            );

            return $rec;
        });

        return view('pages.students.lifecycle.checkout', array_merge($this->baseData(), compact('students')));
    }

    public function checkoutStore(Request $request, $id)
    {
        $request->validate([
            'checkout_type' => 'required|in:transferred,graduated,withdrawn,expelled',
            'checkout_date' => 'required|date',
            'notes'         => 'nullable|string|max:1000',
            'immediate_checkout' => 'nullable|boolean',
        ]);

        $record = StudentAcademicInformation::where('id', $id)
            ->where('is_current', true)
            ->where('academic_status', 'active')
            ->firstOrFail();

        $totalDue = Fee::where('student_id', $record->student_id)
            ->where('is_active', 1)
            ->where('status', '!=', 'paid')
            ->whereRaw('(amount - COALESCE(scholarship_discount,0) - COALESCE(paid_amount,0)) > 0')
            ->sum(DB::raw('amount - COALESCE(scholarship_discount,0) - COALESCE(paid_amount,0)'));

        $isImmediateCheckout = $request->boolean('immediate_checkout');

        if ($totalDue > 0 && ! $isImmediateCheckout) {
            return back()->withErrors([
                'checkout_type' => 'Cannot checkout: student has pending dues of ' . number_format($totalDue, 2) . '. Please clear all fees first or use Immediate Checkout.',
            ]);
        }

        DB::transaction(function () use ($record, $request, $isImmediateCheckout) {
            if ($isImmediateCheckout) {
                Fee::where('student_id', $record->student_id)
                    ->where('is_active', 1)
                    ->where('status', '!=', 'paid')
                    ->whereRaw('(amount - COALESCE(scholarship_discount,0) - COALESCE(paid_amount,0)) > 0')
                    ->update(['is_active' => 0]);
            }

            $record->update([
                'academic_status' => $request->checkout_type,
                'is_current'      => false,
                'checkout_date'   => $request->checkout_date,
                'notes'           => $request->notes,
            ]);

            Student::where('id', $record->student_id)->update([
                'status' => 0,
            ]);
        });

        return redirect()->route('students.checkout')->with('success', 'Student checked out. Record preserved for history.');
    }

    public function checkedOutIndex(Request $request)
    {
        $records = StudentAcademicInformation::with(['student', 'academicSession', 'schoolClass', 'section', 'group'])
            ->where('is_current', false)
            ->whereIn('academic_status', ['transferred', 'graduated', 'withdrawn', 'expelled'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim($request->search);
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('full_name_en', 'like', "%{$search}%")
                        ->orWhere('full_name_bn', 'like', "%{$search}%")
                        ->orWhere('student_cid', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('checkout_date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('pages.students.lifecycle.checked-out', compact('records'));
    }

    // ─── E. Academic History ─────────────────────────────────────────────────

    public function historyIndex(Request $request)
    {
        $students = collect();
        if ($request->filled('search')) {
            $q = $request->search;
            $students = Student::where('full_name_en', 'like', "%{$q}%")
                ->orWhere('full_name_bn', 'like', "%{$q}%")
                ->orWhere('student_cid', 'like', "%{$q}%")
                ->limit(30)->get();
        }

        return view('pages.students.lifecycle.history-index', compact('students'));
    }

    public function historyShow(Student $student)
    {
        $records = StudentAcademicInformation::with(['academicSession', 'schoolClass', 'section', 'group'])
            ->where('student_id', $student->id)
            ->orderByDesc('academic_session_id')
            ->get();

        return view('pages.students.lifecycle.history-show', compact('student', 'records'));
    }

    // ─── F. Certificates ─────────────────────────────────────────────────────

    private function certificateData(Student $student): array
    {
        $student->loadMissing([
            'presentDivision',
            'presentDistrict',
            'presentPoliceStation',
            'presentPostOffice',
            'permanentDivision',
            'permanentDistrict',
            'permanentPoliceStation',
            'permanentPostOffice',
        ]);

        $academicInfo = StudentAcademicInformation::with(['academicSession', 'schoolClass', 'section', 'group'])
            ->where('student_id', $student->id)
            ->orderByDesc('id')
            ->first();

        return [
            'student'      => $student,
            'academicInfo' => $academicInfo,
            'leavingReason' => $this->certificateLeavingReason($academicInfo),
            'setting'      => SchoolSetting::current(),
            'issueDate'    => now()->format('d F Y'),
            'styles'       => $this->certificateStyles(),
            'isPdf'        => false,
        ];
    }

    private function certificateLeavingReason(?StudentAcademicInformation $academicInfo): string
    {
        $notes = trim((string) ($academicInfo?->notes ?? ''));
        if ($notes !== '') {
            return $notes;
        }

        return match ($academicInfo?->academic_status) {
            'transferred' => 'Transfer of residence',
            'graduated' => 'Promotion to another institution',
            'withdrawn' => 'Guardian request',
            'expelled' => 'Disciplinary action',
            default => 'No reason provided',
        };
    }

    public function certificateIndex(Request $request)
    {
        Certificate::ensureDefaults();

        if ($request->filled('search')) {
            $q = $request->search;
            $students = Student::where('full_name_en', 'like', "%{$q}%")
                ->orWhere('student_cid', $q)
                ->limit(20)->get();
        } else {
            $students = collect();
        }

        return view('pages.students.lifecycle.certificates', [
            'students' => $students,
            'certificates' => Certificate::with(['templates', 'activeTemplate'])->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    private function certificateTemplateHtmlForCertificate(Certificate $certificate, Student $student, ?StudentAcademicInformation $academicInfo, SchoolSetting $setting): string
    {
        $template = $this->activeCertificateTemplate($certificate);
        $templateBody = trim((string) ($template?->body ?? ''));

        if ($templateBody === '') {
            $templateBody = $this->schoolCertificateFallback($certificate->slug ?? '');
        }

        $replacements = [
            '{{ student.full_name_en }}' => $student->full_name_en ?? '',
            '{{ student.full_name_bn }}' => $student->full_name_bn ?? '',
            '{{ student.student_cid }}' => $student->student_cid ?? '',
            '{{ student.father_name }}' => $student->father_name ?? '',
            '{{ student.mother_name }}' => $student->mother_name ?? '',
            '{{ student.date_of_birth }}' => $student->date_of_birth ? $student->date_of_birth->format('d F Y') : '',
            '{{ student.birth_certificate_number }}' => $student->birth_certificate_number ?? '',
            '{{ student.present_address }}' => $student->present_address ?? '',
            '{{ student.present_division.name }}' => $student->presentDivision?->name ?? '',
            '{{ student.present_district.name }}' => $student->presentDistrict?->name ?? '',
            '{{ student.present_police_station.name }}' => $student->presentPoliceStation?->name ?? '',
            '{{ student.present_post_office.name }}' => $student->presentPostOffice?->name ?? '',
            '{{ student.permanent_address }}' => $student->permanent_address ?? '',
            '{{ student.permanent_division.name }}' => $student->permanentDivision?->name ?? '',
            '{{ student.permanent_district.name }}' => $student->permanentDistrict?->name ?? '',
            '{{ student.permanent_police_station.name }}' => $student->permanentPoliceStation?->name ?? '',
            '{{ student.permanent_post_office.name }}' => $student->permanentPostOffice?->name ?? '',
            '{{ student.previous_school }}' => $student->previous_school ?? '',
            '{{ student.previous_class_appeared }}' => $student->previous_class_appeared ?? '',
            '{{ student.tc_number }}' => $student->tc_number ?? '',
            '{{ academicInfo.schoolClass.name_en }}' => $academicInfo?->schoolClass?->name_en ?? '',
            '{{ academicInfo.section.name_en }}' => $academicInfo?->section?->name_en ?? '',
            '{{ academicInfo.academicSession.name_en }}' => $academicInfo?->academicSession?->name_en ?? '',
            '{{ academicInfo.roll }}' => (string) ($academicInfo?->roll ?? ''),
            '{{ academicInfo.checkout_date }}' => $academicInfo?->checkout_date ? $academicInfo->checkout_date->format('d F Y') : '',
            '{{ academicInfo.academic_status }}' => $academicInfo?->academic_status ? ucfirst($academicInfo?->academic_status) : '',
            '{{ academicInfo.notes }}' => $academicInfo?->notes ?? '',
            '{{ issueYear }}' => now()->format('Y'),
            '{{ issueDate }}' => now()->format('d F Y'),
            '{{ setting.name }}' => $setting->name ?? config('app.name'),
            '{{ setting.address }}' => $setting->address ?? '',
        ];

        return $this->renderTemplateText($templateBody, $replacements);
    }

    public function certificatePreview(Request $request, Student $student, Certificate $certificate)
    {
        Certificate::ensureDefaults();
        $data = $this->certificateData($student);
        $data['certificate'] = $certificate->loadMissing(['templates', 'activeTemplate']);
        $data['certificateTextHtml'] = $this->certificateTemplateHtmlForCertificate(
            $certificate,
            $student,
            $data['academicInfo'],
            $data['setting']
        );

        return view('pages.students.lifecycle.certificate-preview', $data);
    }

    public function certificatePdf(Request $request, Student $student, Certificate $certificate)
    {
        Certificate::ensureDefaults();
        $data = $this->certificateData($student);
        $data['certificate'] = $certificate->loadMissing(['templates', 'activeTemplate']);
        $data['certificateTextHtml'] = $this->certificateTemplateHtmlForCertificate(
            $certificate,
            $student,
            $data['academicInfo'],
            $data['setting']
        );

        $html = view('pages.students.lifecycle.certificate-pdf', $data)->render();
        $mpdf = new \Mpdf\Mpdf(['margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 20, 'margin_right' => 20]);
        $mpdf->WriteHTML($html);

        return $this->pdfDownloadResponse(
            $mpdf,
            Str::slug($certificate->name) . '-' . $student->student_cid . '.pdf'
        );
    }

    public function transferCertificate(Request $request, Student $student)
    {
        Certificate::ensureDefaults();
        $style = $this->normalizeCertificateStyle($request->get('style'));
        $data  = $this->certificateData($student);
        $data['style'] = $style;
        $certificate = $this->certificateBySlug('transfer-certificate');
        $data['certificateTextHtml'] = $this->certificateTemplateHtmlForCertificate(
            $certificate,
            $student,
            $data['academicInfo'],
            $data['setting']
        );

        return view("pages.students.lifecycle.tc-{$style}", $data);
    }

    public function transferCertificatePdf(Request $request, Student $student)
    {
        Certificate::ensureDefaults();
        $style = $this->normalizeCertificateStyle($request->get('style'), 'classic');
        $data = $this->certificateData($student);
        $data['isPdf'] = true;
        $certificate = $this->certificateBySlug('transfer-certificate');
        $data['certificateTextHtml'] = $this->certificateTemplateHtmlForCertificate(
            $certificate,
            $student,
            $data['academicInfo'],
            $data['setting']
        );
        $html = view("pages.students.lifecycle.tc-{$style}", $data)->render();

        $mpdf = new \Mpdf\Mpdf(['margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 20, 'margin_right' => 20]);
        $mpdf->WriteHTML($html);

        return $this->pdfDownloadResponse(
            $mpdf,
            'TC-' . $student->student_cid . '-' . $style . '.pdf'
        );
    }

    public function testimonial(Request $request, Student $student)
    {
        Certificate::ensureDefaults();
        $style = $this->normalizeCertificateStyle($request->get('style'));
        $data  = $this->certificateData($student);
        $data['style'] = $style;
        $certificate = $this->certificateBySlug('testimonial');
        $data['certificateTextHtml'] = $this->certificateTemplateHtmlForCertificate(
            $certificate,
            $student,
            $data['academicInfo'],
            $data['setting']
        );

        return view("pages.students.lifecycle.testimonial-{$style}", $data);
    }

    public function testimonialPdf(Request $request, Student $student)
    {
        Certificate::ensureDefaults();
        $style = $this->normalizeCertificateStyle($request->get('style'), 'classic');
        $data = $this->certificateData($student);
        $data['isPdf'] = true;
        $certificate = $this->certificateBySlug('testimonial');
        $data['certificateTextHtml'] = $this->certificateTemplateHtmlForCertificate(
            $certificate,
            $student,
            $data['academicInfo'],
            $data['setting']
        );
        $html = view("pages.students.lifecycle.testimonial-{$style}", $data)->render();

        $mpdf = new \Mpdf\Mpdf(['margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 20, 'margin_right' => 20]);
        $mpdf->WriteHTML($html);

        return $this->pdfDownloadResponse(
            $mpdf,
            'Testimonial-' . $student->student_cid . '-' . $style . '.pdf'
        );
    }
}
