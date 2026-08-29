<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\AcademicSession;
use App\Models\AdmissionAdmitCard;
use App\Models\AdmissionApplication;
use App\Models\AdmissionExam;
use App\Models\AdmissionExamClassSetting;
use App\Models\AdmissionPayment;
use App\Models\Account;
use App\Models\HandCash;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\SchoolClass;
use App\Models\Transaction;
use App\Models\Division;
use App\Models\District;
use App\Models\FeeSet;
use App\Models\Group;
use App\Models\PoliceStation;
use App\Models\PostOffice;
use App\Models\Profession;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Services\AdmissionConversionService;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class AdmissionController extends Controller
{
    public function hub()
    {
        return view('pages.admissions.hub', ['cards' => $this->cards()]);
    }

    public function applications(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $classId = $request->integer('school_class_id');
        $applications = AdmissionApplication::with(['exam', 'schoolClass', 'payment', 'admitCard'])
            ->when($classId, fn ($query) => $query->where('school_class_id', $classId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('full_name_en', 'like', "%{$search}%")
                        ->orWhere('full_name_bn', 'like', "%{$search}%")
                        ->orWhere('father_phone', 'like', "%{$search}%")
                        ->orWhere('mother_phone', 'like', "%{$search}%")
                        ->orWhere('guardian_phone', 'like', "%{$search}%")
                        ->orWhere('birth_certificate_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $classes = SchoolClass::where('status', 1)->orderBy('order')->get();

        return view('pages.admissions.applications.index', compact('applications', 'search', 'classId', 'classes'));
    }

    public function approved()
    {
        $applications = AdmissionApplication::with(['exam', 'schoolClass'])->where('review_status', 'approved')->where('conversion_status', 'not_converted')->latest()->paginate(20);
        return view('pages.admissions.applications.approved', compact('applications'));
    }

    public function converted()
    {
        $applications = AdmissionApplication::with(['exam', 'schoolClass', 'convertedStudent'])->where('conversion_status', 'converted')->latest()->paginate(20);
        return view('pages.admissions.applications.converted', compact('applications'));
    }

    public function showApplication(AdmissionApplication $application)
    {
        $application->load(['exam', 'schoolClass', 'academicSession', 'payment', 'admitCard', 'reviews']);
        return view('pages.admissions.applications.show', compact('application'));
    }

    public function exams()
    {
        $exams = AdmissionExam::with('academicSession')->withCount('applications')->latest()->get();
        return view('pages.admissions.exams.index', compact('exams'));
    }

    public function createExam()
    {
        return view('pages.admissions.exams.form', ['exam' => null, 'sessions' => AcademicSession::orderByDesc('id')->get(), 'classes' => SchoolClass::where('status', 1)->orderBy('order')->get()]);
    }

    public function editExam(AdmissionExam $exam)
    {
        $exam->load('classSettings');
        return view('pages.admissions.exams.form', ['exam' => $exam, 'sessions' => AcademicSession::orderByDesc('id')->get(), 'classes' => SchoolClass::where('status', 1)->orderBy('order')->get()]);
    }

    public function storeExam(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'academic_session_id' => 'required|exists:academic_sessions,id', 'exam_date' => 'required|date', 'form_fee' => 'required|numeric|min:0.01', 'venue' => 'nullable|string|max:255', 'reporting_time' => 'nullable|string|max:100', 'instructions' => 'nullable|string', 'status' => 'nullable|boolean', 'pass_marks' => 'required|array|min:1', 'pass_marks.*' => 'required|numeric|min:0']);
        return DB::transaction(function () use ($data, $request) {
            if ($request->boolean('status')) AdmissionExam::where('status', true)->update(['status' => false]);
            $exam = AdmissionExam::create(array_merge(collect($data)->except('pass_marks')->toArray(), ['status' => $request->boolean('status'), 'created_by' => auth()->id()]));
            foreach ($data['pass_marks'] as $classId => $passMark) AdmissionExamClassSetting::create(['admission_exam_id' => $exam->id, 'school_class_id' => $classId, 'pass_mark' => $passMark]);
            return redirect()->route('admissions.exams')->with('success', 'Admission exam created successfully.');
        });
    }

    public function toggleExam(AdmissionExam $exam)
    {
        DB::transaction(function () use ($exam) { AdmissionExam::where('status', true)->whereKeyNot($exam->id)->update(['status' => false]); $exam->update(['status' => ! $exam->status]); });
        return back()->with('success', 'Exam status updated.');
    }

    public function updateExam(Request $request, AdmissionExam $exam)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'academic_session_id' => 'required|exists:academic_sessions,id', 'exam_date' => 'required|date', 'form_fee' => 'required|numeric|min:0.01', 'venue' => 'nullable|string|max:255', 'reporting_time' => 'nullable|string|max:100', 'instructions' => 'nullable|string', 'status' => 'nullable|boolean', 'pass_marks' => 'required|array|min:1', 'pass_marks.*' => 'required|numeric|min:0']);
        DB::transaction(function () use ($data, $request, $exam) {
            if ($request->boolean('status')) AdmissionExam::where('status', true)->where('id', '!=', $exam->id)->update(['status' => false]);
            $exam->update(array_merge(collect($data)->except('pass_marks')->toArray(), ['status' => $request->boolean('status')]));
            foreach ($data['pass_marks'] as $classId => $passMark) AdmissionExamClassSetting::updateOrCreate(['admission_exam_id' => $exam->id, 'school_class_id' => $classId], ['pass_mark' => $passMark]);
        });
        return redirect()->route('admissions.exams')->with('success', 'Admission exam updated successfully.');
    }

    public function deleteExam(AdmissionExam $exam)
    {
        abort_if($exam->applications()->exists(), 422, 'An exam with applications cannot be deleted.');
        $exam->delete();
        return back()->with('success', 'Admission exam deleted.');
    }

    public function payments(Request $request)
    {
        $payments = AdmissionPayment::with('application')->latest()->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))->paginate(20)->withQueryString();
        return view('pages.admissions.payments.index', compact('payments'));
    }

    public function updatePayment(Request $request, AdmissionPayment $payment)
    {
        $data = $request->validate(['status' => 'required|in:paid,pending_verification,rejected,refunded', 'payment_method' => 'nullable|string|max:100', 'payment_reference' => 'nullable|string|max:255', 'remarks' => 'nullable|string']);
        $payment->update(array_merge($data, $data['status'] === 'paid' ? ['paid_at' => now(), 'verified_by' => auth()->id(), 'verified_at' => now()] : []));
        $payment->application->update(['payment_status' => $data['status']]);
        return back()->with('success', 'Admission payment updated.');
    }

    public function collectPayment(Request $request, AdmissionApplication $application)
    {
        abort_if($application->payment_status === 'paid', 422, 'This application has already been paid.');
        $application->loadMissing('exam');

        $data = $request->validate([
            'discount_amount' => 'nullable|integer|min:0|max:' . (int) floor((float) ($application->exam?->form_fee ?? 0)),
            'payment_reference' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ]);
        $grossAmount = round((float) ($application->exam?->form_fee ?? 0), 2);
        abort_if($grossAmount <= 0, 422, 'Admission form fee is not configured.');
        $discountAmount = (int) ($data['discount_amount'] ?? 0);
        $amount = round($grossAmount - $discountAmount, 2);

        DB::transaction(function () use ($application, $data, $grossAmount, $discountAmount, $amount) {
            $category = IncomeCategory::firstOrCreate(
                ['slug' => 'admission-form'],
                ['name' => 'Admission Form', 'is_active' => true]
            );
            $cash = HandCash::where('is_active', true)->firstOrFail();
            $income = Income::create([
                'income_category_id' => $category->id,
                'title' => 'Admission form payment - ' . $application->application_number,
                'amount' => $amount,
                'income_date' => now()->format('Y-m-d'),
                'payment_method' => 'Cash',
                'reference_no' => $data['payment_reference'] ?? null,
                'description' => $data['remarks'] ?? null,
                'recorded_by' => auth()->id(),
                'account_type' => HandCash::class,
                'account_id' => $cash->id,
            ]);

            Transaction::create([
                'reference_no' => Transaction::generateReference(),
                'type' => 'income',
                'income_category_id' => $category->id,
                'amount' => $amount,
                'description' => $data['remarks'] ?? 'Admission form payment.',
                'transaction_date' => now()->format('Y-m-d'),
                'payment_method' => 'Cash',
                'reference_note' => $data['payment_reference'] ?? null,
                'transactionable_type' => Income::class,
                'transactionable_id' => $income->id,
                'recorded_by' => auth()->id(),
            ]);

            JournalService::postSafe(
                now()->toDateString(),
                $income->title,
                [
                    ['account_id' => Account::resolveForSource(HandCash::class, $cash->id), 'debit' => $amount, 'credit' => 0],
                    ['account_id' => Account::resolveForSource(IncomeCategory::class, $category->id), 'debit' => 0, 'credit' => $amount],
                ],
                Income::class,
                $income->id,
                auth()->id()
            );

            $application->payment()->updateOrCreate([], [
                'amount' => $amount,
                'gross_amount' => $grossAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $amount,
                'payment_method' => 'Cash',
                'payment_reference' => $data['payment_reference'] ?? null,
                'status' => 'paid',
                'paid_at' => now(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'remarks' => $data['remarks'] ?? null,
            ]);
            $application->update(['payment_status' => 'paid']);
        });

        return back()->with('success', 'Admission form payment collected and recorded as income.');
    }

    public function publicPayment(Request $request, AdmissionApplication $application)
    {
        $data = $request->validate(['payment_method' => 'required|string|max:100', 'payment_reference' => 'required|string|max:255']);
        $application->payment()->updateOrCreate([], array_merge($data, ['status' => 'pending_verification']));
        $application->update(['payment_status' => 'pending_verification']);
        return back()->with('success', 'Payment reference submitted for verification.');
    }

    public function marks(AdmissionExam $exam)
    {
        $applications = $exam->applications()->with('schoolClass')->where('payment_status', 'paid')->latest()->get();
        return view('pages.admissions.results.marks', compact('exam', 'applications'));
    }

    public function storeMarks(Request $request, AdmissionApplication $application)
    {
        $data = $request->validate(['total_marks' => 'required|numeric|min:0']);
        $passMark = AdmissionExamClassSetting::where('admission_exam_id', $application->admission_exam_id)->where('school_class_id', $application->school_class_id)->value('pass_mark');
        abort_if($passMark === null, 422, 'No pass mark is configured for this class.');
        $application->update(['total_marks' => $data['total_marks'], 'pass_mark_snapshot' => $passMark, 'result_status' => (float) $data['total_marks'] >= (float) $passMark ? 'passed' : 'failed']);
        return back()->with('success', 'Marks saved.');
    }

    public function results(Request $request)
    {
        $applications = AdmissionApplication::with(['exam', 'schoolClass'])->whereNotNull('total_marks')->orderByDesc('total_marks')->when($request->filled('result_status'), fn ($q) => $q->where('result_status', $request->result_status))->get();
        return view('pages.admissions.results.index', compact('applications'));
    }

    public function review(Request $request, AdmissionApplication $application)
    {
        $data = $request->validate(['decision' => 'required|in:approved,rejected,pending', 'notes' => 'nullable|string']);
        $application->update(['review_status' => $data['decision'], 'approved_by' => $data['decision'] === 'approved' ? auth()->id() : null, 'approved_at' => $data['decision'] === 'approved' ? now() : null, 'admin_notes' => $data['notes'] ?? null]);
        $application->reviews()->create(['decision' => $data['decision'], 'notes' => $data['notes'] ?? null, 'reviewed_by' => auth()->id(), 'reviewed_at' => now()]);
        return back()->with('success', 'Application review updated.');
    }

    public function convert(AdmissionApplication $application, AdmissionConversionService $service)
    {
        $service->convert($application, auth()->id());
        return back()->with('success', 'Application proceeded to admission successfully.');
    }

    public function admitCards()
    {
        $applications = AdmissionApplication::with(['exam', 'schoolClass', 'admitCard'])->where('payment_status', 'paid')->latest()->paginate(20);
        return view('pages.admissions.admit-cards.index', compact('applications'));
    }

    public function generateAdmitCard(AdmissionApplication $application)
    {
        abort_unless($application->payment_status === 'paid', 422, 'Payment must be verified first.');
        $card = $application->admitCard ?: $application->admitCard()->create(['admit_card_number' => 'ADM-' . now()->format('Y') . '-' . str_pad((string) $application->id, 6, '0', STR_PAD_LEFT)]);
        $card->update(['generated_at' => now(), 'generated_by' => auth()->id()]);
        return back()->with('success', 'Admit card generated.');
    }

    public function admitCardPdf(AdmissionApplication $application)
    {
        abort_unless($application->payment_status === 'paid', 403);
        $application->load(['exam', 'schoolClass', 'academicSession', 'admitCard']);
        $admitCard = DB::transaction(function () use ($application) {
            $card = AdmissionAdmitCard::where('admission_application_id', $application->id)->lockForUpdate()->first();
            if (! $card) {
                $card = AdmissionAdmitCard::create([
                    'admission_application_id' => $application->id,
                    'admit_card_number' => 'ADM-' . now()->format('Y') . '-' . str_pad((string) $application->id, 6, '0', STR_PAD_LEFT),
                ]);
            }

            if (! $card->roll_number || ! $card->candidate_id) {
                $rolls = StudentAcademicInformation::where('academic_session_id', $application->academic_session_id)
                    ->where('school_class_id', $application->school_class_id)
                    ->lockForUpdate()
                    ->pluck('roll');
                $admitCardRolls = AdmissionAdmitCard::whereHas('application', function ($query) use ($application) {
                    $query->where('academic_session_id', $application->academic_session_id)
                        ->where('school_class_id', $application->school_class_id);
                })->whereNotNull('roll_number')->lockForUpdate()->pluck('roll_number');
                $nextRoll = max(
                    $rolls->filter(fn ($roll) => is_numeric($roll))->map(fn ($roll) => (int) $roll)->max() ?? 0,
                    $admitCardRolls->filter(fn ($roll) => is_numeric($roll))->map(fn ($roll) => (int) $roll)->max() ?? 0
                ) + 1;

                $card->update([
                    'roll_number' => (string) $nextRoll,
                    'candidate_id' => 'ADM-' . now()->format('Y') . '-' . str_pad((string) $card->id, 6, '0', STR_PAD_LEFT),
                    'generated_at' => $card->generated_at ?? now(),
                    'generated_by' => $card->generated_by ?? auth()->id(),
                ]);
            }

            return $card->fresh();
        });
        $application->setRelation('admitCard', $admitCard);
        $pdf = new Mpdf(['format' => 'A4', 'margin_top' => 12, 'margin_bottom' => 12, 'margin_left' => 12, 'margin_right' => 12]);
        $pdf->WriteHTML(view('pages.admissions.admit-cards.pdf', compact('application'))->render());
        return response($pdf->Output('', 'S'))->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="admit-card-' . $application->application_number . '.pdf"');
    }

    public function applicationPdf(AdmissionApplication $application)
    {
        $application->load(['exam', 'schoolClass', 'academicSession']);
        $pdf = new Mpdf(['format' => 'A4', 'margin_top' => 12, 'margin_bottom' => 12, 'margin_left' => 12, 'margin_right' => 12]);
        $pdf->WriteHTML(view('pages.admissions.applications.pdf', compact('application'))->render());
        return response($pdf->Output('', 'S'))->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="application-' . $application->application_number . '.pdf"');
    }

    public function publicForm()
    {
        return view('admissions.public.form', [
            'exam' => AdmissionExam::with('classSettings.schoolClass')->where('status', true)->latest()->first(),
            'academicSessions' => AcademicSession::all(),
            'classes' => SchoolClass::where('status', 1)->orderBy('order')->get(),
            'sections' => Section::orderBy('name_en')->get(),
            'groups' => Group::orderBy('name_en')->get(),
            'divisions' => Division::orderBy('name')->get(),
            'districts' => District::orderBy('name')->get(),
            'policeStations' => PoliceStation::orderBy('name')->get(),
            'postOffices' => PostOffice::orderBy('name')->get(),
            'professions' => Profession::orderBy('name')->get(),
            'feeSets' => FeeSet::all(),
            'nextStudentCid' => Student::generateNextCid(),
        ]);
    }

    public function publicStore(Request $request)
    {
        $exam = AdmissionExam::with('classSettings')->where('status', true)->latest()->firstOrFail();
        $data = $request->validate([
            'academic_session_id' => 'nullable|exists:academic_sessions,id', 'school_class_id' => 'required|exists:school_classes,id', 'section_id' => 'nullable|exists:sections,id', 'group_id' => 'nullable|exists:groups,id',
            'full_name_en' => 'required|string|max:255', 'full_name_bn' => 'nullable|string|max:255', 'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:200', 'dimensions:min_width=290,max_width=300,min_height=440,max_height=450'], 'date_of_birth' => 'nullable|date', 'birth_certificate_number' => 'nullable|string|max:255', 'gender' => 'required|integer|in:1,2', 'religion' => 'required|integer|in:1,2,3,4', 'blood_group' => 'nullable|integer', 'disable' => 'nullable|boolean',
            'father_name' => 'required|string|max:255', 'father_nid_number' => 'nullable|string|max:255', 'fathers_profession_id' => 'nullable|exists:professions,id', 'father_phone' => 'nullable|string|required_without:mother_phone|max:50', 'father_email' => 'nullable|email|max:255', 'mother_name' => 'required|string|max:255', 'mother_nid_number' => 'nullable|string|max:255', 'mothers_profession_id' => 'nullable|exists:professions,id', 'mother_phone' => 'nullable|string|required_without:father_phone|max:50', 'mother_email' => 'nullable|email|max:255', 'annual_income' => 'nullable|string|max:255',
            'present_address' => 'nullable|string', 'present_division_id' => 'nullable|exists:divisions,id', 'present_district_id' => 'nullable|exists:districts,id', 'present_police_station_id' => 'nullable|exists:police_stations,id', 'present_post_office_id' => 'nullable|exists:post_offices,id', 'permanent_address' => 'nullable|string', 'permanent_division_id' => 'nullable|exists:divisions,id', 'permanent_district_id' => 'nullable|exists:districts,id', 'permanent_police_station_id' => 'nullable|exists:police_stations,id', 'permanent_post_office_id' => 'nullable|exists:post_offices,id',
            'guardian_type' => 'nullable|integer|in:1,2,3', 'guardian_name' => 'nullable|string|max:255', 'guardian_relation' => 'nullable|string|max:255', 'guardian_profession_id' => 'nullable|exists:professions,id', 'guardian_address' => 'nullable|string', 'guardian_phone' => 'nullable|string|max:50', 'guardian_email' => 'nullable|email|max:255', 'previous_school' => 'nullable|string|max:255', 'previous_class_appeared' => 'nullable|string|max:255', 'tc_number' => 'nullable|string|max:255',
        ]);
        if (!empty($data['date_of_birth'])) {
            $data['date_of_birth'] = preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data['date_of_birth'])
                ? Carbon::createFromFormat('d/m/Y', $data['date_of_birth'])->format('Y-m-d')
                : Carbon::parse($data['date_of_birth'])->format('Y-m-d');
        }
        abort_unless($exam->classSettings->contains('school_class_id', (int) $data['school_class_id']), 422, 'This class is not configured for the active exam.');
        $number = 'APP-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
        if ($request->hasFile('image')) { $image = $request->file('image'); $filename = time() . '_' . Str::random(12) . '.' . $image->getClientOriginalExtension(); $image->move(public_path('uploads/students'), $filename); $data['image'] = 'uploads/students/' . $filename; }
        $data['academic_session_id'] = $exam->academic_session_id;
        $application = AdmissionApplication::create(['admission_exam_id' => $exam->id, 'application_number' => $number, 'application_no' => $number, 'academic_session_id' => $exam->academic_session_id, 'school_class_id' => $data['school_class_id'], 'class_id' => $data['school_class_id'], 'applicant_data' => $data, 'full_name_en' => $data['full_name_en'], 'full_name_bn' => $data['full_name_bn'] ?? null, 'date_of_birth' => $data['date_of_birth'] ?? null, 'sex' => $data['gender'] == 1 ? 'male' : 'female', 'gender' => $data['gender'], 'religion' => $data['religion'], 'blood_group' => $data['blood_group'] ?? null, 'birth_certificate_number' => $data['birth_certificate_number'] ?? null, 'disable' => $data['disable'] ?? false, 'father_name' => $data['father_name'], 'father_nid_number' => $data['father_nid_number'] ?? null, 'fathers_profession_id' => $data['fathers_profession_id'] ?? null, 'father_phone' => $data['father_phone'] ?? null, 'father_email' => $data['father_email'] ?? null, 'mother_name' => $data['mother_name'], 'mother_nid_number' => $data['mother_nid_number'] ?? null, 'mothers_profession_id' => $data['mothers_profession_id'] ?? null, 'mother_phone' => $data['mother_phone'] ?? null, 'mother_email' => $data['mother_email'] ?? null, 'annual_income' => $data['annual_income'] ?? null, 'guardian_type' => $data['guardian_type'] ?? null, 'guardian_name' => $data['guardian_name'] ?? null, 'guardian_relation' => $data['guardian_relation'] ?? null, 'guardian_profession_id' => $data['guardian_profession_id'] ?? null, 'guardian_phone' => $data['guardian_phone'] ?? null, 'guardian_email' => $data['guardian_email'] ?? null, 'present_address' => $data['present_address'] ?? null, 'present_division_id' => $data['present_division_id'] ?? null, 'present_district_id' => $data['present_district_id'] ?? null, 'present_police_station_id' => $data['present_police_station_id'] ?? null, 'present_post_office_id' => $data['present_post_office_id'] ?? null, 'permanent_address' => $data['permanent_address'] ?? null, 'permanent_division_id' => $data['permanent_division_id'] ?? null, 'permanent_district_id' => $data['permanent_district_id'] ?? null, 'permanent_police_station_id' => $data['permanent_police_station_id'] ?? null, 'permanent_post_office_id' => $data['permanent_post_office_id'] ?? null, 'guardian_phone' => $data['guardian_phone'] ?? null, 'previous_school' => $data['previous_school'] ?? null, 'previous_class_appeared' => $data['previous_class_appeared'] ?? null, 'tc_number' => $data['tc_number'] ?? null, 'image' => $data['image'] ?? null, 'status' => 'pending', 'submitted_at' => now()]);
        $application->payment()->create(['amount' => (float) $exam->form_fee, 'status' => 'pending']);
        return redirect()->route('public.admission.search', ['application_number' => $application->application_number])->with('success', 'Application submitted. Please complete payment and keep your application number.');
    }

    public function publicSearch(Request $request)
    {
        $term = trim((string) $request->input('search', $request->input('application_number', '')));
        $application = null;

        if ($term !== '') {
            $digits = preg_replace('/\D+/', '', $term);

            $application = AdmissionApplication::with(['exam', 'schoolClass', 'payment', 'admitCard'])
                ->where(function ($query) use ($term, $digits) {
                    $query->where('application_number', $term)
                        ->orWhere('birth_certificate_number', $term)
                        ->orWhere('father_phone', $term)
                        ->orWhere('mother_phone', $term);

                    if ($digits !== '') {
                        $normalizedPhone = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(father_phone, '-', ''), ' ', ''), '+', ''), '(', ''), ')', '')";
                        $normalizedMotherPhone = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(mother_phone, '-', ''), ' ', ''), '+', ''), '(', ''), ')', '')";
                        $query->orWhereRaw("{$normalizedPhone} = ?", [$digits])
                            ->orWhereRaw("{$normalizedMotherPhone} = ?", [$digits]);
                    }
                })
                ->latest('submitted_at')
                ->latest('id')
                ->first();
        }

        return view('admissions.public.search', [
            'application' => $application,
            'searchTerm' => $term,
        ]);
    }

    public function publicApplicationPdf(AdmissionApplication $application)
    {
        $application->load(['exam', 'schoolClass', 'payment']);
        $pdf = new Mpdf(['format' => 'A4', 'margin_top' => 12, 'margin_bottom' => 12, 'margin_left' => 12, 'margin_right' => 12]);
        $pdf->WriteHTML(view('admissions.public.application-pdf', compact('application'))->render());

        return response($pdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="application-' . $application->application_number . '.pdf"');
    }

    private function cards(): array
    {
        return [
            ['icon' => 'fa-calendar-alt', 'title' => 'Admission Exams', 'subtitle' => 'Create exams and classwise pass marks', 'route' => 'admissions.exams', 'permission' => 'view_admission_exams', 'from' => '#0f766e', 'to' => '#0d9488'],
            ['icon' => 'fa-file-alt', 'title' => 'Applications', 'subtitle' => 'Review every submitted application', 'route' => 'admissions.applications', 'permission' => 'view_admission_applications', 'from' => '#2563eb', 'to' => '#1d4ed8'],
            ['icon' => 'fa-coins', 'title' => 'Admission Payments', 'subtitle' => 'Verify application form payments', 'route' => 'admissions.payments', 'permission' => 'view_admission_payments', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-id-card', 'title' => 'Admit Cards', 'subtitle' => 'Generate and print paid applicants cards', 'route' => 'admissions.admit-cards', 'permission' => 'manage_admission_admit_cards', 'from' => '#7c3aed', 'to' => '#6d28d9'],
            ['icon' => 'fa-pen', 'title' => 'Merit and Results', 'subtitle' => 'Enter marks and view passed or failed lists', 'route' => 'admissions.results', 'permission' => 'view_admission_results', 'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-user-check', 'title' => 'Approved Students', 'subtitle' => 'Track approvals and proceed to admission', 'route' => 'admissions.approved', 'permission' => 'view_approved_admission_students', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-user-graduate', 'title' => 'Converted Admissions', 'subtitle' => 'View students created from applications', 'route' => 'admissions.converted', 'permission' => 'view_converted_admissions', 'from' => '#334155', 'to' => '#0f172a'],
        ];
    }
}
