<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $orderedClasses = [
            'Play',
            'Nursery',
            'KG',
            'One',
            'Two',
            'Three',
            'Four',
            'Five',
            'Six',
            'Seven',
            'Eight',
            'Nine',
            'Ten',
            'Nazera',
            'Hifz',
        ];

        foreach ($orderedClasses as $index => $className) {
            DB::table('school_classes')
                ->where('name_en', $className)
                ->update(['order' => $index + 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('school_classes')->update(['order' => 0]);
    }
};
