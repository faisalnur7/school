# Student Mobile Application Plan

## Recommendation

Build an Android-first Flutter student application connected to the existing Laravel application.

The application will use:

- Flutter for the mobile frontend
- Laravel as the API and business-logic backend
- The existing MySQL database
- Laravel Sanctum for mobile authentication
- Firebase Cloud Messaging (FCM) for push notifications
- The Firebase Spark plan for no-cost FCM delivery

The application should not be a simple WebView. The current Laravel application is primarily an admin and teacher system. Its API is almost empty, and students are not currently linked to user accounts.

## Essential MVP scope

The first version should contain only these features:

1. Student login and account
2. Student dashboard
3. Attendance
4. Absent-student notifications
5. Class routine
6. Examination results
7. Fees and payment history
8. Notices and notifications
9. Homework
10. Student profile and account settings

Avoid implementing every existing school module in the first release.

## 1. Student login and account

The existing `users` table should be extended with a nullable, unique `student_id` column. A dedicated `student` role should be added.

Required functionality:

- Login using student CID or assigned username and password
- Forgot-password flow
- Logout
- Session and token management
- Profile photo
- Student name and CID
- Current class, section, and roll
- Change password
- Account activation and deactivation

Each account must be linked to exactly one student record.

The current `User` model supports roles and employees, while the `Student` model is separate. Add a student relationship and Sanctum support to the user model.

Student users must not be allowed to access admin or teacher web routes.

## 2. Student dashboard

The dashboard should display:

- Student name and photo
- Student CID
- Current class, section, and roll
- Today's attendance
- Attendance percentage
- Today's routine
- Latest result
- Current fee due
- Latest notices
- Unread notifications

The dashboard should load through one optimized API request where practical.

## 3. Attendance

Include:

- Daily attendance history
- Monthly attendance summary
- Present, absent, and leave status
- Attendance percentage
- Leave application and leave status, if required after the first release
- Absent notification

Only currently active students can receive attendance notifications.

An eligible student must satisfy:

```text
students.status = 1
student_academic_information.is_current = 1
student_academic_information.academic_status = active
```

Exclude:

- Graduated students
- Withdrawn students
- Transferred students
- Checked-out students
- Inactive student accounts
- Students without a current academic record

## 4. Class routine

Include:

- Today's routine
- Weekly routine
- Subject
- Teacher
- Room
- Period time

This feature is small, frequently used, and can reuse the existing routine data.

## 5. Results

Include:

- Tutorial results
- Terminal/progress results
- Yearly final results
- Subject-wise marks
- Grade
- GPA
- Failed subjects
- Result PDF download

The Flutter application must not duplicate result calculations. Laravel should expose the existing result logic through secured API resources so mobile results remain consistent with the web reports.

Historical exam marks must remain available even if a current subject assignment has later been deactivated.

## 6. Fees

The first release should provide read-only fee information:

- Total payable
- Paid amount
- Due amount
- Payment history
- Payment date
- Payment method
- Receipt download

Do not add online payment to the first release unless the payment gateway and reconciliation flow are already fully verified.

## 7. Notices and notifications

Support:

- School notices
- Exam announcements
- Holiday announcements
- Result publication alerts
- Attendance alerts
- Fee reminders
- Routine changes
- Homework notices

FCM should be used only for delivery. Laravel should decide who receives each notification and should remain the source of truth.

## 8. Homework

The first version should show:

- Subject
- Homework title
- Description
- Attachment
- Publish date
- Due date

Student submissions, teacher feedback, and grading should be postponed until the basic read-only workflow is stable.

## Backend architecture

### Authentication

Use Laravel Sanctum for mobile API tokens.

Recommended approach:

- Add `student_id` to `users`
- Add a dedicated student role
- Add `User::student()` relationship
- Add Sanctum's API token support
- Add student-only API middleware
- Add policies for student-owned resources

The API must determine the student from the authenticated account. It must never trust an arbitrary `student_id` supplied by Flutter.

### API versioning

Use versioned endpoints:

```text
POST   /api/v1/student/login
POST   /api/v1/student/logout
GET    /api/v1/student/me
POST   /api/v1/student/devices
DELETE /api/v1/student/devices/{device}

GET    /api/v1/student/dashboard
GET    /api/v1/student/routine
GET    /api/v1/student/attendance
POST   /api/v1/student/leave-requests
GET    /api/v1/student/results
GET    /api/v1/student/results/{result}
GET    /api/v1/student/fees
GET    /api/v1/student/payments
GET    /api/v1/student/notices
GET    /api/v1/student/homework
GET    /api/v1/student/notifications
PATCH  /api/v1/student/notifications/{notification}/read
```

Use API Resources or DTOs rather than returning raw Eloquent models.

### Device-token table

Create a `student_devices` table:

```text
id
user_id
student_id
fcm_token
platform
app_version
last_seen_at
is_active
created_at
updated_at
```

A student may use multiple devices. The same token must not be stored repeatedly.

### Notification log

Create a `mobile_notification_logs` table:

```text
id
student_id
device_id
notification_type
reference_type
reference_id
unique_event_key
sent_at
delivery_status
error_message
created_at
updated_at
```

The `unique_event_key` prevents duplicate notifications, for example:

```text
attendance:2026-09-27:student:123
```

The existing `mobile_notifications` table is currently only a timestamp placeholder. It can be expanded for an in-app inbox or replaced with a dedicated notification design.

## Active-student audience service

Create one reusable service, such as `StudentAudienceService`, and use it for every student notification.

It should provide audiences such as:

- All active students
- Active students in a session
- Active students in a class
- Active students in a section
- Active students absent on a specific date

Every audience query must apply the current active-student rules. This prevents attendance, fee, result, notice, and homework modules from implementing inconsistent filters.

## Absent-notification workflow

When attendance is saved:

1. The teacher submits attendance.
2. Laravel identifies students whose status is `absent`.
3. Laravel checks each student through `StudentAudienceService`.
4. Laravel finds the student's active device tokens.
5. Laravel queues an FCM notification job.
6. The job sends the notification.
7. Laravel records success or failure.
8. Duplicate notifications for the same student and date are blocked.

Example notification:

```text
Title: Attendance Alert
Message: Your attendance was marked absent today.
```

Do not place sensitive result or fee details in notification previews. The app should load private details after the student taps the notification.

## FCM design

In Flutter:

- Install `firebase_messaging`
- Request notification permission
- Retrieve the FCM token
- Send the token to Laravel after login
- Handle token refresh
- Remove or deactivate the token on logout
- Handle foreground notifications
- Handle background notifications
- Handle notifications that open a terminated app
- Navigate to the correct screen when a notification is tapped

In Laravel:

- Store the Firebase server credential only on the server
- Never include the server credential in Flutter
- Send through the Firebase Admin SDK or HTTP v1 API
- Use queue jobs
- Retry temporary failures
- Remove invalid and expired tokens

Use individual device tokens for private notifications such as results, attendance, and fees.

Use topics for broad announcements such as school-wide notices or class-wide routine changes.

## Flutter architecture

Use a feature-based structure:

```text
lib/
  core/
    network/
    storage/
    auth/
    notifications/
    routing/
    theme/
  features/
    authentication/
    dashboard/
    attendance/
    routine/
    results/
    fees/
    notices/
    homework/
    profile/
```

Recommended packages:

- `dio` for API communication
- `flutter_secure_storage` for tokens
- `go_router` for navigation
- `firebase_messaging` for push notifications
- `riverpod` for state management
- `freezed` and `json_serializable` for typed API models

Flutter should handle presentation and local state. Laravel should handle authorization, business rules, calculations, and data filtering.

## Efficient development sequence

### Phase 1: Backend foundation

Deliver:

- Student account linking
- Student role
- Sanctum authentication
- Student-only authorization
- Base API response format
- API validation and error format
- API logging

Acceptance criteria:

- A student can log in.
- A student cannot access another student's records.
- An inactive student cannot log in.
- Admin and teacher accounts remain unaffected.

### Phase 2: Flutter foundation

Deliver:

- Flutter project setup
- Theme and design system
- Routing
- Login screen
- Secure token storage
- API client
- Logout
- Loading, error, and empty states

Acceptance criteria:

- Login survives app restart.
- Logout clears local credentials.
- Expired tokens redirect to login.
- Network failures show a useful retry state.

### Phase 3: Dashboard and profile

Deliver:

- Dashboard API
- Dashboard screen
- Profile screen
- Pull-to-refresh
- Current academic information

Acceptance criteria:

- The dashboard loads with minimal requests.
- No admin-only information is exposed.

### Phase 4: Attendance and push notifications

Deliver:

- Attendance API
- Attendance screen
- FCM project and Android configuration
- Device-token registration
- Absent-notification job
- Duplicate prevention
- Token cleanup

Acceptance criteria:

- Only active absent students receive alerts.
- Graduated, withdrawn, transferred, and checked-out students receive nothing.
- One absence creates only one notification per day.

### Phase 5: Routine and results

Deliver:

- Today's routine
- Weekly routine
- Tutorial results
- Terminal/progress results
- Yearly results
- PDF download

Acceptance criteria:

- Mobile results match the existing Laravel reports.
- Failed subjects and grades are consistent.
- Students can see only their own results.

### Phase 6: Fees, notices, and homework

Deliver:

- Fee summary
- Payment history
- Receipt download
- Notices
- Homework list
- Notification inbox

Acceptance criteria:

- Fee data matches the admin system.
- Private fee information is never sent through public topics.
- Notice and homework links open the correct Flutter screen.

### Phase 7: Hardening and release

Deliver:

- Security review
- API feature tests
- Notification tests
- Device testing
- Crash logging
- Release signing
- APK distribution process
- Backup and rollback procedure

## Testing requirements

Test at API level:

- Student-to-student data access
- Expired-token behavior
- Account deactivation
- Multiple devices per student
- Token refresh
- Graduated-student exclusion
- Withdrawn-student exclusion
- Transferred-student exclusion
- Duplicate notification prevention
- Invalid FCM-token cleanup

Test on physical Android devices:

- Foreground notification
- Background notification
- Terminated-app notification
- Notification tap navigation
- Poor network connection
- App restart
- Logout and login with another account
- PDF download
- Empty attendance, result, fee, and homework states

## Features to postpone

Do not include these in the first release:

- Online exams
- Chat
- Library management
- Transport tracking
- Clubs and sports
- Counseling
- Health records
- Digital certificates
- Online fee payment
- Parent accounts
- Complex homework submission

These features can be added after the MVP is stable and actual student usage is understood.

## No-subscription deployment

Use:

- Existing Laravel server
- Existing MySQL database
- Firebase FCM Spark plan
- Flutter Android APK
- Laravel queue worker

Avoid:

- Firebase Phone Authentication
- Firebase Cloud Functions
- Firestore
- Firebase Storage
- SMS OTP
- Paid notification platforms

The remaining operational costs may include the existing Laravel server, domain, internet connection, and optional app-store distribution. FCM itself does not require a monthly notification subscription.

## Final MVP definition

The first production release is complete when a currently active student can:

1. Log in securely.
2. View their dashboard.
3. View their routine.
4. View their attendance.
5. Receive an absent notification.
6. View their results.
7. View their fee balance and payment history.
8. Read school notices.
9. View homework.
10. Receive and open relevant push notifications.

The most important engineering rules are:

- Never trust a student ID supplied by the mobile client.
- Apply active-student filtering centrally.
- Keep private student notifications targeted to device tokens.
- Keep result calculations in Laravel.
- Queue notification delivery.
- Keep the first release small enough to test properly.
