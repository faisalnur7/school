<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
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

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::with(['student', 'academicSession', 'feeCategory'])
            ->latest()
            ->paginate(20);
        
        return view('scholarships.index', compact('scholarships'));
    }

    public function create()
    {
        $sessions = AcademicSession::all();
        $classes = SchoolClass::all();
        $feeCategories = FeeCategory::all();

        return view('scholarships.create', compact('sessions', 'classes', 'feeCategories'));
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

        $students = $query->orderBy('roll')->get()->map(function ($academicInfo) use ($request) {
            $existingScholarship = Scholarship::where('student_id', $academicInfo->student_id)
                ->where('academic_session_id', $request->academic_session_id)
                ->where('fee_category_id', $request->fee_category_id)
                ->first();

            // Get fee category amount from FeeSetItem
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
                'existing_type' => $existingScholarship?->type,
                'existing_amount' => $existingScholarship?->amount,
                'existing_percentage' => $existingScholarship?->percentage,
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

                Scholarship::updateOrCreate(
                    [
                        'student_id' => $studentData['student_id'],
                        'academic_session_id' => $validated['academic_session_id'],
                        'fee_category_id' => $validated['fee_category_id'],
                    ],
                    [
                        'student_academic_information_id' => $studentData['academic_info_id'] ?? null,
                        'name' => $feeCategory->name . ' Scholarship ' . date('Y'),
                        'type' => $studentData['type'],
                        'amount' => $studentData['type'] === 'fixed' ? $studentData['amount'] : null,
                        'percentage' => $studentData['type'] === 'percentage' ? $studentData['percentage'] : null,
                        'status' => 'active',
                    ]
                );
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Scholarships saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function edit(Scholarship $scholarship)
    {
        $students = Student::where('status', 'active')->get();
        $sessions = AcademicSession::all();
        
        return view('scholarships.edit', compact('scholarship', 'students', 'sessions'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:fixed,percentage',
            'amount' => 'required_if:type,fixed|nullable|numeric|min:0',
            'percentage' => 'required_if:type,percentage|nullable|numeric|min:0|max:100',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,inactive,expired',
            'remarks' => 'nullable|string',
        ]);

        $scholarship->update($validated);

        return redirect()->route('scholarships.index')
            ->with('success', 'Scholarship updated successfully.');
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();

        return redirect()->route('scholarships.index')
            ->with('success', 'Scholarship deleted successfully.');
    }
}
