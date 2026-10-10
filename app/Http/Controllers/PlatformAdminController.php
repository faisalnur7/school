<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlatformAdminController extends Controller
{
    private function platformRole(): Role
    {
        return Role::where('system_key', 'platform_admin')->firstOrFail();
    }

    public function index()
    {
        $platformRole = $this->platformRole();
        $platformAdmins = User::where('role_id', $platformRole->id)->latest('id')->get();
        $permissionSections = $this->buildPermissionSections();
        $permissionColumns = $this->permissionColumns();
        $selectedPermissionIds = Permission::where('exposed_to_roles', true)->pluck('id')->all();

        return view('pages.platform-admins.index', [
            'platformAdmins' => $platformAdmins,
            'permissionSections' => $permissionSections,
            'permissionColumns' => $permissionColumns,
            'selectedPermissionIds' => $selectedPermissionIds,
        ]);
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
                return [$column, preg_replace('/^all_/', '', substr($name, strlen($prefix)))];
            }
        }

        return ['view', preg_replace('/^all_/', '', $name)];
    }

    private function buildPermissionSections(): array
    {
        $permissions = Permission::with('category')->orderBy('id')->get();
        $columnKeys = array_keys($this->permissionColumns());

        return $permissions
            ->groupBy(fn ($permission) => $permission->category?->name ?? 'General')
            ->sortBy(fn ($group, $category) => $this->permissionCategoryOrder()[$category] ?? 999)
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
                    })->values()->all();

                return [
                    'name' => $category,
                    'slug' => Str::slug($category),
                    'rows' => $rows,
                ];
            })->values()->all();
    }

    private function permissionCategoryOrder(): array
    {
        return [
            'Dashboard' => 1, 'Academics' => 2, 'Students' => 3,
            'Result Management' => 4, 'Attendance' => 5, 'Fee Collection' => 6,
            'Accounts' => 7, 'Reports' => 8, 'Inventory' => 9, 'Financials' => 10,
            'Shareholders' => 11, 'HR & Payroll' => 12, 'Assets' => 13,
            'Budget Control' => 14, 'Institute Settings' => 15, 'User & Roles' => 16,
            'Audit Trail' => 17, 'Location Settings' => 18, 'Communications' => 19,
            'Website Management' => 20,
        ];
    }

    public function create()
    {
        return view('pages.platform-admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $this->platformRole()->id,
            'is_active' => true,
        ]);

        return redirect()->route('platform-admins.index')->with('success', 'Platform admin created successfully.');
    }

    public function edit(User $user)
    {
        abort_unless($user->isPlatformAdmin(), 404);

        return view('pages.platform-admins.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->isPlatformAdmin(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route('platform-admins.index')->with('success', 'Platform admin updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->isPlatformAdmin(), 404);
        abort_if(auth()->id() === $user->id, 422, 'You cannot delete your own platform-admin account.');

        $activeCount = User::where('role_id', $this->platformRole()->id)->where('is_active', true)->count();
        abort_if($user->is_active && $activeCount <= 1, 422, 'The final active platform admin cannot be deleted.');

        $user->delete();

        return redirect()->route('platform-admins.index')->with('success', 'Platform admin deleted successfully.');
    }

    public function saveVisibility(Request $request)
    {
        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $selected = collect($validated['permissions'] ?? [])->map(fn ($id) => (int) $id)->all();
        Permission::query()->update(['exposed_to_roles' => false]);
        if ($selected !== []) {
            Permission::whereKey($selected)->update(['exposed_to_roles' => true]);
        }

        return redirect()->route('platform-admins.index')->with('success', 'Role permission visibility updated successfully.');
    }
}
