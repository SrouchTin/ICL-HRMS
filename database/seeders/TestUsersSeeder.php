<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;

class TestUsersSeeder extends Seeder
{
    public function run()
    {
        $users =[
            [
                'employee_id' => 1,
                'username' => 'admin',
                'role_id' => 1,
                'password' => bcrypt('Admin@12345'),
                'branch_id' => 1,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'employee_id' => 2,
                'username' => 'hr',
                'role_id' => 2,
                'password' => bcrypt('Hr@12345'),
                'branch_id' => 1,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        User::insert($users);
    }
}