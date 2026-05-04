<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\SubjectClassConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Bangladesh NCTB Subject Seeder
 *
 * Covers:
 *  - Primary      : Class 1–5   (100-mark subjects, no MCQ split, pass 33)
 *  - Junior Sec.  : Class 6–8   (100-mark subjects, CQ 70 + MCQ 30, pass 33)
 *  - SSC          : Class 9–10  (100-mark subjects, CQ 70 + MCQ 30, pass 33;
 *                                Science/Business/Humanities groups)
 *
 * SSC Marks breakdown (standard):
 *   Written subjects  : CQ 70 + MCQ 30 = 100, pass 33
 *   Science practicals: CQ 55 + MCQ 20 + Practical 25 = 100, pass 33
 *   Math              : CQ 75 + MCQ 25 = 100, pass 33
 *   ICT               : Theory 50 + Practical 25 = 75, pass 25
 *   Physical Ed.      : Theory 50 + Practical 50 = 100, pass 33
 */
class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Wipe existing data in correct FK order
            SubjectClassAssignment::query()->delete();
            SubjectClassConfig::query()->delete();
            Subject::withTrashed()->forceDelete();

            $classes = SchoolClass::orderBy('id')->get()->keyBy('name_en');
            $groups  = Group::all()->keyBy('name_en');

            // ─────────────────────────────────────────────────────────────
            // HELPER CLOSURES
            // ─────────────────────────────────────────────────────────────

            /**
             * Create a standalone subject (no papers).
             */
            $makeSubject = function (
                string $name,
                string $code,
                string $type,
                float $cq,
                float $mcq,
                float $practical,
                float $pass,
                bool $isActive = true
            ): Subject {
                return Subject::create([
                    'name'                      => $name,
                    'code'                      => $code,
                    'type'                      => $type,
                    'has_multiple_papers'       => false,
                    'combine_papers_for_result' => false,
                    'is_parent'                 => false,
                    'is_paper'                  => false,
                    'creative_marks'            => $cq,
                    'mcq_marks'                 => $mcq,
                    'practical_marks'           => $practical,
                    'viva_marks'                => 0,
                    'pass_mark'                 => $pass,
                    'is_active'                 => $isActive,
                ]);
            };

            /**
             * Create a parent subject with two papers.
             */
            $makeParentWithPapers = function (
                string $parentName,
                string $parentCode,
                array  $papers          // [['name','code','cq','mcq','practical','pass'], ...]
            ) use ($makeSubject): Subject {
                $parent = Subject::create([
                    'name'                      => $parentName,
                    'code'                      => $parentCode,
                    'type'                      => 'mandatory',
                    'has_multiple_papers'       => true,
                    'combine_papers_for_result' => true,
                    'is_parent'                 => true,
                    'is_paper'                  => false,
                    'creative_marks'            => 0,
                    'mcq_marks'                 => 0,
                    'practical_marks'           => 0,
                    'viva_marks'                => 0,
                    'pass_mark'                 => 0,
                    'is_active'                 => true,
                ]);

                foreach ($papers as $p) {
                    Subject::create([
                        'name'                      => $p['name'],
                        'code'                      => $p['code'],
                        'type'                      => 'mandatory',
                        'parent_id'                 => $parent->id,
                        'has_multiple_papers'       => false,
                        'combine_papers_for_result' => false,
                        'is_parent'                 => false,
                        'is_paper'                  => true,
                        'creative_marks'            => $p['cq'],
                        'mcq_marks'                 => $p['mcq'],
                        'practical_marks'           => $p['practical'] ?? 0,
                        'viva_marks'                => 0,
                        'pass_mark'                 => $p['pass'],
                        'is_active'                 => true,
                    ]);
                }

                return $parent;
            };

            /**
             * Assign a subject to a class (optionally with group/optional flag).
             */
            $assign = function (
                Subject     $subject,
                SchoolClass $class,
                ?Group      $group = null,
                bool        $isOptional = false,
                ?string     $exclusiveKey = null
            ): void {
                SubjectClassAssignment::updateOrCreate([
                    'subject_id'      => $subject->id,
                    'school_class_id' => $class->id,
                    'group_id'        => $group?->id,
                    'gender'          => 'all',
                    'religion'        => 'all',
                ], [
                    'is_optional'        => $isOptional,
                    'is_compulsory'      => ! $isOptional,
                    'exclusive_group_key'=> $exclusiveKey,
                    'is_active'          => true,
                ]);
            };

            /**
             * Add a class-specific marks config override.
             */
            $config = function (
                Subject     $subject,
                SchoolClass $class,
                float $cq,
                float $mcq,
                float $practical,
                float $pass
            ): void {
                SubjectClassConfig::updateOrCreate([
                    'subject_id'      => $subject->id,
                    'school_class_id' => $class->id,
                ], [
                    'creative_marks'  => $cq,
                    'mcq_marks'       => $mcq,
                    'practical_marks' => $practical,
                    'viva_marks'      => 0,
                    'pass_mark'       => $pass,
                ]);
            };

            // ─────────────────────────────────────────────────────────────
            // 0. PRE-PRIMARY — Play, Nursery, KG
            //    No MCQ at this level. All written/oral/activity based.
            //    Play/Nursery : 50-mark subjects (oral + written), pass 17
            //    KG           : 100-mark subjects, pass 33
            // ─────────────────────────────────────────────────────────────

            // Play & Nursery subjects (50 marks each, pass 17)
            $bangla_pp   = $makeSubject('Bangla',                    'BAN-PP',  'mandatory', 50, 0, 0, 17);
            $english_pp  = $makeSubject('English',                   'ENG-PP',  'mandatory', 50, 0, 0, 17);
            $math_pp     = $makeSubject('Mathematics',               'MATH-PP', 'mandatory', 50, 0, 0, 17);
            $drawing_pp  = $makeSubject('Drawing & Coloring',        'DRW-PP',  'mandatory',  0, 0, 50, 17);
            $religion_pp = $makeSubject('Religion & Moral Education','REL-PP',  'mandatory', 50, 0, 0, 17);

            // KG subjects (100 marks each, pass 33)
            $bangla_kg   = $makeSubject('Bangla',                    'BAN-KG',  'mandatory', 100, 0, 0, 33);
            $english_kg  = $makeSubject('English',                   'ENG-KG',  'mandatory', 100, 0, 0, 33);
            $math_kg     = $makeSubject('Mathematics',               'MATH-KG', 'mandatory', 100, 0, 0, 33);
            $drawing_kg  = $makeSubject('Drawing & Coloring',        'DRW-KG',  'mandatory',   0, 0, 50, 17);
            $religion_kg = $makeSubject('Religion & Moral Education','REL-KG',  'mandatory', 100, 0, 0, 33);
            $gk_kg       = $makeSubject('General Knowledge',         'GK-KG',   'mandatory', 100, 0, 0, 33);

            foreach (['Play', 'Nursery'] as $cn) {
                if (! isset($classes[$cn])) continue;
                $cl = $classes[$cn];
                foreach ([$bangla_pp, $english_pp, $math_pp, $drawing_pp, $religion_pp] as $s) {
                    $assign($s, $cl);
                }
            }

            if (isset($classes['KG'])) {
                $cl = $classes['KG'];
                foreach ([$bangla_kg, $english_kg, $math_kg, $drawing_kg, $religion_kg, $gk_kg] as $s) {
                    $assign($s, $cl);
                }
            }

            // ─────────────────────────────────────────────────────────────
            // 1. PRIMARY LEVEL — Class 1–5
            //    NCTB Primary: 100 marks each, no MCQ split in lower classes.
            //    Class 1–2: 100 marks written (CQ=100, MCQ=0)
            //    Class 3–5: CQ=80, MCQ=20 (some boards), pass=33
            // ─────────────────────────────────────────────────────────────

            $primaryClasses   = ['One', 'Two', 'Three', 'Four', 'Five'];
            $primaryClasses12 = ['One', 'Two'];
            $primaryClasses35 = ['Three', 'Four', 'Five'];

            // Primary subjects (standalone, no papers at primary level)
            $bangla_p   = $makeSubject('Bangla',                    'BAN-P',  'mandatory', 100, 0,  0, 40);
            $english_p  = $makeSubject('English',                   'ENG-P',  'mandatory', 100, 0,  0, 40);
            $math_p     = $makeSubject('Mathematics',               'MATH-P', 'mandatory', 100, 0,  0, 40);
            $science_p  = $makeSubject('Science',                   'SCI-P',  'mandatory', 100, 0,  0, 40);
            $bgs_p      = $makeSubject('Bangladesh & Global Studies','BGS-P',  'mandatory', 100, 0,  0, 40);
            $religion_p = $makeSubject('Religion & Moral Education','REL-P',  'mandatory', 100, 0,  0, 40);
            $ict_p      = $makeSubject('ICT',                       'ICT-P',  'mandatory',  50, 0, 25, 25);

            // Class 1–2: pure written (CQ=100)
            foreach ($primaryClasses12 as $cn) {
                if (! isset($classes[$cn])) continue;
                $cl = $classes[$cn];
                foreach ([$bangla_p, $english_p, $math_p] as $s) {
                    $assign($s, $cl);
                }
            }

            // Class 3–5: CQ=80, MCQ=20
            foreach ($primaryClasses35 as $cn) {
                if (! isset($classes[$cn])) continue;
                $cl = $classes[$cn];
                foreach ([$bangla_p, $english_p, $math_p, $science_p, $bgs_p, $religion_p] as $s) {
                    $assign($s, $cl);
                    $config($s, $cl, 80, 20, 0, 33);
                }
            }

            // ICT only from Class 4–5
            foreach (['Four', 'Five'] as $cn) {
                if (! isset($classes[$cn])) continue;
                $assign($ict_p, $classes[$cn]);
            }

            // ─────────────────────────────────────────────────────────────
            // 2. JUNIOR SECONDARY — Class 6–8
            //    NCTB: CQ=70, MCQ=30, total=100, pass=33
            //    Science has practical: CQ=55, MCQ=20, Practical=25
            //    ICT: Theory=50, Practical=25, total=75, pass=25
            // ─────────────────────────────────────────────────────────────

            $jsClasses = ['Six', 'Seven', 'Eight'];

            // Two-paper subjects for JS
            $bangla_js = $makeParentWithPapers('Bangla', 'BAN-JS', [
                ['name' => 'Bangla 1st Paper', 'code' => 'BAN-JS-1', 'cq' => 70, 'mcq' => 30, 'practical' => 0, 'pass' => 33],
                ['name' => 'Bangla 2nd Paper', 'code' => 'BAN-JS-2', 'cq' => 70, 'mcq' => 30, 'practical' => 0, 'pass' => 33],
            ]);

            $english_js = $makeParentWithPapers('English', 'ENG-JS', [
                ['name' => 'English 1st Paper', 'code' => 'ENG-JS-1', 'cq' => 70, 'mcq' => 30, 'practical' => 0, 'pass' => 33],
                ['name' => 'English 2nd Paper', 'code' => 'ENG-JS-2', 'cq' => 70, 'mcq' => 30, 'practical' => 0, 'pass' => 33],
            ]);

            // Standalone JS subjects
            $math_js     = $makeSubject('Mathematics',                  'MATH-JS', 'mandatory', 70, 30,  0, 33);
            $science_js  = $makeSubject('General Science',              'SCI-JS',  'mandatory', 55, 20, 25, 33);
            $bgs_js      = $makeSubject('Bangladesh & Global Studies',  'BGS-JS',  'mandatory', 70, 30,  0, 33);
            $religion_js = $makeSubject('Religion & Moral Education',   'REL-JS',  'mandatory', 70, 30,  0, 33);
            $ict_js      = $makeSubject('ICT',                          'ICT-JS',  'mandatory', 50,  0, 25, 25);
            $pe_js       = $makeSubject('Physical Education & Health',  'PE-JS',   'mandatory', 50,  0, 50, 33);
            $arts_js     = $makeSubject('Arts & Crafts',                'ART-JS',  'mandatory', 50,  0, 50, 33);
            $homeec_js   = $makeSubject('Home Science',                 'HS-JS',   'optional',  70, 30,  0, 33);
            $agri_js     = $makeSubject('Agriculture Studies',          'AGR-JS',  'optional',  70, 30,  0, 33);

            foreach ($jsClasses as $cn) {
                if (! isset($classes[$cn])) continue;
                $cl = $classes[$cn];
                foreach ([
                    $bangla_js, $english_js, $math_js, $science_js,
                    $bgs_js, $religion_js, $ict_js, $pe_js, $arts_js,
                ] as $s) {
                    $assign($s, $cl);
                }
                // Optional: Home Science or Agriculture (student picks one)
                $assign($homeec_js, $cl, null, true, 'js_optional_vocational');
                $assign($agri_js,   $cl, null, true, 'js_optional_vocational');
            }

            // ─────────────────────────────────────────────────────────────
            // 3. SSC LEVEL — Class 9–10
            //    Three groups: Science, Business Studies, Humanities
            //
            //    Common to ALL groups (compulsory):
            //      Bangla 1st Paper  : CQ 70 + MCQ 30 = 100, pass 33
            //      Bangla 2nd Paper  : CQ 70 + MCQ 30 = 100, pass 33  (no MCQ in 2nd paper per NCTB)
            //      English 1st Paper : CQ 70 + MCQ 30 = 100, pass 33
            //      English 2nd Paper : CQ 70 + MCQ 30 = 100, pass 33
            //      Mathematics       : CQ 75 + MCQ 25 = 100, pass 33
            //      Religion          : CQ 70 + MCQ 30 = 100, pass 33
            //      ICT               : Theory 50 + Practical 25 = 75, pass 25
            //      Physical Ed.      : Theory 50 + Practical 50 = 100, pass 33
            //
            //    SCIENCE group:
            //      Physics    : CQ 55 + MCQ 20 + Practical 25 = 100, pass 33
            //      Chemistry  : CQ 55 + MCQ 20 + Practical 25 = 100, pass 33
            //      Biology    : CQ 55 + MCQ 20 + Practical 25 = 100, pass 33  (or Higher Math)
            //      Higher Math: CQ 75 + MCQ 25 = 100, pass 33  (exclusive with Biology)
            //
            //    BUSINESS STUDIES group:
            //      Accounting                    : CQ 70 + MCQ 30 = 100, pass 33
            //      Business Organization & Mgmt  : CQ 70 + MCQ 30 = 100, pass 33
            //      Finance & Banking             : CQ 70 + MCQ 30 = 100, pass 33
            //      Economics                     : CQ 70 + MCQ 30 = 100, pass 33 (optional)
            //
            //    HUMANITIES group:
            //      Bangladesh History & World Civilization : CQ 70 + MCQ 30 = 100, pass 33
            //      Geography & Environment                 : CQ 70 + MCQ 30 = 100, pass 33
            //      Civics & Citizenship                    : CQ 70 + MCQ 30 = 100, pass 33
            //      Economics                               : CQ 70 + MCQ 30 = 100, pass 33 (optional)
            // ─────────────────────────────────────────────────────────────

            $sscClasses = ['Nine', 'Ten'];
            $sciGroup   = $groups['Science']          ?? null;
            $busGroup   = $groups['Business Studies'] ?? null;
            $humGroup   = $groups['Humanities']       ?? null;

            // ── SSC Common Compulsory ──
            $bangla_ssc = $makeParentWithPapers('Bangla', 'BAN-SSC', [
                ['name' => 'Bangla 1st Paper', 'code' => 'BAN-SSC-1', 'cq' => 70, 'mcq' => 30, 'practical' => 0, 'pass' => 33],
                ['name' => 'Bangla 2nd Paper', 'code' => 'BAN-SSC-2', 'cq' => 100,'mcq' => 0,  'practical' => 0, 'pass' => 33],
            ]);

            $english_ssc = $makeParentWithPapers('English', 'ENG-SSC', [
                ['name' => 'English 1st Paper', 'code' => 'ENG-SSC-1', 'cq' => 70, 'mcq' => 30, 'practical' => 0, 'pass' => 33],
                ['name' => 'English 2nd Paper', 'code' => 'ENG-SSC-2', 'cq' => 70, 'mcq' => 30, 'practical' => 0, 'pass' => 33],
            ]);

            $math_ssc     = $makeSubject('Mathematics',              'MATH-SSC', 'mandatory', 75, 25,  0, 33);
            $religion_ssc = $makeSubject('Religion & Moral Education','REL-SSC', 'mandatory', 70, 30,  0, 33);
            $ict_ssc      = $makeSubject('ICT',                      'ICT-SSC',  'mandatory', 50,  0, 25, 25);
            $pe_ssc       = $makeSubject('Physical Education & Health','PE-SSC',  'optional',  50,  0, 50, 33);

            // ── SSC Science Group ──
            $physics_ssc  = $makeSubject('Physics',     'PHY-SSC',   'mandatory', 55, 20, 25, 33);
            $chem_ssc     = $makeSubject('Chemistry',   'CHEM-SSC',  'mandatory', 55, 20, 25, 33);
            $bio_ssc      = $makeSubject('Biology',     'BIO-SSC',   'optional',  55, 20, 25, 33);
            $hmath_ssc    = $makeSubject('Higher Math', 'HMATH-SSC', 'optional',  75, 25,  0, 33);

            // ── SSC Business Studies Group ──
            $acc_ssc      = $makeSubject('Accounting',                          'ACC-SSC',  'mandatory', 70, 30, 0, 33);
            $bom_ssc      = $makeSubject('Business Organization & Management',  'BOM-SSC',  'mandatory', 70, 30, 0, 33);
            $finance_ssc  = $makeSubject('Finance & Banking',                   'FIN-SSC',  'mandatory', 70, 30, 0, 33);
            $econ_ssc     = $makeSubject('Economics',                           'ECON-SSC', 'optional',  70, 30, 0, 33);

            // ── SSC Humanities Group ──
            $history_ssc  = $makeSubject('Bangladesh History & World Civilization', 'HIST-SSC', 'mandatory', 70, 30, 0, 33);
            $geo_ssc      = $makeSubject('Geography & Environment',                 'GEO-SSC',  'mandatory', 70, 30, 0, 33);
            $civics_ssc   = $makeSubject('Civics & Citizenship',                    'CIV-SSC',  'mandatory', 70, 30, 0, 33);
            // Economics shared between Business & Humanities as optional

            // ── Assign to Class 9 & 10 ──
            foreach ($sscClasses as $cn) {
                if (! isset($classes[$cn])) continue;
                $cl = $classes[$cn];

                // Common compulsory (no group filter)
                foreach ([$bangla_ssc, $english_ssc, $math_ssc, $religion_ssc, $ict_ssc] as $s) {
                    $assign($s, $cl);
                }
                $assign($pe_ssc, $cl, null, true); // optional

                // Science group
                if ($sciGroup) {
                    foreach ([$physics_ssc, $chem_ssc] as $s) {
                        $assign($s, $cl, $sciGroup);
                    }
                    // Biology OR Higher Math (exclusive)
                    $assign($bio_ssc,   $cl, $sciGroup, true, 'ssc_sci_bio_hmath');
                    $assign($hmath_ssc, $cl, $sciGroup, true, 'ssc_sci_bio_hmath');
                }

                // Business Studies group
                if ($busGroup) {
                    foreach ([$acc_ssc, $bom_ssc, $finance_ssc] as $s) {
                        $assign($s, $cl, $busGroup);
                    }
                    $assign($econ_ssc, $cl, $busGroup, true);
                }

                // Humanities group
                if ($humGroup) {
                    foreach ([$history_ssc, $geo_ssc, $civics_ssc] as $s) {
                        $assign($s, $cl, $humGroup);
                    }
                    $assign($econ_ssc, $cl, $humGroup, true);
                }
            }

            // ─────────────────────────────────────────────────────────────
            // 4. MADRASA — Nazera & Hifz (basic literacy subjects)
            // ─────────────────────────────────────────────────────────────

            $quran_m   = $makeSubject('Quran Majeed & Tajweed', 'QUR-M',  'mandatory', 100, 0, 0, 40);
            $bangla_m  = $makeSubject('Bangla',                 'BAN-M',  'mandatory', 100, 0, 0, 40);
            $english_m = $makeSubject('English',                'ENG-M',  'mandatory', 100, 0, 0, 40);
            $math_m    = $makeSubject('Mathematics',            'MATH-M', 'mandatory', 100, 0, 0, 40);
            $arabic_m  = $makeSubject('Arabic',                 'ARB-M',  'mandatory', 100, 0, 0, 40);

            foreach (['Nazera', 'Hifz'] as $cn) {
                if (! isset($classes[$cn])) continue;
                $cl = $classes[$cn];
                foreach ([$quran_m, $bangla_m, $english_m, $math_m, $arabic_m] as $s) {
                    $assign($s, $cl);
                }
            }
        });
    }
}
