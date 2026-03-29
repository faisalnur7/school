<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Salary',           'slug' => 'salary'],
            ['name' => 'Utility Bill',     'slug' => 'utility-bill'],
            ['name' => 'Maintenance',      'slug' => 'maintenance'],
            ['name' => 'Transport Cost',   'slug' => 'transport-cost'],
            ['name' => 'Scholarship',      'slug' => 'scholarship'],
            ['name' => 'Office Expense',   'slug' => 'office-expense'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate(['slug' => $category['slug']], [
                'name' => $category['name'],
            ]);
        }
    }
}
