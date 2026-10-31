<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE exams MODIFY status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE exams SET status = 'draft' WHERE status = 'archived'");
            DB::statement("ALTER TABLE exams MODIFY status ENUM('draft', 'published') NOT NULL DEFAULT 'draft'");
        }
    }
};
