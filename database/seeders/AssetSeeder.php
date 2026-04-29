<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            ['id' => 1, 'asset_category_id' => 1, 'name' => 'Computer Lab Accessories', 'description' => 'Accessories used in computer lab operations.', 'quantity' => 15, 'purchase_price' => 8000.00, 'current_value' => 6400.00, 'status' => 'active'],
            ['id' => 2, 'asset_category_id' => 1, 'name' => 'Fingerprint Scanner', 'description' => 'Biometric attendance device.', 'quantity' => 3, 'purchase_price' => 12000.00, 'current_value' => 9600.00, 'status' => 'active'],
            ['id' => 3, 'asset_category_id' => 1, 'name' => 'School Financial Software', 'description' => 'Software used for managing school accounts.', 'quantity' => 1, 'purchase_price' => 50000.00, 'current_value' => 40000.00, 'status' => 'active'],
            ['id' => 4, 'asset_category_id' => 2, 'name' => 'Sound System & Accessories', 'description' => 'Audio system for events and announcements.', 'quantity' => 2, 'purchase_price' => 30000.00, 'current_value' => 24000.00, 'status' => 'active'],
            ['id' => 5, 'asset_category_id' => 3, 'name' => 'Furniture & Fixtures', 'description' => 'General furniture used across school.', 'quantity' => 50, 'purchase_price' => 5000.00, 'current_value' => 4500.00, 'status' => 'active'],
            ['id' => 6, 'asset_category_id' => 3, 'name' => 'Furniture & Fixtures (Hifz)', 'description' => 'Furniture used in Hifz section.', 'quantity' => 20, 'purchase_price' => 4500.00, 'current_value' => 4050.00, 'status' => 'active'],
            ['id' => 7, 'asset_category_id' => 3, 'name' => 'Bench & Others (Hifz)', 'description' => 'Benches and related items for Hifz.', 'quantity' => 30, 'purchase_price' => 2500.00, 'current_value' => 2250.00, 'status' => 'active'],
            ['id' => 8, 'asset_category_id' => 3, 'name' => 'School Bench', 'description' => 'Student seating benches.', 'quantity' => 100, 'purchase_price' => 2500.00, 'current_value' => 2250.00, 'status' => 'active'],
            ['id' => 9, 'asset_category_id' => 4, 'name' => 'School (Porda & Others)', 'description' => 'Curtains and classroom accessories.', 'quantity' => 25, 'purchase_price' => 1500.00, 'current_value' => 1200.00, 'status' => 'active'],
            ['id' => 10, 'asset_category_id' => 5, 'name' => 'Science Club Equipments', 'description' => 'Equipment used for science club activities.', 'quantity' => 10, 'purchase_price' => 10000.00, 'current_value' => 8000.00, 'status' => 'active'],
            ['id' => 11, 'asset_category_id' => 6, 'name' => 'Library Books', 'description' => 'Books available in school library.', 'quantity' => 500, 'purchase_price' => 500.00, 'current_value' => 450.00, 'status' => 'active'],
            ['id' => 12, 'asset_category_id' => 7, 'name' => 'IPS', 'description' => 'Backup power system for electricity.', 'quantity' => 2, 'purchase_price' => 40000.00, 'current_value' => 32000.00, 'status' => 'active'],
            ['id' => 13, 'asset_category_id' => 8, 'name' => 'Electric Installation', 'description' => 'Electrical wiring and installation.', 'quantity' => 1, 'purchase_price' => 100000.00, 'current_value' => 80000.00, 'status' => 'active'],
            ['id' => 14, 'asset_category_id' => 8, 'name' => 'Electrical Goods Fixed', 'description' => 'Fixed electrical fittings and components.', 'quantity' => 40, 'purchase_price' => 2000.00, 'current_value' => 1600.00, 'status' => 'active'],
            ['id' => 15, 'asset_category_id' => 8, 'name' => 'Electric Fan', 'description' => 'Ceiling and wall-mounted fans.', 'quantity' => 60, 'purchase_price' => 3500.00, 'current_value' => 2800.00, 'status' => 'active'],
            ['id' => 16, 'asset_category_id' => 9, 'name' => 'School Canteen', 'description' => 'Canteen-related equipment and setup.', 'quantity' => 1, 'purchase_price' => 150000.00, 'current_value' => 120000.00, 'status' => 'active'],
            ['id' => 17, 'asset_category_id' => 9, 'name' => 'Water Purifier', 'description' => 'Water filtration system.', 'quantity' => 3, 'purchase_price' => 18000.00, 'current_value' => 14400.00, 'status' => 'active'],
            ['id' => 18, 'asset_category_id' => 9, 'name' => 'School Dress (Clothes)', 'description' => 'Uniform and clothing items.', 'quantity' => 200, 'purchase_price' => 800.00, 'current_value' => 600.00, 'status' => 'active'],
            ['id' => 19, 'asset_category_id' => 9, 'name' => 'Sanitary Goods', 'description' => 'Sanitary and hygiene-related items.', 'quantity' => 100, 'purchase_price' => 200.00, 'current_value' => 150.00, 'status' => 'active'],
            ['id' => 20, 'asset_category_id' => 9, 'name' => 'Crockeries & Cutleries', 'description' => 'Utensils and kitchen items.', 'quantity' => 80, 'purchase_price' => 300.00, 'current_value' => 240.00, 'status' => 'active'],
            ['id' => 21, 'asset_category_id' => 10, 'name' => 'Digital Camera/CCTV Camera', 'description' => 'Surveillance and photography devices.', 'quantity' => 10, 'purchase_price' => 6000.00, 'current_value' => 4800.00, 'status' => 'active'],
            ['id' => 22, 'asset_category_id' => 10, 'name' => 'Fire Extinguisher', 'description' => 'Fire safety equipment.', 'quantity' => 10, 'purchase_price' => 3000.00, 'current_value' => 2400.00, 'status' => 'active'],
            ['id' => 23, 'asset_category_id' => 11, 'name' => 'Basin & Tiles Accessories', 'description' => 'Bathroom fittings and accessories.', 'quantity' => 30, 'purchase_price' => 1500.00, 'current_value' => 1200.00, 'status' => 'active'],
            ['id' => 24, 'asset_category_id' => 11, 'name' => 'School Grill', 'description' => 'Metal grills for windows and security.', 'quantity' => 20, 'purchase_price' => 4000.00, 'current_value' => 3200.00, 'status' => 'active'],
        ];

        foreach ($assets as $asset) {
            Asset::updateOrCreate(
                ['id' => $asset['id']],
                $asset
            );
        }

        $this->command?->info('Assets seeded: ' . count($assets));
    }
}
