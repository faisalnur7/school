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
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\IncomeCategory;
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

        $pendingFees = Fee::with('feeSet')
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending','partial'])
            ->when($sessionId, function($q) use ($sessionId){
                $q->whereHas('feeSet', function($subQ) use ($sessionId) {
                    $subQ->where('academic_session_id', $sessionId);
                });
            })
            ->orderBy('due_date')
            ->get();

        return view('pages.fees.collect_fee', compact('pendingFees','student','payments'));

    }

    public function pay(Request $request)
    {
        $request->validate([
            'fees' => 'required|array',
            'fees.*' => 'exists:fees,id',
            'payment_method' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {

            $fees = Fee::whereIn('id', $request->fees)->lockForUpdate()->get();

            if ($fees->isEmpty()) {
                return back()->with('error', 'No fees selected');
            }

            // Ensure all fees belong to same student
            $studentId = $fees->first()->student_id;

            if ($fees->where('student_id', '!=', $studentId)->count()) {
                return back()->with('error', 'Fees must belong to same student');
            }

            // Calculate total due
            $totalAmount = $fees->sum(function ($fee) {
                return max(0, $fee->amount - $fee->paid_amount);
            });

            if ($totalAmount <= 0) {
                return back()->with('error', 'Selected fees already paid');
            }

            $payment_data = [
                'student_id'     => $studentId,
                'amount'         => $totalAmount ?? 0,
                'payment_date'   => now(),
                'payment_method' => $request->payment_method ?? 'Cash',
                'receipt_no'     => 'R-' . now()->timestamp . rand(100000,999999),
                'collected_by'   => auth()->id()
            ];

            // Create Payment
            $payment = Payment::create($payment_data);

            foreach ($fees as $fee) {

                $dueAmount = $fee->amount - $fee->paid_amount;

                if ($dueAmount <= 0) {
                    continue;
                }

                // Create Payment Item
                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'fee_id'     => $fee->id,
                    'amount'     => $dueAmount
                ]);

                // Update Fee
                $fee->paid_amount += $dueAmount;

                if ($fee->paid_amount >= $fee->amount) {
                    $fee->status = 'paid';
                } else {
                    $fee->status = 'partial';
                }

                $fee->save();
            }

            // Record Income
            $category = IncomeCategory::where('slug', 'student-payment')->firstOrFail();

            $payment->recordIncome($category->id, [
                'amount'         => $totalAmount,
                'payment_method' => $payment->payment_method,
                'description'    => "Fee payment for student ID {$studentId}",
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Payment collected successfully');

        } catch (\Exception $e) {
            
            dd($e->getMessage());
            DB::rollBack();

            return back()->with('error', $e->getMessage());
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
