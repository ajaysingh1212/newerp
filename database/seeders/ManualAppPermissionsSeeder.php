<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ManualAppPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect([
            'manual_entry_access',
            'manual_app_create',
            'manual_app_edit',
            'manual_app_show',
            'manual_app_delete',
            'manual_app_access',
        ])->map(function ($title) {
            $permission = Permission::withTrashed()->firstOrCreate(['title' => $title]);

            if ($permission->trashed()) {
                $permission->restore();
            }

            return $permission;
        });

        $admin = Role::find(1);

        if ($admin) {
            $admin->permissions()->syncWithoutDetaching($permissions->pluck('id'));
        }
    }
}
