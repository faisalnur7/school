<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $permissionIds = Permission::query()
            ->whereIn('name', [
                'view_card_terminal_report',
                'view_card_tutorial_exam_report',
            ])
            ->pluck('id')
            ->all();

        if (empty($permissionIds)) {
            return;
        }

        Role::query()
            ->whereIn('name', ['Admin', 'Teacher'])
            ->get()
            ->each(function (Role $role) use ($permissionIds) {
                $role->permissions()->syncWithoutDetaching($permissionIds);
            });
    }

    public function down(): void
    {
        $permissionIds = Permission::query()
            ->whereIn('name', [
                'view_card_terminal_report',
                'view_card_tutorial_exam_report',
            ])
            ->pluck('id')
            ->all();

        if (empty($permissionIds)) {
            return;
        }

        Role::query()
            ->whereIn('name', ['Admin', 'Teacher'])
            ->get()
            ->each(function (Role $role) use ($permissionIds) {
                $role->permissions()->detach($permissionIds);
            });
    }
};
