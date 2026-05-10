<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $isUserExists = User::where('email', 'test@example.com')->exists();
        if(!$isUserExists){
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Make admin user super admin
        $adminUser = User::where('email', 'admin@abc.com')->first();
        if ($adminUser) {
            $adminUser->update(['is_super_admin' => true]);
        }

        $this->call([
            AcademicSessionSeeder::class,
            SchoolClassSeeder::class,
            FeeCategorySeeder::class,
            IncomeCategorySeeder::class,
            ExpenseCategorySeeder::class,
            HandCashSeeder::class,
            BankAccountSeeder::class,
            MobileBankingAccountSeeder::class,
            AccountingSeeder::class,
            DesignationSeeder::class,
            HrSeeder::class,
            DepartmentSeeder::class,
            BuildingSeeder::class,
            RoomSeeder::class,
            BudgetAllocationSeeder::class,
            ProfessionSeeder::class,
            AssetCategorySeeder::class,
            AssetSeeder::class,
            SubjectSeeder::class,
            StudentSeeder::class,
            TeacherSectionAssignmentSeeder::class,
        ]);
    }
}
