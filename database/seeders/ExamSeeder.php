<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $session = AcademicSession::firstOrCreate(
            ['name_en' => '2026'],
            ['name_bn' => '2026', 'status' => 1]
        );

        $exams = [
            [
                'name' => 'First Tutorial Examination',
                'type' => Exam::TYPE_TUTORIAL,
                'exam_category' => 'tutorial',
                'pair_no' => 1,
                'pair_weight_percent' => 20,
                'academic_session_id' => $session->id,
                'year' => '2026',
                'start_date' => '2026-06-01',
                'end_date' => '2026-06-12',
                'status' => Exam::STATUS_PUBLISHED,
            ],
            [
                'name' => 'Second Tutorial Examination',
                'type' => Exam::TYPE_TUTORIAL,
                'exam_category' => 'tutorial',
                'pair_no' => 2,
                'pair_weight_percent' => 20,
                'academic_session_id' => $session->id,
                'year' => '2026',
                'start_date' => null,
                'end_date' => null,
                'status' => Exam::STATUS_PUBLISHED,
            ],
            [
                'name' => 'Third Tutorial Examination',
                'type' => Exam::TYPE_TUTORIAL,
                'exam_category' => 'tutorial',
                'pair_no' => 3,
                'pair_weight_percent' => 60,
                'academic_session_id' => $session->id,
                'year' => '2026',
                'start_date' => null,
                'end_date' => null,
                'status' => Exam::STATUS_PUBLISHED,
            ],
            [
                'name' => 'First Terminal Examination 2026',
                'type' => Exam::TYPE_TERMINAL,
                'exam_category' => 'terminal',
                'pair_no' => 1,
                'pair_weight_percent' => 20,
                'academic_session_id' => $session->id,
                'year' => '2026',
                'start_date' => null,
                'end_date' => null,
                'status' => Exam::STATUS_PUBLISHED,
            ],
            [
                'name' => 'Second Terminal Examination 2026',
                'type' => Exam::TYPE_TERMINAL,
                'exam_category' => 'terminal',
                'pair_no' => 2,
                'pair_weight_percent' => 20,
                'academic_session_id' => $session->id,
                'year' => '2026',
                'start_date' => null,
                'end_date' => null,
                'status' => Exam::STATUS_PUBLISHED,
            ],
            [
                'name' => 'Final Exam 2026',
                'type' => Exam::TYPE_TERMINAL,
                'exam_category' => 'terminal',
                'pair_no' => 3,
                'pair_weight_percent' => 60,
                'academic_session_id' => $session->id,
                'year' => '2026',
                'start_date' => null,
                'end_date' => null,
                'status' => Exam::STATUS_PUBLISHED,
            ],
        ];

        foreach ($exams as $exam) {
            Exam::updateOrCreate(
                [
                    'academic_session_id' => $exam['academic_session_id'],
                    'exam_category' => $exam['exam_category'],
                    'pair_no' => $exam['pair_no'],
                ],
                $exam
            );
        }
    }
}
