<?php

namespace Database\Seeders;

use App\Models\DiscountCategory;
use Illuminate\Database\Seeder;

class DiscountCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Teacher & Staff Child',
            'Application',
            'Special/Director discount',
        ] as $name) {
            DiscountCategory::firstOrCreate(['name' => $name]);
        }
    }
}
