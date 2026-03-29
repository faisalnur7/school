<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;
use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\SchoolClass;
use App\Models\StudentAcademicInformation;
use Illuminate\Support\Facades\DB;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\Fee;
use Carbon\Carbon;

class TransportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transport::with(['student', 'academicSession', 'feeCategory'])->latest();
        $feeCategory = FeeCategory::where('is_transport',1)->first();
        if ($request->academic_session_id) {
            $query->where('academic_session_id', $request->academic_session_id);
        }
        if ($feeCategory->id) {
            $query->where('fee_category_id', $feeCategory->id);
        }
        if ($request->school_class_id) {
            $query->whereHas('studentAcademicInformation', fn($q) => $q->where('school_class_id', $request->school_class_id));
        }
        if ($request->section_id) {
            $query->whereHas('studentAcademicInformation', fn($q) => $q->where('section_id', $request->section_id));
        }
        if ($request->group_id) {
            $query->whereHas('studentAcademicInformation', fn($q) => $q->where('group_id', $request->group_id));
        }

        $transports = $query->paginate(20)->withQueryString();
        $sessions = AcademicSession::all();
        $feeCategories = FeeCategory::all();
        $classes = SchoolClass::all();

        return view('transports.index', compact('transports', 'sessions', 'feeCategories', 'classes'));
    }

    public function create()
    {
        $sessions = AcademicSession::all();
        $feeCategories = FeeCategory::all();
        $classes = SchoolClass::all();

        return view('transports.create', compact('sessions', 'feeCategories', 'classes'));
    }

    public function getStudents(Request $request)
    {
        $query = StudentAcademicInformation::with(['student', 'schoolClass', 'section', 'group'])
            ->where('academic_session_id', $request->academic_session_id);

        if ($request->school_class_id) {
            $query->where('school_class_id', $request->school_class_id);
        }

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->group_id) {
            $query->where('group_id', $request->group_id);
        }

        $students = $query->get();
        $feeCategory = FeeCategory::where('is_transport',1)->first();
        $existingTransports = Transport::where('academic_session_id', $request->academic_session_id)
            ->where('fee_category_id', $feeCategory->id)
            ->whereIn('student_id', $students->pluck('student_id'))
            ->get()
            ->keyBy('student_id');

        $result = $students->map(function ($info) use ($existingTransports) {
            $existing = $existingTransports->get($info->student_id);
            return [
                'student_id' => $info->student_id,
                'student_cid' => $info->student->student_cid,
                'name' => $info->student->full_name_en,
                'class' => $info->schoolClass->name_en,
                'section' => $info->section->name_en ?? 'N/A',
                'group' => $info->group->name_en ?? 'N/A',
                'existing_transport' => $existing ? [
                    'amount' => $existing->amount,
                    'status' => $existing->status,
                ] : null,
            ];
        });

        return response()->json($result);
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'transports' => 'required|array',
            'transports.*.student_id' => 'required|exists:students,id',
            'transports.*.amount' => 'required|numeric|min:0',
            'transports.*.status' => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();

        try {
            $session = AcademicSession::find($request->academic_session_id);
            $year = $session ? Carbon::parse($session->start_date)->year : now()->year;
            $feeCategory = FeeCategory::where('is_transport', 1)->first();

            foreach ($request->transports as $transportData) {
                $studentInfo = StudentAcademicInformation::where('student_id', $transportData['student_id'])
                    ->where('academic_session_id', $request->academic_session_id)
                    ->first();

                Transport::updateOrCreate(
                    [
                        'student_id'          => $transportData['student_id'],
                        'academic_session_id' => $request->academic_session_id,
                        'fee_category_id'     => $feeCategory->id,
                    ],
                    [
                        'student_academic_information_id' => $studentInfo?->id,
                        'amount'  => $transportData['amount'],
                        'status'  => $transportData['status'],
                        'remarks' => $transportData['remarks'] ?? null,
                    ]
                );

                // One FeeSet per session+class+group for transport
                $feeSet = FeeSet::firstOrCreate(
                    [
                        'academic_session_id' => $request->academic_session_id,
                        'school_class_id'     => $studentInfo?->school_class_id,
                        'frequency'           => 'monthly',
                        'group_id'            => $studentInfo?->group_id ?? null,
                        'name' => 'Transport Fee - ' . ($session->name_en ?? $request->academic_session_id),
                        'bn_name' => 'পরিবহন ফি - ' . ($session->name_bn ?? $request->academic_session_id),
                    ]
                );

                FeeSetItem::updateOrCreate(
                    ['fee_set_id' => $feeSet->id, 'fee_category_id' => $feeCategory->id],
                    ['amount' => $transportData['amount']]
                );

                for ($m = 1; $m <= 12; $m++) {
                    $dueDate = Carbon::create($year, $m, 1)->endOfMonth()->format('Y-m-d');
                    Fee::updateOrCreate(
                        [
                            'student_id' => $transportData['student_id'],
                            'fee_set_id' => $feeSet->id,
                            'due_date'   => $dueDate,
                        ],
                        [
                            'amount' => $transportData['amount'],
                            'status' => 'pending',
                        ]
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transport fees assigned successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $transport = Transport::findOrFail($id);
        $transport->delete();

        return redirect()->route('transports.index')
            ->with('success', 'Transport fee removed successfully');
    }
}
