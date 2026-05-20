<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
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
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentLifecycleController extends Controller
{
    private function baseData(): array
    {
        return [
            'sessions' => AcademicSession::orderByDesc('id')->get(),
            'classes'  => SchoolClass::get(),
            'sections' => Section::orderBy('name_en')->get(),
            'groups'   => Group::orderBy('name_en')->get(),
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
        if ($request->filled(['academic_session_id', 'school_class_id'])) {
            $students = StudentAcademicInformation::with(['student', 'section'])
                ->where('academic_session_id', $request->academic_session_id)
                ->where('school_class_id', $request->school_class_id)
                ->where('is_current', true)
                ->where('academic_status', 'active')
                ->get()
                ->map(function ($rec) {
                    $rec->pendingFees = Fee::where('student_id', $rec->student_id)
                        ->where('status', '!=', 'paid')
                        ->whereRaw('(amount - COALESCE(scholarship_discount,0) - COALESCE(paid_amount,0)) > 0')
                        ->with('feeSet')
                        ->orderBy('due_date')
                        ->get();
                    $rec->totalDue = $rec->pendingFees->sum(
                        fn($f) => max(0, $f->amount - ($f->scholarship_discount ?? 0) - ($f->paid_amount ?? 0))
                    );
                    return $rec;
                });
        }

        return view('pages.students.lifecycle.checkout', array_merge($this->baseData(), compact('students')));
    }

    public function checkoutStore(Request $request, $id)
    {
        $request->validate([
            'checkout_type' => 'required|in:transferred,graduated,withdrawn,expelled',
            'checkout_date' => 'required|date',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $record = StudentAcademicInformation::where('id', $id)
            ->where('is_current', true)
            ->where('academic_status', 'active')
            ->firstOrFail();

        $totalDue = Fee::where('student_id', $record->student_id)
            ->where('status', '!=', 'paid')
            ->whereRaw('(amount - COALESCE(scholarship_discount,0) - COALESCE(paid_amount,0)) > 0')
            ->sum(DB::raw('amount - COALESCE(scholarship_discount,0) - COALESCE(paid_amount,0)'));

        if ($totalDue > 0) {
            return back()->withErrors([
                'checkout_type' => 'Cannot checkout: student has pending dues of ' . number_format($totalDue, 2) . '. Please clear all fees first.',
            ]);
        }

        DB::transaction(function () use ($record, $request) {
            $record->update([
                'academic_status' => $request->checkout_type,
                'is_current'      => false,
                'checkout_date'   => $request->checkout_date,
                'notes'           => $request->notes,
            ]);
        });

        return redirect()->route('students.checkout')->with('success', 'Student checked out. Record preserved for history.');
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
        $academicInfo = StudentAcademicInformation::with(['academicSession', 'schoolClass', 'section', 'group'])
            ->where('student_id', $student->id)
            ->orderByDesc('id')
            ->first();

        return [
            'student'      => $student,
            'academicInfo' => $academicInfo,
            'setting'      => SchoolSetting::current(),
            'issueDate'    => now()->format('d F Y'),
        ];
    }

    public function certificateIndex(Request $request)
    {
        $student = null;
        if ($request->filled('search')) {
            $q = $request->search;
            $students = Student::where('full_name_en', 'like', "%{$q}%")
                ->orWhere('student_cid', $q)
                ->limit(20)->get();
        } else {
            $students = collect();
        }

        return view('pages.students.lifecycle.certificates', compact('students'));
    }

    public function transferCertificate(Request $request, Student $student)
    {
        $style = $request->get('style', 'modern');
        $data  = $this->certificateData($student);
        return view("pages.students.lifecycle.tc-{$style}", $data);
    }

    public function transferCertificatePdf(Student $student)
    {
        $data = $this->certificateData($student);
        $html = view('pages.students.lifecycle.tc-classic', $data)->render();

        $mpdf = new \Mpdf\Mpdf(['margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 20, 'margin_right' => 20]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('TC-' . $student->student_cid . '.pdf', 'D');
    }

    public function testimonial(Request $request, Student $student)
    {
        $style = $request->get('style', 'modern');
        $data  = $this->certificateData($student);
        return view("pages.students.lifecycle.testimonial-{$style}", $data);
    }

    public function testimonialPdf(Student $student)
    {
        $data = $this->certificateData($student);
        $html = view('pages.students.lifecycle.testimonial-classic', $data)->render();

        $mpdf = new \Mpdf\Mpdf(['margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 20, 'margin_right' => 20]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Testimonial-' . $student->student_cid . '.pdf', 'D');
    }
}
