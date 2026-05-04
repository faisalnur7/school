<?php

namespace Database\Seeders;

use App\Models\HandCash;
use Illuminate\Database\Seeder;

class HandCashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HandCash::updateOrCreate(
            ['label' => 'Petty Cash'],
            [
                'opening_amount' => 10000.00,
                'opening_date' => now()->toDateString(),
                'is_active' => true,
                'notes' => 'Default hand cash account created by seeder.',
            ]
        );
    }
}
