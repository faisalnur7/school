<?php

namespace Tests\Feature;

use App\Models\InventorySale;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentShowInventorySaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_detail_handles_inventory_sale_payment_dates(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $student = Student::create(['full_name_en' => 'Inventory Sale Student']);
        $payment = Payment::create([
            'student_id' => $student->id,
            'amount' => 125,
            'payment_date' => '2026-10-11',
            'receipt_no' => 'INV-127',
        ]);
        InventorySale::create([
            'payment_id' => $payment->id,
            'student_id' => $student->id,
            'total_amount' => 125,
        ]);

        $this->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('INV-127')
            ->assertSee('11 Oct, 2026');
    }
}
