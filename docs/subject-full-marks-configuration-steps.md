# Subject Full Marks Configuration Steps

This guide compares the supplied result sheets with the current database configuration and explains how to correct the mismatches.

## 1. Create the missing Handwriting subject

The database currently has no `Handwriting` subject.

Create it with:

- **Subject name:** Handwriting
- **Type:** Mandatory
- **Full marks:** 30
- **Pass mark:** 9.9
- **Include in Tutorial Exam:** Enable only if required
- **Assign to:** Play and KG/Nursery

## 2. Use class-wise marks configuration

For a subject that has different marks in different classes:

1. Open **Subjects → Edit**.
2. Open the **Class-wise marks** section.
3. Select the required class.
4. Enter the class-specific marks.
5. Set the pass mark to 33% of the full marks.
6. Save the subject.
7. Verify the class-wise row after saving.

Use class-wise configuration instead of changing the global subject marks when only one class differs.

### Pass-mark reference

| Full marks | Pass mark |
|---:|---:|
| 100 | 33 |
| 80 | 26.4 |
| 75 | 24.75 |
| 50 | 16.5 |
| 30 | 9.9 |
| 20 | 6.6 |

## 3. Configure Play

| Subject | Full marks | Pass mark |
|---|---:|---:|
| Bangla | 100 | 33 |
| English | 100 | 33 |
| Mathematics | 100 | 33 |
| Drawing | 50 | 16.5 |
| General Knowledge | 20 | 6.6 |
| Spoken English | 20 | 6.6 |
| Handwriting | 30 | 9.9 |

Actions:

- Change Play **Computer** from 0 to 20, or archive it if it is not part of the sheet.
- Archive **Reading & Writing** if it is not part of the Play result sheet.

## 4. Configure KG/Nursery

| Subject | Full marks | Pass mark |
|---|---:|---:|
| Bangla | 100 | 33 |
| English | 100 | 33 |
| Mathematics | 100 | 33 |
| Religion | 50 | 16.5 |
| Drawing | 50 | 16.5 |
| General Knowledge | 30 | 9.9 |
| Computer | 20 | 6.6 |
| Spoken English | 20 | 6.6 |
| Handwriting | 30 | 9.9 |

Correct these database values:

- General Knowledge: 100 or 20 → 30
- Spoken: 50 → 20
- Selected religion: 30 or 100 → 50
- Add Handwriting: 30

Keep only the religion applicable to the student. Archive the other religion assignments.

## 5. Configure Class One

The sheet shows `Al-Quran / Religion` as one row with 80 marks, but the database stores Quran and Religion as separate subjects.

Other Class One values are:

| Subject | Full marks |
|---|---:|
| Bangla | 100 |
| English | 100 |
| Mathematics | 100 |
| Drawing | 50 |
| General Knowledge | 30 |
| Reading & Writing | 30 |
| Computer | 20 |
| Spoken | 20 |

Do not assign every religion subject. Keep only the correct religion for the student.

Decide whether Quran and Religion should appear as two separate report rows or be combined into one report row. The database cannot reproduce the sheet's single 80-mark row without a report aggregation rule.

## 6. Configure Class Two

Use these marks:

| Subject | Full marks |
|---|---:|
| Bangla | 100 |
| English | 100 |
| Mathematics | 100 |
| Al-Quran / applicable Religion | 30 |
| Drawing | 50 |
| General Knowledge | 30 |
| Reading & Writing | 30 |
| Computer | 20 |
| Spoken | 20 |

Archive the extra religion assignments. Keep only the applicable religion.

## 7. Configure Classes Three and Four

The main values already match the sheet:

- Bangla, English, Mathematics, Science, Bangladesh & World Studies: 100
- Applicable religion: 100
- Drawing: 50
- General Knowledge: 30
- Reading & Writing: 30
- Computer: 20
- Spoken: 20

Archive the religion assignments that do not apply to the student.

## 8. Configure Class Five

Use:

- Bangla 1st, English 1st, Mathematics, Science, Bangladesh & World Studies: 100
- Bangla 2nd: 50
- English 2nd: 50
- Applicable religion: 100
- General Knowledge and Reading & Writing: 30
- Computer and Spoken: 20

Correct:

- Bangla 2nd: 100 → 50
- English 2nd: 100 → 50

## 9. Configure Class Six

Use:

- Bangla 1st, English 1st, Mathematics, Science, Bangladesh & Global Studies: 100
- Bangla 2nd: 50
- English 2nd: 50
- Agriculture: 50
- ICT: 50
- Drawing: 50
- General Knowledge: 30
- Reading & Writing: 30
- Spoken English: 20
- Computer: 20
- Applicable religion: 100

Correct these values:

- Bangla 2nd: 100 → 50
- English 2nd: 100 → 50
- Agriculture: 100 → 50
- ICT: 75 → 50
- Drawing: 100 → 50
- General Knowledge: 100 → 30
- Reading & Writing: 100 → 30
- Spoken & Communication: 100 → 20

## 10. Configure Class Seven

Use the same corrections as Class Six:

- Bangla 2nd: 50
- English 2nd: 50
- Agriculture: 50
- ICT: 50
- General Knowledge: 30
- Reading & Writing: 30
- Spoken English: 20
- Drawing: 50
- Computer: 20

## 11. Configure Class Eight

Use:

- Bangla 1st, English 1st, Mathematics, Science, Bangladesh & Global Studies: 100
- Bangla 2nd: 50
- English 2nd: 50
- Agriculture: 50
- ICT: 50
- General Knowledge and Reading & Writing: 30
- Computer and Spoken: 20
- Applicable religion: 100

Correct:

- Bangla 2nd: 100 → 50
- General Knowledge: 100 → 30

## 12. Configure Class Nine groups

### Science group

- Common subjects: 100
- Physics, Chemistry, Biology, Higher Mathematics: 100
- ICT: 50
- General Knowledge and Reading & Writing: 30
- Computer and Spoken: 20
- Applicable religion: 100

### Business Studies group

- Agriculture: 100
- ICT: 50
- General Knowledge and Reading & Writing: 30
- Computer and Spoken: 20
- Accounting, Finance & Banking, Business Entrepreneurship: 100

Correct Business Studies Agriculture:

- 50 → 100

### Humanities group

- Bangladesh History & World Civilization: 100
- Geography & Environment: 100
- Civics & Citizenship: 100
- Economics: 100

Assign group-specific subjects only to their correct group. Do not leave them as global class assignments.

## 13. Clean duplicate religion assignments

For each class:

1. Open the religion subject assignment.
2. Keep the religion applicable to the student.
3. Archive the other religion assignments.
4. Ensure there is no active class-wide religion assignment when religion-specific assignments exist.

This prevents multiple religion subjects from appearing in the result.

## 14. Verify the configuration

After saving each class configuration:

1. Open the class-wise subject list.
2. Confirm the subject full marks.
3. Confirm the pass mark is 33%.
4. Enter a sample mark.
5. Open the result report.
6. Confirm the displayed Full Marks matches the sheet.
7. Confirm the total, percentage, GPA, and grade update correctly.

The most urgent corrections are Handwriting, Play Computer, KG values, Classes Five–Eight second-paper values, Class Six–Seven component values, and Class Nine Business Studies Agriculture.
