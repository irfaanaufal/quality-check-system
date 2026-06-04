<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Demo user
        User::updateOrCreate([
            'username' => 'demo',
        ], [
            'email' => 'user@gmail.com',
            'name' => 'Demo User',
            'password' => Hash::make('password'),
        ]);

        // Admin user
        $admin = User::updateOrCreate([
            'username' => 'admin',
        ], [
            'email' => 'admin@gmail.com',
            'name' => 'Administrator',
            'password' => Hash::make('password'),
        ]);

        // Super admin user
        $super = User::updateOrCreate([
            'username' => 'super',
        ], [
            'email' => 'super@gmail.com',
            'name' => 'Super Administrator',
            'password' => Hash::make('password'),
        ]);

        // Initialize roles with fixed IDs (if table exists)
        try {
            if (SchemaExists('roles')) {
                // Use DB to ensure we can set specific IDs
                DB::table('roles')->updateOrInsert(['id' => 1], ['id' => 1, 'name' => 'super admin', 'guard_name' => 'web']);
                DB::table('roles')->updateOrInsert(['id' => 2], ['id' => 2, 'name' => 'admin', 'guard_name' => 'web']);
                DB::table('roles')->updateOrInsert(['id' => 3], ['id' => 3, 'name' => 'user', 'guard_name' => 'web']);

                // Assign roles: super -> super admin, admin -> admin
                $admin->assignRole('admin');
                $super->assignRole('super admin');
            }
        } catch (\Throwable $e) {
            Log::warning('Could not initialize roles or assign admin role: '.$e->getMessage());
        }
    }
}

function SchemaExists($table)
{
    try {
        return DB::getSchemaBuilder()->hasTable($table);
    } catch (\Throwable $e) {
        return false;
    }
}
