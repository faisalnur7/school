<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing subjects (optional - uncomment if needed)
        // Subject::whereIn('id', range(1, 50))->delete();
        // Subject::whereIn('code', ['BAN', 'ENG'])->delete();

        $subjects = [
            // Subjects with multiple papers (combined subjects)
            [
                'name' => 'Bangla',
                'code' => 'BAN',
                'type' => 'mandatory',
                'has_multiple_papers' => true,
                'combine_papers_for_result' => true,
                'is_parent' => true,
                'is_paper' => false,
                'creative_marks' => 50.00,
                'mcq_marks' => 50.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 33.00,
                'is_active' => true,
            ],
            [
                'name' => 'English',
                'code' => 'ENG',
                'type' => 'mandatory',
                'has_multiple_papers' => true,
                'combine_papers_for_result' => true,
                'is_parent' => true,
                'is_paper' => false,
                'creative_marks' => 50.00,
                'mcq_marks' => 50.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 33.00,
                'is_active' => true,
            ],
            // Other standalone subjects
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 75.00,
                'mcq_marks' => 25.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 33.00,
                'is_active' => true,
            ],
            [
                'name' => 'Physics',
                'code' => 'PHY',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 35.00,
                'mcq_marks' => 15.00,
                'practical_marks' => 10.00,
                'viva_marks' => 0,
                'pass_mark' => 20.00,
                'is_active' => true,
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHEM',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 35.00,
                'mcq_marks' => 15.00,
                'practical_marks' => 10.00,
                'viva_marks' => 0,
                'pass_mark' => 20.00,
                'is_active' => true,
            ],
            [
                'name' => 'Biology',
                'code' => 'BIO',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 50.00,
                'mcq_marks' => 25.00,
                'practical_marks' => 25.00,
                'viva_marks' => 0,
                'pass_mark' => 33.00,
                'is_active' => true,
            ],
            [
                'name' => 'Higher Math',
                'code' => 'HMATH',
                'type' => 'optional',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 75.00,
                'mcq_marks' => 25.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 33.00,
                'is_active' => true,
            ],
            [
                'name' => 'Business Organization & Management',
                'code' => 'BOM',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 60.00,
                'mcq_marks' => 40.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 33.00,
                'is_active' => true,
            ],
            [
                'name' => 'Accounting',
                'code' => 'ACC',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 50.00,
                'mcq_marks' => 25.00,
                'practical_marks' => 25.00,
                'viva_marks' => 0,
                'pass_mark' => 33.00,
                'is_active' => true,
            ],
            [
                'name' => 'Economics',
                'code' => 'ECON',
                'type' => 'optional',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 60.00,
                'mcq_marks' => 40.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 33.00,
                'is_active' => true,
            ],
            [
                'name' => 'Religion',
                'code' => 'REL',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 50.00,
                'mcq_marks' => 25.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 25.00,
                'is_active' => true,
            ],
            [
                'name' => 'Social Science',
                'code' => 'SS',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 50.00,
                'mcq_marks' => 25.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 25.00,
                'is_active' => true,
            ],
            [
                'name' => 'General Science',
                'code' => 'GS',
                'type' => 'mandatory',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 50.00,
                'mcq_marks' => 25.00,
                'practical_marks' => 0,
                'viva_marks' => 0,
                'pass_mark' => 25.00,
                'is_active' => true,
            ],
            [
                'name' => 'Physical Education',
                'code' => 'PE',
                'type' => 'optional',
                'has_multiple_papers' => false,
                'combine_papers_for_result' => false,
                'is_parent' => false,
                'is_paper' => false,
                'creative_marks' => 0,
                'mcq_marks' => 25.00,
                'practical_marks' => 25.00,
                'viva_marks' => 0,
                'pass_mark' => 17.00,
                'is_active' => true,
            ],
        ];

        DB::transaction(function () use ($subjects) {
            foreach ($subjects as $subject) {
                Subject::create($subject);
            }

            // Create papers for Bangla
            $bangla = Subject::where('code', 'BAN')->first();
            if ($bangla) {
                Subject::create([
                    'name' => 'Bangla 1st Paper',
                    'code' => 'BAN-1',
                    'type' => 'mandatory',
                    'parent_id' => $bangla->id,
                    'is_paper' => true,
                    'is_parent' => false,
                    'has_multiple_papers' => false,
                    'creative_marks' => 50.00,
                    'mcq_marks' => 50.00,
                    'practical_marks' => 0,
                    'viva_marks' => 0,
                    'pass_mark' => 33.00,
                    'is_active' => true,
                ]);
                Subject::create([
                    'name' => 'Bangla 2nd Paper',
                    'code' => 'BAN-2',
                    'type' => 'mandatory',
                    'parent_id' => $bangla->id,
                    'is_paper' => true,
                    'is_parent' => false,
                    'has_multiple_papers' => false,
                    'creative_marks' => 50.00,
                    'mcq_marks' => 50.00,
                    'practical_marks' => 0,
                    'viva_marks' => 0,
                    'pass_mark' => 33.00,
                    'is_active' => true,
                ]);
            }

            // Create papers for English
            $english = Subject::where('code', 'ENG')->first();
            if ($english) {
                Subject::create([
                    'name' => 'English 1st Paper',
                    'code' => 'ENG-1',
                    'type' => 'mandatory',
                    'parent_id' => $english->id,
                    'is_paper' => true,
                    'is_parent' => false,
                    'has_multiple_papers' => false,
                    'creative_marks' => 50.00,
                    'mcq_marks' => 50.00,
                    'practical_marks' => 0,
                    'viva_marks' => 0,
                    'pass_mark' => 33.00,
                    'is_active' => true,
                ]);
                Subject::create([
                    'name' => 'English 2nd Paper',
                    'code' => 'ENG-2',
                    'type' => 'mandatory',
                    'parent_id' => $english->id,
                    'is_paper' => true,
                    'is_parent' => false,
                    'has_multiple_papers' => false,
                    'creative_marks' => 50.00,
                    'mcq_marks' => 50.00,
                    'practical_marks' => 0,
                    'viva_marks' => 0,
                    'pass_mark' => 33.00,
                    'is_active' => true,
                ]);
            }
        });
    }
}
