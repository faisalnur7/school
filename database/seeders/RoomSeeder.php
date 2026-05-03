<?php

namespace Database\Seeders;

use App\Enums\RoomType;
use App\Models\Building;
use App\Models\Department;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = Building::query()->pluck('id', 'code');
        $departments = Department::query()->pluck('id', 'code');

        $rooms = [
            ['building_code' => 'ABA', 'department_code' => 'ACADEMIC', 'name' => 'Classroom 101', 'code' => 'ABA-101', 'floor_number' => 1, 'room_type' => RoomType::CLASSROOM->value, 'seating_capacity' => 40, 'is_active' => true],
            ['building_code' => 'ABA', 'department_code' => 'ACADEMIC', 'name' => 'Classroom 102', 'code' => 'ABA-102', 'floor_number' => 1, 'room_type' => RoomType::CLASSROOM->value, 'seating_capacity' => 40, 'is_active' => true],
            ['building_code' => 'ABA', 'department_code' => 'ACADEMIC', 'name' => 'Classroom 201', 'code' => 'ABA-201', 'floor_number' => 2, 'room_type' => RoomType::CLASSROOM->value, 'seating_capacity' => 45, 'is_active' => true],
            ['building_code' => 'ABA', 'department_code' => 'ACADEMIC', 'name' => 'Classroom 202', 'code' => 'ABA-202', 'floor_number' => 2, 'room_type' => RoomType::CLASSROOM->value, 'seating_capacity' => 45, 'is_active' => true],
            ['building_code' => 'ABA', 'department_code' => 'ACADEMIC', 'name' => 'Junior Staff Room', 'code' => 'ABA-SR1', 'floor_number' => 2, 'room_type' => RoomType::STAFFROOM->value, 'seating_capacity' => 12, 'is_active' => true],

            ['building_code' => 'ABB', 'department_code' => 'ACADEMIC', 'name' => 'Classroom 301', 'code' => 'ABB-301', 'floor_number' => 3, 'room_type' => RoomType::CLASSROOM->value, 'seating_capacity' => 50, 'is_active' => true],
            ['building_code' => 'ABB', 'department_code' => 'ACADEMIC', 'name' => 'Classroom 302', 'code' => 'ABB-302', 'floor_number' => 3, 'room_type' => RoomType::CLASSROOM->value, 'seating_capacity' => 50, 'is_active' => true],
            ['building_code' => 'ABB', 'department_code' => 'ACADEMIC', 'name' => 'Classroom 401', 'code' => 'ABB-401', 'floor_number' => 4, 'room_type' => RoomType::CLASSROOM->value, 'seating_capacity' => 55, 'is_active' => true],
            ['building_code' => 'ABB', 'department_code' => 'ACADEMIC', 'name' => 'Senior Staff Room', 'code' => 'ABB-SR1', 'floor_number' => 4, 'room_type' => RoomType::STAFFROOM->value, 'seating_capacity' => 16, 'is_active' => true],
            ['building_code' => 'ABB', 'department_code' => 'ACADEMIC', 'name' => 'Multipurpose Hall', 'code' => 'ABB-MPH', 'floor_number' => 1, 'room_type' => RoomType::OTHER->value, 'seating_capacity' => 120, 'is_active' => true],

            ['building_code' => 'ADMIN', 'department_code' => 'ADMIN', 'name' => 'Principal Office', 'code' => 'ADM-PO', 'floor_number' => 1, 'room_type' => RoomType::OFFICE->value, 'seating_capacity' => 6, 'is_active' => true],
            ['building_code' => 'ADMIN', 'department_code' => 'ACCTS', 'name' => 'Accounts Office', 'code' => 'ADM-ACC', 'floor_number' => 1, 'room_type' => RoomType::OFFICE->value, 'seating_capacity' => 8, 'is_active' => true],
            ['building_code' => 'ADMIN', 'department_code' => 'ADMIN', 'name' => 'Reception', 'code' => 'ADM-REC', 'floor_number' => 1, 'room_type' => RoomType::OFFICE->value, 'seating_capacity' => 4, 'is_active' => true],
            ['building_code' => 'ADMIN', 'department_code' => 'IT', 'name' => 'Server Room', 'code' => 'ADM-SRV', 'floor_number' => 1, 'room_type' => RoomType::STORAGE->value, 'seating_capacity' => null, 'is_active' => true],
            ['building_code' => 'ADMIN', 'department_code' => 'MNT', 'name' => 'Maintenance Store', 'code' => 'ADM-MST', 'floor_number' => 0, 'room_type' => RoomType::STORAGE->value, 'seating_capacity' => null, 'is_active' => true],

            ['building_code' => 'LLB', 'department_code' => 'LIB', 'name' => 'Central Library', 'code' => 'LLB-LIB', 'floor_number' => 1, 'room_type' => RoomType::LIBRARY->value, 'seating_capacity' => 60, 'is_active' => true],
            ['building_code' => 'LLB', 'department_code' => 'LAB', 'name' => 'Physics Lab', 'code' => 'LLB-PHY', 'floor_number' => 2, 'room_type' => RoomType::LAB->value, 'seating_capacity' => 30, 'is_active' => true],
            ['building_code' => 'LLB', 'department_code' => 'LAB', 'name' => 'Chemistry Lab', 'code' => 'LLB-CHE', 'floor_number' => 2, 'room_type' => RoomType::LAB->value, 'seating_capacity' => 30, 'is_active' => true],
            ['building_code' => 'LLB', 'department_code' => 'LAB', 'name' => 'Biology Lab', 'code' => 'LLB-BIO', 'floor_number' => 3, 'room_type' => RoomType::LAB->value, 'seating_capacity' => 30, 'is_active' => true],
            ['building_code' => 'LLB', 'department_code' => 'IT', 'name' => 'Computer Lab', 'code' => 'LLB-COMP', 'floor_number' => 3, 'room_type' => RoomType::LAB->value, 'seating_capacity' => 35, 'is_active' => true],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(
                ['code' => $room['code']],
                [
                    'building_id' => $buildings[$room['building_code']] ?? null,
                    'department_id' => isset($room['department_code']) ? ($departments[$room['department_code']] ?? null) : null,
                    'name' => $room['name'],
                    'code' => $room['code'],
                    'floor_number' => $room['floor_number'],
                    'room_type' => $room['room_type'],
                    'seating_capacity' => $room['seating_capacity'],
                    'is_active' => $room['is_active'],
                ]
            );
        }

        $this->command?->info('Rooms seeded: ' . count($rooms));
    }
}
