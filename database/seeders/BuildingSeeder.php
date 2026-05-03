<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = [
            ['name' => 'Academic Block A', 'code' => 'ABA', 'description' => 'Primary academic block for junior classes and teaching spaces.', 'is_active' => true],
            ['name' => 'Academic Block B', 'code' => 'ABB', 'description' => 'Secondary academic block for senior classes and staff rooms.', 'is_active' => true],
            ['name' => 'Administration Block', 'code' => 'ADMIN', 'description' => 'Administrative offices, accounts, and support rooms.', 'is_active' => true],
            ['name' => 'Library and Lab Block', 'code' => 'LLB', 'description' => 'Dedicated block for library, science labs, and specialist rooms.', 'is_active' => true],
        ];

        foreach ($buildings as $building) {
            Building::updateOrCreate(
                ['code' => $building['code']],
                $building
            );
        }

        $this->command?->info('Buildings seeded: ' . count($buildings));
    }
}
