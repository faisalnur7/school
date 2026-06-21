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
        $feeCategory = FeeCategory::where('is_transport', 1)->first();

        $query = Transport::with(['student', 'academicSession', 'feeCategory'])
            ->join('students', 'students.id', '=', 'transports.student_id')
            ->select('transports.*')
            ->orderBy('students.full_name_en');

        // Always restrict to transport category, if defined
        if ($feeCategory && $feeCategory->id) {
            $query->where('transports.fee_category_id', $feeCategory->id);
        }

        // Academic filters (transport-level)
        if ($request->filled('academic_session_id')) {
            $query->where('transports.academic_session_id', $request->academic_session_id);
        }

        if ($request->filled('school_class_id')) {
            $query->whereHas('studentAcademicInformation', fn($q) => $q->where('school_class_id', $request->school_class_id));
        }

        if ($request->filled('section_id')) {
            $query->whereHas('studentAcademicInformation', fn($q) => $q->where('section_id', $request->section_id));
        }

        if ($request->filled('group_id')) {
            $query->whereHas('studentAcademicInformation', fn($q) => $q->where('group_id', $request->group_id));
        }

        if ($request->filled('student_cid')) {
            $studentCid = trim($request->student_cid);
            $query->where('students.student_cid', 'like', "%{$studentCid}%");
        }

        // Student-level filters (match StudentController)
        if ($request->filled('permanent_division_id')) {
            $query->where('students.permanent_division_id', $request->permanent_division_id);
        }

        if ($request->filled('permanent_district_id')) {
            $query->where('students.permanent_district_id', $request->permanent_district_id);
        }

        if ($request->filled('permanent_police_station_id')) {
            $query->where('students.permanent_police_station_id', $request->permanent_police_station_id);
        }

        if ($request->filled('permanent_post_office_id')) {
            $query->where('students.permanent_post_office_id', $request->permanent_post_office_id);
        }

        if ($request->filled('phone')) {
            $phone = $request->phone;
            $query->where(function ($q) use ($phone) {
                $q->where('students.father_phone', 'like', "%{$phone}%")
                  ->orWhere('students.mother_phone', 'like', "%{$phone}%")
                  ->orWhere('students.guardian_phone', 'like', "%{$phone}%");
            });
        }

        if ($request->filled('age_from') || $request->filled('age_to')) {
            $today = Carbon::today();
            if ($request->filled('age_from')) {
                $dateFrom = $today->copy()->subYears($request->age_from)->endOfYear();
                $query->where('students.date_of_birth', '<=', $dateFrom);
            }
            if ($request->filled('age_to')) {
                $dateTo = $today->copy()->subYears($request->age_to)->startOfYear();
                $query->where('students.date_of_birth', '>=', $dateTo);
            }
        }

        if ($request->filled('gender')) {
            $query->where('students.gender', $request->gender);
        }

        // Same as StudentController search scope
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('students.full_name_en', 'like', "%{$search}%")
                  ->orWhere('students.full_name_bn', 'like', "%{$search}%")
                  ->orWhere('students.birth_certificate_number', 'like', "%{$search}%")
                  ->orWhereHas('studentAcademicInformation', function ($subQ) use ($search) {
                      $subQ->where('roll', 'like', "%{$search}%");
                  });
            });
        }

        $transports = $query->paginate(20)->withQueryString();
        $sessions = AcademicSession::all();
        $feeCategories = FeeCategory::where('status', 1)->where('is_transport',1)->get();
        $classes = SchoolClass::all();

        return view('transports.index', compact('transports', 'sessions', 'feeCategories', 'classes'));
    }

    public function create()
    {
        $sessions = AcademicSession::all();
        $feeCategories = FeeCategory::where('status', 1)->where('is_transport',1)->get();
        $classes = SchoolClass::all();

        return view('transports.create', compact('sessions', 'feeCategories', 'classes'));
    }

    public function edit($id)
    {
        $transport = Transport::with([
            'student',
            'studentAcademicInformation.student',
            'studentAcademicInformation.schoolClass',
            'studentAcademicInformation.section',
            'studentAcademicInformation.group',
            'academicSession',
            'feeCategory',
        ])->findOrFail($id);

        $sessions = AcademicSession::all();
        $feeCategories = FeeCategory::where('status', 1)->where('is_transport',1)->get();
        $classes = SchoolClass::all();

        return view('transports.edit', compact('transport', 'sessions', 'feeCategories', 'classes'));
    }

    public function update(Request $request, Transport $transport)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $transport) {
            $transport->loadMissing([
                'studentAcademicInformation',
                'studentAcademicInformation.schoolClass',
                'studentAcademicInformation.group',
            ]);

            $transport->update([
                'amount' => $request->amount,
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]);

            $studentInfo = $transport->studentAcademicInformation;
            if (!$studentInfo) {
                return;
            }

            $feeSetQuery = FeeSet::where('academic_session_id', $transport->academic_session_id)
                ->where('school_class_id', $studentInfo->school_class_id)
                ->where('frequency', 'monthly');

            if ($studentInfo->group_id) {
                $feeSetQuery->where('group_id', $studentInfo->group_id);
            } else {
                $feeSetQuery->whereNull('group_id');
            }

            $feeSet = $feeSetQuery->first();
            if (!$feeSet) {
                return;
            }

            FeeSetItem::where('fee_set_id', $feeSet->id)
                ->where('fee_category_id', $transport->fee_category_id)
                ->update(['amount' => $transport->amount]);

            Fee::where('student_id', $transport->student_id)
                ->where('fee_set_id', $feeSet->id)
                ->where('status', '!=', 'paid')
                ->update([
                    'amount' => $transport->amount,
                    'is_active' => $transport->status === Transport::STATUS_ACTIVE ? 1 : 0,
                ]);
        });

        return redirect()->route('transports.index')
            ->with('success', 'Transport fee updated successfully');
    }

    public function getStudents(Request $request)
    {
        $query = StudentAcademicInformation::with(['student', 'schoolClass', 'section', 'group'])
            ->where('academic_session_id', $request->academic_session_id)
            ->where('is_current', true)
            ->where('academic_status', 'active');

        if ($request->school_class_id) {
            $query->where('school_class_id', $request->school_class_id);
        }

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->group_id) {
            $query->where('group_id', $request->group_id);
        }

        $students = $query->orderBy('roll', 'asc')->get();
        $feeCategory = FeeCategory::where('is_transport', 1)->first();
        $existingTransports = $feeCategory
            ? Transport::where('academic_session_id', $request->academic_session_id)
                ->where('fee_category_id', $feeCategory->id)
                ->whereIn('student_id', $students->pluck('student_id'))
                ->get()
                ->keyBy('student_id')
            : collect();

        $result = $students->map(function ($info) use ($existingTransports) {
            $existing = $existingTransports->get($info->student_id);
            return [
                'student_id' => $info->student_id,
                'student_cid' => $info->student->student_cid,
                'name' => $info->student->full_name_en,
                'class' => $info->schoolClass->name_en,
                'section' => $info->section->name_en ?? 'N/A',
                'group' => $info->group->name_en ?? 'N/A',
                'roll' => $info->roll,
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

    public function toggleStatus(Transport $transport)
    {
        $transport->status = $transport->status === Transport::STATUS_ACTIVE ? Transport::STATUS_INACTIVE : Transport::STATUS_ACTIVE;
        $transport->save();

        // Reflect transport active/inactive on pending/unpaid fee rows for this student and category
        Fee::where('student_id', $transport->student_id)
            ->where('status', '!=', 'paid')
            ->whereHas('feeSet.items', function ($q) use ($transport) {
                $q->where('fee_category_id', $transport->fee_category_id);
            })
            ->update(['is_active' => $transport->status === Transport::STATUS_ACTIVE ? 1 : 0]);

        return redirect()->route('transports.index')
            ->with('success', 'Transport fee status updated successfully');
    }

    public function destroy($id)
    {
        $transport = Transport::findOrFail($id);
        $transport->delete();

        return redirect()->route('transports.index')
            ->with('success', 'Transport fee removed successfully');
    }
}
