<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\Transport;
use App\Models\User;
use App\Http\Middleware\CheckPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportEditPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_page_shows_single_student_form(): void
    {
        $user = User::create([
            'name' => 'Transport Admin',
            'email' => 'transport@example.com',
            'password' => bcrypt('password'),
        ]);

        [$transport, $session, $class, $section] = $this->seedTransportRecord();

        $response = $this->withoutMiddleware(CheckPermission::class)
            ->actingAs($user)
            ->get(route('transports.edit', $transport->id));

        $response->assertOk();
        $response->assertSee('Edit Transport Fee');
        $response->assertSee('Transport Student');
        $response->assertSee('TRN-001');
        $response->assertSee('Update Transport Fee');
        $response->assertSee('value="300.00"', false);
        $response->assertSee('Class 1');
        $response->assertSee('A');
    }

    public function test_update_page_updates_transport_and_fee_rows(): void
    {
        $user = User::create([
            'name' => 'Transport Admin',
            'email' => 'transport@example.com',
            'password' => bcrypt('password'),
        ]);

        [$transport, $session, $class, $section, $feeSet, $feeCategory] = $this->seedTransportRecord(withBilling: true);

        $response = $this->withoutMiddleware(CheckPermission::class)
            ->actingAs($user)
            ->put(route('transports.update', $transport->id), [
                'amount' => 450,
                'status' => Transport::STATUS_INACTIVE,
                'remarks' => 'Updated transport amount',
            ]);

        $response->assertRedirect(route('transports.index'));

        $this->assertDatabaseHas('transports', [
            'id' => $transport->id,
            'amount' => 450,
            'status' => Transport::STATUS_INACTIVE,
            'remarks' => 'Updated transport amount',
        ]);

        $this->assertDatabaseHas('fee_set_items', [
            'fee_set_id' => $feeSet->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 450,
        ]);

        $this->assertDatabaseHas('fees', [
            'student_id' => $transport->student_id,
            'fee_set_id' => $feeSet->id,
            'status' => 'pending',
            'amount' => 450,
            'is_active' => 0,
        ]);

        $this->assertDatabaseHas('fees', [
            'student_id' => $transport->student_id,
            'fee_set_id' => $feeSet->id,
            'status' => 'paid',
            'amount' => 300,
        ]);
    }

    private function seedTransportRecord(bool $withBilling = false): array
    {
        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status'  => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 1',
            'name_bn' => 'Class 1',
            'status'  => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
        ]);

        $student = Student::create([
            'full_name_en' => 'Transport Student',
            'student_cid'   => 'TRN-001',
            'status'        => 1,
        ]);

        $academicInfo = StudentAcademicInformation::create([
            'student_id'          => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id'     => $class->id,
            'section_id'          => $section->id,
            'roll'                => '1',
            'academic_status'     => 'active',
            'is_current'          => true,
        ]);

        $feeCategory = FeeCategory::create([
            'name_en' => 'Transport Fee',
            'status' => 1,
            'is_transport' => 1,
        ]);

        $transport = Transport::create([
            'student_id' => $student->id,
            'student_academic_information_id' => $academicInfo->id,
            'academic_session_id' => $session->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 300,
            'status' => Transport::STATUS_ACTIVE,
            'remarks' => 'Initial transport amount',
        ]);

        if ($withBilling) {
            $feeSet = FeeSet::create([
                'name' => 'Transport Fee - 2026',
                'bn_name' => 'পরিবহন ফি - ২০২৬',
                'academic_session_id' => $session->id,
                'school_class_id' => $class->id,
                'group_id' => null,
                'frequency' => 'monthly',
            ]);

            FeeSetItem::create([
                'fee_set_id' => $feeSet->id,
                'fee_category_id' => $feeCategory->id,
                'amount' => 300,
            ]);

            Fee::create([
                'student_id' => $student->id,
                'fee_set_id' => $feeSet->id,
                'amount' => 300,
                'paid_amount' => 0,
                'due_date' => now()->endOfMonth()->toDateString(),
                'status' => 'pending',
                'is_active' => 1,
            ]);

            Fee::create([
                'student_id' => $student->id,
                'fee_set_id' => $feeSet->id,
                'amount' => 300,
                'paid_amount' => 300,
                'due_date' => now()->endOfMonth()->toDateString(),
                'status' => 'paid',
                'is_active' => 1,
            ]);

            return [$transport, $session, $class, $section, $feeSet, $feeCategory];
        }

        return [$transport, $session, $class, $section];
    }
}
