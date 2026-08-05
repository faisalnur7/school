<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('student-data.json');

        if (! File::exists($path)) {
            $this->command?->error("student-data.json not found at {$path}");
            return;
        }

        $payload = json_decode(File::get($path), true);

        if (! is_array($payload) || ! isset($payload['students']) || ! is_array($payload['students'])) {
            $this->command?->error('student-data.json must contain a top-level "students" array.');
            return;
        }

        $now = now();
        $studentRows = [];
        $academicRows = [];

        // Build a map of school_class_id => section_id for fallback seeding.
        $sectionMap = DB::table('sections')->pluck('id', 'school_class_id')->toArray();

        foreach ($payload['students'] as $student) {
            $academicInfo = $student['student_academic_information'] ?? null;

            if (! is_array($academicInfo) || ! isset($academicInfo['student_id'])) {
                $cid = $student['student_cid'] ?? 'unknown';
                $this->command?->warn("Skipping student {$cid}: missing student_academic_information.student_id");
                continue;
            }

            $studentId = (int) $academicInfo['student_id'];

            $studentRows[] = [
                'id' => $studentId,
                'student_cid' => $student['student_cid'] ?? null,
                'full_name_bn' => $student['full_name_bn'] ?? null,
                'full_name_en' => $student['full_name_en'] ?? null,
                'image' => $student['image'] ?? null,
                'date_of_birth' => $student['date_of_birth'] ?? null,
                'gender' => $student['gender'] ?? null,
                'birth_certificate_number' => $student['birth_certificate_number'] ?? null,
                'religion' => $student['religion'] ?? null,
                'blood_group' => $student['blood_group'] ?? null,
                'disable' => $student['disable'] ?? false,
                'father_name' => $student['father_name'] ?? null,
                'father_nid_number' => $student['father_nid_number'] ?? null,
                'father_occupation' => $student['father_occupation'] ?? null,
                'fathers_profession_id' => $student['fathers_profession_id'] ?? null,
                'father_phone' => $student['father_phone'] ?? null,
                'father_email' => $student['father_email'] ?? null,
                'mother_name' => $student['mother_name'] ?? null,
                'mother_nid_number' => $student['mother_nid_number'] ?? null,
                'mother_occupation' => $student['mother_occupation'] ?? null,
                'mothers_profession_id' => $student['mothers_profession_id'] ?? null,
                'mother_phone' => $student['mother_phone'] ?? null,
                'mother_email' => $student['mother_email'] ?? null,
                'annual_income' => $student['annual_income'] ?? null,
                'present_address' => $student['present_address'] ?? null,
                'present_division_id' => $student['present_division_id'] ?? null,
                'present_district_id' => $student['present_district_id'] ?? null,
                'present_police_station_id' => $student['present_police_station_id'] ?? null,
                'present_post_office_id' => $student['present_post_office_id'] ?? null,
                'permanent_address' => $student['permanent_address'] ?? null,
                'permanent_division_id' => $student['permanent_division_id'] ?? null,
                'permanent_district_id' => $student['permanent_district_id'] ?? null,
                'permanent_police_station_id' => $student['permanent_police_station_id'] ?? null,
                'permanent_post_office_id' => $student['permanent_post_office_id'] ?? null,
                'guardian_type' => $student['guardian_type'] ?? 1,
                'guardian_name' => $student['guardian_name'] ?? null,
                'guardian_relation' => $student['guardian_relation'] ?? null,
                'guardian_occupation' => $student['guardian_occupation'] ?? null,
                'guardian_profession_id' => $student['guardian_profession_id'] ?? null,
                'guardian_address' => $student['guardian_address'] ?? null,
                'guardian_phone' => $student['guardian_phone'] ?? null,
                'guardian_email' => $student['guardian_email'] ?? null,
                'previous_school' => $student['previous_school'] ?? null,
                'previous_class_appeared' => $student['previous_class_appeared'] ?? null,
                'tc_number' => $student['tc_number'] ?? null,
                'status' => $student['status'] ?? 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $classId = $academicInfo['school_class_id'] ?? null;
            $sectionId = $academicInfo['section_id'] ?? ($classId ? ($sectionMap[$classId] ?? null) : null);

            $academicRows[] = [
                'student_id' => $studentId,
                'academic_session_id' => $academicInfo['academic_session_id'] ?? null,
                'school_class_id' => $classId,
                'section_id' => $sectionId,
                'group_id' => $academicInfo['group_id'] ?? null,
                'roll' => $academicInfo['roll'] ?? null,
                'academic_status' => $academicInfo['academic_status'] ?? 'active',
                'promotion_status' => $academicInfo['promotion_status'] ?? 'new_admission',
                'is_current' => $academicInfo['is_current'] ?? true,
                'previous_academic_information_id' => $academicInfo['previous_academic_information_id'] ?? null,
                'checkout_date' => $academicInfo['checkout_date'] ?? null,
                'notes' => $academicInfo['notes'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('student_academic_information')->truncate();
        DB::table('students')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::transaction(function () use ($studentRows, $academicRows) {
            foreach (array_chunk($studentRows, 200) as $chunk) {
                DB::table('students')->insert($chunk);
            }

            foreach (array_chunk($academicRows, 200) as $chunk) {
                DB::table('student_academic_information')->insert($chunk);
            }
        });

        $this->command?->info('Students seeded: ' . count($studentRows));
        $this->command?->info('Student academic information seeded: ' . count($academicRows));
    }
}
