<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Vendors: add missing columns ──────────────────────────
        Schema::table('vendors', function (Blueprint $table) {
            foreach (['name', 'contact_person', 'phone', 'email', 'address'] as $col) {
                if (!Schema::hasColumn('vendors', $col)) {
                    match ($col) {
                        'name'           => $table->string('name'),
                        'contact_person' => $table->string('contact_person')->nullable(),
                        'phone'          => $table->string('phone')->nullable(),
                        'email'          => $table->string('email')->nullable(),
                        'address'        => $table->text('address')->nullable(),
                    };
                }
            }
        });

        // ── Bills, bill_items, vendor_payments already exist ──────
        // (created by a prior migration — skip)

        // ── Audit fields on existing financial tables ─────────────
        foreach (['transactions', 'accounts'] as $tbl) {
            if (!Schema::hasTable($tbl)) continue;
            Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                if (!Schema::hasColumn($tbl, 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn($tbl, 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down(): void
    {
        // intentionally left empty — do not drop pre-existing tables
    }
};
