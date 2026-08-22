<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('free_studentships', function (Blueprint $table) {
            $table->foreignId('discount_category_id')
                ->nullable()
                ->after('fee_category_id')
                ->constrained('discount_categories')
                ->nullOnDelete();
            $table->string('attachment')->nullable()->after('discount_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('free_studentships', function (Blueprint $table) {
            $table->dropForeign(['discount_category_id']);
            $table->dropColumn(['discount_category_id', 'attachment']);
        });
    }
};
