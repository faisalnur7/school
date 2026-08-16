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
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Transport;
use App\Models\Scholarship;
use App\Models\FreeStudentship;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Services\PettyCashService;
use App\Models\InventorySale;
use App\Models\InventorySaleItem;
use App\Models\PaymentInventoryItem;
use App\Models\StockMovement;

class FeeCollectionController extends Controller
{
    private function currentAcademicInformation(Student $student)
    {
        return $student->academicInformations
            ->firstWhere('is_current', true)
            ?? $student->academicInformations->sortByDesc('id')->first();
    }

    private function discountSourceLabel(bool $hasScholarship, bool $hasFreeStudentship): string
    {
        if ($hasScholarship && $hasFreeStudentship) {
            return 'Scholarship + Free Studentship';
        }

        if ($hasFreeStudentship) {
            return 'Free Studentship';
        }

        if ($hasScholarship) {
            return 'Scholarship';
        }

        return 'Fee Discount';
    }

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

    private function buildStudentFeeDescription(Student $student): string
    {
        $info = $this->currentAcademicInformation($student);

        $studentName = $student->full_name_en ?: 'Student';
        $studentCid = $student->student_cid ?: $student->id;
        $className = $info?->schoolClass?->name_en ?? 'N/A';
        $sectionName = $info?->section?->name_en ?? 'N/A';
        $groupName = $info?->group?->name_en ?? 'N/A';

        return sprintf(
            'Student fee payment for %s (ID %s) | Class: %s | Section: %s | Group: %s',
            $studentName,
            $studentCid,
            $className,
            $sectionName,
            $groupName
        );
    }

    private function buildStudentSearchQuery(Request $request)
    {
        $studentCid = trim((string) $request->input('student_id', ''));

        return Student::with([
            'academicInformations' => function ($q) {
                $q->where('is_current', true)
                    ->where('academic_status', 'active')
                    ->with(['schoolClass', 'section', 'group', 'academicSession']);
            },
        ])
            ->where('status', 1)
            ->when($studentCid !== '', function ($query) use ($studentCid) {
                $query->where('student_cid', 'like', '%' . $studentCid . '%');
            })
            ->whereHas('academicInformations', function ($q) use ($request) {
                $q->where('is_current', true)
                    ->where('academic_status', 'active')
                    ->when($request->filled('academic_session_id'), function ($subQuery) use ($request) {
                        $subQuery->where('academic_session_id', $request->academic_session_id);
                    })
                    ->when($request->filled('school_class_id'), function ($subQuery) use ($request) {
                        $subQuery->where('school_class_id', $request->school_class_id);
                    })
                    ->when($request->filled('section_id'), function ($subQuery) use ($request) {
                        $subQuery->where('section_id', $request->section_id);
                    })
                    ->when($request->filled('group_id'), function ($subQuery) use ($request) {
                        $subQuery->where('group_id', $request->group_id);
                    });
            });
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
        $studentQuery = $this->buildStudentSearchQuery($request);

        // Only search if filters applied
        if (
            $request->filled('student_id') ||
            $request->filled('school_class_id') ||
            $request->filled('section_id') ||
            $request->filled('group_id') ||
            $request->filled('academic_session_id')
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
            'redirect_url' => route('fees.collect_payment', ['student_id' => $student->id])
        ]);
    }

    public function collect_payment(Request $request)
    {
        $classes = SchoolClass::all();
        $sections = Section::all();
        $groups = Group::all();
        $sessions = AcademicSession::all();
        $studentId = $request->query('student_id', $request->query('id'));

        if (blank($studentId)) {
            return view('pages.fees.collect_fee', [
                'student' => null,
                'payments' => collect(),
                'pendingFees' => collect(),
                'assignedFees' => collect(),
                'inventoryCategories' => collect(),
                'inventoryDueItems' => collect(),
                'classes' => $classes,
                'sections' => $sections,
                'groups' => $groups,
                'sessions' => $sessions,
            ]);
        }

        $student = Student::with([
            'academicInformations.schoolClass',
            'academicInformations.section',
            'academicInformations.group',
            'academicInformations.academicSession',
            'fees' => function ($query) {
                $query->with(['feeSet.items.category', 'scholarship']);
            },
            'payments' => function ($query) {
                $query->orderByDesc('payment_date')->orderByDesc('id');
            },
            'payments.items.fee.feeSet',
            'payments.inventorySale.items.inventoryItem.category',
            'payments.inventoryDueItems.inventorySaleItem.inventoryItem.category',
            'payments.collector',
        ])->findOrFail($studentId);

        $payments = $student->payments;

        $sessionId = optional($this->currentAcademicInformation($student))->academic_session_id;

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

        $assignedFees = Fee::with(['feeSet.items.category', 'scholarship'])
            ->where('student_id', $student->id)
            ->when($sessionId, function ($q) use ($sessionId) {
                $q->whereHas('feeSet', function ($subQ) use ($sessionId) {
                    $subQ->where('academic_session_id', $sessionId);
                });
            })
            ->orderBy('due_date')
            ->orderBy('id')
            ->get()
            ->filter(function ($fee) use ($studentType) {
                $items = $fee->feeSet->items;
                if ($items->isEmpty()) {
                    return true;
                }

                return $items->contains(fn ($item) =>
                    in_array($item->category->student_type ?? 'both', ['both', $studentType])
                );
            })
            ->values();

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
            $hasScholarship = false;
            $hasFreeStudentship = false;

            $feeGross = max(0, $fee->amount - $fee->paid_amount);

            foreach ($fee->feeSet->items as $item) {
                // Scholarships - specific category
                if (isset($scholarshipsByCategory[$item->fee_category_id])) {
                    $scholarship = $scholarshipsByCategory[$item->fee_category_id];
                    $discount = $scholarship->calculateDiscount($item->amount);
                    $categoryDiscounts[] = [
                        'category' => $item->category->name_en . ' (Scholarship)',
                        'amount' => $item->amount,
                        'discount' => $discount,
                        'net' => $item->amount - $discount,
                    ];
                    $totalDiscount += $discount;
                    $hasScholarship = true;
                }

                // Free Studentships - specific category
                if (isset($freeStudentshipsByCategory[$item->fee_category_id])) {
                    $freeStudentship = $freeStudentshipsByCategory[$item->fee_category_id];
                    $discount = $freeStudentship->calculateDiscount($item->amount);
                    $categoryDiscounts[] = [
                        'category' => $item->category->name_en . ' (Free Studentship)',
                        'amount' => $item->amount,
                        'discount' => $discount,
                        'net' => $item->amount - $discount,
                    ];
                    $totalDiscount += $discount;
                    $hasFreeStudentship = true;
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
                        $hasScholarship = true;
                    }
                }
            }

            // Free Studentships - applies to all categories (once per fee)
            if (!empty($freeStudentshipsForAll)) {
                foreach ($freeStudentshipsForAll as $freeStudentship) {
                    $discount = $freeStudentship->calculateDiscount($feeGross - $totalDiscount);
                    if ($discount > 0) {
                        $categoryDiscounts[] = [
                            'category' => 'Free Studentship (All Categories)',
                            'amount' => $feeGross - $totalDiscount,
                            'discount' => $discount,
                            'net' => ($feeGross - $totalDiscount) - $discount,
                        ];
                        $totalDiscount += $discount;
                        $hasFreeStudentship = true;
                    }
                }
            }

            $fee->category_discounts = $categoryDiscounts;
            $fee->total_scholarship_discount = $totalDiscount;
            $fee->discount_label = $this->discountSourceLabel($hasScholarship, $hasFreeStudentship);
            $fee->total_transport_fee = $totalTransport;
            $fee->remaining_gross = $feeGross;
            $fee->calculated_net_amount = max(0, $feeGross - $totalDiscount + $totalTransport);
        }

        $studentClassId = optional($this->currentAcademicInformation($student))->school_class_id;

        $inventoryCategories = InventoryCategory::with(['items' => function ($q) use ($studentClassId) {
            $q->where('is_active', true)
              ->where(function ($sub) use ($studentClassId) {
                  $sub->where('item_type', 'common')
                      ->orWhere(function ($cls) use ($studentClassId) {
                          $cls->where('item_type', 'classwise')
                              ->where('school_class_id', $studentClassId);
                      });
              })
              ->orderByRaw("CASE WHEN stock_type = 'made_to_order' THEN 0 WHEN current_stock > 0 THEN 1 ELSE 2 END, name ASC");
        }])->where('is_active', true)->get()
          ->filter(fn($cat) => $cat->items->isNotEmpty())
          ->values();

        $inventoryDueItems = InventorySaleItem::with(['inventorySale.payment', 'inventoryItem.category'])
            ->whereHas('inventorySale', fn($q) => $q->where('student_id', $student->id))
            ->whereRaw('(subtotal - COALESCE(paid_amount,0)) > 0')
            ->get()
            ->map(function ($item) {
                $due = max(0, (float) $item->subtotal - (float) ($item->paid_amount ?? 0));
                $item->due_amount = $due;
                return $item;
            })
            ->groupBy(fn($item) => $item->inventoryItem?->category_id ?? 0)
            ->map(fn($items) => $items->values())
            ->filter()
            ->values();

        return view('pages.fees.collect_fee', compact(
            'pendingFees',
            'assignedFees',
            'student',
            'payments',
            'inventoryCategories',
            'inventoryDueItems',
            'classes',
            'sections',
            'groups',
            'sessions'
        ));

    }

    public function searchStudents(Request $request)
    {
        $students = $this->buildStudentSearchQuery($request)
            ->orderBy('student_cid')
            ->limit(50)
            ->get();

        return view('pages.fees.partials.student_search_results', compact('students'))->render();
    }

    public function pay(Request $request)
    {
        $feesInput = collect($request->input('fees', []));
        if ($feesInput->isNotEmpty() && ! is_array($feesInput->first())) {
            $request->merge([
                'fees' => $feesInput
                    ->map(fn ($feeId) => ['fee_id' => $feeId])
                    ->all(),
            ]);
        }

        $request->validate([
            'fees'           => 'nullable|array',
            'fees.*.fee_id'   => 'required_with:fees|exists:fees,id',
            'fees.*.amount'   => 'nullable|numeric|min:0',
            'items'          => 'nullable|array',
            'items.*.inventory_item_id' => 'required_with:items|exists:inventory_items,id',
            'items.*.quantity'          => 'required_with:items|integer|min:1',
            'items.*.unit_price'        => 'nullable|numeric|min:0',
            'items.*.paid_amount'       => 'nullable|numeric|min:0',
            'inventory_dues'            => 'nullable|array',
            'inventory_dues.*.inventory_sale_item_id' => 'required_with:inventory_dues|exists:inventory_sale_items,id',
            'inventory_dues.*.paid_amount'            => 'nullable|numeric|min:0',
            'student_id'     => 'nullable|exists:students,id',
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other,cash,bank,mobile_wallet',
            'account_type'   => 'nullable|in:App\\Models\\HandCash,App\\Models\\BankAccount,App\\Models\\MobileBankingAccount',
            'account_id'     => 'nullable|integer',
            'description'    => 'nullable|string|max:1000',
        ]);

        if (empty($request->fees) && empty($request->items) && empty($request->inventory_dues)) {
            return response()->json(['message' => 'Please select at least one fee, inventory item, or inventory due item'], 422);
        }

        DB::beginTransaction();

        try {
            $requestedFees = collect($request->input('fees', []))
                ->filter(fn ($fee) => is_array($fee) && !empty($fee['fee_id']))
                ->values();
            $requestedItems = collect($request->input('items', []))
                ->filter(fn ($item) => is_array($item) && !empty($item['inventory_item_id']))
                ->values();
            $requestedInventoryDues = collect($request->input('inventory_dues', []))
                ->filter(fn ($item) => is_array($item) && !empty($item['inventory_sale_item_id']))
                ->values();

            $fees = Fee::with(['feeSet.items.category'])
                ->whereIn('id', $requestedFees->pluck('fee_id')->all())
                ->lockForUpdate()
                ->get();

            $studentId = $fees->isNotEmpty() ? $fees->first()->student_id : null;
            if (!$studentId) {
                // items-only sale — student_id must be passed in payload
                $studentId = (int) $request->student_id;
            }

            $inventoryItems = $requestedItems->isNotEmpty()
                ? InventoryItem::whereIn('id', $requestedItems->pluck('inventory_item_id'))->lockForUpdate()->get()->keyBy('id')
                : collect();

            $inventoryDueItems = $requestedInventoryDues->isNotEmpty()
                ? InventorySaleItem::with(['inventorySale'])
                    ->whereIn('id', $requestedInventoryDues->pluck('inventory_sale_item_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id')
                : collect();

            // Validate stock before any writes
            foreach ($requestedItems as $ri) {
                $invItem = $inventoryItems->get($ri['inventory_item_id']);
                if (!$invItem) {
                    DB::rollBack();
                    return response()->json(['message' => 'Invalid inventory item selected'], 422);
                }

                if (!$invItem->isMadeToOrder() && $invItem->current_stock < $ri['quantity']) {
                    DB::rollBack();
                    return response()->json(['message' => 'Insufficient stock for: ' . ($invItem->name ?? 'item')], 422);
                }
            }

            foreach ($requestedInventoryDues as $ri) {
                $saleItem = $inventoryDueItems->get($ri['inventory_sale_item_id']);
                if (!$saleItem || $saleItem->inventorySale?->student_id != $studentId) {
                    DB::rollBack();
                    return response()->json(['message' => 'Invalid inventory due item selected'], 422);
                }
            }

            if ($fees->isEmpty() && $requestedItems->isEmpty() && $requestedInventoryDues->isEmpty()) {
                return response()->json(['message' => 'No fees, items, or due items selected'], 422);
            }

            if ($fees->where('student_id', '!=', $studentId)->count()) {
                return response()->json(['message' => 'Fees must belong to the same student'], 422);
            }

            $student = Student::with(['academicInformations.schoolClass', 'academicInformations.section', 'academicInformations.group'])->find($studentId);
            $sessionId = optional($this->currentAcademicInformation($student))->academic_session_id;

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
            $feeNetAmounts            = []; // fee_id => net after scholarship/discount
            $feeScholarshipAmounts    = [];
            $feeFreeStudentshipAmounts = [];
            $requestedFeeAmounts      = [];

            foreach ($requestedFees as $feeLine) {
                $requestedFeeAmounts[(int) $feeLine['fee_id']] = isset($feeLine['amount'])
                    ? max(0, (float) $feeLine['amount'])
                    : null;
            }

            foreach ($fees as $fee) {
                $feeGross       = $fee->amount - $fee->paid_amount;
                $feeScholarship = 0;
                $feeScholarshipOnly = 0;
                $feeFreeStudentshipOnly = 0;

                foreach ($fee->feeSet->items as $item) {
                    // Scholarships - specific category
                    if (isset($scholarshipsByCategory[$item->fee_category_id])) {
                        $discount = $scholarshipsByCategory[$item->fee_category_id]->calculateDiscount($item->amount);
                        $feeScholarship += $discount;
                        $feeScholarshipOnly += $discount;
                    }
                    // Free Studentships - specific category
                    if (isset($freeStudentshipsByCategory[$item->fee_category_id])) {
                        $discount = $freeStudentshipsByCategory[$item->fee_category_id]->calculateDiscount($item->amount);
                        $feeScholarship += $discount;
                        $feeFreeStudentshipOnly += $discount;
                    }
                }

                // Scholarships - applies to all categories
                if (!empty($scholarshipsForAll)) {
                    foreach ($scholarshipsForAll as $scholarship) {
                        $discount = $scholarship->calculateDiscount($feeGross - $feeScholarship);
                        $feeScholarship += $discount;
                        $feeScholarshipOnly += $discount;
                    }
                }

                // Free Studentships - applies to all categories
                if (!empty($freeStudentshipsForAll)) {
                    foreach ($freeStudentshipsForAll as $freeStudentship) {
                        $discount = $freeStudentship->calculateDiscount($feeGross - $feeScholarship);
                        $feeScholarship += $discount;
                        $feeFreeStudentshipOnly += $discount;
                    }
                }

                $netAfterScholarship              = max(0, $feeGross - $feeScholarship);
                $feeNetAmounts[$fee->id]           = $netAfterScholarship;
                $feeScholarshipAmounts[$fee->id]   = $feeScholarshipOnly;
                $feeFreeStudentshipAmounts[$fee->id] = $feeFreeStudentshipOnly;
                $grossAmount                      += $feeGross;
                $totalScholarshipDiscount         += $feeScholarship;
            }

            $inventorySaleTotal   = 0.0;
            $inventoryPaidTotal   = 0.0;
            $inventoryGrossTotal  = 0.0;
            $inventoryPaidAmounts = [];
            $resolvedInventoryItems = [];

            foreach ($requestedItems as $ri) {
                $invItem = $inventoryItems->get($ri['inventory_item_id']);
                $qty     = (int) $ri['quantity'];
                $unitPrice = $invItem->is_flexible_price
                    ? round((float) ($ri['unit_price'] ?? $invItem->selling_price), 2)
                    : round((float) $invItem->selling_price, 2);
                $subtotal = round($unitPrice * $qty, 2);
                $paidAmount = isset($ri['paid_amount'])
                    ? max(0, (float) $ri['paid_amount'])
                    : $subtotal;

                $resolvedInventoryItems[] = [
                    'item' => $invItem,
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'paid_amount' => $paidAmount,
                ];

                $inventoryGrossTotal += $subtotal;
                $inventorySaleTotal += $subtotal;
                $inventoryPaidAmounts[(int) $ri['inventory_item_id']] = min($paidAmount, $subtotal);
                $inventoryPaidTotal += $inventoryPaidAmounts[(int) $ri['inventory_item_id']];
            }

            $inventoryDueGrossTotal = 0.0;
            $inventoryDuePaidTotal = 0.0;
            $inventoryDuePaidAmounts = [];

            foreach ($requestedInventoryDues as $ri) {
                $saleItem = $inventoryDueItems->get($ri['inventory_sale_item_id']);
                $dueAmount = max(0, (float) $saleItem->subtotal - (float) ($saleItem->paid_amount ?? 0));
                $inventoryDueGrossTotal += $dueAmount;
                $inventoryDuePaidAmounts[(int) $ri['inventory_sale_item_id']] = isset($ri['paid_amount'])
                    ? max(0, (float) $ri['paid_amount'])
                    : $dueAmount;
                $inventoryDuePaidAmounts[(int) $ri['inventory_sale_item_id']] = min(
                    $inventoryDuePaidAmounts[(int) $ri['inventory_sale_item_id']],
                    $dueAmount
                );
                $inventoryDuePaidTotal += $inventoryDuePaidAmounts[(int) $ri['inventory_sale_item_id']];
            }

            $totalAfterScholarship = $grossAmount - $totalScholarshipDiscount;
            $cartDiscount          = (float)($request->discount_amount ?? 0);
            $feePaymentDefault = max(0, $totalAfterScholarship - $cartDiscount);
            $finalAmount       = $feePaymentDefault + $inventoryPaidTotal + $inventoryDuePaidTotal;

            if ($finalAmount <= 0) {
                return response()->json(['message' => 'Payment amount must be greater than zero.'], 422);
            }

            $paymentMethod = $this->normalizePaymentMethod($request->payment_method);
            $paymentAmount = 0.0;

            foreach ($fees as $fee) {
                $netAfterScholarship = $feeNetAmounts[$fee->id] ?? 0;
                $feeCartDiscount = $totalAfterScholarship > 0
                    ? round($cartDiscount * ($netAfterScholarship / $totalAfterScholarship), 2)
                    : 0;
                $netAmount = max(0, $netAfterScholarship - $feeCartDiscount);
                $lineAmount = $requestedFeeAmounts[$fee->id] ?? $netAmount;
                $lineAmount = min(max(0, (float) $lineAmount), $netAmount);
                $requestedFeeAmounts[$fee->id] = $lineAmount;
                $paymentAmount += $lineAmount;
            }

            $paymentAmount += $inventoryPaidTotal + $inventoryDuePaidTotal;
            $paymentDescription = trim($this->buildStudentFeeDescription($student) . (
                filled($request->description) ? ' | Note: ' . trim((string) $request->description) : ''
            ));

            $payment = Payment::create([
                'student_id'         => $studentId,
                'amount'             => $paymentAmount,
                'gross_amount'       => $grossAmount + $inventorySaleTotal + $inventoryDueGrossTotal,
                'scholarship_amount' => $totalScholarshipDiscount,
                'discount_type'      => $cartDiscount > 0 ? ($request->discount_type ?? 'flat') : null,
                'discount_amount'    => $cartDiscount,
                'payment_date'    => now(),
                'payment_method'  => $paymentMethod,
                'account_type'    => $request->account_type ?? null,
                'account_id'      => $request->account_id ?? null,
                'description'     => $paymentDescription,
                'receipt_no'      => 'R-' . now()->format('Ymd') . '-' . str_pad(
                                        Payment::whereDate('created_at', today())->count() + 1,
                                        4, '0', STR_PAD_LEFT
                                    ),
                'collected_by'    => auth()->id(),
            ]);

            foreach ($fees as $fee) {
                $netAfterScholarship = $feeNetAmounts[$fee->id] ?? 0;
                $feeCartDiscount = $totalAfterScholarship > 0
                    ? round($cartDiscount * ($netAfterScholarship / $totalAfterScholarship), 2)
                    : 0;
                $netAmount = max(0, $netAfterScholarship - $feeCartDiscount);
                $paidAmount = min($netAmount, (float) ($requestedFeeAmounts[$fee->id] ?? $netAmount));

                PaymentItem::create([
                    'payment_id'              => $payment->id,
                    'fee_id'                  => $fee->id,
                    'amount'                  => $paidAmount,
                    'scholarship_amount'      => $feeScholarshipAmounts[$fee->id] ?? 0,
                    'free_studentship_amount' => $feeFreeStudentshipAmounts[$fee->id] ?? 0,
                ]);

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
                    $transportShare = 0.0;
                    $regularShare   = (float) $paidAmount;
                }

                $feeScholarship = ($fee->amount - $fee->paid_amount) - $netAfterScholarship;
                $netDue = max(0, $fee->amount - $feeScholarship - $feeCartDiscount);
                $fee->paid_amount += $paidAmount;
                $fee->paid_amount = max(0, min($fee->paid_amount, $netDue));
                $fee->status = $fee->paid_amount <= 0 ? 'pending' : ($fee->paid_amount >= $netDue ? 'paid' : 'partial');
                $fee->save();
            }

            $studentPaymentAmount   = 0.0;
            $transportPaymentAmount = 0.0;
            foreach ($fees as $fee) {
                $feePaid = (float) ($requestedFeeAmounts[$fee->id] ?? 0);
                if ($feePaid <= 0) {
                    continue;
                }

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
                    $transportShare = round($feePaid * ($transportBase / $totalBase), 2);
                    $regularShare   = round($feePaid - $transportShare, 2);
                } else {
                    $transportShare = 0.0;
                    $regularShare   = (float) $feePaid;
                }

                $transportPaymentAmount += $transportShare;
                $studentPaymentAmount   += $regularShare;
            }

            if ($studentPaymentAmount > 0) {
                $category = IncomeCategory::where('slug', 'student-payment')->first();
                if ($category) {
                    $payment->recordIncome($category->id, 'Student Payment', [
                        'amount'         => $studentPaymentAmount,
                        'payment_method' => $paymentMethod,
                        'account_type'   => $payment->account_type,
                        'account_id'     => $payment->account_id,
                        'description'    => $paymentDescription,
                    ]);
                }
            }

            if ($transportPaymentAmount > 0) {
                $category = IncomeCategory::where('slug', 'transport-fee')->first();
                if ($category) {
                    $payment->recordIncome($category->id, 'Transport Fee', [
                        'amount'         => $transportPaymentAmount,
                        'payment_method' => $paymentMethod,
                        'account_type'   => $payment->account_type,
                        'account_id'     => $payment->account_id,
                        'description'    => $paymentDescription,
                    ]);
                }
            }

            if ($inventoryDuePaidTotal > 0 && (!$request->account_type || !$request->account_id)) {
                PettyCashService::credit(
                    (float) $inventoryDuePaidTotal,
                    'inventory_sale',
                    $payment->receipt_no,
                    'Inventory due settlement — ' . $payment->receipt_no,
                    now(),
                    Payment::class,
                    $payment->id
                );
            }

            foreach ($requestedInventoryDues as $ri) {
                $saleItem = $inventoryDueItems->get($ri['inventory_sale_item_id']);
                if (!$saleItem) {
                    continue;
                }

                $paidAmount = (float) ($inventoryDuePaidAmounts[(int) $saleItem->id] ?? 0);
                if ($paidAmount <= 0) {
                    continue;
                }

                PaymentInventoryItem::create([
                    'payment_id' => $payment->id,
                    'inventory_sale_item_id' => $saleItem->id,
                    'amount' => $paidAmount,
                ]);

                $saleItem->paid_amount = min(
                    (float) $saleItem->subtotal,
                    (float) ($saleItem->paid_amount ?? 0) + $paidAmount
                );
                $saleItem->save();
            }

            // ── Inventory sale ──
            if ($requestedItems->isNotEmpty()) {
                $sale = InventorySale::create([
                    'payment_id'   => $payment->id,
                    'student_id'   => $studentId,
                    'total_amount' => $inventorySaleTotal,
                    'created_by'   => auth()->id(),
                ]);

                foreach ($resolvedInventoryItems as $line) {
                    $invItem = $line['item'];
                    $qty     = $line['qty'];
                    $price   = $line['unit_price'];
                    $subtotal = $line['subtotal'];
                    $paidAmount = (float) $line['paid_amount'];

                    InventorySaleItem::create([
                        'inventory_sale_id'  => $sale->id,
                        'inventory_item_id'  => $invItem->id,
                        'quantity'           => $qty,
                        'unit_price'         => $price,
                        'subtotal'           => $subtotal,
                        'paid_amount'        => min($subtotal, max(0, $paidAmount)),
                    ]);

                    if (!$invItem->isMadeToOrder()) {
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
                }

                $payment->inventory_sale_id = $sale->id;
                $payment->save();

                if ($inventoryPaidTotal > 0) {
                    $category = IncomeCategory::firstOrCreate(
                        ['slug' => 'inventory-sale'],
                        ['name' => 'Inventory Sale']
                    );

                    $payment->recordIncome($category->id, 'Inventory Sale', [
                        'amount'         => round((float) $inventoryPaidTotal, 2),
                        'income_date'    => Carbon::parse($payment->payment_date ?? now())->toDateString(),
                        'transaction_date' => Carbon::parse($payment->payment_date ?? now())->toDateString(),
                        'payment_method' => $paymentMethod,
                        'account_type'   => $payment->account_type,
                        'account_id'     => $payment->account_id,
                        'description'    => 'Inventory sale payment for receipt ' . $payment->receipt_no . ' | ' . $paymentDescription,
                    ]);
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
