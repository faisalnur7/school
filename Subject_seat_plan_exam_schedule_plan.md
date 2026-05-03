Here’s a **clean, practical implementation plan** for your school management system covering:

* Subject Assignation (Class & Student)
* Exam CRUD (with types)
* Seat Plan (Building → Room → Capacity)
* Admit Card
* Exam Schedule

I’ll keep it **developer-focused + ready to implement (Laravel-style thinking)**.

---

> **STATUS: Already implemented** (see existing subject_assignments and student_subjects tables)

# 1. Subject Assignation System

## A. Database Design

### subjects

* id
* name
* code
* total_marks
* cq_marks
* mcq_marks
* practical_marks
* viva_marks
* pass_marks

---

### class_subjects (Assign subjects to class)

* id
* class_id
* subject_id

👉 A class can have multiple subjects

---

### student_subjects (Optional – if elective subjects exist)

* id
* student_id
* subject_id

👉 Only needed if:

* Optional subjects
* Group-based subjects (Science/Commerce/Arts)

---

## B. Logic Flow

### Assign Subjects to Class

* Admin selects class
* Select multiple subjects
* Save in `class_subjects`

### Assign Subjects to Student (if needed)

* Based on class subjects OR manual override

---

> **STATUS: Implemented** (2026_04_16_000001 - enhances exam table; models updated)

# 2. Exam Management (CRUD)

## A. exams table

* id
* name
* type → ENUM: `tutorial`, `term`
* class_id
* year
* start_date
* end_date
* status (draft/published)

---

## B. exam_subjects (Subjects under exam)

* id
* exam_id
* subject_id
* exam_date
* start_time
* end_time
* full_marks

---

## C. exam_marks (Result entry)

* id
* exam_id
* student_id
* subject_id
* cq_marks
* mcq_marks
* practical_marks
* viva_marks
* total

---

## D. Features

* CRUD for Exam
* Attach subjects automatically from class
* Publish / Draft system
* Marks entry per subject

---

> **STATUS: Buildings & Rooms already exist** (see Building, Room models + seeders), Seat Plan tables implemented

# 3. Building & Room ভিত্তিক Seat Plan

## A. buildings

* id
* name

---

## B. rooms

* id
* building_id
* room_name
* capacity

---

## C. seat_plans

* id
* exam_id
* class_id

---

## D. seat_plan_students

* id
* seat_plan_id
* student_id
* room_id
* seat_number

---

## E. Seat Generation Logic

### Step-by-step:

1. Get students of class
2. Get rooms sorted by capacity
3. Assign sequentially

```php
foreach ($students as $index => $student) {
    assign room based on remaining capacity
    assign seat_number++
}
```

👉 Ensure:

* No room exceeds capacity
* Roll-wise distribution

---

> **STATUS: Implemented** (via exam_subjects table)

# 4. Exam Schedule

## Table: exam_schedules

(Alternative: use `exam_subjects` directly)

* id
* exam_id
* subject_id
* date
* start_time
* end_time

👉 This is basically your **routine generator**

---

## Features

* Auto-generate from subjects
* Manual edit option
* Conflict validation:

  * Same class → no overlapping exams

---

> **STATUS: Implemented** (2026_04_16_000006 - admit_cards table)

# 5. Admit Card System

## A. admit_cards

* id
* exam_id
* student_id
* roll_number
* seat_plan_id

---

## B. What Admit Card Should Show

* School Info
* Student Info
* Exam Name
* Subject Schedule
* Room + Seat Number

---

## C. Data Source Mapping

| Data     | Source             |
| -------- | ------------------ |
| Student  | students           |
| Subjects | exam_subjects      |
| Seat     | seat_plan_students |
| Room     | rooms              |

---

## D. Generate Logic

```php
$student = Student::with([
    'class',
    'examSubjects',
    'seatPlan.room'
])->find($id);
```

---

# 6. UI Flow (Very Important)

## Admin Panel Flow

### Step 1:

👉 Create Subjects

### Step 2:

👉 Assign Subjects to Class

### Step 3:

👉 Create Exam (Tutorial / Term)

### Step 4:

👉 Generate Exam Schedule

### Step 5:

👉 Create Seat Plan

* Select Exam
* Select Class
* Auto assign rooms

### Step 6:

👉 Generate Admit Cards

---

# 7. Smart Enhancements (Recommended)

### ✅ Auto Seat Shuffle

* Prevent same bench friends sitting together

### ✅ Subject Gap Rule

* No 2 hard subjects consecutively

### ✅ Room Balancing

* Mix students from different sections

### ✅ PDF Export

* Admit Card
* Seat Plan
* Exam Routine

---

# 8. Seeder Ideas (Optional)

### Buildings

* Academic Building A
* Academic Building B

### Rooms

* A-101 (40)
* A-102 (50)
* B-201 (60)

---

# 9. API / Controller Structure

### Controllers

* SubjectController
* ClassSubjectController
* ExamController
* ExamScheduleController
* SeatPlanController
* AdmitCardController

---

# 10. Final Architecture Overview

```
Class
 ├── Subjects
 ├── Students
 └── Exams
       ├── Subjects (Schedule)
       ├── Seat Plan
       │     ├── Rooms
       │     └── Students توزيع
       └── Admit Cards
```

---
