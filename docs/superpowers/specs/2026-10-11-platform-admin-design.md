# Platform Admin Design

## Goal

Add a separate, reserved `platform_admin` role that controls which permissions and protected institute roles are exposed in normal role management, while preserving the existing institute `Super Admin` role and behavior.

## Decisions

- Platform admins have unrestricted application access. `User::hasPermission()` and permission middleware must recognize the reserved platform-admin role as a full bypass.
- Existing `is_super_admin` users are not migrated.
- The institute `Super Admin` remains an institute-level privileged role and remains in normal user counts unless filtered by an existing project rule.
- `platform_admin` is a reserved system role and is not exposed in normal Roles CRUD or normal user role selectors.
- Platform-admin users are excluded from normal user lists, role tabs, and user counts.
- Permission visibility is separate from runtime permissions. Hiding a permission removes it from normal role create/edit forms and rejects future assignment; it does not silently revoke permissions already assigned to existing roles.
- The institute `Super Admin` role is hidden from normal role management and user role selectors by default. Platform admins can expose or hide protected institute roles through the dedicated Platform Admin settings.
- Platform Admin routes are protected by a dedicated server-side authorization check, not only by sidebar visibility.
- The requested platform-admin account is provisioned without storing its password in source control.

## Architecture

Add a stable system marker to roles so reserved roles are identified by data, not display names. Add a platform-admin visibility table that stores the permissions and protected institute roles exposed to ordinary role management. Add a dedicated controller, routes, views, and sidebar entry for managing platform-admin users and visibility settings.

The existing `is_super_admin` bypass remains unchanged. Platform-admin authorization is an additional full bypass; it does not replace or migrate institute Super Admin accounts. Platform-admin users can access every application route, including routes whose permission is hidden from ordinary role management.

## Acceptance Criteria

1. A seeded `platform_admin` role exists and cannot be renamed, deleted, or shown in normal Roles CRUD.
2. Platform-admin users can access every permission-protected application module.
3. Platform-admin users can be created, edited, activated/deactivated, and deleted only from the dedicated Platform Admin area.
4. Non-platform-admin users receive HTTP 403 for Platform Admin routes.
5. Platform-admin users do not appear in normal user lists, role tabs, total counts, or normal user role selectors.
6. Normal role create/edit pages show only platform-admin-approved permissions and cannot assign hidden permissions through tampered requests.
7. Normal role create/edit pages cannot expose or modify the institute `Super Admin` role.
8. Existing institute Super Admin users and `is_super_admin` authorization continue to work unchanged.
9. The requested platform-admin account is created with the supplied email and password during provisioning only.
