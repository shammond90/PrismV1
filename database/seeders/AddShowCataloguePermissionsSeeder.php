<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddShowCataloguePermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            'show_catalogues.view',
            'show_catalogues.create',
            'show_catalogues.update',
            'show_catalogues.delete',
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
