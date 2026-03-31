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
                'opening_balance' => 50000.00,
                'opening_date' => now()->toDateString(),
                'is_active' => true,
                'notes' => 'Default bKash account created by seeder.',
            ]
        );

        MobileBankingAccount::updateOrCreate(
            ['account_number' => '01800000000'],
            [
                'provider' => 'Nagad',
                'account_name' => 'School Nagad',
                'account_type' => 'Merchant',
                'opening_balance' => 40000.00,
                'opening_date' => now()->toDateString(),
                'is_active' => true,
                'notes' => 'Default Nagad account created by seeder.',
            ]
        );
    }
}
