<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Symfony\Component\Clock\now;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                "name" => 'admin',
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => 'hr',
                "created_at" => now(),
                "updated_at" => now(),
            ],
            [
                "name" => 'employee',
                "created_at" => now(),
                "updated_at" => now(),
            ]

        ];

        Role::insert($roles);
    }
}
