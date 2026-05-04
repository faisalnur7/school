# Bangladesh NCTB-Aligned Subject Management & Assignment Engine
### Laravel School Management System — Architect Specification (Refined)

> **Curriculum Reference:** NCTB 2024–2026 | SSC (Class 9–10) | HSC (Class 11–12) | Primary (Class 1–8)
> **Assessment Framework:** Traditional CQ/MCQ/Practical system (restored for SSC 2026) + New Curriculum competency system (Class 6–10 transition)

---

## 🇧🇩 BANGLADESH CURRICULUM REALITY CHECK

Before implementation, understand the **two parallel systems** currently in operation:

### System A — Traditional (Legacy + Restored for 2026)
Applies to SSC/HSC examinations. NCTB **restored** streams (Science, Business Studies, Humanities) for SSC 2026 after the new curriculum experiment.

| Level | Classes | Structure |
|---|---|---|
| Primary | 1–5 | Single stream, no groups |
| Junior Secondary | 6–8 | 10 common subjects, no groups (new curriculum) |
| Secondary | 9–10 | 3 groups (Science / Business Studies / Humanities) + compulsory |
| Higher Secondary | 11–12 | 3 groups, 1st paper (Class 11) + 2nd paper (Class 12) |

### System B — New Competency Curriculum (2022–present, phased)
Classes 6–9 are under this. No CQ/MCQ — competency-based assessment with 7 proficiency levels:
`Ananya → Agragami → Sakrio → Anushandhani → Bikashoman → Prathamik`

**Your system MUST support both.** Use `curriculum_version` field to toggle.

---

## 📚 REAL BANGLADESH SUBJECT STRUCTURE

### PRIMARY (Class 1–5) — Common Subjects, No Groups

| Subject | Code | Papers | Notes |
|---|---|---|---|
| Bangla (বাংলা) | 101 | 1st & 2nd | Multi-paper |
| English | 107 | 1st & 2nd | Multi-paper |
| Mathematics | 109 | Single | — |
| Bangladesh & World Identity | 111 | Single | — |
| Science | 113 | Single | — |
| Religion (Islam/Hindu/Buddhist/Christian) | 115–118 | Single | Religion-gated |

### JUNIOR SECONDARY (Class 6–8) — 10 Common Subjects (New Curriculum)

| Subject | Notes |
|---|---|
| Bangla | — |
| English | — |
| Mathematics | — |
| Science | — |
| Social Science | — |
| Digital Technology | — |
| Life & Livelihood | — |
| Religion & Moral Education | Religion-gated |
| Well-being (Shastho Suraksha) | — |
| Art & Culture | — |

> No group separation. All compulsory. Assessment is competency-based (new curriculum).

### SECONDARY (Class 9–10) — SSC Level

#### ✅ Compulsory for ALL Groups (3 subjects)

| Subject | Code | Papers | Marks |
|---|---|---|---|
| Bangla | 101 | 1st Paper + 2nd Paper | 100+100 |
| English | 107 | 1st Paper + 2nd Paper | 100+100 |
| General Mathematics | 109 | Single | 100 |
| Religion (Islam/Hindu/Buddhist/Christian) | 111–114 | Single | 100 |
| Bangladesh & Global Studies (BGS) | 150 | Single | 100 |
| ICT (Information & Communication Tech) | 154 | Single | 75 |

> Note: Bangla and English are each split into Paper 1 and Paper 2. Results combined.

#### 🔬 Science Group

| Subject | Code | Type | Marks | Breakdown |
|---|---|---|---|---|
| Physics | 136 | Compulsory | 100 | 75 Theory + 25 Practical |
| Chemistry | 137 | Compulsory | 100 | 75 Theory + 25 Practical |
| Higher Mathematics | 126 | **Exclusive Choice** | 100 | 70 CQ + 30 MCQ |
| Biology | 138 | **Exclusive Choice** | 100 | 75 Theory + 25 Practical |
| Agriculture / Computer Science | 134 / 154 | 4th Subject (Optional) | 100 | — |

> `exclusive_group_key = "science_3rd_subject"` → Must choose ONE: Higher Math OR Biology

#### 💼 Business Studies Group

| Subject | Code | Type | Marks |
|---|---|---|---|
| Accounting | 146 | Compulsory | 100 (70 CQ + 30 MCQ) |
| Business Entrepreneurship | 143 | Compulsory | 100 |
| Finance & Banking | 152 | Compulsory | 100 |
| Economics | 141 | Compulsory | 100 |
| Agriculture / Computer Science | 134 / 154 | 4th Subject (Optional) | 100 |

#### 📖 Humanities Group

| Subject | Code | Type | Marks |
|---|---|---|---|
| Bangladesh History & World Civilization | 153 | Compulsory | 100 |
| Economics | 141 | Compulsory | 100 |
| Civics & Good Governance | 140 | Compulsory | 100 |
| Geography & Environment | 110 | Compulsory | 100 |
| Social Work / Home Science / Music / Arabic / Sanskrit | Various | Elective (choose 2) | 100 each |
| Agriculture / Computer Science | 134 / 154 | 4th Subject (Optional) | 100 |

### HIGHER SECONDARY (Class 11–12) — HSC Level

#### ✅ Compulsory for ALL (5 subjects)

| Subject | Papers | Marks |
|---|---|---|
| Bangla | 1st Paper (Class 11) + 2nd Paper (Class 12) | 100+100 |
| English | 1st Paper (Class 11) + 2nd Paper (Class 12) | 100+100 |
| ICT | Single | 75 |

#### 🔬 HSC Science Group

| Subject | Papers | Type |
|---|---|---|
| Physics | 1st Paper + 2nd Paper | Compulsory |
| Chemistry | 1st Paper + 2nd Paper | Compulsory |
| Mathematics | 1st Paper + 2nd Paper | **Exclusive Choice** |
| Biology | 1st Paper + 2nd Paper | **Exclusive Choice** |
| 4th Elective | 1st + 2nd Paper | Optional (e.g., Statistics, Computer Science) |

> `exclusive_group_key = "hsc_science_3rd_elective"` → Math OR Biology (1st choice defines stream)

> **HSC Result Rule:** Class 11 exam (1st papers) + Class 12 exam (2nd papers) = Combined HSC result

---

## 🧱 REFINED DATABASE SCHEMA

### 1. `subjects` Table

```sql
CREATE TABLE subjects (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150) NOT NULL,
    name_bn         VARCHAR(150),                       -- Bangla name (বাংলা)
    code            VARCHAR(10) UNIQUE,                 -- NCTB subject code e.g. "101"
    parent_id       BIGINT UNSIGNED REFERENCES subjects(id),
    paper_number    TINYINT UNSIGNED,                   -- 1 or 2 (for 1st/2nd paper)
    is_parent       BOOLEAN DEFAULT FALSE,              -- True = has sub-papers
    is_paper        BOOLEAN DEFAULT FALSE,              -- True = is a sub-paper of parent
    has_multiple_papers     BOOLEAN DEFAULT FALSE,
    combine_papers_for_result BOOLEAN DEFAULT TRUE,     -- Combine paper 1+2 for final GPA
    assessment_system ENUM('traditional','competency') DEFAULT 'traditional',
    curriculum_version VARCHAR(20) DEFAULT '2012',      -- '2012', '2022_new', '2026_ssc'
    level           ENUM('primary','junior_secondary','secondary','higher_secondary') NOT NULL,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP,
    deleted_at      TIMESTAMP                           -- SoftDeletes
);
```

### 2. `subject_defaults` Table

```sql
CREATE TABLE subject_defaults (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id      BIGINT UNSIGNED NOT NULL REFERENCES subjects(id),
    creative_marks  TINYINT UNSIGNED DEFAULT 0,         -- CQ (Srijonshil Proshno)
    short_q_marks   TINYINT UNSIGNED DEFAULT 0,         -- Short answer (new 2026 pattern)
    mcq_marks       TINYINT UNSIGNED DEFAULT 0,         -- MCQ (Bahunirbachoni)
    practical_marks TINYINT UNSIGNED DEFAULT 0,         -- Byaboharik / Lab
    viva_marks      TINYINT UNSIGNED DEFAULT 0,
    total_marks     TINYINT UNSIGNED AS (creative_marks + short_q_marks + mcq_marks + practical_marks + viva_marks) STORED,
    pass_mark       TINYINT UNSIGNED DEFAULT 33,
    cq_pass_mark    TINYINT UNSIGNED DEFAULT 23,        -- Separate CQ pass threshold
    mcq_pass_mark   TINYINT UNSIGNED DEFAULT 10,        -- Separate MCQ pass threshold
    practical_pass_mark TINYINT UNSIGNED DEFAULT 0,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);
```

### 3. `subject_class_configs` Table (PRIMARY — overrides defaults)

```sql
CREATE TABLE subject_class_configs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id      BIGINT UNSIGNED NOT NULL REFERENCES subjects(id),
    class_id        BIGINT UNSIGNED NOT NULL REFERENCES classes(id),
    creative_marks  TINYINT UNSIGNED DEFAULT 0,
    short_q_marks   TINYINT UNSIGNED DEFAULT 0,
    mcq_marks       TINYINT UNSIGNED DEFAULT 0,
    practical_marks TINYINT UNSIGNED DEFAULT 0,
    viva_marks      TINYINT UNSIGNED DEFAULT 0,
    total_marks     TINYINT UNSIGNED AS (creative_marks + short_q_marks + mcq_marks + practical_marks + viva_marks) STORED,
    pass_mark       TINYINT UNSIGNED DEFAULT 33,
    cq_pass_mark    TINYINT UNSIGNED DEFAULT 23,
    mcq_pass_mark   TINYINT UNSIGNED DEFAULT 10,
    practical_pass_mark TINYINT UNSIGNED DEFAULT 0,
    exam_duration_minutes SMALLINT UNSIGNED DEFAULT 180,
    cq_duration_minutes   SMALLINT UNSIGNED DEFAULT 150,
    mcq_duration_minutes  SMALLINT UNSIGNED DEFAULT 20,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP,
    UNIQUE (subject_id, class_id)
);
```

> **2026 SSC Mark Rules (NCTB Official):**
> - No Practical → 70 marks CQ (50 creative + 20 short) + 30 MCQ
> - With Practical → 75 marks Theory + 25 Practical
> - Bangla 2nd Paper → 70 marks Writing + 30 marks Grammar (no MCQ)
> - ICT → 75 total (25 theory + 25 MCQ + 25 practical)

### 4. `groups` Table

```sql
CREATE TABLE groups (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,              -- "Science", "Business Studies", "Humanities"
    name_bn         VARCHAR(100),
    code            VARCHAR(20),                        -- "science", "business", "humanities"
    applicable_from_class TINYINT UNSIGNED DEFAULT 9,
    is_active       BOOLEAN DEFAULT TRUE
);
```

### 5. `subject_assignments` Table

```sql
CREATE TABLE subject_assignments (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_id      BIGINT UNSIGNED NOT NULL REFERENCES subjects(id),
    class_id        BIGINT UNSIGNED NOT NULL REFERENCES classes(id),
    group_id        BIGINT UNSIGNED REFERENCES groups(id),       -- NULL = all groups
    gender          ENUM('all','male','female') DEFAULT 'all',
    religion        ENUM('all','islam','hindu','buddhist','christian') DEFAULT 'all',
    assignment_type ENUM('compulsory','optional','elective','fourth_subject') NOT NULL,
    exclusive_group_key VARCHAR(100),                            -- e.g. "science_3rd_subject"
    min_select      TINYINT UNSIGNED DEFAULT 0,                  -- min to pick from exclusive group
    max_select      TINYINT UNSIGNED DEFAULT 1,                  -- max to pick from exclusive group
    curriculum_version VARCHAR(20) DEFAULT '2012',
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP,
    UNIQUE (subject_id, class_id, group_id, gender, religion)
);
```

### 6. `student_subjects` Table

```sql
CREATE TABLE student_subjects (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id      BIGINT UNSIGNED NOT NULL REFERENCES students(id),
    subject_id      BIGINT UNSIGNED NOT NULL REFERENCES subjects(id),
    class_id        BIGINT UNSIGNED NOT NULL REFERENCES classes(id),
    academic_year   YEAR NOT NULL,
    assignment_type ENUM('compulsory','optional','elective','fourth_subject') NOT NULL,
    is_selected     BOOLEAN DEFAULT TRUE,
    selected_at     TIMESTAMP,
    selected_by     BIGINT UNSIGNED REFERENCES users(id),        -- admin/teacher who confirmed
    UNIQUE (student_id, subject_id, class_id, academic_year)
);
```

---

## ⚙️ MARK DISTRIBUTION RULES (Bangladesh-Specific)

```php
// config/nctb_marks.php

return [

    // Standard rules for SSC 2026 (NCTB official)
    'ssc_2026' => [
        'no_practical' => [
            'creative_marks' => 50,
            'short_q_marks'  => 20,
            'mcq_marks'      => 30,
            'practical_marks'=> 0,
            'total'          => 100,
            'pass'           => 33,
            'cq_pass'        => 23,     // CQ (50+20) combined
            'mcq_pass'       => 10,
        ],
        'with_practical' => [
            'creative_marks' => 55,
            'short_q_marks'  => 20,
            'mcq_marks'      => 0,
            'practical_marks'=> 25,
            'total'          => 100,
            'pass'           => 33,
        ],
        'bangla_2nd_paper' => [
            'writing_marks'  => 70,     // No MCQ
            'grammar_marks'  => 30,
            'mcq_marks'      => 0,
            'total'          => 100,
        ],
        'ict' => [
            'theory_marks'   => 25,
            'mcq_marks'      => 25,
            'practical_marks'=> 25,
            'total'          => 75,
        ],
    ],

    // Competency levels for new curriculum (Class 6-9)
    'competency_levels' => [
        6 => 'Ananya (অনন্য)',
        5 => 'Agragami (অগ্রগামী)',
        4 => 'Sakrio (সক্রিয়)',
        3 => 'Anushandhani (অনুসন্ধানী)',
        2 => 'Bikashoman (বিকাশমান)',
        1 => 'Prathamik (প্রাথমিক)',
    ],

    // Pass logic
    'gpa_scale' => [
        ['min' => 80, 'grade' => 'A+', 'point' => 5.00],
        ['min' => 70, 'grade' => 'A',  'point' => 4.00],
        ['min' => 60, 'grade' => 'A-', 'point' => 3.50],
        ['min' => 50, 'grade' => 'B',  'point' => 3.00],
        ['min' => 40, 'grade' => 'C',  'point' => 2.00],
        ['min' => 33, 'grade' => 'D',  'point' => 1.00],
        ['min' => 0,  'grade' => 'F',  'point' => 0.00],
    ],
];
```

---

## 🧠 SERVICE LAYER (Refined)

### SubjectService

```php
class SubjectService
{
    public function createWithPapers(array $data): Subject
    {
        // If has_multiple_papers, create parent + Paper 1 + Paper 2 atomically
        DB::transaction(function () use ($data) {
            $parent = Subject::create([...$data, 'is_parent' => true, 'is_paper' => false]);

            foreach ($data['papers'] as $i => $paper) {
                Subject::create([
                    ...$paper,
                    'parent_id'     => $parent->id,
                    'is_paper'      => true,
                    'is_parent'     => false,
                    'paper_number'  => $i + 1,
                    'curriculum_version' => $parent->curriculum_version,
                ]);
            }
        });
    }

    public function resolveForResult(Subject $subject): Collection
    {
        // Returns all sub-papers if combine_papers_for_result = true
        if ($subject->is_parent && $subject->combine_papers_for_result) {
            return $subject->papers;
        }
        return collect([$subject]);
    }
}
```

### StudentSubjectService (Core Engine)

```php
class StudentSubjectService
{
    /**
     * Main assignment engine
     */
    public function generateSubjectList(Student $student, int $classId, int $academicYear): array
    {
        $assignments = SubjectAssignment::query()
            ->where('class_id', $classId)
            ->where('curriculum_version', $student->curriculum_version)
            ->where(fn($q) => $q->whereNull('group_id')->orWhere('group_id', $student->group_id))
            ->where(fn($q) => $q->where('gender', 'all')->orWhere('gender', $student->gender))
            ->where(fn($q) => $q->where('religion', 'all')->orWhere('religion', $student->religion))
            ->with('subject.papers')
            ->get();

        // Expand parent subjects to include their papers
        $expanded = $this->expandParentSubjects($assignments);

        return [
            'compulsory'       => $expanded->where('assignment_type', 'compulsory'),
            'optional'         => $expanded->where('assignment_type', 'optional'),
            'elective'         => $expanded->where('assignment_type', 'elective'),
            'fourth_subject'   => $expanded->where('assignment_type', 'fourth_subject'),
            'exclusive_groups' => $this->groupExclusiveSubjects($expanded),
        ];
    }

    /**
     * Validate student selection — enforce Bangladesh rules
     */
    public function validateSelection(Student $student, array $selectedSubjectIds, int $classId): void
    {
        $assignments = $this->generateSubjectList($student, $classId, now()->year);

        // Rule 1: All compulsory subjects must be present
        $compulsoryIds = $assignments['compulsory']->pluck('subject_id');
        $missing = $compulsoryIds->diff($selectedSubjectIds);
        if ($missing->isNotEmpty()) {
            throw new ValidationException("Missing compulsory subjects: " . $missing->implode(', '));
        }

        // Rule 2: Exclusive group — exactly 1 from each exclusive_group_key
        foreach ($assignments['exclusive_groups'] as $key => $group) {
            $selected = collect($selectedSubjectIds)->intersect($group->pluck('subject_id'));
            $min = $group->first()->min_select ?? 1;
            $max = $group->first()->max_select ?? 1;

            if ($selected->count() < $min) {
                throw new ValidationException("Must select at least {$min} subject from group: {$key}");
            }
            if ($selected->count() > $max) {
                throw new ValidationException("Cannot select more than {$max} subject from group: {$key}");
            }
        }

        // Rule 3: Fourth subject — max 1
        $fourthSelected = collect($selectedSubjectIds)
            ->intersect($assignments['fourth_subject']->pluck('subject_id'));
        if ($fourthSelected->count() > 1) {
            throw new ValidationException("Only one fourth/optional subject allowed.");
        }
    }

    private function expandParentSubjects(Collection $assignments): Collection
    {
        return $assignments->flatMap(function ($assignment) {
            if ($assignment->subject->is_parent) {
                // Replace parent with its papers, inheriting assignment_type
                return $assignment->subject->papers->map(fn($paper) => tap(
                    clone $assignment,
                    fn($a) => $a->subject_id = $paper->id
                ));
            }
            return [$assignment];
        });
    }

    private function groupExclusiveSubjects(Collection $assignments): array
    {
        return $assignments
            ->whereNotNull('exclusive_group_key')
            ->groupBy('exclusive_group_key')
            ->toArray();
    }
}
```

### MarkConfigurationService

```php
class MarkConfigurationService
{
    public function getConfig(int $subjectId, int $classId): object
    {
        // Priority: class-specific > subject default > system config
        return SubjectClassConfig::where('subject_id', $subjectId)
                ->where('class_id', $classId)
                ->first()
            ?? SubjectDefault::where('subject_id', $subjectId)->first()
            ?? $this->systemDefault($subjectId);
    }

    public function validateConfig(array $data): void
    {
        $total = $data['creative_marks'] + ($data['short_q_marks'] ?? 0)
               + $data['mcq_marks'] + $data['practical_marks'] + $data['viva_marks'];

        if ($total === 0) throw new \InvalidArgumentException("At least one mark component must be > 0");
        if ($data['pass_mark'] > $total) throw new \InvalidArgumentException("Pass mark cannot exceed total marks");

        // Enforce separate CQ and MCQ pass thresholds
        if (isset($data['cq_pass_mark']) && $data['cq_pass_mark'] > ($data['creative_marks'] + ($data['short_q_marks'] ?? 0))) {
            throw new \InvalidArgumentException("CQ pass mark exceeds CQ total");
        }
    }
}
```

---

## 🎯 EXCLUSIVE GROUP EXAMPLES (Bangladesh-Specific)

### Science Group — SSC

```php
// SubjectAssignment Seeder entries
[
    ['subject' => 'Physics',    'group' => 'Science', 'type' => 'compulsory', 'exclusive_group_key' => null],
    ['subject' => 'Chemistry',  'group' => 'Science', 'type' => 'compulsory', 'exclusive_group_key' => null],
    ['subject' => 'Higher Math','group' => 'Science', 'type' => 'elective',   'exclusive_group_key' => 'science_3rd_subject', 'min_select' => 1, 'max_select' => 1],
    ['subject' => 'Biology',    'group' => 'Science', 'type' => 'elective',   'exclusive_group_key' => 'science_3rd_subject', 'min_select' => 1, 'max_select' => 1],
]
```

### Humanities Group — Optional Choice (choose 2 from 5+)

```php
[
    ['subject' => 'Social Work',  'type' => 'optional', 'exclusive_group_key' => 'humanities_elective', 'min_select' => 2, 'max_select' => 2],
    ['subject' => 'Home Science', 'type' => 'optional', 'exclusive_group_key' => 'humanities_elective', 'min_select' => 2, 'max_select' => 2],
    ['subject' => 'Music',        'type' => 'optional', 'exclusive_group_key' => 'humanities_elective', 'min_select' => 2, 'max_select' => 2],
    ['subject' => 'Arabic',       'type' => 'optional', 'exclusive_group_key' => 'humanities_elective', 'min_select' => 2, 'max_select' => 2],
    ['subject' => 'Sanskrit',     'type' => 'optional', 'exclusive_group_key' => 'humanities_elective', 'min_select' => 2, 'max_select' => 2],
]
```

### Religion-Gated Subject

```php
[
    ['subject' => 'Islam & Moral Education',    'religion' => 'islam',     'type' => 'compulsory'],
    ['subject' => 'Hindu Religion & Morality',  'religion' => 'hindu',     'type' => 'compulsory'],
    ['subject' => 'Buddhist Religion',          'religion' => 'buddhist',  'type' => 'compulsory'],
    ['subject' => 'Christian Religion',         'religion' => 'christian', 'type' => 'compulsory'],
]
```

---

## 🌱 DATABASE SEEDER (Realistic Bangladesh Data)

```php
class BangladeshNCTBSubjectSeeder extends Seeder
{
    public function run(): void
    {
        // ─── COMPULSORY SSC SUBJECTS ────────────────────────────────

        // Bangla — Parent with 2 papers
        $bangla = Subject::create([
            'name' => 'Bangla', 'name_bn' => 'বাংলা', 'code' => '101',
            'is_parent' => true, 'has_multiple_papers' => true,
            'combine_papers_for_result' => true,
            'level' => 'secondary', 'curriculum_version' => '2026_ssc',
        ]);
        Subject::create(['name' => 'Bangla 1st Paper', 'name_bn' => 'বাংলা ১ম পত্র', 'code' => '101',
            'parent_id' => $bangla->id, 'is_paper' => true, 'paper_number' => 1, 'level' => 'secondary']);
        Subject::create(['name' => 'Bangla 2nd Paper', 'name_bn' => 'বাংলা ২য় পত্র', 'code' => '102',
            'parent_id' => $bangla->id, 'is_paper' => true, 'paper_number' => 2, 'level' => 'secondary']);

        // English — Parent with 2 papers
        $english = Subject::create([
            'name' => 'English', 'code' => '107',
            'is_parent' => true, 'has_multiple_papers' => true,
            'combine_papers_for_result' => true,
            'level' => 'secondary', 'curriculum_version' => '2026_ssc',
        ]);
        Subject::create(['name' => 'English 1st Paper', 'code' => '107', 'parent_id' => $english->id,
            'is_paper' => true, 'paper_number' => 1, 'level' => 'secondary']);
        Subject::create(['name' => 'English 2nd Paper', 'code' => '108', 'parent_id' => $english->id,
            'is_paper' => true, 'paper_number' => 2, 'level' => 'secondary']);

        // Single compulsory subjects
        $mathGeneral = Subject::create(['name' => 'General Mathematics', 'name_bn' => 'সাধারণ গণিত',
            'code' => '109', 'level' => 'secondary']);
        $ict = Subject::create(['name' => 'Information & Communication Technology', 'name_bn' => 'তথ্য ও যোগাযোগ প্রযুক্তি',
            'code' => '154', 'level' => 'secondary']);
        $bgs = Subject::create(['name' => 'Bangladesh & Global Studies', 'name_bn' => 'বাংলাদেশ ও বিশ্বপরিচয়',
            'code' => '150', 'level' => 'secondary']);

        // Religion subjects (religion-gated)
        $islamReligion  = Subject::create(['name' => 'Islam & Moral Education', 'name_bn' => 'ইসলাম ও নৈতিক শিক্ষা', 'code' => '111', 'level' => 'secondary']);
        $hinduReligion  = Subject::create(['name' => 'Hindu Religion & Morality', 'name_bn' => 'হিন্দুধর্ম ও নৈতিক শিক্ষা', 'code' => '112', 'level' => 'secondary']);
        $buddhism       = Subject::create(['name' => 'Buddhist Religion', 'name_bn' => 'বৌদ্ধধর্ম ও নৈতিক শিক্ষা', 'code' => '113', 'level' => 'secondary']);
        $christianity   = Subject::create(['name' => 'Christian Religion', 'name_bn' => 'খ্রিষ্টধর্ম ও নৈতিক শিক্ষা', 'code' => '114', 'level' => 'secondary']);

        // ─── SCIENCE GROUP SUBJECTS ─────────────────────────────────

        $physics    = Subject::create(['name' => 'Physics', 'name_bn' => 'পদার্থবিজ্ঞান', 'code' => '136', 'level' => 'secondary']);
        $chemistry  = Subject::create(['name' => 'Chemistry', 'name_bn' => 'রসায়ন', 'code' => '137', 'level' => 'secondary']);
        $higherMath = Subject::create(['name' => 'Higher Mathematics', 'name_bn' => 'উচ্চতর গণিত', 'code' => '126', 'level' => 'secondary']);
        $biology    = Subject::create(['name' => 'Biology', 'name_bn' => 'জীববিজ্ঞান', 'code' => '138', 'level' => 'secondary']);
        $agri       = Subject::create(['name' => 'Agriculture Studies', 'name_bn' => 'কৃষিশিক্ষা', 'code' => '134', 'level' => 'secondary']);

        // ─── BUSINESS STUDIES GROUP SUBJECTS ────────────────────────

        $accounting = Subject::create(['name' => 'Accounting', 'name_bn' => 'হিসাববিজ্ঞান', 'code' => '146', 'level' => 'secondary']);
        $bizEntrepr = Subject::create(['name' => 'Business Entrepreneurship', 'name_bn' => 'ব্যবসায় উদ্যোগ', 'code' => '143', 'level' => 'secondary']);
        $finance    = Subject::create(['name' => 'Finance & Banking', 'name_bn' => 'ফিনান্স ও ব্যাংকিং', 'code' => '152', 'level' => 'secondary']);
        $economics  = Subject::create(['name' => 'Economics', 'name_bn' => 'অর্থনীতি', 'code' => '141', 'level' => 'secondary']);

        // ─── HUMANITIES GROUP SUBJECTS ───────────────────────────────

        $history    = Subject::create(['name' => 'Bangladesh History & World Civilization', 'name_bn' => 'বাংলাদেশের ইতিহাস ও বিশ্বসভ্যতা', 'code' => '153', 'level' => 'secondary']);
        $civics     = Subject::create(['name' => 'Civics & Good Governance', 'name_bn' => 'পৌরনীতি ও সুশাসন', 'code' => '140', 'level' => 'secondary']);
        $geography  = Subject::create(['name' => 'Geography & Environment', 'name_bn' => 'ভূগোল ও পরিবেশ', 'code' => '110', 'level' => 'secondary']);
        $socialWork = Subject::create(['name' => 'Social Work', 'name_bn' => 'সমাজকর্ম', 'code' => '273', 'level' => 'secondary']);
        $arabic     = Subject::create(['name' => 'Arabic', 'name_bn' => 'আরবি', 'code' => '230', 'level' => 'secondary']);

        // ─── MARKS CONFIGURATIONS ────────────────────────────────────

        $class9Id = DB::table('classes')->where('name', 'Class 9')->value('id');
        $class10Id = DB::table('classes')->where('name', 'Class 10')->value('id');

        foreach ([$class9Id, $class10Id] as $classId) {

            // Standard: 70 CQ + 30 MCQ
            foreach ([$mathGeneral, $bgs, $economics, $accounting, $bizEntrepr, $finance,
                      $history, $civics, $geography, $socialWork] as $subj) {
                SubjectClassConfig::create([
                    'subject_id' => $subj->id, 'class_id' => $classId,
                    'creative_marks' => 50, 'short_q_marks' => 20, 'mcq_marks' => 30,
                    'pass_mark' => 33, 'cq_pass_mark' => 23, 'mcq_pass_mark' => 10,
                ]);
            }

            // With Practical: 75 theory + 25 practical
            foreach ([$physics, $chemistry, $biology] as $subj) {
                SubjectClassConfig::create([
                    'subject_id' => $subj->id, 'class_id' => $classId,
                    'creative_marks' => 55, 'short_q_marks' => 20,
                    'practical_marks' => 25, 'pass_mark' => 33,
                ]);
            }

            // Higher Math: CQ only (no practical)
            SubjectClassConfig::create([
                'subject_id' => $higherMath->id, 'class_id' => $classId,
                'creative_marks' => 70, 'mcq_marks' => 30, 'pass_mark' => 33,
            ]);

            // ICT: 25 theory + 25 MCQ + 25 practical = 75
            SubjectClassConfig::create([
                'subject_id' => $ict->id, 'class_id' => $classId,
                'creative_marks' => 25, 'mcq_marks' => 25, 'practical_marks' => 25,
                'pass_mark' => 25,
            ]);

            // Bangla 2nd Paper (no MCQ)
            $bangla2PaperId = Subject::where('parent_id', $bangla->id)->where('paper_number', 2)->value('id');
            SubjectClassConfig::create([
                'subject_id' => $bangla2PaperId, 'class_id' => $classId,
                'creative_marks' => 70, 'short_q_marks' => 30,
                'mcq_marks' => 0, 'pass_mark' => 33,
            ]);
        }

        // ─── SUBJECT ASSIGNMENTS ─────────────────────────────────────

        $scienceGroup   = Group::where('code', 'science')->first();
        $businessGroup  = Group::where('code', 'business')->first();
        $humanitiesGroup= Group::where('code', 'humanities')->first();

        foreach ([$class9Id, $class10Id] as $classId) {

            // Compulsory all groups
            foreach ([$bangla->id, $english->id, $mathGeneral->id, $ict->id, $bgs->id] as $subjectId) {
                SubjectAssignment::create([
                    'subject_id' => $subjectId, 'class_id' => $classId,
                    'group_id' => null, 'gender' => 'all', 'religion' => 'all',
                    'assignment_type' => 'compulsory',
                ]);
            }

            // Religion subjects — religion-gated, all groups
            SubjectAssignment::create(['subject_id' => $islamReligion->id, 'class_id' => $classId, 'group_id' => null, 'religion' => 'islam', 'assignment_type' => 'compulsory']);
            SubjectAssignment::create(['subject_id' => $hinduReligion->id, 'class_id' => $classId, 'group_id' => null, 'religion' => 'hindu', 'assignment_type' => 'compulsory']);
            SubjectAssignment::create(['subject_id' => $buddhism->id,      'class_id' => $classId, 'group_id' => null, 'religion' => 'buddhist', 'assignment_type' => 'compulsory']);
            SubjectAssignment::create(['subject_id' => $christianity->id,  'class_id' => $classId, 'group_id' => null, 'religion' => 'christian', 'assignment_type' => 'compulsory']);

            // Science group: Physics, Chemistry compulsory
            SubjectAssignment::create(['subject_id' => $physics->id,   'class_id' => $classId, 'group_id' => $scienceGroup->id, 'assignment_type' => 'compulsory']);
            SubjectAssignment::create(['subject_id' => $chemistry->id, 'class_id' => $classId, 'group_id' => $scienceGroup->id, 'assignment_type' => 'compulsory']);

            // Science group: Higher Math OR Biology (exclusive, min 1, max 1)
            SubjectAssignment::create(['subject_id' => $higherMath->id, 'class_id' => $classId, 'group_id' => $scienceGroup->id,
                'assignment_type' => 'elective', 'exclusive_group_key' => 'science_3rd_subject', 'min_select' => 1, 'max_select' => 1]);
            SubjectAssignment::create(['subject_id' => $biology->id,    'class_id' => $classId, 'group_id' => $scienceGroup->id,
                'assignment_type' => 'elective', 'exclusive_group_key' => 'science_3rd_subject', 'min_select' => 1, 'max_select' => 1]);

            // Fourth subject (optional, max 1) — available to all groups
            SubjectAssignment::create(['subject_id' => $agri->id, 'class_id' => $classId,
                'group_id' => null, 'assignment_type' => 'fourth_subject']);
            SubjectAssignment::create(['subject_id' => $ict->id, 'class_id' => $classId,
                'group_id' => null, 'assignment_type' => 'fourth_subject']);

            // Business Studies: compulsory
            foreach ([$accounting->id, $bizEntrepr->id, $finance->id, $economics->id] as $subjectId) {
                SubjectAssignment::create(['subject_id' => $subjectId, 'class_id' => $classId,
                    'group_id' => $businessGroup->id, 'assignment_type' => 'compulsory']);
            }

            // Humanities: compulsory
            foreach ([$history->id, $civics->id, $geography->id, $economics->id] as $subjectId) {
                SubjectAssignment::create(['subject_id' => $subjectId, 'class_id' => $classId,
                    'group_id' => $humanitiesGroup->id, 'assignment_type' => 'compulsory']);
            }

            // Humanities elective: choose 2 from pool
            foreach ([$socialWork->id, $arabic->id] as $subjectId) {
                SubjectAssignment::create(['subject_id' => $subjectId, 'class_id' => $classId,
                    'group_id' => $humanitiesGroup->id, 'assignment_type' => 'optional',
                    'exclusive_group_key' => 'humanities_elective', 'min_select' => 2, 'max_select' => 2]);
            }
        }
    }
}
```

---

## 🎨 ADMINLTE UI — Key Blade Components

### Student Subject Selection Page

```blade
{{-- resources/views/student/subject-selection.blade.php --}}

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        বিষয় নির্বাচন — {{ $student->name }} ({{ $class->name }}, {{ $student->group->name ?? 'N/A' }})
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.subjects.save', $student) }}" method="POST">
                        @csrf

                        {{-- COMPULSORY SUBJECTS (read-only) --}}
                        <div class="card card-success card-outline mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-lock"></i> বাধ্যতামূলক বিষয়সমূহ (Compulsory)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($subjectList['compulsory'] as $assignment)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                id="subj_{{ $assignment->subject_id }}"
                                                name="subjects[]"
                                                value="{{ $assignment->subject_id }}"
                                                checked disabled>
                                            <label class="custom-control-label" for="subj_{{ $assignment->subject_id }}">
                                                {{ $assignment->subject->name }}
                                                <small class="text-muted">({{ $assignment->subject->name_bn }})</small>
                                                <input type="hidden" name="subjects[]" value="{{ $assignment->subject_id }}">
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- EXCLUSIVE CHOICE GROUPS --}}
                        @foreach($subjectList['exclusive_groups'] as $groupKey => $group)
                        <div class="card card-warning card-outline mb-4">
                            <div class="card-header">
                                <h5>
                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                    বিষয় নির্বাচন করুন — ঠিক {{ $group->first()->min_select }}টি
                                    <span class="badge badge-warning">
                                        {{ $group->first()->min_select === $group->first()->max_select ? 'যেকোনো ১টি বেছে নিন' : "মিনিমাম {$group->first()->min_select}টি" }}
                                    </span>
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($group->first()->max_select == 1)
                                {{-- Radio buttons for single choice --}}
                                @foreach($group as $assignment)
                                <div class="custom-control custom-radio mb-2">
                                    <input type="radio" class="custom-control-input"
                                        id="excl_{{ $groupKey }}_{{ $assignment->subject_id }}"
                                        name="exclusive[{{ $groupKey }}]"
                                        value="{{ $assignment->subject_id }}"
                                        {{ in_array($assignment->subject_id, $studentCurrentSubjects) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="excl_{{ $groupKey }}_{{ $assignment->subject_id }}">
                                        {{ $assignment->subject->name }}
                                        <small class="badge badge-info">{{ $assignment->subject->code }}</small>
                                    </label>
                                </div>
                                @endforeach
                                @else
                                {{-- Checkboxes for multi-select --}}
                                @foreach($group as $assignment)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input exclusive-check"
                                        data-group="{{ $groupKey }}"
                                        data-min="{{ $group->first()->min_select }}"
                                        data-max="{{ $group->first()->max_select }}"
                                        id="excl_{{ $groupKey }}_{{ $assignment->subject_id }}"
                                        name="exclusive[{{ $groupKey }}][]"
                                        value="{{ $assignment->subject_id }}"
                                        {{ in_array($assignment->subject_id, $studentCurrentSubjects) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="excl_{{ $groupKey }}_{{ $assignment->subject_id }}">
                                        {{ $assignment->subject->name }}
                                    </label>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                        @endforeach

                        {{-- FOURTH/OPTIONAL SUBJECT --}}
                        @if($subjectList['fourth_subject']->count())
                        <div class="card card-info card-outline mb-4">
                            <div class="card-header">
                                <h5><i class="fas fa-plus-circle"></i> ঐচ্ছিক বিষয় (Optional / 4th Subject) — সর্বোচ্চ ১টি</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($subjectList['fourth_subject'] as $assignment)
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input"
                                                id="fourth_{{ $assignment->subject_id }}"
                                                name="fourth_subject"
                                                value="{{ $assignment->subject_id }}"
                                                {{ in_array($assignment->subject_id, $studentCurrentSubjects) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="fourth_{{ $assignment->subject_id }}">
                                                {{ $assignment->subject->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                    <div class="col-md-4 mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" id="fourth_none" name="fourth_subject" value="">
                                            <label class="custom-control-label" for="fourth_none">কোনোটি নয়</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> বিষয় নিশ্চিত করুন
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 📋 FORM REQUEST CLASSES

```php
class StudentSubjectSelectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'subjects'           => ['array'],
            'subjects.*'         => ['integer', 'exists:subjects,id'],
            'exclusive'          => ['array'],
            'exclusive.*'        => ['integer', Rule::exists('subjects', 'id')],
            'fourth_subject'     => ['nullable', 'integer', 'exists:subjects,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'exclusive.*.exists' => 'অবৈধ বিষয় নির্বাচন।',
        ];
    }
}

class SubjectClassConfigRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'creative_marks'    => ['required', 'integer', 'min:0', 'max:100'],
            'short_q_marks'     => ['integer', 'min:0', 'max:50'],
            'mcq_marks'         => ['required', 'integer', 'min:0', 'max:100'],
            'practical_marks'   => ['required', 'integer', 'min:0', 'max:100'],
            'viva_marks'        => ['integer', 'min:0', 'max:30'],
            'pass_mark'         => ['required', 'integer', 'min:1'],
            'cq_pass_mark'      => ['nullable', 'integer', 'min:1'],
            'mcq_pass_mark'     => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->all();
            $total = ($data['creative_marks'] ?? 0) + ($data['short_q_marks'] ?? 0)
                   + ($data['mcq_marks'] ?? 0) + ($data['practical_marks'] ?? 0)
                   + ($data['viva_marks'] ?? 0);

            if ($total === 0) {
                $validator->errors()->add('total', 'কমপক্ষে একটি নম্বর ক্ষেত্রে মান দিতে হবে।');
            }
            if (($data['pass_mark'] ?? 0) > $total) {
                $validator->errors()->add('pass_mark', 'পাস নম্বর মোট নম্বরের চেয়ে বেশি হতে পারবে না।');
            }
        });
    }
}
```

---

## 🔁 HSC SPECIAL RULES

### Multi-Year Paper Result Logic

HSC results are combined across two academic years (Class 11 + Class 12). Add a separate concern:

```php
class HscResultCombiner
{
    /**
     * HSC Final = Class 11 exam (1st papers) + Class 12 exam (2nd papers)
     */
    public function combineResult(Student $student): array
    {
        $class11Results = ExamResult::where('student_id', $student->id)
            ->whereHas('subject', fn($q) => $q->where('paper_number', 1))
            ->where('class_id', $this->class11Id())
            ->get();

        $class12Results = ExamResult::where('student_id', $student->id)
            ->whereHas('subject', fn($q) => $q->where('paper_number', 2))
            ->where('class_id', $this->class12Id())
            ->get();

        return $this->computeFinalGPA(
            $class11Results->merge($class12Results)
        );
    }
}
```

---

## ✅ KEY DIFFERENCES FROM ORIGINAL PROMPT

| Original Assumption | Bangladesh Reality | Fix Applied |
|---|---|---|
| Generic "group" concept | 3 specific groups: Science / Business Studies / Humanities | Named groups with `code` field |
| Simple exclusive (Biology OR Higher Math) | `min_select`/`max_select` fields for flexible exclusive groups (Humanities picks 2 of 5) | Added `min_select`, `max_select` to assignment |
| Single pass_mark | Separate CQ pass + MCQ pass (student can fail MCQ alone) | Added `cq_pass_mark`, `mcq_pass_mark` |
| Generic marks | Bangla 2nd Paper has NO MCQ; ICT is 75 marks | Config per-paper, not per-subject only |
| No paper_number | HSC 1st paper (Class 11) / 2nd paper (Class 12) are separate exam events | Added `paper_number` to subjects |
| No curriculum duality | New curriculum (Class 6–9) vs traditional (SSC/HSC) coexist | `assessment_system` + `curriculum_version` |
| Religion as optional | In Bangladesh, religion is compulsory but religion-gated | `religion` field on assignment with `compulsory` type |
| No 4th subject concept | Bangladesh has an official "4th subject" system for GPA boost | New `assignment_type = 'fourth_subject'` |
| Generic "elective" | Fourth subject uses radio (max 1), humanities uses checkbox (min 2 of pool) | UI driven by `min_select`/`max_select` |
| No Bangla names | All UI and data use both English + Bangla (বাংলা) names | `name_bn` field throughout |
| No board logic | 8 general boards + Madrasah board + Technical board | Add `board_type` to classes/exams |

---

## 📦 MODEL RELATIONSHIPS

```php
// Subject.php
class Subject extends Model
{
    use SoftDeletes;

    public function parent(): BelongsTo     { return $this->belongsTo(Subject::class, 'parent_id'); }
    public function papers(): HasMany        { return $this->hasMany(Subject::class, 'parent_id')->orderBy('paper_number'); }
    public function classConfigs(): HasMany  { return $this->hasMany(SubjectClassConfig::class); }
    public function assignments(): HasMany   { return $this->hasMany(SubjectAssignment::class); }
    public function studentSubjects(): HasMany { return $this->hasMany(StudentSubject::class); }

    public function getEffectiveConfig(int $classId): ?object
    {
        return $this->classConfigs()->where('class_id', $classId)->first()
            ?? $this->defaultConfig;
    }

    public function isAssignableDirectly(): bool
    {
        // Parent subjects cannot be directly assigned — only via paper expansion
        return !$this->is_parent;
    }
}
```

---
