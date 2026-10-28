<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutorial_report_settings', function (Blueprint $table) {
            $table->foreignId('school_class_id')
                ->nullable()
                ->after('id')
                ->constrained('school_classes')
                ->nullOnDelete();
            $table->index('school_class_id');
        });
    }

    public function down(): void
    {
        Schema::table('tutorial_report_settings', function (Blueprint $table) {
            $table->dropForeign(['school_class_id']);
            $table->dropIndex(['school_class_id']);
            $table->dropColumn('school_class_id');
        });
    }
};
