<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('incomes')
            ->whereNull('payment_id')
            ->whereNotNull('reference_no')
            ->orderBy('id')
            ->chunkById(500, function ($incomes): void {
                foreach ($incomes as $income) {
                    $paymentId = DB::table('transactions')
                        ->where('reference_no', $income->reference_no)
                        ->where('transactionable_type', 'App\\Models\\Payment')
                        ->value('transactionable_id');

                    if ($paymentId) {
                        DB::table('incomes')
                            ->where('id', $income->id)
                            ->update(['payment_id' => $paymentId]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Backfilled ownership is intentionally retained if this migration is rolled back.
    }
};
