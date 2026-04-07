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
use App\Models\AccountTransaction;
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
            ->where('is_active', true)
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

            // ── Pass 1: compute per-fee scholarship discount & totals ──
            $grossAmount              = 0;
            $totalScholarshipDiscount = 0;
            $feeNetAmounts            = []; // fee_id => net after scholarship

            foreach ($fees as $fee) {
                $feeGross       = $fee->amount - $fee->paid_amount;
                $feeScholarship = 0;

                foreach ($fee->feeSet->items as $item) {
                    if (isset($scholarships[$item->fee_category_id])) {
                        $feeScholarship += $scholarships[$item->fee_category_id]->calculateDiscount($item->amount);
                    }
                }

                $netAfterScholarship              = max(0, $feeGross - $feeScholarship);
                $feeNetAmounts[$fee->id]           = $netAfterScholarship;
                $grossAmount                      += $feeGross;
                $totalScholarshipDiscount         += $feeScholarship;
            }

            $totalAfterScholarship = $grossAmount - $totalScholarshipDiscount;
            $cartDiscount          = (float)($request->discount_amount ?? 0);
            $finalAmount           = max(0, $totalAfterScholarship - $cartDiscount);

            if ($finalAmount <= 0 && $totalAfterScholarship <= 0) {
                return response()->json(['message' => 'Selected fees are already paid'], 422);
            }

            $payment = Payment::create([
                'student_id'         => $studentId,
                'amount'             => $finalAmount,
                'gross_amount'       => $grossAmount,
                'scholarship_amount' => $totalScholarshipDiscount,
                'discount_type'      => $cartDiscount > 0 ? ($request->discount_type ?? 'flat') : null,
                'discount_amount'    => $cartDiscount,
                'payment_date'    => now(),
                'payment_method'  => $request->payment_method ?? 'Cash',
                'account_type'    => $request->account_type ?? null,
                'account_id'      => $request->account_id ?? null,
                'receipt_no'      => 'R-' . now()->format('Ymd') . '-' . str_pad(
                                        Payment::whereDate('created_at', today())->count() + 1,
                                        4, '0', STR_PAD_LEFT
                                    ),
                'collected_by'    => auth()->id(),
            ]);

            // ── Pass 2: create PaymentItems distributing cart discount proportionally ──
            foreach ($fees as $fee) {
                $netAfterScholarship = $feeNetAmounts[$fee->id] ?? 0;
                if ($netAfterScholarship <= 0) continue;

                // Distribute cart discount proportionally based on share of totalAfterScholarship
                $feeCartDiscount = $totalAfterScholarship > 0
                    ? round($cartDiscount * ($netAfterScholarship / $totalAfterScholarship), 2)
                    : 0;

                $netAmount = max(0, $netAfterScholarship - $feeCartDiscount);
                if ($netAmount <= 0) continue;

                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'fee_id'     => $fee->id,
                    'amount'     => $netAmount,
                ]);

                $feeScholarship = ($fee->amount - $fee->paid_amount) - $netAfterScholarship;
                $netDue = max(0, $fee->amount - $feeScholarship - $feeCartDiscount);
                $fee->paid_amount += $netAmount;
                $fee->status = $fee->paid_amount >= $netDue ? 'paid' : 'partial';
                $fee->save();
            }

            $category = IncomeCategory::where('slug', 'student-payment')->firstOrFail();
            $title = 'Student Payment';

            $payment->recordIncome($category->id, $title, [
                'amount'         => $finalAmount,
                'payment_method' => $payment->payment_method,
                'description'    => "Fee payment for student ID {$studentId}",
            ]);

            if ($payment->account_type && $payment->account_id) {
                AccountTransaction::record(
                    $payment->account_type,
                    $payment->account_id,
                    'credit',
                    $finalAmount,
                    'student_payment',
                    $payment->receipt_no,
                    "Fee payment for student ID {$studentId}",
                    now(),
                    Payment::class,
                    $payment->id,
                    auth()->id()
                );
            }

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
