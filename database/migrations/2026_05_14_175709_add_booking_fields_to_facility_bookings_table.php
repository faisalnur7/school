<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('facility_bookings')) {
            return;
        }

        Schema::table('facility_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('facility_bookings', 'title')) {
                $table->string('title')->after('id');
            }
            if (!Schema::hasColumn('facility_bookings', 'facility_name')) {
                $table->string('facility_name')->after('title');
            }
            if (!Schema::hasColumn('facility_bookings', 'booking_date')) {
                $table->date('booking_date')->after('facility_name');
            }
            if (!Schema::hasColumn('facility_bookings', 'start_time')) {
                $table->time('start_time')->nullable()->after('booking_date');
            }
            if (!Schema::hasColumn('facility_bookings', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('facility_bookings', 'booked_by')) {
                $table->string('booked_by')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('facility_bookings', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0)->after('booked_by');
            }
            if (!Schema::hasColumn('facility_bookings', 'payment_method')) {
                $table->string('payment_method')->default('Cash')->after('amount');
            }
            if (!Schema::hasColumn('facility_bookings', 'account_type')) {
                $table->string('account_type')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('facility_bookings', 'account_id')) {
                $table->unsignedBigInteger('account_id')->nullable()->after('account_type');
            }
            if (!Schema::hasColumn('facility_bookings', 'status')) {
                $table->string('status')->default('confirmed')->after('account_id');
            }
            if (!Schema::hasColumn('facility_bookings', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('status');
            }
            if (!Schema::hasColumn('facility_bookings', 'notes')) {
                $table->text('notes')->nullable()->after('reference_no');
            }
            if (!Schema::hasColumn('facility_bookings', 'recorded_by')) {
                $table->unsignedBigInteger('recorded_by')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('facility_bookings')) {
            return;
        }

        $columns = [
            'title',
            'facility_name',
            'booking_date',
            'start_time',
            'end_time',
            'booked_by',
            'amount',
            'payment_method',
            'account_type',
            'account_id',
            'status',
            'reference_no',
            'notes',
            'recorded_by',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('facility_bookings', $column)) {
                Schema::table('facility_bookings', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
