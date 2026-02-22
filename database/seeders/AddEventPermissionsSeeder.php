<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddEventPermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            'events.view',
            'events.create',
            'events.update',
            'events.delete',
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
