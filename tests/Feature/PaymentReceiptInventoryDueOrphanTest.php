<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventorySale;
use App\Models\InventorySaleItem;
use App\Models\Payment;
use App\Models\PaymentInventoryItem;
use App\Models\PaymentItem;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PaymentReceiptInventoryDueOrphanTest extends TestCase
{
    use RefreshDatabase;

    public function test_receipt_ignores_orphaned_inventory_due_rows(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'KG',
            'name_bn' => 'KG',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $category = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'টিউশন ফি',
            'student_type' => 'both',
            'status' => 1,
        ]);

        $feeSet = FeeSet::create([
            'name' => 'Monthly Fees',
            'bn_name' => 'Monthly Fees',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'frequency' => 'monthly',
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $category->id,
            'amount' => 1920,
        ]);

        $student = Student::create([
            'full_name_en' => 'Receipt Student',
            'student_cid' => '0104',
            'status' => 1,
        ]);

        $student->academicInformations()->create([
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '4',
            'is_current' => 1,
            'academic_status' => 'active',
        ]);

        $fee = Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 1920,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => 1,
            'due_date' => now()->addMonth(),
        ]);

        $payment = Payment::create([
            'student_id' => $student->id,
            'amount' => 1920,
            'gross_amount' => 1920,
            'scholarship_amount' => 0,
            'discount_type' => null,
            'discount_amount' => 0,
            'payment_date' => now(),
            'payment_method' => 'Cash',
            'receipt_no' => 'R-TEST-0001',
            'collected_by' => $user->id,
            'description' => 'Tuition Fee - July',
        ]);

        PaymentItem::create([
            'payment_id' => $payment->id,
            'fee_id' => $fee->id,
            'amount' => 1920,
        ]);

        $inventoryCategory = InventoryCategory::create([
            'name' => 'Stationary',
            'status' => 1,
        ]);

        $inventoryItem = InventoryItem::create([
            'name' => 'Item',
            'category_id' => $inventoryCategory->id,
            'selling_price' => 20,
            'current_stock' => 1,
            'is_active' => 1,
            'item_type' => 'common',
            'stock_type' => 'stocked',
        ]);

        $sale = InventorySale::create([
            'payment_id' => $payment->id,
            'student_id' => $student->id,
            'total_amount' => 20,
            'created_by' => $user->id,
        ]);

        $saleItem = InventorySaleItem::create([
            'inventory_sale_id' => $sale->id,
            'inventory_item_id' => $inventoryItem->id,
            'quantity' => 1,
            'unit_price' => 20,
            'subtotal' => 20,
            'paid_amount' => 20,
        ]);

        PaymentInventoryItem::create([
            'payment_id' => $payment->id,
            'inventory_sale_item_id' => $saleItem->id,
            'amount' => 20,
        ]);

        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            InventorySaleItem::whereKey($saleItem->id)->delete();
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            InventorySaleItem::whereKey($saleItem->id)->delete();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('payments.receipt', $payment->id));

        file_put_contents(base_path('receipt-debug.html'), $response->getContent());

        $response->assertOk();
        $response->assertDontSee('Inventory Due Settlements', false);
        $response->assertDontSee('Inventory — Item', false);
        $response->assertDontSee('Stationary — Item', false);
    }

    public function test_receipt_shows_each_inventory_sale_item_on_its_own_line(): void
    {
        $category = new InventoryCategory(['name' => 'Stationary']);

        $pencil = new InventoryItem([
            'name' => 'Pencil',
            'quantity' => 2,
            'unit_price' => 30,
            'subtotal' => 60,
        ]);
        $pencil->setRelation('category', $category);

        $notebook = new InventoryItem([
            'name' => 'Notebook',
            'quantity' => 1,
            'unit_price' => 150,
            'subtotal' => 150,
        ]);
        $notebook->setRelation('category', $category);

        $sale = new InventorySale([
            'total_amount' => 210,
        ]);
        $saleItems = collect([
            tap(new InventorySaleItem(['quantity' => 2, 'subtotal' => 60]), function ($saleItem) use ($pencil) {
                $saleItem->setRelation('inventoryItem', $pencil);
            }),
            tap(new InventorySaleItem(['quantity' => 1, 'subtotal' => 150]), function ($saleItem) use ($notebook) {
                $saleItem->setRelation('inventoryItem', $notebook);
            }),
        ]);
        $sale->setRelation('items', $saleItems);

        $payment = new Payment([
            'amount' => 210,
            'gross_amount' => 210,
            'payment_date' => now(),
            'payment_method' => 'Cash',
            'receipt_no' => 'R-TEST-0002',
        ]);
        $payment->setRelation('inventorySale', $sale);
        $payment->setRelation('inventoryDueItems', collect());
        $payment->setRelation('items', collect());
        $payment->setRelation('student', null);
        $payment->setRelation('collector', null);

        $html = view('pages.payments.receipt-body', [
            'payment' => $payment,
            'setting' => null,
            'receiptSummary' => [],
            'inventorySaleItems' => $saleItems,
        ])->render();

        $this->assertStringContainsString('Items Sold', $html);
        $this->assertStringContainsString('Stationary', $html);
        $this->assertStringContainsString('Pencil', $html);
        $this->assertStringContainsString('Notebook', $html);
        $this->assertStringContainsString('2', $html);
        $this->assertStringContainsString('1', $html);
        $this->assertStringContainsString('BDT 60.00', $html);
        $this->assertStringContainsString('BDT 150.00', $html);
        $this->assertStringNotContainsString('Stationary — Pencil, Notebook', $html);
    }
}
