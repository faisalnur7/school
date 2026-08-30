<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_sequences', function (Blueprint $table) {
            $table->date('sequence_date')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        // Continue existing receipt sequences instead of restarting at 0001.
        $lastNumbers = [];
        DB::table('payments')
            ->select(['id', 'receipt_no'])
            ->whereNotNull('receipt_no')
            ->orderBy('id')
            ->chunk(1000, function ($payments) use (&$lastNumbers): void {
                foreach ($payments as $payment) {
                    if (!preg_match('/^R-(\d{8})-(\d+)$/', $payment->receipt_no, $matches)) {
                        continue;
                    }

                    $date = substr($matches[1], 0, 4) . '-'
                        . substr($matches[1], 4, 2) . '-'
                        . substr($matches[1], 6, 2);
                    $number = (int) $matches[2];

                    $lastNumbers[$date] = max($lastNumbers[$date] ?? 0, $number);
                }
            });

        $timestamp = now();
        foreach ($lastNumbers as $date => $lastNumber) {
            DB::table('payment_sequences')->insert([
                'sequence_date' => $date,
                'last_number' => $lastNumber,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_sequences');
    }
};
