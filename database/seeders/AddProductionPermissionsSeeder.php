<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddProductionPermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            'productions.view',
            'productions.create',
            'productions.update',
            'productions.delete',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo($perms);
        }
    }
}
