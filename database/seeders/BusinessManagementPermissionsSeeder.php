<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class BusinessManagementPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $ids = collect(['access', 'create', 'show', 'edit', 'delete', 'import', 'activate', 'report'])->map(function ($action) {
            $permission = Permission::withTrashed()->firstOrCreate(['title' => 'business_management_'.$action]);
            if ($permission->trashed()) {
                $permission->restore();
            }

            return $permission->id;
        });
        $admin = Role::find(1);
        if ($admin) {
            $admin->permissions()->syncWithoutDetaching($ids);
        }
    }
}
