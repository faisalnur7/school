<?php

namespace Database\Seeders;

use App\Models\MobileBankingAccount;
use Illuminate\Database\Seeder;

class MobileBankingAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MobileBankingAccount::updateOrCreate(
            ['account_number' => '01700000000'],
            [
                'provider' => 'bKash',
                'account_name' => 'School bKash',
                'account_type' => 'Agent',
                'opening_balance' => 0,
                'opening_date' => now()->toDateString(),
                'is_active' => true,
                'notes' => 'Default bKash account created by seeder.',
            ]
        );
    }
}
