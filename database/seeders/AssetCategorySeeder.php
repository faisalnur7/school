<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
                        ['id' => 1, 'name' => 'IT Equipment',        'description' => 'Computers, networking devices, and related IT hardware.', 'is_active' => true],
                        ['id' => 2, 'name' => 'Electronics',         'description' => 'Electronic display and audio-visual equipment.', 'is_active' => true],
                        ['id' => 3, 'name' => 'Furniture',           'description' => 'Desks, chairs, benches, and storage furniture.', 'is_active' => true],
                        ['id' => 4, 'name' => 'Classroom Equipment', 'description' => 'Boards, smart panels, and classroom teaching aids.', 'is_active' => true],
                        ['id' => 5, 'name' => 'Laboratory Equipment','description' => 'Science lab instruments, kits, and experimental tools.', 'is_active' => true],
                        ['id' => 6, 'name' => 'Library Assets',      'description' => 'Books, bookshelves, and library reading furniture.', 'is_active' => true],
                        ['id' => 7, 'name' => 'Power Equipment',     'description' => 'Generators, UPS units, and backup power systems.', 'is_active' => true],
                        ['id' => 8, 'name' => 'Electrical',          'description' => 'Fans, lights, switchboards, and electrical fittings.', 'is_active' => true],
                        ['id' => 9, 'name' => 'Utility',             'description' => 'Water filters, cleaning machines, and general utility items.', 'is_active' => true],
                        ['id' => 10,'name' => 'Security Equipment',  'description' => 'CCTV cameras, alarm systems, and fire safety equipment.', 'is_active' => true],
                        ['id' => 11,'name' => 'Maintenance Tools',   'description' => 'Toolboxes, ladders, drills, and general maintenance equipment.', 'is_active' => true],
                    ];

        foreach ($categories as $category) {
            AssetCategory::updateOrCreate(
                ['id' => $category['id']],
                $category
            );
        }

        $this->command?->info('Asset categories seeded: ' . count($categories));
    }
}
