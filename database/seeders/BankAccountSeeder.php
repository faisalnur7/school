<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BankAccount::updateOrCreate(
            ['account_number' => '0123456789'],
            [
                'bank_name' => 'Example Bank',
                'account_name' => 'School Main Account',
                'branch_name' => 'Head Office',
                'routing_number' => '000111222',
                'opening_balance' => 250000.00,
                'opening_date' => now()->toDateString(),
                'is_active' => true,
                'notes' => 'Default bank account seeded for testing.',
            ]
        );

        BankAccount::updateOrCreate(
            ['account_number' => '0987654321'],
            [
                'bank_name' => 'Demo Bank',
                'account_name' => 'School Secondary Account',
                'branch_name' => 'City Branch',
                'routing_number' => '333444555',
                'opening_balance' => 150000.00,
                'opening_date' => now()->toDateString(),
                'is_active' => true,
                'notes' => 'Secondary bank account seeded for demo purposes.',
            ]
        );
    }
}
