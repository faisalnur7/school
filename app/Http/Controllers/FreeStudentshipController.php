<?php

namespace App\Http\Controllers;

use App\Models\FreeStudentship;
use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\FeeCategory;
use App\Models\FeeSetItem;
use App\Models\Section;
use App\Models\Group;
use App\Models\StudentAcademicInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FreeStudentshipController extends Controller
{
    public function index(Request $request)
    {
        $sessions = AcademicSession::orderByDesc('id')->get();
        $classes  = SchoolClass::orderBy('id')->get();
        $sections = $request->filled('class_id')
            ? Section::where('school_class_id', $request->class_id)->orderBy('name_en')->get()
            : collect();
        $groups = Group::orderBy('name_en')->get();

        $freeStudentships = FreeStudentship::with(['student', 'academicSession', 'feeCategory', 'studentAcademicInformation.schoolClass', 'studentAcademicInformation.section', 'studentAcademicInformation.group'])
            ->when($request->filled('session_id'), fn($q) =>
                $q->where('academic_session_id', $request->session_id)
            )
            ->when($request->filled('class_id'), fn($q) =>
                $q->whereHas('studentAcademicInformation', fn($q) =>
                    $q->where('school_class_id', $request->class_id)
                )
            )
            ->when($request->filled('section_id'), fn($q) =>
                $q->whereHas('studentAcademicInformation', fn($q) =>
                    $q->where('section_id', $request->section_id)
                )
            )
            ->when($request->filled('group_id'), fn($q) =>
                $q->whereHas('studentAcademicInformation', fn($q) =>
                    $q->where('group_id', $request->group_id)
                )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('free-studentships.index', compact(
            'freeStudentships', 'sessions', 'classes', 'sections', 'groups'
        ));
    }

    public function pdf(Request $request)
    {
        $freeStudentships = FreeStudentship::with(['student', 'academicSession', 'feeCategory', 'studentAcademicInformation.schoolClass', 'studentAcademicInformation.section', 'studentAcademicInformation.group'])
            ->when($request->filled('session_id'), fn($q) =>
                $q->where('academic_session_id', $request->session_id)
            )
            ->when($request->filled('class_id'), fn($q) =>
                $q->whereHas('studentAcademicInformation', fn($q) =>
                    $q->where('school_class_id', $request->class_id)
                )
            )
            ->when($request->filled('section_id'), fn($q) =>
                $q->whereHas('studentAcademicInformation', fn($q) =>
                    $q->where('section_id', $request->section_id)
                )
            )
            ->when($request->filled('group_id'), fn($q) =>
                $q->whereHas('studentAcademicInformation', fn($q) =>
                    $q->where('group_id', $request->group_id)
                )
            )
            ->latest()
            ->get();

        $session = AcademicSession::find($request->session_id);

        $html = view('free-studentships.pdf', compact('freeStudentships', 'session'))->render();

        $mpdf = new \Mpdf\Mpdf(['margin_top' => 10, 'margin_bottom' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('free-studentships.pdf', 'D');
    }

    public function create()
    {
        $sessions = AcademicSession::all();
        $classes = SchoolClass::all();
        $sections = collect();
        $feeCategories = FeeCategory::where('status', 1)->where('is_transport', 0)->get();

        return view('free-studentships.create', compact('sessions', 'classes', 'sections', 'feeCategories'));
    }

    public function getStudents(Request $request)
    {
        $query = StudentAcademicInformation::with('student')
            ->where('academic_session_id', $request->academic_session_id)
            ->orderByRaw('CAST(roll AS UNSIGNED)');

        if ($request->school_class_id) {
            $query->where('school_class_id', $request->school_class_id);
        }

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->group_id) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('student_cid')) {
            $studentCid = trim((string) $request->student_cid);
            $query->whereHas('student', function ($q) use ($studentCid) {
                $q->where('student_cid', $studentCid);
            });
        }

        $students = $query->orderBy('roll')->get()->map(function ($academicInfo) use ($request) {
            $existingFreeStudentship = FreeStudentship::where('student_id', $academicInfo->student_id)
                ->where('academic_session_id', $request->academic_session_id)
                ->where('fee_category_id', $request->fee_category_id)
                ->first();

            $feeCategoryAmount = null;
            if ($academicInfo->student) {
                $feeSetItem = FeeSetItem::whereHas('feeSet', function($q) use ($academicInfo, $request) {
                    $q->where('academic_session_id', $request->academic_session_id)
                      ->where('school_class_id', $academicInfo->school_class_id);
                })
                ->where('fee_category_id', $request->fee_category_id)
                ->first();
                
                $feeCategoryAmount = $feeSetItem?->amount;
            }

            return [
                'id' => $academicInfo->student_id,
                'academic_info_id' => $academicInfo->id,
                'student_cid' => $academicInfo->student->student_cid,
                'name' => $academicInfo->student->full_name_en,
                'father_name' => $academicInfo->student->father_name,
                'mother_name' => $academicInfo->student->mother_name,
                'roll' => $academicInfo->roll,
                'class' => optional($academicInfo->schoolClass)->name_en,
                'section' => optional($academicInfo->section)->name_en,
                'group' => optional($academicInfo->group)->name_en,
                'academic_session' => optional($academicInfo->academicSession)->name_en,
                'fee_category_amount' => $feeCategoryAmount,
                'existing_type' => $existingFreeStudentship?->type,
                'existing_amount' => $existingFreeStudentship?->amount,
                'existing_percentage' => $existingFreeStudentship?->percentage,
            ];
        });

        return response()->json($students);
    }

    public function storeBulk(Request $request)
    {
        $validated = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'fee_category_id' => 'required|exists:fee_categories,id',
            'students' => 'required|array',
            'students.*' => 'required|array',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.academic_info_id' => 'nullable|exists:student_academic_information,id',
            'students.*.type' => 'required|in:fixed,percentage',
            'students.*.amount' => 'required_if:students.*.type,fixed|nullable|numeric|min:0',
            'students.*.percentage' => 'required_if:students.*.type,percentage|nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            $feeCategory = FeeCategory::findOrFail($validated['fee_category_id']);
            
            foreach ($validated['students'] as $studentData) {
                if (empty($studentData['amount']) && empty($studentData['percentage'])) {
                    continue;
                }

                FreeStudentship::updateOrCreate(
                    [
                        'student_id' => $studentData['student_id'],
                        'academic_session_id' => $validated['academic_session_id'],
                        'fee_category_id' => $validated['fee_category_id'],
                    ],
                    [
                        'student_academic_information_id' => $studentData['academic_info_id'] ?? null,
                        'name' => $feeCategory->name . ' Free Studentship ' . date('Y'),
                        'type' => $studentData['type'],
                        'amount' => $studentData['type'] === 'fixed' ? $studentData['amount'] : null,
                        'percentage' => $studentData['type'] === 'percentage' ? $studentData['percentage'] : null,
                        'status' => 'active',
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Free Studentships saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(FreeStudentship $freeStudentship)
    {
        $freeStudentship->delete();

        return redirect()->route('free-studentships.index')
            ->with('success', 'Free Studentship deleted successfully.');
    }
}
