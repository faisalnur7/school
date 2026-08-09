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
use App\Models\PaymentItem;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPaymentReportSelectedGrandTotalTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_report_shows_selected_fee_total_only(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '2026',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 1',
            'name_bn' => 'Class 1',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $student = Student::create([
            'student_cid' => 'STU-001',
            'full_name_en' => 'Selected Total Student',
            'full_name_bn' => 'Selected Total Student',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $tuition = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'Tuition Fee',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $admission = FeeCategory::create([
            'name' => 'Admission Fee',
            'bn_name' => 'Admission Fee',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $feeSet = FeeSet::create([
            'name' => 'Term Fees',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'frequency' => 'monthly',
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $tuition->id,
            'amount' => 2000,
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $admission->id,
            'amount' => 500,
        ]);

        $fee = Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 2500,
            'paid_amount' => 0,
            'due_date' => '2026-08-09',
            'status' => 'pending',
            'is_active' => 1,
        ]);

        $payment = Payment::create([
            'student_id' => $student->id,
            'amount' => 2500,
            'receipt_no' => 'R-001',
            'payment_date' => '2026-08-08',
            'payment_method' => 'Cash',
            'collected_by' => $user->id,
            'status' => 'completed',
        ]);

        PaymentItem::create([
            'payment_id' => $payment->id,
            'fee_id' => $fee->id,
            'amount' => 2500,
        ]);

        $tuitionColumnKey = 'category_' . substr(md5(strtolower(trim($tuition->name))), 0, 12);

        $response = $this->actingAs($user)->get(route('fees.payment-report', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'from_date' => '2026-08-08',
            'to_date' => '2026-08-09',
            'columns_present' => 1,
            'columns' => [$tuitionColumnKey],
        ]));

        $response->assertOk();
        $response->assertSee('Selected Fee Total');
        $response->assertSee('2,000.00');
        $response->assertDontSee('Grand Total Paid');
    }

    public function test_payment_report_filters_rows_by_class_and_section(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '2026',
            'status' => 1,
        ]);

        $classFive = SchoolClass::create([
            'name_en' => 'Class Five',
            'name_bn' => 'Class Five',
            'status' => 1,
        ]);

        $classSix = SchoolClass::create([
            'name_en' => 'Class Six',
            'name_bn' => 'Class Six',
            'status' => 1,
        ]);

        $sectionA = Section::create([
            'school_class_id' => $classFive->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $sectionB = Section::create([
            'school_class_id' => $classSix->id,
            'name_en' => 'B',
            'name_bn' => 'B',
            'status' => 1,
        ]);

        $classFiveStudent = Student::create([
            'student_cid' => 'STU-101',
            'full_name_en' => 'Class Five Student',
            'full_name_bn' => 'Class Five Student',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $classFiveStudent->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $classFive->id,
            'section_id' => $sectionA->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $classSixStudent = Student::create([
            'student_cid' => 'STU-202',
            'full_name_en' => 'Class Six Student',
            'full_name_bn' => 'Class Six Student',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $classSixStudent->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $classSix->id,
            'section_id' => $sectionB->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $feeCategory = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'Tuition Fee',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $feeSetFive = FeeSet::create([
            'name' => 'Class Five Fees',
            'academic_session_id' => $session->id,
            'school_class_id' => $classFive->id,
            'frequency' => 'monthly',
        ]);

        $feeSetSix = FeeSet::create([
            'name' => 'Class Six Fees',
            'academic_session_id' => $session->id,
            'school_class_id' => $classSix->id,
            'frequency' => 'monthly',
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSetFive->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 1000,
        ]);

        FeeSetItem::create([
            'fee_set_id' => $feeSetSix->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 1500,
        ]);

        $feeFive = Fee::create([
            'student_id' => $classFiveStudent->id,
            'fee_set_id' => $feeSetFive->id,
            'amount' => 1000,
            'paid_amount' => 0,
            'due_date' => '2026-08-09',
            'status' => 'pending',
            'is_active' => 1,
        ]);

        $feeSix = Fee::create([
            'student_id' => $classSixStudent->id,
            'fee_set_id' => $feeSetSix->id,
            'amount' => 1500,
            'paid_amount' => 0,
            'due_date' => '2026-08-09',
            'status' => 'pending',
            'is_active' => 1,
        ]);

        $paymentFive = Payment::create([
            'student_id' => $classFiveStudent->id,
            'amount' => 1000,
            'receipt_no' => 'R-101',
            'payment_date' => '2026-08-08',
            'payment_method' => 'Cash',
            'collected_by' => $user->id,
            'status' => 'completed',
        ]);

        PaymentItem::create([
            'payment_id' => $paymentFive->id,
            'fee_id' => $feeFive->id,
            'amount' => 1000,
        ]);

        $paymentSix = Payment::create([
            'student_id' => $classSixStudent->id,
            'amount' => 1500,
            'receipt_no' => 'R-202',
            'payment_date' => '2026-08-08',
            'payment_method' => 'Cash',
            'collected_by' => $user->id,
            'status' => 'completed',
        ]);

        PaymentItem::create([
            'payment_id' => $paymentSix->id,
            'fee_id' => $feeSix->id,
            'amount' => 1500,
        ]);

        $response = $this->actingAs($user)->get(route('fees.payment-report', [
            'session_id' => $session->id,
            'class_id' => $classFive->id,
            'section_id' => $sectionA->id,
            'from_date' => '2026-08-01',
            'to_date' => '2026-08-10',
            'columns_present' => 1,
            'columns' => ['category_' . substr(md5(strtolower(trim($feeCategory->name))), 0, 12)],
        ]));

        $response->assertOk();
        $response->assertSee('Class Five Student');
        $response->assertSee('Class: Class Five');
    }

    public function test_payment_report_filters_inventory_rows_by_class_and_section(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '2026',
            'status' => 1,
        ]);

        $classFive = SchoolClass::create([
            'name_en' => 'Class Five',
            'name_bn' => 'Class Five',
            'status' => 1,
        ]);

        $classSix = SchoolClass::create([
            'name_en' => 'Class Six',
            'name_bn' => 'Class Six',
            'status' => 1,
        ]);

        $sectionA = Section::create([
            'school_class_id' => $classFive->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $sectionB = Section::create([
            'school_class_id' => $classSix->id,
            'name_en' => 'B',
            'name_bn' => 'B',
            'status' => 1,
        ]);

        $classFiveStudent = Student::create([
            'student_cid' => 'STU-501',
            'full_name_en' => 'Class Five Inventory Student',
            'full_name_bn' => 'Class Five Inventory Student',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $classFiveStudent->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $classFive->id,
            'section_id' => $sectionA->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $classSixStudent = Student::create([
            'student_cid' => 'STU-601',
            'full_name_en' => 'Class Six Inventory Student',
            'full_name_bn' => 'Class Six Inventory Student',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $classSixStudent->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $classSix->id,
            'section_id' => $sectionB->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $inventoryCategory = InventoryCategory::create([
            'name' => 'Books',
            'is_active' => 1,
        ]);

        $inventoryItem = InventoryItem::create([
            'category_id' => $inventoryCategory->id,
            'name' => 'Science Book',
            'current_stock' => 10,
            'selling_price' => 300,
            'is_active' => 1,
        ]);

        $inventoryPayment = Payment::create([
            'student_id' => $classSixStudent->id,
            'amount' => 300,
            'receipt_no' => 'INV-001',
            'payment_date' => '2026-08-08',
            'payment_method' => 'Cash',
            'collected_by' => $user->id,
            'status' => 'completed',
        ]);

        $inventorySale = InventorySale::create([
            'payment_id' => $inventoryPayment->id,
            'student_id' => $classSixStudent->id,
            'total_amount' => 300,
            'created_by' => $user->id,
        ]);

        InventorySaleItem::create([
            'inventory_sale_id' => $inventorySale->id,
            'inventory_item_id' => $inventoryItem->id,
            'quantity' => 1,
            'unit_price' => 300,
            'subtotal' => 300,
            'paid_amount' => 300,
        ]);

        $inventoryPayment->update(['inventory_sale_id' => $inventorySale->id]);

        $response = $this->actingAs($user)->get(route('fees.payment-report', [
            'session_id' => $session->id,
            'class_id' => $classFive->id,
            'section_id' => $sectionA->id,
            'from_date' => '2026-08-01',
            'to_date' => '2026-08-10',
            'columns_present' => 1,
            'columns' => ['category_' . substr(md5(strtolower(trim($inventoryCategory->name))), 0, 12)],
        ]));

        $response->assertOk();
        $response->assertDontSee('Class Six Inventory Student');
        $response->assertDontSee('Class: Class Six');
    }
}
