<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facility_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('facility_name');
            $table->date('booking_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('booked_by')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('payment_method')->default('Cash');
            $table->string('account_type')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('status')->default('confirmed'); // pending, confirmed, cancelled
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_bookings');
    }
};
