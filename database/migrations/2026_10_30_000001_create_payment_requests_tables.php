<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->string('status', 20)->default('pending');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('completed_payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'expires_at']);
            $table->index(['student_id', 'status']);
            $table->index(['academic_session_id', 'school_class_id', 'section_id', 'status'], 'payment_requests_filter_idx');
        });

        Schema::create('payment_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_request_id')->constrained('payment_requests')->cascadeOnDelete();
            $table->string('item_type', 20);
            $table->foreignId('fee_id')->nullable()->constrained('fees')->nullOnDelete();
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('requested_amount', 12, 2)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->string('display_name');
            $table->string('display_reference')->nullable();
            $table->timestamps();

            $table->index(['payment_request_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_request_items');
        Schema::dropIfExists('payment_requests');
    }
};
