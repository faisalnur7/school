<?php

namespace Database\Seeders;

use App\Models\IncomeCategory;
use Illuminate\Database\Seeder;

class IncomeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Student Payment', 'slug' => 'student-payment'],
            ['name' => 'Admission Fee',   'slug' => 'admission-fee'],
            ['name' => 'Exam Fee',        'slug' => 'exam-fee'],
            ['name' => 'Transport Fee',   'slug' => 'transport-fee'],
            ['name' => 'Donation',        'slug' => 'donation'],
            ['name' => 'Canteen Income',  'slug' => 'canteen-income'],
        ];

        foreach ($categories as $category) {
            IncomeCategory::firstOrCreate(['slug' => $category['slug']], [
                'name' => $category['name'],
            ]);
        }
    }
}
