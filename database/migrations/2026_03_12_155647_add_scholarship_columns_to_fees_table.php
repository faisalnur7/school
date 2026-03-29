<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->unsignedBigInteger('scholarship_id')->nullable()->after('fee_set_id');
            $table->decimal('scholarship_discount', 10, 2)->default(0)->after('amount');
            
            $table->foreign('scholarship_id')->references('id')->on('scholarships')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropForeign(['scholarship_id']);
            $table->dropColumn(['scholarship_id', 'scholarship_discount']);
        });
    }
};
