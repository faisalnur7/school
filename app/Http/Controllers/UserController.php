<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'login_verification_enabled' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $validated['password'] = Hash::make($validated['password']);
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
        $roles = Role::all();
        return view('pages.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'login_verification_enabled' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role_id = $data['role_id'] ?? null;
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

        $user->save();
        
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
