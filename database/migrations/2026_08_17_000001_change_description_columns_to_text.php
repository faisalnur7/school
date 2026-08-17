<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE `transactions` MODIFY `description` TEXT NULL');
        DB::statement('ALTER TABLE `incomes` MODIFY `description` TEXT NULL');
        DB::statement('ALTER TABLE `expenses` MODIFY `description` TEXT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE `transactions` MODIFY `description` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `incomes` MODIFY `description` VARCHAR(255) NULL');
        DB::statement('ALTER TABLE `expenses` MODIFY `description` VARCHAR(255) NULL');
    }
};
