<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddShowPermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            'shows.view',
            'shows.create',
            'shows.update',
            'shows.delete',
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
