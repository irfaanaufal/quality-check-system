<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixUserIdsSeeder extends Seeder
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

        $offset = 1000000; // large temp offset to avoid collisions

        $found = [];
        // do not use explicit transaction to avoid nested transaction issues in this environment
        // operations below are idempotent for existing users

        // gather existing ids
        foreach ($map as $username => $targetId) {
            $id = DB::table('users')->where('username', $username)->value('id');
            if ($id !== null) {
                $found[$username] = $id;
            }
        }

        // bump existing ids by offset
        foreach ($found as $username => $id) {
            DB::table('users')->where('id', $id)->update(['id' => $id + $offset]);
        }

        // update pivot entries (model_has_roles) to point to final ids
        foreach ($found as $username => $id) {
            $tempId = $id + $offset;
            $finalId = $map[$username];
            DB::table('model_has_roles')
                ->where('model_type', 'App\\Models\\User')
                ->where('model_id', $tempId)
                ->update(['model_id' => $finalId]);
        }

        // move users to final ids
        foreach ($found as $username => $id) {
            $tempId = $id + $offset;
            $finalId = $map[$username];
            DB::table('users')->where('id', $tempId)->update(['id' => $finalId]);
        }

        // reset auto-increment to max(id)+1
        $max = DB::table('users')->max('id');
        DB::statement('ALTER TABLE `users` AUTO_INCREMENT = ' . ($max + 1));
    }
}
