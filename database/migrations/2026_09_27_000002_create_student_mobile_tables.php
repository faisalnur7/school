<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('fcm_token', 512);
            $table->string('platform', 20)->default('android');
            $table->string('app_version', 50)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['user_id', 'fcm_token']);
            $table->index(['student_id', 'is_active']);
        });

        Schema::create('mobile_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('student_devices')->nullOnDelete();
            $table->string('notification_type', 80);
            $table->string('reference_type', 120)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('unique_event_key', 191)->unique();
            $table->timestamp('sent_at')->nullable();
            $table->string('delivery_status', 30)->default('queued');
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->index(['student_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_notification_logs');
        Schema::dropIfExists('student_devices');
    }
};
