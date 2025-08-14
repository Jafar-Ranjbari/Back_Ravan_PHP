<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin'],
            ['name' => 'Admin'],
            ['name' => 'Editor'],
            ['name' => 'User'],
            ['name' => 'Writer'],
            ['name' => 'Manager'],
            ['name' => 'Support'],
            ['name' => 'Teacher'],
            ['name' => 'Student'],
            ['name' => 'Guest'],
        ];

        foreach ($roles as &$role) {
            $role['created_at'] = now();
            $role['updated_at'] = now();
        }

        DB::table('roles')->insert($roles);
    }
}
