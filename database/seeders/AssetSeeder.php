<?php

namespace Database\Seeders;

use App\Models\Asset;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            ['id' => 1, 'asset_category_id' => 1, 'name' => 'Desktop Computer', 'description' => 'Standard desktop computer unit for lab and admin use.', 'quantity' => 25, 'purchase_price' => 45000.00, 'current_value' => 33750.00, 'status' => 'active'],
            ['id' => 2, 'asset_category_id' => 1, 'name' => 'Laptop', 'description' => 'Portable laptop for staff and mobile computing needs.', 'quantity' => 10, 'purchase_price' => 65000.00, 'current_value' => 48750.00, 'status' => 'active'],
            ['id' => 3, 'asset_category_id' => 1, 'name' => 'Network Switch', 'description' => 'Managed network switch for LAN connectivity.', 'quantity' => 5, 'purchase_price' => 12000.00, 'current_value' => 9000.00, 'status' => 'active'],
            ['id' => 4, 'asset_category_id' => 1, 'name' => 'WiFi Router', 'description' => 'Wireless router for campus internet distribution.', 'quantity' => 8, 'purchase_price' => 5000.00, 'current_value' => 3750.00, 'status' => 'active'],

            ['id' => 5, 'asset_category_id' => 2, 'name' => 'Projector', 'description' => 'Digital projector for classroom and presentation use.', 'quantity' => 8, 'purchase_price' => 30000.00, 'current_value' => 24000.00, 'status' => 'active'],
            ['id' => 6, 'asset_category_id' => 2, 'name' => 'LED Television', 'description' => 'Large-screen LED TV for display and media purposes.', 'quantity' => 4, 'purchase_price' => 40000.00, 'current_value' => 32000.00, 'status' => 'active'],
            ['id' => 7, 'asset_category_id' => 2, 'name' => 'Sound System', 'description' => 'Public address and audio system for events and assemblies.', 'quantity' => 3, 'purchase_price' => 25000.00, 'current_value' => 20000.00, 'status' => 'active'],

            ['id' => 8, 'asset_category_id' => 3, 'name' => 'Printer', 'description' => 'Laser/inkjet printer for document printing.', 'quantity' => 6, 'purchase_price' => 15000.00, 'current_value' => 12000.00, 'status' => 'active'],
            ['id' => 9, 'asset_category_id' => 3, 'name' => 'Scanner', 'description' => 'Flatbed scanner for digitising documents and records.', 'quantity' => 3, 'purchase_price' => 12000.00, 'current_value' => 9600.00, 'status' => 'active'],
            ['id' => 10, 'asset_category_id' => 3, 'name' => 'Photocopier Machine', 'description' => 'High-volume photocopier for administrative document copying.', 'quantity' => 2, 'purchase_price' => 80000.00, 'current_value' => 64000.00, 'status' => 'active'],

            ['id' => 11, 'asset_category_id' => 4, 'name' => 'Wooden Bench', 'description' => 'Student seating bench for classrooms.', 'quantity' => 120, 'purchase_price' => 2500.00, 'current_value' => 2250.00, 'status' => 'active'],
            ['id' => 12, 'asset_category_id' => 4, 'name' => 'Teacher Desk', 'description' => 'Desk unit for teaching staff use in classrooms.', 'quantity' => 30, 'purchase_price' => 6000.00, 'current_value' => 5400.00, 'status' => 'active'],
            ['id' => 13, 'asset_category_id' => 4, 'name' => 'Chair', 'description' => 'General-purpose chair for staff and common areas.', 'quantity' => 150, 'purchase_price' => 1500.00, 'current_value' => 1350.00, 'status' => 'active'],
            ['id' => 14, 'asset_category_id' => 4, 'name' => 'Almirah', 'description' => 'Steel/wooden almirah for storage in offices and staff rooms.', 'quantity' => 10, 'purchase_price' => 12000.00, 'current_value' => 10800.00, 'status' => 'active'],

            ['id' => 15, 'asset_category_id' => 5, 'name' => 'Whiteboard', 'description' => 'Wall-mounted whiteboard for classroom teaching.', 'quantity' => 30, 'purchase_price' => 4000.00, 'current_value' => 3400.00, 'status' => 'active'],
            ['id' => 16, 'asset_category_id' => 5, 'name' => 'Smart Board', 'description' => 'Interactive smart board with touch capability.', 'quantity' => 5, 'purchase_price' => 85000.00, 'current_value' => 72250.00, 'status' => 'active'],
            ['id' => 17, 'asset_category_id' => 5, 'name' => 'Marker Set', 'description' => 'Whiteboard marker set (assorted colours, pack of 6).', 'quantity' => 50, 'purchase_price' => 200.00, 'current_value' => 170.00, 'status' => 'active'],

            ['id' => 18, 'asset_category_id' => 6, 'name' => 'Science Lab Kit', 'description' => 'Complete science experiment kit for school laboratory.', 'quantity' => 10, 'purchase_price' => 15000.00, 'current_value' => 12750.00, 'status' => 'active'],
            ['id' => 19, 'asset_category_id' => 6, 'name' => 'Microscope', 'description' => 'Optical microscope for biology and science experiments.', 'quantity' => 6, 'purchase_price' => 20000.00, 'current_value' => 17000.00, 'status' => 'active'],
            ['id' => 20, 'asset_category_id' => 6, 'name' => 'Test Tube Set', 'description' => 'Glass test tube set for laboratory experiments.', 'quantity' => 25, 'purchase_price' => 500.00, 'current_value' => 425.00, 'status' => 'active'],

            ['id' => 21, 'asset_category_id' => 7, 'name' => 'Books', 'description' => 'Reference and academic books for the school library.', 'quantity' => 500, 'purchase_price' => 500.00, 'current_value' => 450.00, 'status' => 'active'],
            ['id' => 22, 'asset_category_id' => 7, 'name' => 'Bookshelf', 'description' => 'Wooden bookshelf unit for library storage.', 'quantity' => 20, 'purchase_price' => 8000.00, 'current_value' => 7200.00, 'status' => 'active'],
            ['id' => 23, 'asset_category_id' => 7, 'name' => 'Reading Table', 'description' => 'Long reading table for library use.', 'quantity' => 10, 'purchase_price' => 7000.00, 'current_value' => 6300.00, 'status' => 'active'],

            ['id' => 24, 'asset_category_id' => 8, 'name' => 'Football', 'description' => 'Standard size 5 football for outdoor sports.', 'quantity' => 10, 'purchase_price' => 1200.00, 'current_value' => 960.00, 'status' => 'active'],
            ['id' => 25, 'asset_category_id' => 8, 'name' => 'Cricket Kit', 'description' => 'Full cricket kit including bat, ball, pads, and helmet.', 'quantity' => 5, 'purchase_price' => 5000.00, 'current_value' => 4000.00, 'status' => 'active'],
            ['id' => 26, 'asset_category_id' => 8, 'name' => 'Badminton Set', 'description' => 'Badminton racket and shuttlecock set.', 'quantity' => 8, 'purchase_price' => 1500.00, 'current_value' => 1200.00, 'status' => 'active'],

            ['id' => 27, 'asset_category_id' => 9, 'name' => 'School Bus', 'description' => 'Large school bus for student transportation.', 'quantity' => 2, 'purchase_price' => 2500000.00, 'current_value' => 2000000.00, 'status' => 'active'],
            ['id' => 28, 'asset_category_id' => 9, 'name' => 'Van', 'description' => 'Mini-van for staff and short-distance school transport.', 'quantity' => 3, 'purchase_price' => 800000.00, 'current_value' => 640000.00, 'status' => 'active'],

            ['id' => 29, 'asset_category_id' => 10, 'name' => 'Generator', 'description' => 'Diesel generator for backup power supply.', 'quantity' => 1, 'purchase_price' => 250000.00, 'current_value' => 212500.00, 'status' => 'active'],
            ['id' => 30, 'asset_category_id' => 10, 'name' => 'UPS', 'description' => 'Uninterruptible power supply unit for critical equipment.', 'quantity' => 5, 'purchase_price' => 20000.00, 'current_value' => 17000.00, 'status' => 'active'],
            ['id' => 31, 'asset_category_id' => 10, 'name' => 'Battery', 'description' => 'Deep-cycle battery for UPS and power backup systems.', 'quantity' => 10, 'purchase_price' => 10000.00, 'current_value' => 8500.00, 'status' => 'active'],

            ['id' => 32, 'asset_category_id' => 11, 'name' => 'Ceiling Fan', 'description' => 'Standard ceiling fan for classroom and office ventilation.', 'quantity' => 60, 'purchase_price' => 3500.00, 'current_value' => 2975.00, 'status' => 'active'],
            ['id' => 33, 'asset_category_id' => 11, 'name' => 'LED Light', 'description' => 'Energy-efficient LED light for indoor lighting.', 'quantity' => 100, 'purchase_price' => 800.00, 'current_value' => 680.00, 'status' => 'active'],
            ['id' => 34, 'asset_category_id' => 11, 'name' => 'Switch Board', 'description' => 'Electrical switchboard panel for room power distribution.', 'quantity' => 50, 'purchase_price' => 500.00, 'current_value' => 425.00, 'status' => 'active'],

            ['id' => 35, 'asset_category_id' => 12, 'name' => 'Water Filter', 'description' => 'Commercial water purifier for safe drinking water.', 'quantity' => 4, 'purchase_price' => 18000.00, 'current_value' => 15300.00, 'status' => 'active'],
            ['id' => 36, 'asset_category_id' => 12, 'name' => 'Vacuum Cleaner', 'description' => 'Industrial vacuum cleaner for campus cleaning.', 'quantity' => 2, 'purchase_price' => 12000.00, 'current_value' => 10200.00, 'status' => 'active'],
            ['id' => 37, 'asset_category_id' => 12, 'name' => 'Cleaning Kit', 'description' => 'Complete cleaning kit including mop, bucket, and supplies.', 'quantity' => 15, 'purchase_price' => 1500.00, 'current_value' => 1275.00, 'status' => 'active'],

            ['id' => 38, 'asset_category_id' => 13, 'name' => 'CCTV Camera', 'description' => 'HD CCTV camera for campus surveillance.', 'quantity' => 12, 'purchase_price' => 6000.00, 'current_value' => 4800.00, 'status' => 'active'],
            ['id' => 39, 'asset_category_id' => 13, 'name' => 'Fire Extinguisher', 'description' => 'CO2/dry powder fire extinguisher for fire safety.', 'quantity' => 10, 'purchase_price' => 3000.00, 'current_value' => 2550.00, 'status' => 'active'],
            ['id' => 40, 'asset_category_id' => 13, 'name' => 'Alarm System', 'description' => 'Intruder and fire alarm system with siren and sensors.', 'quantity' => 2, 'purchase_price' => 20000.00, 'current_value' => 17000.00, 'status' => 'active'],

            ['id' => 41, 'asset_category_id' => 14, 'name' => 'First Aid Box', 'description' => 'Stocked first aid kit for emergency medical response.', 'quantity' => 5, 'purchase_price' => 2000.00, 'current_value' => 1700.00, 'status' => 'active'],
            ['id' => 42, 'asset_category_id' => 14, 'name' => 'Stretcher', 'description' => 'Folding stretcher for patient transport within the campus.', 'quantity' => 2, 'purchase_price' => 7000.00, 'current_value' => 5950.00, 'status' => 'active'],
            ['id' => 43, 'asset_category_id' => 14, 'name' => 'Thermometer', 'description' => 'Digital thermometer for health screening.', 'quantity' => 10, 'purchase_price' => 300.00, 'current_value' => 255.00, 'status' => 'active'],

            ['id' => 44, 'asset_category_id' => 15, 'name' => 'Toolbox Set', 'description' => 'Complete hand tool set for general maintenance and repairs.', 'quantity' => 3, 'purchase_price' => 5000.00, 'current_value' => 4250.00, 'status' => 'active'],
            ['id' => 45, 'asset_category_id' => 15, 'name' => 'Ladder', 'description' => 'Aluminium step ladder for maintenance and installation work.', 'quantity' => 4, 'purchase_price' => 4000.00, 'current_value' => 3400.00, 'status' => 'active'],
            ['id' => 46, 'asset_category_id' => 15, 'name' => 'Drill Machine', 'description' => 'Electric drill machine for carpentry and installation tasks.', 'quantity' => 2, 'purchase_price' => 7000.00, 'current_value' => 5950.00, 'status' => 'active'],
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
