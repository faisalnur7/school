<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::query()
            ->when(!auth()->user()?->isPlatformAdmin(), fn ($query) => $query->where('system_key', '!=', 'platform_admin'))
            ->withCount(['users' => fn ($query) => $query->withoutPlatformAdmins()])
            ->orderBy('name')
            ->get();
        $totalUsers = User::withoutPlatformAdmins()->count();
        $selectedRole = $request->query('role', 'all');

        $usersQuery = User::withoutPlatformAdmins()->with('role')->latest('id');

        if ($selectedRole === 'make-super-admin') {
            $usersQuery->where('is_super_admin', false);
        } elseif ($selectedRole !== 'all' && ctype_digit((string) $selectedRole)) {
            $usersQuery->where('role_id', (int) $selectedRole);
        } else {
            $selectedRole = 'all';
        }

        $users = $usersQuery->paginate(15)->withQueryString();

        return view('pages.users.index', compact('users', 'roles', 'selectedRole', 'totalUsers'));
    }

    public function create()
    {
        $roles = Role::assignableToInstituteUsers()->get();
        return view('pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => ['nullable', Rule::exists('roles', 'id')->where(fn ($query) => $query->where('is_reserved', false))],
            'is_active' => 'nullable|boolean',
            'login_verification_enabled' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['login_verification_enabled'] = $request->boolean('login_verification_enabled');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = 'images/user_image';
            if (! is_dir(public_path($path))) {
                mkdir(public_path($path), 0775, true);
            }
            $filename = 'user_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->move(public_path($path), $filename);
            $validated['image'] = $path.'/'.$filename;
        }

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManage($user);
        $roles = $this->assignableRoles()->get();
        return view('pages.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManage($user);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => ['nullable', Rule::in($this->assignableRoles()->pluck('id')->all())],
            'is_active' => 'nullable|boolean',
            'login_verification_enabled' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role_id = $data['role_id'] ?? null;
        $user->is_active = $request->boolean('is_active');
        $user->login_verification_enabled = $request->boolean('login_verification_enabled');

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = 'images/user_image';
            if (! is_dir(public_path($path))) {
                mkdir(public_path($path), 0775, true);
            }
            $filename = 'user_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path($path), $filename);
            $user->image = $path.'/'.$filename;
        }

        if (! $user->login_verification_enabled) {
            $user->login_verification_code = null;
            $user->login_verification_expires_at = null;
        }

        if (! $user->is_active) {
            $user->login_verification_code = null;
            $user->login_verification_expires_at = null;
        }

        $user->save();
        
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManage($user);
        $user->is_active = ! $user->is_active;

        if (! $user->is_active) {
            $user->login_verification_code = null;
            $user->login_verification_expires_at = null;
        }

        $user->save();

        return back()->with('success', 'User status updated successfully');
    }

    public function makeSuperAdmin($id)
    {
        abort_unless(auth()->user()?->is_super_admin, 403);

        $user = User::findOrFail($id);
        $user->update(['is_super_admin' => true]);

        return redirect()->route('users.index', ['role' => 'make-super-admin'])
            ->with('success', $user->name . ' is now a Super Admin.');
    }

    public function revokeSuperAdmin($id)
    {
        abort_unless(auth()->user()?->isPlatformAdmin(), 403);

        $user = User::with('role')->findOrFail($id);
        abort_if($user->isPlatformAdmin(), 404);

        if (! $user->is_super_admin && ! $user->role?->isInstituteSuperAdmin()) {
            return back()->with('error', $user->name . ' is not a Super Admin.');
        }

        $user->is_super_admin = false;

        if ($user->role?->isInstituteSuperAdmin()) {
            $user->role_id = null;
        }

        $user->save();

        return back()->with('success', $user->name . ' is no longer a Super Admin. Assign a new role from Edit if needed.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManage($user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    /**
     * Reserved-role users are hidden from institute admins. The Platform Admin
     * may manage Super Admin role users, but Platform Admin accounts stay off-limits here.
     */
    private function ensureCanManage(User $user): void
    {
        if (! $user->role?->is_reserved) {
            return;
        }

        $canManage = auth()->user()?->isPlatformAdmin() && ! $user->role->isPlatformAdmin();

        abort_unless($canManage, 404);
    }

    private function assignableRoles()
    {
        return Role::query()
            ->when(
                auth()->user()?->isPlatformAdmin(),
                fn ($query) => $query->where(fn ($q) => $q->where('is_reserved', false)->orWhere('system_key', 'super_admin')),
                fn ($query) => $query->assignableToInstituteUsers(),
            )
            ->orderBy('name');
    }
}
