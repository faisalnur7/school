<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Certificate;
use App\Models\Division;
use App\Models\District;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\Group;
use App\Models\PoliceStation;
use App\Models\PostOffice;
use App\Models\Profession;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\CertificateTemplate;
use Illuminate\Support\Str;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $students = collect();
        if ($request->filled(['academic_session_id', 'school_class_id'])) {
            $students = StudentAcademicInformation::with(['student', 'section', 'group'])
                ->where('academic_session_id', $request->academic_session_id)
                ->where('school_class_id', $request->school_class_id)
                ->where('is_current', true)
                ->where('academic_status', 'active')
                ->get();
        }

        return view('pages.students.lifecycle.promote', array_merge($this->baseData(), compact('students')));
    }

    public function promoteStore(Request $request)
    {
        $request->validate([
            'source_session_id'            => 'required|exists:academic_sessions,id',
            'target_session_id'            => 'required|exists:academic_sessions,id',
            'promotions'                   => 'required|array|min:1',
            'promotions.*.id'              => 'required|exists:student_academic_information,id',
            'promotions.*.school_class_id' => 'required|exists:school_classes,id',
            'promotions.*.section_id'      => 'required|exists:sections,id',
        ]);

        if ($request->source_session_id == $request->target_session_id) {
            return back()->withErrors(['target_session_id' => 'Use Mid-Year Correction for same-session changes.']);
        }

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->promotions as $data) {
                    $old = StudentAcademicInformation::findOrFail($data['id']);

                    $promotionStatus = ($data['school_class_id'] == $old->school_class_id) ? 'retained' : 'promoted';

                    $roll = StudentAcademicInformation::getNextRoll(
                        $request->target_session_id,
                        $data['school_class_id'],
                        $data['section_id'],
                        $data['group_id'] ?? null
                    );

                    StudentAcademicInformation::where('student_id', $old->student_id)->update(['is_current' => false]);

                    StudentAcademicInformation::create([
                        'student_id'                       => $old->student_id,
                        'academic_session_id'              => $request->target_session_id,
                        'school_class_id'                  => $data['school_class_id'],
                        'section_id'                       => $data['section_id'],
                        'group_id'                         => $data['group_id'] ?? null,
                        'roll'                             => $roll,
                        'academic_status'                  => 'active',
                        'promotion_status'                 => $promotionStatus,
                        'is_current'                       => true,
                        'previous_academic_information_id' => $old->id,
                    ]);
                }
            });
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors(['target_session_id' => 'One or more students already have a record for the target session.']);
        }

        return redirect()->route('students.promote')->with('success', count($request->promotions) . ' student(s) promoted successfully.');
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
