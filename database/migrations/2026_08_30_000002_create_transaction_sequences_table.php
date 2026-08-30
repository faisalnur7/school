<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_sequences', function (Blueprint $table) {
            $table->date('sequence_date')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        // Include soft-deleted transactions so their references are never reused.
        $lastNumbers = [];
        DB::table('transactions')
            ->select(['id', 'reference_no'])
            ->whereNotNull('reference_no')
            ->orderBy('id')
            ->chunk(1000, function ($transactions) use (&$lastNumbers): void {
                foreach ($transactions as $transaction) {
                    if (!preg_match('/^TXN-(\d{8})-(\d+)$/', $transaction->reference_no, $matches)) {
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
            DB::table('transaction_sequences')->insert([
                'sequence_date' => $date,
                'last_number' => $lastNumber,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_sequences');
    }
};
