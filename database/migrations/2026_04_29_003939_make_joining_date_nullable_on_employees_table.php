<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE employees MODIFY joining_date DATE NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('UPDATE employees SET joining_date = CURDATE() WHERE joining_date IS NULL');
        DB::statement('ALTER TABLE employees MODIFY joining_date DATE NOT NULL');
    }
};
