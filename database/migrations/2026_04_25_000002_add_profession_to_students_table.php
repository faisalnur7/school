<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('fathers_profession_id')->nullable()->after('father_occupation')->constrained('professions')->nullOnDelete();
            $table->foreignId('mothers_profession_id')->nullable()->after('mother_occupation')->constrained('professions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['fathers_profession_id']);
            $table->dropForeign(['mothers_profession_id']);
            $table->dropColumn(['fathers_profession_id', 'mothers_profession_id']);
        });
    }
};
