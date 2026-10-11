# Platform Admin Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a separate reserved platform-admin subsystem that controls normal role permission visibility without migrating or changing institute Super Admin users.

**Architecture:** Mark reserved roles in the roles table, add a dedicated platform-admin visibility model/table, and enforce the visibility scope in RoleController validation and views. Add dedicated platform-admin user management protected by explicit platform-admin authorization; platform admins bypass all ordinary permission checks, while platform-admin users are filtered from ordinary user queries and counts.

**Tech Stack:** Laravel 11, PHP 8.2, Eloquent, Blade, PHPUnit feature tests, existing permission middleware.

**Spec:** `docs/superpowers/specs/2026-10-11-platform-admin-design.md`

## Global Constraints

- Do not migrate or modify existing institute Super Admin users.
- Platform admins must be able to access every permission-protected application module.
- Do not store the requested platform-admin password in source control.
- Do not expose `platform_admin` in normal Roles CRUD or normal user role selectors.
- Do not silently revoke permissions already assigned to existing roles when visibility is changed.
- Preserve unrelated uncommitted routine/mobile changes.

## Review Focus

- A tampered role form cannot assign a hidden permission: covered by RoleController feature tests.
- A non-platform-admin cannot reach Platform Admin URLs directly: covered by authorization tests.
- Platform admins are absent from every ordinary user count/query: covered by UserController tests.
- Reserved institute Super Admin remains intact but cannot be edited through ordinary Roles CRUD: covered by role protection tests.
- The final active platform admin cannot be accidentally removed: covered by platform-admin lifecycle tests.

### Task 1: Reserved role metadata and platform-admin visibility storage

**Files:**
- Create: migration for reserved role metadata and platform-admin visibility tables
- Create: `app/Models/PlatformAdminVisibility.php` or equivalent focused model
- Modify: `app/Models/Role.php`, `app/Models/User.php`
- Modify: `database/seeders/RoleSeeder.php`, `database/seeders/PermissionSeeder.php` only as needed
- Test: new feature test for seeded reserved roles and visibility defaults

- [ ] Write failing tests for reserved role identification, default protected-role hiding, and platform-admin role seeding.
- [ ] Run the focused test and confirm failure because the metadata and visibility behavior do not exist.
- [ ] Add the migration, model relationships, and idempotent seeder behavior.
- [ ] Run the focused test and confirm it passes.

### Task 2: Platform-admin authorization and dedicated user management

**Files:**
- Create: `app/Http/Middleware/EnsurePlatformAdmin.php`
- Create: `app/Http/Controllers/PlatformAdminController.php`
- Create: `resources/views/pages/platform-admins/index.blade.php`
- Create: `resources/views/pages/platform-admins/create.blade.php`
- Create: `resources/views/pages/platform-admins/edit.blade.php`
- Modify: `bootstrap/app.php`, `routes/web.php`, sidebar view
- Modify: `app/Models/User.php` with a focused `isPlatformAdmin()` helper
- Test: dedicated Platform Admin feature test

- [ ] Write failing tests for route denial, CRUD behavior, reserved-role assignment, count exclusion, and final-admin protection.
- [ ] Include a failing test proving a platform admin can access a route requiring a permission that is hidden from ordinary role management.
- [ ] Run the focused test and confirm the expected failures.
- [ ] Implement dedicated routes and controller actions using the existing user form conventions.
- [ ] Prevent deleting or deactivating the final active platform admin.
- [ ] Add the permission-visibility UI to the dedicated area.
- [ ] Run the focused test and confirm it passes.

### Task 3: Filter normal users and roles

**Files:**
- Modify: `app/Http/Controllers/UserController.php`
- Modify: `app/Http/Controllers/RoleController.php`
- Modify: normal user and role Blade views
- Test: extend focused role/user feature tests

- [ ] Write failing tests proving platform admins and the reserved platform role are absent from ordinary lists, counts, and selectors.
- [ ] Implement query scopes and role visibility filtering.
- [ ] Add server-side permission whitelist validation for role create/update.
- [ ] Protect ordinary edit/update/destroy actions for reserved roles.
- [ ] Run focused tests and confirm they pass.

### Task 4: Sidebar, permissions, and regression verification

**Files:**
- Modify: sidebar/hub navigation and permission-aware navigation helpers as needed
- Modify: existing role/user tests
- Create or modify: platform-admin feature tests

- [ ] Add the Platform Admin sidebar item only for platform admins.
- [ ] Verify normal Roles CRUD still renders permitted permission sections correctly.
- [ ] Run PHP lint, Blade cache, focused tests, and `git diff --check`.
- [ ] Run the full project test suite with the project-supported PHP/Docker runtime and report unrelated baseline failures separately.

### Task 5: Provision the requested account

- [ ] Run migrations and seeders in the supported application runtime.
- [ ] Create or update the platform-admin account using the supplied email and password without echoing or persisting the password in repository files.
- [ ] Verify the account has the reserved platform-admin role and is excluded from ordinary user counts.

