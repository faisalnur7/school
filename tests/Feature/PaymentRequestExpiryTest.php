<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\PaymentRequest;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use App\Services\PaymentRequestService;
use Tests\TestCase;

class PaymentRequestExpiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_cleanup_deletes_only_expired_pending_requests(): void
    {
        Carbon::setTestNow('2026-10-30 10:00:00');
        $context = $this->createStudentContext('EXPIRY-001');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 100,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);
        $expired = app(PaymentRequestService::class)->createForStudent($context['student'], ['fees' => [['fee_id' => $fee->id]]]);
        $expired->update(['expires_at' => now()->subSecond()]);
        $completed = PaymentRequest::create([
            'student_id' => $context['student']->id,
            'status' => PaymentRequest::STATUS_COMPLETED,
            'total_amount' => 100,
            'expires_at' => now()->subDay(),
            'completed_at' => now()->subHour(),
        ]);

        Artisan::call('payment-requests:remove-expired');

        $this->assertDatabaseMissing('payment_requests', ['id' => $expired->id]);
        $this->assertDatabaseHas('payment_requests', ['id' => $completed->id, 'status' => PaymentRequest::STATUS_COMPLETED]);
    }

    private function createStudentContext(string $cid): array
    {
        $session = AcademicSession::create(['name_bn' => '2026', 'name_en' => '2026', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'Class 1', 'name_en' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $student = Student::create(['student_cid' => $cid, 'full_name_en' => 'Expiry Student', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'is_current' => true,
            'academic_status' => 'active',
        ]);
        $feeSet = FeeSet::create(['name' => 'Monthly Fee', 'academic_session_id' => $session->id]);

        return compact('session', 'class', 'section', 'student', 'feeSet');
    }
}
