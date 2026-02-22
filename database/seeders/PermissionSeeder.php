<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            // contacts permissions
            'contacts.create',
            'contacts.view',
            'contacts.show',
            'contacts.update',
            'contacts.delete',
            // companies permissions
            'companies.create',
            'companies.view',
            'companies.show',
            'companies.update',
            'companies.delete',
            // employments permissions
            'employments.create',
            'employments.view',
            'employments.update',
            'employments.delete',
            // venues permissions
            'venues.create',
            'venues.view',
            'venues.show',
            'venues.update',
            'venues.delete',
            // buildings permissions
            'buildings.create',
            'buildings.view',
            'buildings.show',
            'buildings.update',
            'buildings.delete',
            // spaces permissions
            'spaces.create',
            'spaces.view',
            'spaces.show',
            'spaces.update',
            'spaces.delete',
            // phones permissions
            'phones.create',
            'phones.view',
            'phones.show',
            'phones.update',
            'phones.delete',
            // emails permissions
            'emails.create',
            'emails.view',
            'emails.show',
            'emails.update',
            'emails.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $editor = Role::firstOrCreate(['name' => 'Editor']);
        $editor->givePermissionTo([
            'contacts.create', 'contacts.view', 'contacts.show', 'contacts.update',
            'companies.create', 'companies.view', 'companies.show', 'companies.update',
            'employments.create', 'employments.view', 'employments.update',
            'venues.create', 'venues.view', 'venues.show', 'venues.update',
            'buildings.create', 'buildings.view', 'buildings.show', 'buildings.update',
            'spaces.create', 'spaces.view', 'spaces.show', 'spaces.update',
            'phones.create', 'phones.view', 'phones.show', 'phones.update',
            'emails.create', 'emails.view', 'emails.show', 'emails.update',
        ]);

        $viewOnly = Role::firstOrCreate(['name' => 'View Only']);
        $viewOnly->givePermissionTo([
            'contacts.view', 'contacts.show',
            'companies.view', 'companies.show',
            'employments.view',
            'venues.view', 'venues.show',
            'buildings.view', 'buildings.show',
            'spaces.view', 'spaces.show',
            'phones.view', 'phones.show',
            'emails.view', 'emails.show',
        ]);

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());
    }
}
