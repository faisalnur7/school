# School Management System — Feature List

This document lists the system's features, based on a review of the code (routes, controllers, services, models and the companion Flutter app) as of 2026-11-03.

- **Backend:** Laravel 11 (PHP 8.2+), Blade + Tailwind CSS, MySQL
- **PDF output:** mPDF with QR codes
- **Mobile API:** Laravel Sanctum (`/api/v1/student`)
- **Student mobile app:** Flutter (`school-student-app`): Riverpod, go_router, Dio, Firebase Cloud Messaging
- **Languages:** English and Bangla (বাংলা), switchable in the UI
- **Size:** about 900 web/API routes, about 190 Eloquent models, 21 service classes, 105 feature tests

> Features marked **(planned)** have a route or menu entry, but no working implementation yet (either the controller method is missing or the page only shows the dashboard). See [Planned / Not Yet Implemented](#planned--not-yet-implemented).

---

## Table of Contents

1. [Authentication & Security](#1-authentication--security)
2. [Users, Roles & Permissions](#2-users-roles--permissions)
3. [Dashboard & Hub Navigation](#3-dashboard--hub-navigation)
4. [Institute Setup](#4-institute-setup)
5. [Academics](#5-academics)
6. [Students](#6-students)
7. [Admissions (Online & Office)](#7-admissions-online--office)
8. [Attendance](#8-attendance)
9. [Exams & Results](#9-exams--results)
10. [Fees & Student Payments](#10-fees--student-payments)
11. [Accounting & Financial Reports](#11-accounting--financial-reports)
12. [Income, Expenses & Shareholders](#12-income-expenses--shareholders)
13. [Budgeting](#13-budgeting)
14. [Human Resources & Payroll](#14-human-resources--payroll)
15. [Assets & Facilities](#15-assets--facilities)
16. [Inventory & Purchasing](#16-inventory--purchasing)
17. [Transport](#17-transport)
18. [Certificates & ID Cards](#18-certificates--id-cards)
19. [Communication](#19-communication)
20. [Public Website](#20-public-website)
21. [Student Mobile App & API](#21-student-mobile-app--api)
22. [Platform Administration](#22-platform-administration)
23. [Background Jobs & CLI Commands](#23-background-jobs--cli-commands)
24. [Planned / Not Yet Implemented](#planned--not-yet-implemented)

---

## 1. Authentication & Security

- Login and logout with email and password (Laravel Breeze)
- **Two-step login verification:** a code is emailed to the user, who can request a new one
- **Verification code for password changes:** changing a password requires a code sent by email
- Forgot/reset password, email verification and password confirmation
- Per-user profile editing (account profile, change password)
- Staff can apply for leave from their own account (`account/leave`)
- **Audit trail:** middleware records user actions, and admins can browse the log
- Rate limiting on student mobile login and on the public contact form

## 2. Users, Roles & Permissions

- User management: create, edit, activate/deactivate, delete
- Promote a user to super admin
- **Roles** with assigned permissions
- **Permissions** grouped into **permission categories**
- Route-level permission checks (`CheckPermission` middleware)
- Users hub page

## 3. Dashboard & Hub Navigation

- Main dashboard
- Hub (landing) pages that group related features as cards:
  Academics, Students, Attendance, Results, Fees, Accounts, Financials, Budget, Assets, HR, Institute, Location, Shareholders, Users, Admissions
- Language switcher (English / Bangla), with translations for both languages

## 4. Institute Setup

- **School settings:** institute name, logo, contact details and other branding
- **Buildings** and **rooms** (room types: classroom, lab, office, library, gymnasium, storage, staff room, other)
- **Educational qualifications** master list
- **Professions** master list (for guardians)
- **Certificate types/templates** and **ID card templates**
- **Location data** (Bangladesh): divisions, districts, police stations (thana) and post offices, with cascading dropdowns

## 5. Academics

- **Academic sessions**: create, edit, activate/deactivate
- **Classes**, **sections** and **groups** (e.g. Science/Arts/Commerce), each with an active/inactive toggle
- **Subjects**
  - Assign subjects to classes; archive or remove assignments
  - Class-wise subject configuration: full marks, pass marks, CQ/MCQ/practical/viva marks
  - Control the order subjects appear in
  - Tutorial-exam flag for each subject
  - Optional (4th) subject support
  - Class-wise subject list: print and PDF
- **Student subject assignment**: one student at a time or in bulk
- **Teacher–section assignment** (assign class teachers and subject teachers)
- **Classrooms**
- **Class schedules** (time periods)
- **Class routine**
  - Drag-and-drop routine "playground", with a teacher-wise view
  - Routine print and PDF
- **Holidays and weekend settings**

## 6. Students

- **Student admission** (office entry), with automatic next roll number and student ID (CID)
- Student list with filters, export, PDF list and individual profile PDF
- Student profile view and edit; activate/deactivate
- **Promote students** to the next class/session
- **Mid-year correction** of academic information
- **Academic history** for each student
- **Student checkout** (leaving the school) and a checked-out list, with restore
- **Birthdays** list, with birthday card preview
- **Certificates hub**: testimonial, transfer certificate (TC) and general certificate, each with preview and PDF
- **Student ID cards**: settings, generation and PDF
- Create **mobile app accounts** for students
- Free studentship and scholarship assignment (see [Fees](#10-fees--student-payments))

## 7. Admissions (Online & Office)

**Public online admission portal**
- Online application form, with preview and confirmation
- Application fee payment step
- Search for an existing application
- Download the application PDF and the admit card

**Admin admission workflow**
- Applications list, with detail view and download
- Review applications one at a time or in bulk
- Collect or update admission payments
- **Admission exams**: create, edit, enable/disable, set class-wise exam settings
- Admit card generation for admission exams
- Admission exam **mark entry** (one at a time or in batches) and **results** (with PDF)
- Approved and converted applicant lists
- **Convert an applicant to a student**: assigns a roll number and applies fee sets automatically

## 8. Attendance

- **Daily student attendance**: load a class/section, then mark and update attendance
- **Monthly attendance report**, with PDF
- **Attendance settings:** access control, holidays and weekend days
- **Automatic absent alerts:**
  - Emails to guardians (every 10 minutes)
  - Push notifications to the student app (queued job, every 10 minutes)

## 9. Exams & Results

**Exam setup**
- Create, edit, delete and restore exams (types: final, midterm, quiz, assignment, practical; statuses: scheduled, ongoing, completed, cancelled)
- **Exam routine**: build it by exam/group, then print or download a PDF
- **Admit cards & seat cards**: settings and PDF generation

**Mark entry**
- Subject-wise and student-wise mark entry
- **CSV import/export** of marks
- Component marks: CQ, MCQ, practical, viva
- CLI commands to seed or sweep marks for a session

**Result calculation**
- Bangladesh GPA grading scale (A+ = 5.00 down to F)
- Pass checks by component, using the component's share of the pass mark
- **Multi-paper subjects**, with a pass mode configured by class (`combined` or `individual`)
- Absent handling
- **Merit position / ranking** (`ResultRankingService`)

**Reports**
- **Terminal result** view and PDF, with preview
- **Result sheets** (tabulation) for each exam
- **Progress report**, with component mark columns and failed subjects highlighted; template settings, PDF and **email to guardians**
- **Tutorial exam report**, with settings, PDF and email
- **Yearly final report** (combines all of the year's exams), with template settings, preview, PDF and email
- Email delivery status is tracked for each student
- Public result lookup on the website (see [Public Website](#20-public-website))

## 10. Fees & Student Payments

**Fee setup**
- **Fee categories**, with an active/inactive toggle
- **Fee sets** (bundles of fees by class), which can be duplicated; fee amount history is kept
- **Discount categories**, **scholarships** and **free studentships** (bulk assignment, with PDF lists)
- **Transport fees**

**Collection**
- Fee collection screen: search for a student, switch between students, pay by category or for an individual fee
- Payment receipts (printable)
- Edit and delete payments
- Payment methods include cash, bank, and mobile banking (bKash, Nagad, Rocket)
- **Payment requests:** students can request a payment from the mobile app, staff collect it at the office, and expired requests are removed automatically
- Collected payments post automatically to the accounting journal

**Fee reports (each with a PDF)**
- Student payment report
- Student receive report
- Student receivable report
- All-in-one fee report
- Class-wise due report
- Student due report, including a month-wise due PDF
- Student ledger
- Discount list

## 11. Accounting & Financial Reports

Double-entry accounting, with automatic journal posting from fees, income, expenses, purchases and vendor payments.

- **Chart of accounts**, with **account groups**
- **Accounting periods**, which can be closed
- **Journal entries** (view and reverse) and **transactions** (create, edit, PDF)
- **General ledger**
- **Bank accounts**, **mobile banking accounts** and **hand cash** (petty cash), with cash-to-bank transfers
- **Financial reports (each with a PDF):**
  - Trial balance, detailed trial balance
  - Balance sheet
  - Income & expenditure
  - Receipt & payment
  - Cash book, day book, cash summary, cash flow
  - Head-wise transactions
  - Supplier dues
  - Chart of accounts

## 12. Income, Expenses & Shareholders

- **Incomes** and **income categories**, with vouchers
- **Expenses** and **expense categories**, with vouchers
- **Shareholders** (capital): shareholder records, transactions (with vouchers), and a contribution report with PDF

## 13. Budgeting

- **Budget heads** and **budget allocations**
- **Budget vs actual** report, with PDF

## 14. Human Resources & Payroll

- HR dashboard
- **Employees**: profile, documents upload, payment (bank) information
- **Departments** and **designations** (active/inactive)
- **Employee ID cards**: settings and PDF
- **Leave management**: requests, approve/reject, leave balances
- **Salary structures**, with default salaries by designation
- **Payroll**: generate, preview, lock and mark as paid; salary slips
- **HR reports:** salary sheet, payroll summary, leave report, organisational hierarchy

## 15. Assets & Facilities

- **Asset categories** and an **asset register**
- **Asset purchases**, with vouchers and accounting entries
- **Asset issue register** (issue and return) and asset stock
- **Facility bookings** (book rooms or facilities), with a bookings hub

## 16. Inventory & Purchasing

- Product **categories** and **products**
- **Suppliers**
- **Opening stock**
- **Purchases**, with vouchers and supplier payments
- **Sales** of inventory items to students, linked to fee payments
- **Reports:** stock report, low-stock report

## 17. Transport

- Transport routes and vehicles (active/inactive)
- Assign students to transport in bulk, and list the students on each route
- Transport fees are collected through the fee module

## 18. Certificates & ID Cards

- **Certificate templates**, with placeholders, an active template and a visual designer
- Student certificates: testimonial, transfer certificate, custom certificates (PDF)
- **Student ID cards** and **employee ID cards**, with templates/settings and PDF (with QR codes)
- **Admit cards and seat cards** for exams and admissions

## 19. Communication

- **Notices**: create, edit, delete; shown on the website and in the mobile app
- **Internal chat / messaging**: conversations, read status, edit/delete messages, emoji reactions
- **Email settings** and an email/SMS log
- **Mobile push notifications** to students (Firebase Cloud Messaging)
- Automatic emails: login and password-change codes, absent alerts, result reports

## 20. Public Website

A public school website at `/website`:

- Home, About, custom pages (by URL slug)
- Teacher and staff directory, with profile pages
- Notices and events (list and detail pages)
- Academic calendar, holidays and exam schedule
- Photo gallery
- Contact form (rate-limited)
- **Online result check**, including the yearly final report
- Online admission portal (see [Admissions](#7-admissions-online--office))
- Data models for banners, sections, SEO metadata and site settings

## 21. Student Mobile App & API

**REST API** (`/api/v1/student`, Sanctum tokens; each feature can be turned on or off in settings):

| Feature | Endpoints |
|---|---|
| Auth | login (rate-limited), logout, profile (`me`) |
| Configuration | mobile configuration (enabled features, branding) |
| Dashboard | summary |
| Attendance | attendance history |
| Class routine | routine, routine PDF |
| Exam routine | exam schedule |
| Holidays | holiday list |
| Results | result options, list, detail |
| Fees | fees, payments, receipt, payment requests (create, view, cancel) |
| Notices / Homework | lists |
| Notifications | list, mark as read; device registration for push |

**Flutter app** (`school-student-app`)
- Login, with the session stored in secure storage
- Dashboard, plus a navigation drawer that shows only the enabled features
- Attendance, class routine, exam routine and results screens
- Payment request cart: pick fees and submit a request
- File downloads (PDFs), with support for web and mobile
- School branding loaded from the server
- Push notifications through Firebase

**Admin side**
- Mobile app settings, and turning individual features on or off
- Create student mobile accounts (from the UI or the CLI)

## 22. Platform Administration

- Platform admin accounts (separate from school users), with a one-time bootstrap setup link
- Platform admin permission management
- Mobile app feature control at the platform level (`EnsureMobileFeatureAvailable`)

## 23. Background Jobs & CLI Commands

| Command / Job | Schedule | Purpose |
|---|---|---|
| `attendance:send-absent-emails` | every 10 min | Email guardians of absent students |
| `SendStudentAbsentPushesJob` | every 10 min | Push absent alerts to the student app |
| `payment-requests:remove-expired` | every 10 min | Remove expired mobile payment requests |
| `students:create-mobile-account {student}` | manual | Create one student's app account |
| `students:create-mobile-accounts` | manual | Create app accounts in bulk (optionally reset existing passwords) |
| `results:seed-marks` / `results:sweep-marks` | manual | Seed or clean up exam marks for a session |

The Docker Compose stack includes `queue` and `scheduler` services for these jobs.

---

## Planned / Not Yet Implemented

These modules appear in the routes or menu, but have no working implementation yet:

| Area | Status |
|---|---|
| Analytics (dashboard, performance, trends, predictive, reports) | Placeholder pages |
| LMS (courses, assignments, content, digital classroom, video conference) | Placeholder pages |
| Lesson plans, lessons, topics, homework (web) | Controller methods missing |
| Online exams | Controller method missing |
| Library | Placeholder page |
| Health records, counseling, discipline | Placeholder pages / methods missing |
| Sports, clubs, activities, alumni, dormitory | Placeholder pages / methods missing |
| Parents module | Placeholder pages |
| Procurement (vendors, orders, budget, allocation) | Placeholder pages (purchasing works through Inventory) |
| Communication: email/SMS sending, parent messages, social integration | Placeholder pages |
| Settings: general, email, payment, backup | Placeholder pages (school settings work) |
| Legacy teacher, staff, timetable, ID card, result and marks CRUD routes | Controller methods missing; replaced by HR, Routines, ID Card generation and Exams |
| Website CMS admin (pages, sections, gallery, calendar, settings) | Controllers exist, but no admin routes are registered |
