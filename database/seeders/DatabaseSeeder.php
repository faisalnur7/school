<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $isUserExists = User::where('email', 'test@example.com')->exists();
        if(!$isUserExists){
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        $this->call([
            AcademicSessionSeeder::class,
            SchoolClassSeeder::class,
            FeeCategorySeeder::class,
            IncomeCategorySeeder::class,
            ExpenseCategorySeeder::class,
            HandCashSeeder::class,
            BankAccountSeeder::class,
            // MobileBankingAccountSeeder::class,
            // AccountingSeeder::class,
            DesignationSeeder::class,
            HrSeeder::class,
            SalaryStructureForAllEmployeesSeeder::class,
            ShareHolderSeeder::class,
            BudgetAllocationSeeder::class,
            ProfessionSeeder::class,
            AssetCategorySeeder::class,
            AssetSeeder::class,
            BudgetAllocationSeeder::class,
            ProfessionSeeder::class,
            StudentSeeder::class,
        ]);

        if (app()->environment(['local', 'development'])) {
            $this->call([
                DemoAccountingSeeder::class,
            ]);
        }
    }
}
