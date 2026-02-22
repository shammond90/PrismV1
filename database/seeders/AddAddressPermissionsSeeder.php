<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddAddressPermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            'addresses.create',
            'addresses.update',
            'addresses.delete',
            'addresses.view',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo($perms);
    }
}
