You are a senior Laravel developer and system architect working on an existing School Management System.

Your task is to design and implement a complete **Subject Management + Assignment + Student Selection System**, ensuring UI consistency with the current AdminLTE-based interface.

---

# 🎯 MODULES TO BUILD

1. Subject CRUD (Advanced)
2. Subject Assignment (Class-level rules)
3. Student Subject Selection
4. Combined Papers Logic
5. Class-wise Marks Configuration

---

# 🔵 1. SUBJECT (CRUD SETTINGS)

## Fields

- name (required)
- code (unique, optional)
- type (mandatory | optional)
- has_multiple_papers (boolean)
- combine_papers_for_result (boolean, default true)
- pass_mark (decimal, OPTIONAL → may vary per class)

### Marks Distribution (DEFAULT STRUCTURE)
- creative_marks (CQ)
- mcq_marks
- practical_marks
- viva_marks
- total_marks (AUTO CALCULATED)

- is_active (boolean)
- soft deletes enabled

---

## ⚠️ IMPORTANT: MARKS FLEXIBILITY

Marks can be defined in TWO levels:

### OPTION 1: Default (Subject Level)
Used as fallback

### OPTION 2: Class-wise Override (PRIMARY)

👉 Create table: subject_class_configs

Fields:
- subject_id
- class_id
- creative_marks
- mcq_marks
- practical_marks
- viva_marks
- total_marks (auto)
- pass_mark

---

## RULES

- total_marks = sum of all mark fields
- At least one mark field must be > 0
- pass_mark ≤ total_marks
- code must be unique if provided
- Subject update must NOT break existing assignments
- Soft delete must preserve historical integrity

---

# 🟣 2. COMBINED SUBJECT (MULTI-PAPER SYSTEM)

## Structure

Subjects can be:

### Single Subject
- Normal evaluation

### Combined Subject
Example:

Bangla (Parent)
 ├── Bangla 1st Paper
 └── Bangla 2nd Paper

---

## Database Extension

Add:

- parent_id (nullable)
- is_parent (boolean)
- is_paper (boolean)

---

## Rules

- Parent subject:
  - has_multiple_papers = true
  - cannot have marks directly (optional design)
  
- Papers:
  - linked via parent_id
  - have their own marks (class-wise)

---

## Result Logic Preparation

If combine_papers_for_result = true:

- Total marks = sum of all papers
- Pass condition:
  1. Combined total ≥ pass_mark
  2. Each paper must satisfy minimum threshold (if enabled)

---

# 🟡 3. SUBJECT ASSIGNMENT (CLASS LEVEL)

## Table: subject_assignments

Fields:
- subject_id
- class_id
- group_id (nullable)
- gender (all | male | female)
- religion (all | islam | hindu | etc.)
- is_optional (boolean)
- is_compulsory (boolean)
- exclusive_group_key (nullable)

---

## Rules

- Subject MUST be assigned before use
- No duplicate:
  (subject_id + class_id + group_id + gender + religion)
- NULL group_id = applies to all groups
- gender = all → no restriction
- religion = all → no restriction

---

# 🔴 4. SCIENCE GROUP EXCLUSIVE RULE

Subjects:
- Physics (mandatory)
- Chemistry (mandatory)
- Biology
- Higher Math

---

## Rule

Biology and Higher Math are mutually exclusive

---

## Setup

- exclusive_group_key = "science_core_choice"

Applied to:
- Biology
- Higher Math

---

## Validation

- Student MUST select ONLY ONE:
  → Biology OR Higher Math

- Cannot:
  ❌ select both
  ❌ mark both as compulsory

---

## Behavior

- Selected subject → mandatory
- Other subject → optional or unavailable

---

# 🟢 5. STUDENT SUBJECT ASSIGNMENT

## Table: student_subjects

Fields:
- student_id
- subject_id
- class_id
- is_optional
- is_mandatory

---

## Rules

Subject must match:

- class
- group
- gender
- religion

---

## Validation

- Cannot assign subject not available for class
- Cannot violate gender/religion rules
- Cannot select both Biology and Higher Math
- Must select one from exclusive group
- Optional subjects must be marked correctly

---

# 🔁 6. FLOW

1. Subject Created
2. Papers added (if combined)
3. Class-wise marks configured
4. Subject assigned to class with rules
5. Student gets filtered subject list
6. Student selects optional subjects
7. Final subject mapping saved

---

# ⚙️ 7. IMPLEMENTATION RULES

- Use Service Layer (SubjectService)
- Controllers must be thin
- Use FormRequest validation
- Use Eloquent relationships
- No business logic inside controllers
- Use transactions where needed
- Maintain data integrity

---

# 🎨 8. UI REQUIREMENTS (VERY IMPORTANT)

Match existing AdminLTE UI:

### Subject Form
- Toggle: "Has Multiple Papers"
- Toggle: "Combine Papers for Result"

---

### If SINGLE:
Show:
| Class | CQ | MCQ | Practical | Viva | Total | Pass |

---

### If COMBINED:
- Add Papers dynamically
- For each paper:
  - Show class-wise marks table

---

### Assignment UI
- Dropdown: Class
- Dropdown: Group
- Filters: Gender, Religion
- Checkbox:
  - Optional
  - Compulsory

---

### Student Selection UI
- Show ONLY allowed subjects
- Show exclusive group warning:
  "Select one: Biology OR Higher Math"

---

# 📊 OUTPUT REQUIRED

1. Migrations:
   - subjects
   - subject_class_configs
   - subject_assignments
   - student_subjects

2. Models with relationships

3. Service Layer (SubjectService)

4. Controllers (thin)

5. FormRequest validation classes

6. Blade UI (AdminLTE compatible)

7. Seeder examples

---

# 💡 FINAL GOAL

Build a **fully flexible academic subject engine** supporting:

- Combined subjects (multi-paper)
- Class-wise marks variation
- Smart subject assignment rules
- Student-level subject selection
- Science group exclusivity logic

This must be scalable like a real ERP system.