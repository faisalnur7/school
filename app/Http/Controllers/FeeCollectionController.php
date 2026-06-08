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
use App\Models\FreeStudentship;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Services\PettyCashService;
use App\Models\InventorySale;
use App\Models\InventorySaleItem;
use App\Models\StockMovement;

class FeeCollectionController extends Controller
{
    private function normalizePaymentMethod(?string $method): string
    {
        return match (strtolower((string) $method)) {
            'cash' => 'Cash',
            'bank', 'bank transfer', 'bank_transfer' => 'Bank Transfer',
            'mobile', 'mobile banking', 'mobile_banking', 'mobile wallet', 'mobile_wallet' => 'Mobile Banking',
            'cheque', 'check' => 'Cheque',
            default => 'Cash',
        };
    }

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

    public function switchStudent(Request $request)
    {
        $studentCid = trim((string) $request->input('student_cid', ''));

        if ($studentCid === '') {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid student CID.',
            ], 422);
        }

        $student = Student::where('student_cid', $studentCid)
            ->where('status', 1)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'No student found with CID: ' . $studentCid . '. Please check and try again.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'student_id' => $student->id,
            'student_name' => $student->full_name_en,
            'redirect_url' => route('fees.collect_payment', $student->id)
        ]);
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

        // Filter fees by student type
        $studentType = $student->academicInformations()->count() > 1 ? 'old' : 'new';
        $pendingFees = $pendingFees->filter(function ($fee) use ($studentType) {
            $items = $fee->feeSet->items;
            if ($items->isEmpty()) return true;
            return $items->contains(fn($item) =>
                in_array($item->category->student_type ?? 'both', ['both', $studentType])
            );
        })->values();

        // Get active scholarships for this student
        $scholarships = Scholarship::where('student_id', $student->id)
            ->where('status', 'active')
            ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
            ->get();

        // Index scholarships by fee_category_id (null = applies to all)
        $scholarshipsByCategory = [];
        $scholarshipsForAll = [];
        foreach ($scholarships as $s) {
            if ($s->fee_category_id === null) {
                $scholarshipsForAll[] = $s;
            } else {
                $scholarshipsByCategory[$s->fee_category_id] = $s;
            }
        }

        // Get active free studentships for this student
        $freeStudentships = FreeStudentship::where('student_id', $student->id)
            ->where('status', 'active')
            ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
            ->get();

        // Index free studentships by fee_category_id (null = applies to all)
        $freeStudentshipsByCategory = [];
        $freeStudentshipsForAll = [];
        foreach ($freeStudentships as $fs) {
            if ($fs->fee_category_id === null) {
                $freeStudentshipsForAll[] = $fs;
            } else {
                $freeStudentshipsByCategory[$fs->fee_category_id] = $fs;
            }
        }

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

            $feeGross = max(0, $fee->amount - $fee->paid_amount);

            foreach ($fee->feeSet->items as $item) {
                // Scholarships - specific category
                if (isset($scholarshipsByCategory[$item->fee_category_id])) {
                    $scholarship = $scholarshipsByCategory[$item->fee_category_id];
                    $discount = $scholarship->calculateDiscount($item->amount);
                    $categoryDiscounts[] = [
                        'category' => $item->category->name_en,
                        'amount' => $item->amount,
                        'discount' => $discount,
                        'net' => $item->amount - $discount,
                    ];
                    $totalDiscount += $discount;
                }

                // Free Studentships - specific category
                if (isset($freeStudentshipsByCategory[$item->fee_category_id])) {
                    $freeStudentship = $freeStudentshipsByCategory[$item->fee_category_id];
                    $discount = $freeStudentship->calculateDiscount($item->amount);
                    $categoryDiscounts[] = [
                        'category' => $item->category->name_en . ' (Free)',
                        'amount' => $item->amount,
                        'discount' => $discount,
                        'net' => $item->amount - $discount,
                    ];
                    $totalDiscount += $discount;
                }
            }

            // Scholarships - applies to all categories (once per fee)
            if (!empty($scholarshipsForAll)) {
                foreach ($scholarshipsForAll as $scholarship) {
                    $discount = $scholarship->calculateDiscount($feeGross - $totalDiscount);
                    if ($discount > 0) {
                        $categoryDiscounts[] = [
                            'category' => 'Scholarship (All Categories)',
                            'amount' => $feeGross - $totalDiscount,
                            'discount' => $discount,
                            'net' => ($feeGross - $totalDiscount) - $discount,
                        ];
                        $totalDiscount += $discount;
                    }
                }
            }

            // Free Studentships - applies to all categories (once per fee)
            if (!empty($freeStudentshipsForAll)) {
                foreach ($freeStudentshipsForAll as $freeStudentship) {
                    $discount = $freeStudentship->calculateDiscount($feeGross - $totalDiscount);
                    if ($discount > 0) {
                        $categoryDiscounts[] = [
                            'category' => 'Free All Categories',
                            'amount' => $feeGross - $totalDiscount,
                            'discount' => $discount,
                            'net' => ($feeGross - $totalDiscount) - $discount,
                        ];
                        $totalDiscount += $discount;
                    }
                }
            }

            $fee->category_discounts = $categoryDiscounts;
            $fee->total_scholarship_discount = $totalDiscount;
            $fee->total_transport_fee = $totalTransport;
            $fee->remaining_gross = $feeGross;
            $fee->calculated_net_amount = max(0, $feeGross - $totalDiscount + $totalTransport);
        }

        $studentClassId = optional($student->academicInformations->last())->school_class_id;

        $inventoryCategories = InventoryCategory::with(['items' => function ($q) use ($studentClassId) {
            $q->where('is_active', true)
              ->where(function ($sub) use ($studentClassId) {
                  $sub->where('item_type', 'common')
                      ->orWhere(function ($cls) use ($studentClassId) {
                          $cls->where('item_type', 'classwise')
                              ->where('school_class_id', $studentClassId);
                      });
              });
        }])->where('is_active', true)->get()
          ->filter(fn($cat) => $cat->items->isNotEmpty())
          ->values();

        return view('pages.fees.collect_fee', compact('pendingFees', 'student', 'payments', 'inventoryCategories'));

    }

    public function pay(Request $request)
    {
        $request->validate([
            'fees'           => 'nullable|array',
            'fees.*'         => 'exists:fees,id',
            'items'          => 'nullable|array',
            'items.*.inventory_item_id' => 'required_with:items|exists:inventory_items,id',
            'items.*.quantity'          => 'required_with:items|integer|min:1',
            'student_id'     => 'nullable|exists:students,id',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other,cash,bank,mobile_wallet',
            'account_type'   => 'nullable|in:App\\Models\\HandCash,App\\Models\\BankAccount,App\\Models\\MobileBankingAccount',
            'account_id'     => 'nullable|integer',
            'description'    => 'nullable|string|max:1000',
        ]);

        if (empty($request->fees) && empty($request->items)) {
            return response()->json(['message' => 'Please select at least one fee or item'], 422);
        }

        DB::beginTransaction();

        try {
            $fees = Fee::with(['feeSet.items.category'])->whereIn('id', $request->fees ?? [])->lockForUpdate()->get();

            $requestedItems = collect($request->items ?? []);
            $inventoryItems = $requestedItems->isNotEmpty()
                ? InventoryItem::whereIn('id', $requestedItems->pluck('inventory_item_id'))->lockForUpdate()->get()->keyBy('id')
                : collect();

            // Validate stock before any writes
            foreach ($requestedItems as $ri) {
                $invItem = $inventoryItems->get($ri['inventory_item_id']);
                if (!$invItem || $invItem->current_stock < $ri['quantity']) {
                    DB::rollBack();
                    return response()->json(['message' => 'Insufficient stock for: ' . ($invItem->name ?? 'item')], 422);
                }
            }

            if ($fees->isEmpty() && $requestedItems->isEmpty()) {
                return response()->json(['message' => 'No fees or items selected'], 422);
            }

            $studentId = $fees->isNotEmpty() ? $fees->first()->student_id : null;
            if (!$studentId) {
                // items-only sale — student_id must be passed in payload
                $studentId = (int) $request->student_id;
            }

            if ($fees->where('student_id', '!=', $studentId)->count()) {
                return response()->json(['message' => 'Fees must belong to the same student'], 422);
            }

            // Inventory sale subtotal
            $inventorySaleTotal = 0.0;
            foreach ($requestedItems as $ri) {
                $invItem = $inventoryItems->get($ri['inventory_item_id']);
                $inventorySaleTotal += round((float)$invItem->selling_price * (int)$ri['quantity'], 2);
            }

            $student = Student::with('academicInformations')->find($studentId);
            $sessionId = optional($student->academicInformations->last())->academic_session_id;

            $scholarships = Scholarship::where('student_id', $studentId)
                ->where('status', 'active')
                ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
                ->get();

            // Index scholarships by fee_category_id
            $scholarshipsByCategory = [];
            $scholarshipsForAll = [];
            foreach ($scholarships as $s) {
                if ($s->fee_category_id === null) {
                    $scholarshipsForAll[] = $s;
                } else {
                    $scholarshipsByCategory[$s->fee_category_id] = $s;
                }
            }

            $freeStudentships = FreeStudentship::where('student_id', $studentId)
                ->where('status', 'active')
                ->when($sessionId, fn($q) => $q->where('academic_session_id', $sessionId))
                ->get();

            // Index free studentships by fee_category_id
            $freeStudentshipsByCategory = [];
            $freeStudentshipsForAll = [];
            foreach ($freeStudentships as $fs) {
                if ($fs->fee_category_id === null) {
                    $freeStudentshipsForAll[] = $fs;
                } else {
                    $freeStudentshipsByCategory[$fs->fee_category_id] = $fs;
                }
            }

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
                    // Scholarships - specific category
                    if (isset($scholarshipsByCategory[$item->fee_category_id])) {
                        $feeScholarship += $scholarshipsByCategory[$item->fee_category_id]->calculateDiscount($item->amount);
                    }
                    // Free Studentships - specific category
                    if (isset($freeStudentshipsByCategory[$item->fee_category_id])) {
                        $feeScholarship += $freeStudentshipsByCategory[$item->fee_category_id]->calculateDiscount($item->amount);
                    }
                }

                // Scholarships - applies to all categories
                if (!empty($scholarshipsForAll)) {
                    foreach ($scholarshipsForAll as $scholarship) {
                        $feeScholarship += $scholarship->calculateDiscount($feeGross - $feeScholarship);
                    }
                }

                // Free Studentships - applies to all categories
                if (!empty($freeStudentshipsForAll)) {
                    foreach ($freeStudentshipsForAll as $freeStudentship) {
                        $feeScholarship += $freeStudentship->calculateDiscount($feeGross - $feeScholarship);
                    }
                }

                $netAfterScholarship              = max(0, $feeGross - $feeScholarship);
                $feeNetAmounts[$fee->id]           = $netAfterScholarship;
                $grossAmount                      += $feeGross;
                $totalScholarshipDiscount         += $feeScholarship;
            }

            $totalAfterScholarship = $grossAmount - $totalScholarshipDiscount;
            $cartDiscount          = (float)($request->discount_amount ?? 0);
            $feePaymentAmount      = max(0, $totalAfterScholarship - $cartDiscount);
            $finalAmount           = $feePaymentAmount + $inventorySaleTotal;

            // Handle partial payment
            $paymentAmount = $request->payment_amount ? min($finalAmount, (float)$request->payment_amount) : $finalAmount;
            // Fee-only portion of the payment (exclude inventory)
            $feeOnlyPayment = max(0, $paymentAmount - $inventorySaleTotal);

            // Guard only against a fully empty submission (no fees, no items, no amount).
            if ($paymentAmount <= 0 && $feeOnlyPayment <= 0 && $inventorySaleTotal <= 0 && empty($request->fees) && empty($request->items)) {
                return response()->json(['message' => 'Please select at least one fee or item'], 422);
            }

            $paymentMethod = $this->normalizePaymentMethod($request->payment_method);

            $payment = Payment::create([
                'student_id'         => $studentId,
                'amount'             => $paymentAmount,
                'gross_amount'       => $grossAmount,
                'scholarship_amount' => $totalScholarshipDiscount,
                'discount_type'      => $cartDiscount > 0 ? ($request->discount_type ?? 'flat') : null,
                'discount_amount'    => $cartDiscount,
                'payment_date'    => now(),
                'payment_method'  => $paymentMethod,
                'account_type'    => $request->account_type ?? null,
                'account_id'      => $request->account_id ?? null,
                'description'     => $request->description,
                'receipt_no'      => 'R-' . now()->format('Ymd') . '-' . str_pad(
                                        Payment::whereDate('created_at', today())->count() + 1,
                                        4, '0', STR_PAD_LEFT
                                    ),
                'collected_by'    => auth()->id(),
            ]);

            // ── Pass 2: create PaymentItems distributing fee payment proportionally ──
            $studentPaymentAmount  = 0.0;
            $transportPaymentAmount = 0.0;
            $totalNetAmount = 0.0;

            // First pass: calculate total net amount
            foreach ($fees as $fee) {
                $netAfterScholarship = $feeNetAmounts[$fee->id] ?? 0;
                if ($netAfterScholarship <= 0) continue;

                // Distribute cart discount proportionally based on share of totalAfterScholarship
                $feeCartDiscount = $totalAfterScholarship > 0
                    ? round($cartDiscount * ($netAfterScholarship / $totalAfterScholarship), 2)
                    : 0;

                $netAmount = max(0, $netAfterScholarship - $feeCartDiscount);
                if ($netAmount <= 0) continue;

                $totalNetAmount += $netAmount;
            }

            // Second pass: distribute fee-only payment amount proportionally
            foreach ($fees as $fee) {
                $netAfterScholarship = $feeNetAmounts[$fee->id] ?? 0;
                if ($netAfterScholarship <= 0) continue;

                $feeCartDiscount = $totalAfterScholarship > 0
                    ? round($cartDiscount * ($netAfterScholarship / $totalAfterScholarship), 2)
                    : 0;

                $netAmount = max(0, $netAfterScholarship - $feeCartDiscount);
                if ($netAmount <= 0) continue;

                // Distribute fee-only payment proportionally
                $paidAmount = $totalNetAmount > 0 ? round($feeOnlyPayment * ($netAmount / $totalNetAmount), 2) : 0;

                PaymentItem::create([
                    'payment_id' => $payment->id,
                    'fee_id'     => $fee->id,
                    'amount'     => $paidAmount,
                ]);

                // Split this fee payment by fee-set category type so accounting can post:
                // - student-payment (regular heads)
                // - transport-fee (transport heads)
                $transportBase = 0.0;
                $regularBase   = 0.0;
                foreach ($fee->feeSet->items as $item) {
                    if (($item->category->is_transport ?? 0) == 1) {
                        $transportBase += (float) $item->amount;
                    } else {
                        $regularBase += (float) $item->amount;
                    }
                }

                $totalBase = $transportBase + $regularBase;
                if ($totalBase > 0) {
                    $transportShare = round($paidAmount * ($transportBase / $totalBase), 2);
                    $regularShare   = round($paidAmount - $transportShare, 2);
                } else {
                    // Fallback: treat unknown composition as regular student payment.
                    $transportShare = 0.0;
                    $regularShare   = (float) $paidAmount;
                }

                $transportPaymentAmount += $transportShare;
                $studentPaymentAmount   += $regularShare;

                $feeScholarship = ($fee->amount - $fee->paid_amount) - $netAfterScholarship;
                $netDue = max(0, $fee->amount - $feeScholarship - $feeCartDiscount);
                $fee->paid_amount += $paidAmount;
                $fee->status = $fee->paid_amount >= $netDue ? 'paid' : 'partial';
                $fee->save();
            }

            // Post regular student fees
            if ($studentPaymentAmount > 0) {
                $category = IncomeCategory::where('slug', 'student-payment')->firstOrFail();
                $payment->recordIncome($category->id, 'Student Payment', [
                    'amount'         => $studentPaymentAmount,
                    'payment_method' => $paymentMethod,
                    'account_type'   => $payment->account_type,
                    'account_id'     => $payment->account_id,
                    'description'    => "Student fee payment for student ID {$studentId}",
                ]);
            }

            // Post transport fees separately
            if ($transportPaymentAmount > 0) {
                $category = IncomeCategory::where('slug', 'transport-fee')->firstOrFail();
                $payment->recordIncome($category->id, 'Transport Fee', [
                    'amount'         => $transportPaymentAmount,
                    'payment_method' => $paymentMethod,
                    'account_type'   => $payment->account_type,
                    'account_id'     => $payment->account_id,
                    'description'    => "Transport fee payment for student ID {$studentId}",
                ]);
            }

            // ── Inventory sale ──
            if ($requestedItems->isNotEmpty()) {
                $sale = InventorySale::create([
                    'payment_id'   => $payment->id,
                    'student_id'   => $studentId,
                    'total_amount' => $inventorySaleTotal,
                    'created_by'   => auth()->id(),
                ]);

                foreach ($requestedItems as $ri) {
                    $invItem = $inventoryItems->get($ri['inventory_item_id']);
                    $qty     = (int) $ri['quantity'];
                    $price   = (float) $invItem->selling_price;

                    InventorySaleItem::create([
                        'inventory_sale_id'  => $sale->id,
                        'inventory_item_id'  => $invItem->id,
                        'quantity'           => $qty,
                        'unit_price'         => $price,
                        'subtotal'           => round($price * $qty, 2),
                    ]);

                    $invItem->decrement('current_stock', $qty);

                    StockMovement::create([
                        'inventory_item_id' => $invItem->id,
                        'type'              => 'sale',
                        'quantity_change'   => -$qty,
                        'unit_price'        => $price,
                        'created_by'        => auth()->id(),
                        'note'              => 'Sale via payment ' . $payment->receipt_no,
                    ]);
                }

                $payment->inventory_sale_id = $sale->id;
                $payment->save();

                // Inventory sale increases petty cash (if payment account is not already set to an account)
                if (!$payment->account_type || !$payment->account_id) {
                    PettyCashService::credit(
                        (float) $inventorySaleTotal, 'inventory_sale',
                        $payment->receipt_no, 'Inventory sale — ' . $payment->receipt_no,
                        now(), InventorySale::class, $sale->id
                    );
                }
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
