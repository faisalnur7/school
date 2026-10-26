<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('national_id', 50)->nullable()->after('phone');
            $table->string('passport_number', 50)->nullable()->after('national_id');
            $table->string('emergency_contact_name', 150)->nullable()->after('passport_number');
            $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relation', 50)->nullable()->after('emergency_contact_phone');
            $table->text('notes')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'national_id',
                'passport_number',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'notes',
            ]);
        });
    }
};
