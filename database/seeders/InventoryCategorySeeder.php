<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use Illuminate\Database\Seeder;

class InventoryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Books', 'Stationary', 'Utilities'];

        foreach ($categories as $name) {
            InventoryCategory::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
