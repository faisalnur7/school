<?php

namespace Tests;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Give a user the named permissions (as checked by the `permission:` route middleware),
     * through a role of their own. Permissions are created if the migrations didn't seed them.
     */
    protected function grantPermissions(User $user, string ...$names): User
    {
        $role = $user->role_id ? Role::findOrFail($user->role_id) : Role::create(['name' => 'Test role ' . $user->id]);

        $ids = collect($names)->map(fn (string $name) => Permission::firstOrCreate(
            ['name' => $name],
            ['display_name' => $name]
        )->id);

        $role->permissions()->syncWithoutDetaching($ids->all());
        $user->forceFill(['role_id' => $role->id])->save();

        return $user->refresh();
    }
}
