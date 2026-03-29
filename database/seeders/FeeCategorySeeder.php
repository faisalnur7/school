<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use Illuminate\Database\Seeder;

class FeeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Admission Fee',     'bn_name' => 'ভর্তি ফি','is_transport' => '0'],
            ['name' => 'Tuition Fee',       'bn_name' => 'মাসিক বেতন','is_transport' => '0'],
            ['name' => 'Exam Fee',          'bn_name' => 'পরীক্ষার ফি','is_transport' => '0'],
            ['name' => 'Session Fee',       'bn_name' => 'সেশন ফি','is_transport' => '0'],
            ['name' => 'Lab Fee',           'bn_name' => 'ল্যাব ফি','is_transport' => '0'],
            ['name' => 'Library Fee',       'bn_name' => 'লাইব্রেরি ফি','is_transport' => '0'],
            ['name' => 'Transport Fee',     'bn_name' => 'পরিবহন ফি','is_transport' => '1'],
            ['name' => 'Development Fee',   'bn_name' => 'উন্নয়ন ফি','is_transport' => '0'],
            ['name' => 'Miscellaneous Fee', 'bn_name' => 'অন্যান্য ফি','is_transport' => '0'],
        ];

        foreach ($categories as $category) {
            FeeCategory::firstOrCreate(['name' => $category['name']], [
                'bn_name' => $category['bn_name'],
                'status'  => 1,
                'is_transport' => $category['is_transport'],
            ]);
        }
    }
}
