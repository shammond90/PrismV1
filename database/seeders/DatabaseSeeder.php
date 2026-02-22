<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure permissions and roles exist first
        $this->call([
            PermissionSeeder::class,
            AddShowPermissionsSeeder::class,
            AddShowCataloguePermissionsSeeder::class,
            AddProductionPermissionsSeeder::class,
            AddEventPermissionsSeeder::class,
        ]);

        // Ensure a basic test user exists
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password']
        );

        // Ensure an admin user exists and assign the `admin` role
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => 'password']
        );

        $admin->assignRole('Admin');
    }
}
