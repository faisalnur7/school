<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventorySale;
use App\Models\InventorySaleItem;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use App\Services\StudentPaymentLedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPaymentLedgerTest extends TestCase
{
    use RefreshDatabase;

    private function makeStudent(AcademicSession $session): Student
    {
        $class   = SchoolClass::create(['name_en' => 'Class 1', 'name_bn' => 'Class 1']);
        $section = Section::create(['name_en' => 'A', 'name_bn' => 'A', 'school_class_id' => $class->id]);
        $student = Student::create(['full_name_en' => 'Test Student', 'student_cid' => '000001', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id'          => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id'     => $class->id,
            'section_id'          => $section->id,
            'roll'                => 1,
        ]);
        return $student;
    }

    private function makeFeeSet(AcademicSession $session): FeeSet
    {
        return FeeSet::create([
            'name'                => 'Monthly Fee',
            'academic_session_id' => $session->id,
            'frequency'           => 'monthly',
        ]);
    }

    public function test_ledger_report_page_loads(): void
    {
        $user    = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '2026', 'status' => 1]);
        $student = $this->makeStudent($session);

        $this->actingAs($user)
            ->get(route('fees.student-ledger.show', ['student' => $student->id, 'session_id' => $session->id]))
            ->assertOk()
            ->assertViewIs('pages.student-ledger-report.show');
    }

    public function test_ledger_requires_session_id(): void
    {
        $user    = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '2026', 'status' => 1]);
        $student = $this->makeStudent($session);

        $this->actingAs($user)
            ->get(route('fees.student-ledger.show', ['student' => $student->id]))
            ->assertSessionHasErrors('session_id');
    }

    public function test_running_balance_calculation(): void
    {
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '2026', 'status' => 1]);
        $student = $this->makeStudent($session);
        $feeSet  = $this->makeFeeSet($session);

        // Create a fee invoice of 1000
        Fee::create([
            'student_id'           => $student->id,
            'fee_set_id'           => $feeSet->id,
            'amount'               => 1000,
            'scholarship_discount' => 0,
            'due_date'             => '2026-01-10',
            'is_active'            => 1,
        ]);

        // Create a payment of 600
        $payment = Payment::create([
            'student_id'   => $student->id,
            'amount'       => 600,
            'payment_date' => '2026-01-15',
        ]);
        \App\Models\PaymentItem::create(['payment_id' => $payment->id, 'fee_id' => Fee::first()->id, 'amount' => 600]);

        $service = new StudentPaymentLedgerService();
        $ledger  = $service->build($student->fresh(['academicInformations.schoolClass', 'academicInformations.section', 'academicInformations.group']), $session->id);

        $this->assertEquals(1000.0, $ledger['total_dues']);
        $this->assertEquals(600.0,  $ledger['total_received']);
        $this->assertEquals(400.0,  $ledger['closing_balance']); // still due
    }

    public function test_monthly_grouping(): void
    {
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '2026', 'status' => 1]);
        $student = $this->makeStudent($session);
        $feeSet  = $this->makeFeeSet($session);

        Fee::create(['student_id' => $student->id, 'fee_set_id' => $feeSet->id, 'amount' => 500, 'scholarship_discount' => 0, 'due_date' => '2026-01-10', 'is_active' => 1]);
        Fee::create(['student_id' => $student->id, 'fee_set_id' => $feeSet->id, 'amount' => 500, 'scholarship_discount' => 0, 'due_date' => '2026-02-10', 'is_active' => 1]);

        $service = new StudentPaymentLedgerService();
        $ledger  = $service->build($student->fresh(), $session->id);

        $this->assertCount(2, $ledger['months']);
        $this->assertEquals('Jan-2026', $ledger['months'][0]->label);
        $this->assertEquals('Feb-2026', $ledger['months'][1]->label);
    }

    public function test_inventory_included_in_ledger(): void
    {
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '2026', 'status' => 1]);
        $student = $this->makeStudent($session);

        $category = InventoryCategory::create(['name' => 'Books', 'is_active' => 1]);
        $item     = InventoryItem::create(['name' => 'Math Book', 'category_id' => $category->id, 'quantity' => 10, 'selling_price' => 200]);

        $payment = Payment::create([
            'student_id'   => $student->id,
            'amount'       => 200,
            'payment_date' => '2026-03-05',
        ]);

        $sale = InventorySale::create([
            'payment_id' => $payment->id,
            'student_id' => $student->id,
            'total_amount' => 200,
        ]);

        InventorySaleItem::create([
            'inventory_sale_id'  => $sale->id,
            'inventory_item_id'  => $item->id,
            'quantity'           => 1,
            'unit_price'         => 200,
            'subtotal'           => 200,
        ]);

        $payment->update(['inventory_sale_id' => $sale->id]);

        $service = new StudentPaymentLedgerService();
        $ledger  = $service->build($student->fresh(), $session->id);

        $invMonth = $ledger['months']->first(fn($m) => $m->label === 'Mar-2026');
        $this->assertNotNull($invMonth);
        $invRow = $invMonth->rows->first(fn($r) => $r['code'] === 'INV');
        $this->assertNotNull($invRow);
        $this->assertEquals(200.0, $invRow['dues']);
    }

    public function test_pdf_export_route_is_accessible(): void
    {
        $user    = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '2026', 'status' => 1]);
        $student = $this->makeStudent($session);

        // Verify the PDF view renders without errors (mPDF output is not capturable in test context)
        $ledger = (new StudentPaymentLedgerService())->build($student->fresh(), $session->id);
        $html = view('pages.student-ledger-report.pdf', array_merge($ledger, [
            'session' => $session,
            'school'  => new \App\Models\SchoolSetting(),
        ]))->render();

        $this->assertStringContainsString('Student Payment Ledger', $html);
        $this->assertStringContainsString($student->full_name_en, $html);
    }

    public function test_advance_balance_when_overpaid(): void
    {
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '2026', 'status' => 1]);
        $student = $this->makeStudent($session);
        $feeSet  = $this->makeFeeSet($session);

        Fee::create(['student_id' => $student->id, 'fee_set_id' => $feeSet->id, 'amount' => 500, 'scholarship_discount' => 0, 'due_date' => '2026-01-10', 'is_active' => 1]);

        $payment = Payment::create(['student_id' => $student->id, 'amount' => 700, 'payment_date' => '2026-01-15']);
        \App\Models\PaymentItem::create(['payment_id' => $payment->id, 'fee_id' => Fee::first()->id, 'amount' => 700]);

        $service = new StudentPaymentLedgerService();
        $ledger  = $service->build($student->fresh(), $session->id);

        $this->assertEquals(-200.0, $ledger['closing_balance']); // advance
    }
}
