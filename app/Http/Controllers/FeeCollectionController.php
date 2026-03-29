<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Fee;
use Illuminate\Http\Request;
use App\Models\FeeSet;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use App\Models\FeeCategory;
use App\Models\AcademicSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\IncomeCategory;
use App\Models\Transport;
use App\Models\Scholarship;
use App\Traits\HasTransactions;

class FeeCollectionController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $groups = Group::all();
        $sessions = AcademicSession::all(); // NEW

        $student = null;
        $students = collect();
        $pendingFees = collect();

        // ===========================
        // FILTER STUDENTS
        // ===========================
        $studentQuery = Student::with([
            'academicInformations.schoolClass',
            'academicInformations.section',
            'academicInformations.group',
            'academicInformations.academicSession'
        ]);

        // Student CID
        if ($request->student_id) {
            $studentQuery->where('student_cid', $request->student_id);
        }

        // Academic Session
        if ($request->academic_session_id) {
            $studentQuery->whereHas('academicInformations', function($q) use ($request){
                $q->where('academic_session_id', $request->academic_session_id);
            });
        }

        // Class
        if ($request->school_class_id) {
            $studentQuery->whereHas('academicInformations', function($q) use ($request){
                $q->where('school_class_id', $request->school_class_id);
            });
        }

        // Section
        if ($request->section_id) {
            $studentQuery->whereHas('academicInformations', function($q) use ($request){
                $q->where('section_id', $request->section_id);
            });
        }

        // Group
        if ($request->group_id) {
            $studentQuery->whereHas('academicInformations', function($q) use ($request){
                $q->where('group_id', $request->group_id);
            });
        }

        // Only search if filters applied
        if (
            $request->student_id ||
            $request->school_class_id ||
            $request->section_id ||
            $request->group_id ||
            $request->academic_session_id
        ) {
            $students = $studentQuery->get();
        }

        // ===========================
        // LOAD FEES FOR SINGLE STUDENT
        // ===========================

        return view('pages.fees.collect', compact(
            'classes',
            'sections',
            'groups',
            'sessions', // NEW
            'students',
            'student',
            'pendingFees'
        ));
    }

    public function collect_payment($student_id){
        $student = Student::with([
            'academicInformations.schoolClass',
            'academicInformations.section',
            'academicInformations.group',
            'academicInformations.academicSession',
            'payments'
        ])->findOrFail($student_id);

        $payments = $student->payments;

        $sessionId = optional($student->academicInformations->last())->academic_session_id;

        $pendingFees = Fee::with(['feeSet.items.category', 'scholarship'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending','partial'])
            ->when($sessionId, function($q) use ($sessionId){
                $q->whereHas('feeSet', function($subQ) use ($sessionId) {
                    $subQ->where('academic_session_id', $sessionId);
                });
            })
            ->orderBy('due_date')
            ->get();

        // Get active scholarships for this student
        $scholarships = Scholarship::where('student_id', $student->id)
            ->where('status', 'active')
            ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
            ->get()
            ->keyBy('fee_category_id');

        // Get active transports for this student
        $transports = Transport::where('student_id', $student->id)
            ->where('status', 'active')
            ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
            ->get()
            ->keyBy('fee_category_id');

        // Calculate category-specific discounts and transport fees for each fee
        foreach ($pendingFees as $fee) {
            $categoryDiscounts = [];
            $categoryTransports = [];
            $totalDiscount = 0;
            $totalTransport = 0;

            foreach ($fee->feeSet->items as $item) {
                // Scholarships
                if (isset($scholarships[$item->fee_category_id])) {
                    $scholarship = $scholarships[$item->fee_category_id];
                    $discount = $scholarship->calculateDiscount($item->amount);
                    $categoryDiscounts[] = [
                        'category' => $item->category->name_en,
                        'amount' => $item->amount,
                        'discount' => $discount,
                        'net' => $item->amount - $discount,
                    ];
                    $totalDiscount += $discount;
                }
            }

            $fee->category_discounts = $categoryDiscounts;
            // $fee->category_transports = $categoryTransports;
            $fee->total_scholarship_discount = $totalDiscount;
            $fee->total_transport_fee = $totalTransport;
            $fee->calculated_net_amount = $fee->amount - $totalDiscount + $totalTransport;
        }

        return view('pages.fees.collect_fee', compact('pendingFees','student','payments'));

    }

    public function pay(Request $request)
    {
        $request->validate([
            'fees'           => 'required|array',
            'fees.*'         => 'exists:fees,id',
            'payment_method' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $fees = Fee::with(['feeSet.items.category'])->whereIn('id', $request->fees)->lockForUpdate()->get();

            if ($fees->isEmpty()) {
                return response()->json(['message' => 'No fees selected'], 422);
            }

            $studentId = $fees->first()->student_id;

            if ($fees->where('student_id', '!=', $studentId)->count()) {
                return response()->json(['message' => 'Fees must belong to the same student'], 422);
            }

            $student = Student::with('academicInformations')->find($studentId);
            $sessionId = optional($student->academicInformations->last())->academic_session_id;

            $scholarships = Scholarship::where('student_id', $studentId)
                ->where('status', 'active')
                ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
                ->get()
                ->keyBy('fee_category_id');

            $transports = Transport::where('student_id', $studentId)
                ->where('status', 'active')
                ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
                ->get()
                ->keyBy('fee_category_id');

            $totalAmount = 0;
            foreach ($fees as $fee) {
                $feeAmount = $fee->amount - $fee->paid_amount;
                $totalDiscount = 0;

                foreach ($fee->feeSet->items as $item) {
                    if (isset($scholarships[$item->fee_category_id])) {
                        $scholarship = $scholarships[$item->fee_category_id];
                        $discount = $scholarship->calculateDiscount($item->amount);
                        $totalDiscount += $discount;
                        \Log::debug('[pay] Scholarship discount', [
                            'fee_id'       => $fee->id,
                            'category_id'  => $item->fee_category_id,
                            'item_amount'  => $item->amount,
                            'discount'     => $discount,
                        ]);
                    }
                }

                $netAmount = max(0, $feeAmount - $totalDiscount);
                $totalAmount += $netAmount;

                \Log::debug('[pay] Fee total calculation', [
                    'fee_id'        => $fee->id,
                    'fee_amount'    => $fee->amount,
                    'paid_amount'   => $fee->paid_amount,
                    'fee_due'       => $feeAmount,
                    'total_discount'=> $totalDiscount,
                    'net_amount'    => $netAmount,
                    'running_total' => $totalAmount,
                ]);
            }

            if ($totalAmount <= 0) {
                return response()->json(['message' => 'Selected fees are already paid'], 422);
            }

            $payment = Payment::create([
                'student_id'     => $studentId,
                'amount'         => $totalAmount,
                'payment_date'   => now(),
                'payment_method' => $request->payment_method ?? 'Cash',
                'receipt_no'     => 'R-' . now()->format('Ymd') . '-' . str_pad(
                                        Payment::whereDate('created_at', today())->count() + 1,
                                        4, '0', STR_PAD_LEFT
                                    ),
                'collected_by'   => auth()->id(),
            ]);

            foreach ($fees as $fee) {
                $feeAmount = $fee->amount - $fee->paid_amount;
                $totalDiscount = 0;

                foreach ($fee->feeSet->items as $item) {
                    if (isset($scholarships[$item->fee_category_id])) {
                        $scholarship = $scholarships[$item->fee_category_id];
                        $discount = $scholarship->calculateDiscount($item->amount);
                        $totalDiscount += $discount;
                        \Log::debug('[pay] Scholarship discount (payment loop)', [
                            'fee_id'      => $fee->id,
                            'category_id' => $item->fee_category_id,
                            'item_amount' => $item->amount,
                            'discount'    => $discount,
                        ]);
                    }
                }

                $netAmount = max(0, $feeAmount - $totalDiscount);

                \Log::debug('[pay] PaymentItem calculation', [
                    'fee_id'         => $fee->id,
                    'fee_due'        => $feeAmount,
                    'total_discount' => $totalDiscount,
                    'net_amount'     => $netAmount,
                    'new_paid_amount'=> $fee->paid_amount + $netAmount,
                ]);

                if ($netAmount <= 0) continue;

                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'fee_id'     => $fee->id,
                    'amount'     => $netAmount,
                ]);

                $fee->paid_amount += $netAmount;
                $netDue = max(0, $fee->amount - $totalDiscount);
                $fee->status = $fee->paid_amount >= $netDue ? 'paid' : 'partial';
                $fee->save();

                \Log::debug('[pay] Fee status updated', [
                    'fee_id'     => $fee->id,
                    'paid_amount'=> $fee->paid_amount,
                    'status'     => $fee->status,
                ]);
            }

            $category = IncomeCategory::where('slug', 'student-payment')->firstOrFail();
            $title = 'Student Payment';

            $payment->recordIncome($category->id, $title, [
                'amount'         => $totalAmount,
                'payment_method' => $payment->payment_method,
                'description'    => "Fee payment for student ID {$studentId} (including transport fees)",
            ]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'payment_id' => $payment->id,
                'receipt_no' => $payment->receipt_no,
                'message'    => 'Payment collected successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function categoryPay()
    {
        return view('pages.fees.category_pay');
    }

    // Load unpaid fee by month & category
    public function loadCategoryFees(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'month'      => 'required',
            'frequency'  => 'required'
        ]);

        $date = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();

        $fees = Fee::with('feeSet')
                ->where('student_id', $request->student_id)
                ->where('status','!=','paid')
                ->whereDate('due_date', $date)
                ->get();

        return view('pages.fees.partials.category_fee_rows', compact('fees'));
    }

    // Store payment
    public function storeCategoryPayment(Request $request)
    {
        $fee = Fee::findOrFail($request->fee_id);
        $fee->status = 'paid';
        $fee->save();

        return back()->with('success','Fee paid successfully');
    }
}
