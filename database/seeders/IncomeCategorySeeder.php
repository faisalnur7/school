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
            ['name' => 'Gift & Donation', 'slug' => 'donation'],
            ['name' => 'Canteen Income',  'slug' => 'canteen-income'],
            ['name' => 'Stationery',      'slug' => 'stationery'],
            ['name' => 'Books',           'slug' => 'books'],
            ['name' => 'School Bag',      'slug' => 'school-bag'],
            ['name' => 'Student Uniform', 'slug' => 'student-uniform'],
            ['name' => 'Admission Form',  'slug' => 'admission-form'],
            ['name' => 'Sports Dress',    'slug' => 'sports-dress'],
        ];

        foreach ($categories as $category) {
            IncomeCategory::firstOrCreate(['slug' => $category['slug']], [
                'name' => $category['name'],
            ]);
        }
    }
}
