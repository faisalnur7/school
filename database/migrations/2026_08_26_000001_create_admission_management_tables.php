<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admission_exams')) {
            Schema::create('admission_exams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('academic_session_id')->constrained('academic_sessions');
                $table->date('exam_date');
                $table->string('venue')->nullable();
                $table->string('reporting_time')->nullable();
                $table->text('instructions')->nullable();
                $table->boolean('status')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['status', 'academic_session_id']);
            });
        }

        if (! Schema::hasTable('admission_exam_class_settings')) {
            Schema::create('admission_exam_class_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('admission_exam_id')->constrained()->cascadeOnDelete();
                $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
                $table->decimal('pass_mark', 8, 2)->default(0);
                $table->timestamps();
                $table->unique(['admission_exam_id', 'school_class_id'], 'admission_exam_class_unique');
            });
        }

        if (! Schema::hasColumn('admission_applications', 'admission_exam_id')) {
            Schema::table('admission_applications', function (Blueprint $table) {
                $table->foreignId('admission_exam_id')->nullable()->after('id')->constrained('admission_exams')->nullOnDelete();
                $table->string('application_number')->nullable()->after('application_no');
                $table->foreignId('school_class_id')->nullable()->after('class_id')->constrained('school_classes')->nullOnDelete();
                $table->json('applicant_data')->nullable()->after('school_class_id');
                $table->enum('payment_status', ['unpaid', 'pending_verification', 'paid', 'rejected', 'refunded'])->default('unpaid');
                $table->enum('application_status', ['submitted', 'confirmed', 'rejected'])->default('submitted');
                $table->enum('result_status', ['not_entered', 'passed', 'failed'])->default('not_entered');
                $table->enum('review_status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->enum('conversion_status', ['not_converted', 'converted'])->default('not_converted');
                $table->decimal('total_marks', 8, 2)->nullable();
                $table->decimal('pass_mark_snapshot', 8, 2)->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('converted_student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->text('admin_notes')->nullable();
                $table->index(['admission_exam_id', 'school_class_id', 'result_status']);
            });
        }

        $this->createPaymentTable();
        $this->createAdmitCardTable();
        $this->createReviewTable();
        $this->createConversionTable();
    }

    private function createPaymentTable(): void
    {
        if (Schema::hasTable('admission_payments')) return;
        Schema::create('admission_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_application_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'paid', 'pending_verification', 'rejected', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->index(['status', 'payment_method']);
        });
    }

    private function createAdmitCardTable(): void
    {
        if (Schema::hasTable('admission_admit_cards')) return;
        Schema::create('admission_admit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_application_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('admit_card_number')->unique();
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('printed_at')->nullable();
            $table->enum('attendance_status', ['not_marked', 'present', 'absent'])->default('not_marked');
            $table->timestamps();
        });
    }

    private function createReviewTable(): void
    {
        if (Schema::hasTable('admission_application_reviews')) return;
        Schema::create('admission_application_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_application_id')->constrained()->cascadeOnDelete();
            $table->enum('decision', ['pending', 'approved', 'rejected']);
            $table->text('notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    private function createConversionTable(): void
    {
        if (Schema::hasTable('admission_conversions')) return;
        Schema::create('admission_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_application_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions');
            $table->foreignId('school_class_id')->constrained('school_classes');
            $table->string('roll');
            $table->foreignId('converted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_conversions');
        Schema::dropIfExists('admission_application_reviews');
        Schema::dropIfExists('admission_admit_cards');
        Schema::dropIfExists('admission_payments');
        if (Schema::hasColumn('admission_applications', 'admission_exam_id')) {
            Schema::table('admission_applications', function (Blueprint $table) {
                $table->dropForeign(['admission_exam_id']);
                $table->dropForeign(['school_class_id']);
                $table->dropForeign(['approved_by']);
                $table->dropForeign(['converted_student_id']);
                $table->dropColumn(['admission_exam_id', 'application_number', 'school_class_id', 'applicant_data', 'payment_status', 'application_status', 'result_status', 'review_status', 'conversion_status', 'total_marks', 'pass_mark_snapshot', 'approved_by', 'approved_at', 'converted_student_id', 'admin_notes']);
            });
        }
        Schema::dropIfExists('admission_exam_class_settings');
        Schema::dropIfExists('admission_exams');
    }
};
