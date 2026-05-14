<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('facility_name')->after('title');
            $table->date('booking_date')->after('facility_name');
            $table->time('start_time')->nullable()->after('booking_date');
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('booked_by')->nullable()->after('end_time');
            $table->decimal('amount', 12, 2)->default(0)->after('booked_by');
            $table->string('payment_method')->default('Cash')->after('amount');
            $table->string('account_type')->nullable()->after('payment_method');
            $table->unsignedBigInteger('account_id')->nullable()->after('account_type');
            $table->string('status')->default('confirmed')->after('account_id');
            $table->string('reference_no')->nullable()->after('status');
            $table->text('notes')->nullable()->after('reference_no');
            $table->unsignedBigInteger('recorded_by')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('facility_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'facility_name', 'booking_date', 'start_time', 'end_time',
                'booked_by', 'amount', 'payment_method', 'account_type', 'account_id',
                'status', 'reference_no', 'notes', 'recorded_by',
            ]);
        });
    }
};
