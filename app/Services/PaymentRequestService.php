<?php

namespace App\Services;

use App\Models\Fee;
use App\Models\InventoryItem;
use App\Models\PaymentRequest;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentRequestService
{
    public function createForStudent(Student $student, array $cart): PaymentRequest
    {
        $feesInput = collect($cart['fees'] ?? [])->filter(fn ($value) => is_array($value))->values();
        $itemsInput = collect($cart['items'] ?? [])->filter(fn ($value) => is_array($value))->values();

        if ($feesInput->isEmpty() && $itemsInput->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Please select at least one fee or inventory item.']);
        }

        $feeIds = $feesInput->pluck('fee_id')->filter()->map(fn ($id) => (int) $id);
        $inventoryIds = $itemsInput->pluck('inventory_item_id')->filter()->map(fn ($id) => (int) $id);

        if ($feeIds->count() !== $feeIds->unique()->count()) {
            throw ValidationException::withMessages(['fees' => 'A fee cannot be selected more than once.']);
        }

        if ($inventoryIds->count() !== $inventoryIds->unique()->count()) {
            throw ValidationException::withMessages(['items' => 'An inventory item cannot be selected more than once.']);
        }

        return DB::transaction(function () use ($student, $feesInput, $itemsInput, $feeIds, $inventoryIds) {
            PaymentRequest::query()
                ->where('student_id', $student->id)
                ->where('status', PaymentRequest::STATUS_PENDING)
                ->where('expires_at', '<=', now())
                ->delete();

            if (PaymentRequest::query()
                ->where('student_id', $student->id)
                ->where('status', PaymentRequest::STATUS_PENDING)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->exists()) {
                throw ValidationException::withMessages(['cart' => 'You already have a pending payment request. Cancel it before creating another.']);
            }

            $fees = Fee::with('feeSet')
                ->whereIn('id', $feeIds->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $inventoryItems = InventoryItem::with('category')
                ->whereIn('id', $inventoryIds->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $currentAcademic = $student->latestAcademicInformation()->with(['academicSession', 'schoolClass', 'section'])->first();
            $items = [];
            $total = 0.0;

            foreach ($feesInput as $input) {
                $fee = $fees->get((int) ($input['fee_id'] ?? 0));
                if (! $fee || (int) $fee->student_id !== (int) $student->id) {
                    throw ValidationException::withMessages(['fees' => 'One or more selected fees are invalid.']);
                }

                $due = max(0, round((float) $fee->due_amount, 2));
                if (! $fee->is_active || ! in_array($fee->status, ['pending', 'partial'], true) || $due <= 0) {
                    throw ValidationException::withMessages(['fees' => 'One or more selected fees are no longer payable.']);
                }

                $requested = $due;

                $items[] = [
                    'item_type' => 'fee',
                    'fee_id' => $fee->id,
                    'quantity' => 1,
                    'requested_amount' => $requested,
                    'unit_price' => $requested,
                    'subtotal' => $requested,
                    'display_name' => $fee->feeSet?->name ?? 'Fee',
                    'display_reference' => $fee->due_date?->format('d M, Y'),
                ];
                $total += $requested;
            }

            foreach ($itemsInput as $input) {
                $item = $inventoryItems->get((int) ($input['inventory_item_id'] ?? 0));
                $quantity = (int) ($input['quantity'] ?? 0);
                $classId = $currentAcademic?->school_class_id;

                if (! $item || ! $item->is_active || $quantity < 1) {
                    throw ValidationException::withMessages(['items' => 'One or more selected inventory items are invalid.']);
                }

                if ($item->item_type === 'classwise' && (int) $item->school_class_id !== (int) $classId) {
                    throw ValidationException::withMessages(['items' => 'One or more inventory items are not available for this student.']);
                }

                if ($item->group_id && (int) $item->group_id !== (int) ($currentAcademic?->group_id)) {
                    throw ValidationException::withMessages(['items' => 'One or more inventory items are not available for this student.']);
                }

                if (! $item->isMadeToOrder() && (int) $item->current_stock < $quantity) {
                    throw ValidationException::withMessages(['items' => "Insufficient stock for {$item->name}."]);
                }

                $unitPrice = round((float) $item->selling_price, 2);
                $subtotal = round($unitPrice * $quantity, 2);
                if ($subtotal <= 0) {
                    throw ValidationException::withMessages(['items' => 'Inventory item prices must be greater than zero.']);
                }

                $items[] = [
                    'item_type' => 'inventory',
                    'inventory_item_id' => $item->id,
                    'quantity' => $quantity,
                    'requested_amount' => $subtotal,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'display_name' => $item->name,
                    'display_reference' => $item->category?->name,
                ];
                $total += $subtotal;
            }

            $request = PaymentRequest::create([
                'student_id' => $student->id,
                'academic_session_id' => $currentAcademic?->academic_session_id,
                'school_class_id' => $currentAcademic?->school_class_id,
                'section_id' => $currentAcademic?->section_id,
                'status' => PaymentRequest::STATUS_PENDING,
                'total_amount' => round($total, 2),
                'expires_at' => now()->addHours(24),
            ]);

            $request->items()->createMany($items);

            return $request->load('items');
        });
    }
}
