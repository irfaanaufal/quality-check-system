<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixModelHasRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $map = [
            'super' => 1,
            'admin' => 2,
            'demo'  => 3,
        ];

        foreach ($map as $username => $roleId) {
            $userId = DB::table('users')->where('username', $username)->value('id');
            if ($userId === null) {
                continue;
            }

            // remove any existing entries for this role to avoid duplicates
            DB::table('model_has_roles')->where('role_id', $roleId)->delete();

            // insert mapping
            DB::table('model_has_roles')->insert([
                'role_id' => $roleId,
                'model_id' => $userId,
                'model_type' => 'App\\Models\\User',
            ]);
        }
    }
}
