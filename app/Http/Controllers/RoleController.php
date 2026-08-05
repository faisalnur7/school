<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    private function ensureDashboardPermission(array $permissionIds): array
    {
        $dashboardPermissionId = Permission::where('name', 'view_dashboard')->value('id');

        if ($dashboardPermissionId) {
            $permissionIds[] = $dashboardPermissionId;
        }

        $permissionIds = array_values(array_unique(array_filter($permissionIds, fn ($id) => $id !== null && $id !== '')));

        return $permissionIds;
    }

    private function permissionColumns(): array
    {
        return [
            'card' => 'Card View',
            'view' => 'View',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
        ];
    }

    private function permissionActionMetadata(string $name): array
    {
        $map = [
            'view_card_' => 'card',
            'view_' => 'view',
            'create_' => 'create',
            'edit_' => 'update',
            'update_' => 'update',
            'delete_' => 'delete',
            'manage_' => 'update',
        ];

        foreach ($map as $prefix => $column) {
            if (str_starts_with($name, $prefix)) {
                $resource = substr($name, strlen($prefix));
                $resource = preg_replace('/^all_/', '', $resource);

                return [$column, $resource];
            }
        }

        return ['view', preg_replace('/^all_/', '', $name)];
    }

    private function permissionCategoryOrder(): array
    {
        return [
            'Dashboard' => 1,
            'Academics' => 2,
            'Students' => 3,
            'Result Management' => 4,
            'Attendance' => 5,
            'Fee Collection' => 6,
            'Accounts' => 7,
            'Reports' => 8,
            'Inventory' => 9,
            'Financials' => 10,
            'Shareholders' => 11,
            'HR & Payroll' => 12,
            'Assets' => 13,
            'Budget Control' => 14,
            'Institute Settings' => 15,
            'User & Roles' => 16,
            'Audit Trail' => 17,
            'Location Settings' => 18,
            'Communications' => 19,
            'Website Management' => 20,
        ];
    }

    private function buildPermissionSections(): array
    {
        $permissions = Permission::with('category')->orderBy('id')->get();
        $categoryOrder = $this->permissionCategoryOrder();
        $columnKeys = array_keys($this->permissionColumns());

        return $permissions
            ->groupBy(fn ($permission) => $permission->category?->name ?? 'General')
            ->sortBy(fn ($group, $category) => $categoryOrder[$category] ?? 999)
            ->map(function ($group, $category) use ($columnKeys) {
                $rows = $group
                    ->groupBy(function ($permission) {
                        [, $resource] = $this->permissionActionMetadata($permission->name);
                        return $resource;
                    })
                    ->sortBy(fn ($group) => $group->min('id'))
                    ->map(function ($group, $resource) use ($columnKeys) {
                        $cells = array_fill_keys($columnKeys, null);

                        foreach ($group as $permission) {
                            [$column] = $this->permissionActionMetadata($permission->name);
                            if (array_key_exists($column, $cells)) {
                                $cells[$column] = $permission;
                            }
                        }

                        return [
                            'key' => $resource,
                            'label' => Str::headline($resource),
                            'cells' => $cells,
                            'permission_ids' => $group->pluck('id')->all(),
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'name' => $category,
                    'slug' => Str::slug($category),
                    'rows' => $rows,
                ];
            })
            ->values()
            ->all();
    }

    public function index()
    {
        $roles = Role::withCount('permissions')->paginate(15);
        return view('pages.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionSections = $this->buildPermissionSections();
        $permissionColumns = $this->permissionColumns();
        $selectedPermissionIds = old('permissions', []);

        return view('pages.roles.create', compact('permissionSections', 'permissionColumns', 'selectedPermissionIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($validated) {
            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync($this->ensureDashboardPermission($validated['permissions'] ?? []));
        });

        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    public function edit(Role $role)
    {
        $rolePermissions = $role->permissions()->pluck('id')->toArray();
        $permissionSections = $this->buildPermissionSections();
        $permissionColumns = $this->permissionColumns();
        $selectedPermissionIds = old('permissions', $rolePermissions);

        return view('pages.roles.edit', compact('role', 'permissionSections', 'permissionColumns', 'selectedPermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($validated, $role) {
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            $role->permissions()->sync($this->ensureDashboardPermission($validated['permissions'] ?? []));
        });

        return redirect()->route('roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        if (!auth()->user()->is_super_admin) {
            return redirect()->route('roles.index')->with('error', 'Only Super Admin can delete roles');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
