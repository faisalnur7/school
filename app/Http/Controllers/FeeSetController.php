<?php

namespace App\Http\Controllers;

use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\Student;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\FeeCategory;
use App\Models\Fee;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\StudentAcademicInformation;

class FeeSetController extends Controller
{
    /**
     * List + Create Form
     */
    public function index(Request $request)
    {
        $feeSets = FeeSet::with(['schoolClass', 'items.category'])
                    ->when($request->integer('academic_session_id'), fn ($query, $sessionId) => $query->where('academic_session_id', $sessionId))
                    ->latest()
                    ->get();

        $classes       = SchoolClass::all();
        $feeCategories = FeeCategory::where('status', 1)->where('is_transport',0)->get();
        $sessions = AcademicSession::all(); 
        $groups = Group::all();

        $sessionId = $request->integer('academic_session_id');
        return view('pages.fee_sets.create', compact(
            'feeSets',
            'classes',
            'feeCategories',
            'sessions',
            'groups',
            'sessionId'
        ));
    }

    public function create()
    {
        return redirect()->route('fee-sets.index');
    }

    /**
     * Copy all fee-set definitions from one academic session to another.
     */
    public function duplicate(Request $request)
    {
        $data = $request->validate([
            'source_session_id' => 'required|exists:academic_sessions,id|different:target_session_id',
            'target_session_id' => 'required|exists:academic_sessions,id',
        ]);

        $copied = 0;
        $skipped = 0;

        DB::transaction(function () use ($data, &$copied, &$skipped) {
            $feeSets = FeeSet::with('items')
                ->where('academic_session_id', $data['source_session_id'])
                ->get();

            foreach ($feeSets as $source) {
                $alreadyExists = FeeSet::where('academic_session_id', $data['target_session_id'])
                    ->where('name', $source->name)
                    ->where('school_class_id', $source->school_class_id)
                    ->where('group_id', $source->group_id)
                    ->exists();

                if ($alreadyExists) {
                    $skipped++;
                    continue;
                }

                $copy = $source->replicate();
                $copy->academic_session_id = $data['target_session_id'];
                $copy->save();

                foreach ($source->items as $item) {
                    $copy->items()->create([
                        'fee_category_id' => $item->fee_category_id,
                        'amount' => $item->amount,
                    ]);
                }

                $this->assignFeesToStudents($copy);
                $copied++;
            }
        });

        $message = "{$copied} fee set(s) duplicated successfully.";
        if ($skipped > 0) {
            $message .= " {$skipped} existing fee set(s) skipped.";
        }

        return redirect()->route('fee-sets.index', ['academic_session_id' => $data['target_session_id']])
            ->with('success', $message);
    }

    private function assignFeesToStudents(FeeSet $feeSet): void
    {
        if (!$feeSet->school_class_id) {
            return;
        }

        $academicInfos = StudentAcademicInformation::where('school_class_id', $feeSet->school_class_id)
            ->when($feeSet->group_id, fn ($query) => $query->where('group_id', $feeSet->group_id))
            ->get();
        $items = $feeSet->items()->with('category')->get();
        $dueDates = $this->generateDueDates($feeSet->frequency, $feeSet->month, $feeSet->due_date);
        $entryCounts = StudentAcademicInformation::whereIn('student_id', $academicInfos->pluck('student_id'))
            ->selectRaw('student_id, COUNT(*) as cnt')
            ->groupBy('student_id')
            ->pluck('cnt', 'student_id');

        foreach ($academicInfos as $info) {
            $studentType = ($entryCounts[$info->student_id] ?? 1) > 1 ? 'old' : 'new';
            $applicableAmount = $items->filter(fn ($item) =>
                in_array($item->category->student_type ?? 'both', ['both', $studentType])
            )->sum('amount');

            if ($applicableAmount <= 0) {
                continue;
            }

            foreach ($dueDates as $dueDate) {
                Fee::firstOrCreate(
                    [
                        'student_id' => $info->student_id,
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

    /**
     * Store Fee Set + Items + Preassign Fees
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'bn_name'            => 'nullable|string|max:255',
            'academic_session_id'=> 'nullable|exists:academic_sessions,id',
            'school_class_id'    => 'nullable|exists:school_classes,id',
            'group_id'           => 'nullable|exists:groups,id',
            'frequency'          => 'required|in:monthly,yearly,others',
            'due_date'           => 'required_if:frequency,yearly|nullable|date',
            'month'              => 'nullable|integer|between:1,12',
            'description'        => 'nullable|string',

            'fee_category_id'    => 'required|array|min:1',
            'fee_category_id.*'  => 'exists:fee_categories,id',

            'amount'             => 'required|array|min:1',
            'amount.*'           => 'numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            /* ============================
            1️⃣ Create Fee Set
            ============================ */
            $feeSet = FeeSet::create([
                'name'                => $request->name,
                'bn_name'             => $request->bn_name,
                'academic_session_id' => $request->academic_session_id,
                'school_class_id'     => $request->school_class_id,
                'group_id'            => $request->group_id,
                'frequency'           => $request->frequency,
                'due_date'            => $request->frequency === 'yearly' ? $request->due_date : null,
                'month'               => $request->frequency === 'others' ? $request->month : null,
                'description'         => $request->description,
            ]);

            /* ============================
            2️⃣ Create Fee Set Items
            ============================ */
            foreach ($request->fee_category_id as $index => $categoryId) {
                FeeSetItem::create([
                    'fee_set_id'      => $feeSet->id,
                    'fee_category_id' => $categoryId,
                    'amount'          => $request->amount[$index],
                ]);
            }

            /* ============================
            3️⃣ Preassign Fees To Students
            ============================ */
            if ($request->school_class_id) {

                $academicInfos = StudentAcademicInformation::where('school_class_id', $feeSet->school_class_id)
                                    ->when($feeSet->group_id, function ($q) use ($feeSet) {
                                        $q->where('group_id', $feeSet->group_id);
                                    })
                                    ->get();

                $items      = $feeSet->items()->with('category')->get();
                $dueDates   = $this->generateDueDates($feeSet->frequency, $feeSet->month, $feeSet->due_date);

                // Count academic infos per student to determine new/old
                $entryCounts = StudentAcademicInformation::whereIn('student_id', $academicInfos->pluck('student_id'))
                                    ->selectRaw('student_id, COUNT(*) as cnt')
                                    ->groupBy('student_id')
                                    ->pluck('cnt', 'student_id');

                foreach ($academicInfos as $info) {

                    $studentType  = ($entryCounts[$info->student_id] ?? 1) > 1 ? 'old' : 'new';
                    $applicableAmount = $items->filter(fn($item) =>
                        in_array($item->category->student_type ?? 'both', ['both', $studentType])
                    )->sum('amount');

                    if ($applicableAmount <= 0) continue;

                    foreach ($dueDates as $dueDate) {

                        Fee::firstOrCreate(
                            [
                                'student_id' => $info->student_id,
                                'fee_set_id' => $feeSet->id,
                                'due_date'   => $dueDate,
                            ],
                            [
                                'amount' => $applicableAmount,
                                'status' => 'pending',
                            ]
                        );
                    }
                }
            }
        });

        return redirect()->back()
            ->with('success', 'Fee set created and fees assigned successfully.');
    }

    /**
     * Edit Page
     */
    public function edit($id)
    {
        $feeSet = FeeSet::with('items.category')->findOrFail($id);
        $feeSets = FeeSet::with(['schoolClass', 'items.category'])
                    ->latest()
                    ->get();

        $classes       = SchoolClass::all();
        $feeCategories = FeeCategory::where('status', 1)->where('is_transport',0)->get();
        $sessions = AcademicSession::all();
        $groups = Group::all();

        return view('pages.fee_sets.edit', compact(
            'feeSet',
            'feeSets',
            'classes',
            'feeCategories',
            'sessions',
            'groups'
        ));
    }

    /**
     * Update Fee Set + Items
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'bn_name'            => 'nullable|string|max:255',
            'academic_session_id'=> 'nullable|exists:academic_sessions,id',
            'school_class_id'    => 'nullable|exists:school_classes,id',
            'group_id'           => 'nullable|exists:groups,id',
            'frequency'          => 'required|in:monthly,yearly,others',
            'due_date'           => 'required_if:frequency,yearly|nullable|date',
            'month'              => 'nullable|integer|between:1,12',
            'description'        => 'nullable|string',

            'fee_category_id'    => 'required|array|min:1',
            'fee_category_id.*'  => 'exists:fee_categories,id',

            'amount'             => 'required|array|min:1',
            'amount.*'           => 'numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $id) {

            $feeSet = FeeSet::findOrFail($id);

            /* ============================
            1️⃣ Update Fee Set
            ============================ */
            $feeSet->update([
                'name'                => $request->name,
                'bn_name'             => $request->bn_name,
                'academic_session_id' => $request->academic_session_id,
                'school_class_id'     => $request->school_class_id,
                'group_id'            => $request->group_id ?? null,
                'frequency'           => $request->frequency,
                'due_date'            => $request->frequency === 'yearly' ? $request->due_date : null,
                'month'               => $request->frequency === 'others' ? $request->month : null,
                'description'         => $request->description,
            ]);

            /* ============================
            2️⃣ Replace Fee Set Items
            ============================ */
            $feeSet->items()->delete();

            foreach ($request->fee_category_id as $index => $categoryId) {
                FeeSetItem::create([
                    'fee_set_id'      => $feeSet->id,
                    'fee_category_id' => $categoryId,
                    'amount'          => $request->amount[$index],
                ]);
            }

            /* ============================
            3️⃣ Re-assign Fees To Students
            ============================ */
            if ($request->school_class_id) {

                $academicInfos = StudentAcademicInformation::where('school_class_id', $feeSet->school_class_id)
                                    ->when($feeSet->group_id, function ($q) use ($feeSet) {
                                        $q->where('group_id', $feeSet->group_id);
                                    })
                                    ->get();

                $items    = $feeSet->items()->with('category')->get();
                $dueDates = $this->generateDueDates($feeSet->frequency, $feeSet->month, $feeSet->due_date);

                // Count academic infos per student to determine new/old
                $entryCounts = StudentAcademicInformation::whereIn('student_id', $academicInfos->pluck('student_id'))
                                    ->selectRaw('student_id, COUNT(*) as cnt')
                                    ->groupBy('student_id')
                                    ->pluck('cnt', 'student_id');

                foreach ($academicInfos as $info) {

                    $studentType      = ($entryCounts[$info->student_id] ?? 1) > 1 ? 'old' : 'new';
                    $applicableAmount = $items->filter(fn($item) =>
                        in_array($item->category->student_type ?? 'both', ['both', $studentType])
                    )->sum('amount');

                    if ($applicableAmount <= 0) continue;

                    foreach ($dueDates as $dueDate) {

                        Fee::firstOrCreate([
                            'student_id' => $info->student_id,
                            'fee_set_id' => $feeSet->id,
                            'due_date'   => $dueDate,
                        ], [
                            'amount' => $applicableAmount,
                            'status' => 'pending',
                        ]);
                    }
                }
            }
        });

        return redirect()->route('fee-sets.index')
                        ->with('success', 'Fee set updated successfully.');
    }

    /**
     * Delete Fee Set + Items + Assigned Fees
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $feeSet = FeeSet::findOrFail($id);
            Fee::where('fee_set_id', $feeSet->id)->delete();
            $feeSet->items()->delete();
            $feeSet->delete();
        });

        return redirect()->back()
            ->with('success', 'Fee set and related fees deleted successfully.');
    }

    /**
     * Generate Due Dates based on frequency
     * 
     * monthly: 12 due dates (one per month, end of month)
     * yearly: 1 due date (stored due_date, fallback Dec 31)
     * others: 1 due date (specific month of academic year, end of month)
     */
    private function generateDueDates($frequency, $month = null, $dueDate = null)
    {
        $currentYear = now()->year;
        $dates = [];

        switch ($frequency) {

            case 'monthly':
                // Generate 12 due dates (one per month, end of each month)
                for ($m = 1; $m <= 12; $m++) {
                    $dates[] = Carbon::create($currentYear, $m, 1)->endOfMonth()->format('Y-m-d');
                }
                break;

            case 'yearly':
                // Generate 1 due date from the saved yearly due date when available.
                $dates[] = $dueDate
                    ? Carbon::parse($dueDate)->format('Y-m-d')
                    : Carbon::create($currentYear, 12, 31)->format('Y-m-d');
                break;

            case 'others':
                // Generate 1 due date (specific month of academic year, end of month)
                if ($month) {
                    $dates[] = Carbon::create($currentYear, $month, 1)->endOfMonth()->format('Y-m-d');
                }
                break;
        }

        return $dates;
    }
}
