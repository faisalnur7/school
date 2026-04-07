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
    public function index()
    {
        $feeSets = FeeSet::with(['schoolClass', 'items.category'])
                    ->latest()
                    ->get();

        $classes       = SchoolClass::all();
        $feeCategories = FeeCategory::where('status', 1)->where('is_transport',0)->get();
        $sessions = AcademicSession::all(); 

        return view('pages.fee_sets.create', compact(
            'feeSets',
            'classes',
            'feeCategories',
            'sessions'
        ));
    }

    public function create()
    {
        return redirect()->route('fee-sets.index');
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
            'month'              => 'nullable|integer|between:1,12',
            'year'               => 'nullable|integer|min:1900',
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
                'month'               => $request->frequency === 'others' ? $request->month : null,
                'year'                => in_array($request->frequency, ['yearly', 'others']) ? $request->year : null,
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

                $totalAmount = $feeSet->items()->sum('amount');

                $dueDates = $this->generateDueDates($feeSet->frequency, $feeSet->month ? [$feeSet->month] : [], $request->year);

                foreach ($academicInfos as $info) {

                    foreach ($dueDates as $dueDate) {

                        Fee::updateOrCreate(
                            [
                                'student_id' => $info->student_id,
                                'fee_set_id' => $feeSet->id,
                                'due_date'   => $dueDate,
                            ],
                            [
                                'amount' => $totalAmount,
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
            'month'              => 'nullable|integer|between:1,12',
            'year'               => 'nullable|integer|min:1900',
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
                'month'              => $request->frequency === 'others' ? $request->month : null,
                'year'                => in_array($request->frequency, ['yearly', 'others']) ? $request->year : null,
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

                $totalAmount = $feeSet->items()->sum('amount');

                $dueDates = $this->generateDueDates($feeSet->frequency, $feeSet->month ? [$feeSet->month] : [], $request->year);

                foreach ($academicInfos as $info) {

                    foreach ($dueDates as $dueDate) {

                        Fee::updateOrCreate(
                            [
                                'student_id' => $info->student_id,
                                'fee_set_id' => $feeSet->id,
                                'due_date'   => $dueDate->format('Y-m-d'),
                            ],
                            [
                                'amount' => $totalAmount,
                                'status' => 'pending',
                            ]
                        );
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
     */
    private function generateDueDates($frequency, $months = [], $year = null)
    {
        // dd($frequency, $months, $year);
        $year = $year ?? now()->year;
        $dates = [];

        switch ($frequency) {

            case 'monthly':
                for ($m = 1; $m <= 12; $m++) {
                    $dates[] = Carbon::create($year, $m, 1)->endOfMonth();
                }
                break;

            case 'yearly':
                $dates[] = Carbon::create($year, 12, 31);
                break;

            case 'others':
                if (!empty($months)) {
                    foreach ($months as $m) {
                        $dates[] = Carbon::create($year, $m, 1)->endOfMonth();
                    }
                }
                break;
        }

        return $dates;
    }
}
