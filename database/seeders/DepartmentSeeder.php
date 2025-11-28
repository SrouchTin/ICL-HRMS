<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'department_code' => "IT",
                'department_name' => "Informatoin Technology"
            ],
            [
                'department_code' => "Management",
                'department_name' => "Management"
            ],
                        [
                'department_code' => "R&C",
                'department_name' => "Risk & Compliance"
            ],
            [
                'department_code' => " Audit",
                'department_name' => " Audit"
            ],
            [
                'department_code' => "Operations",
                'department_name' => "Operations"
            ],
                        [
                'department_code' => "HR & Admin",
                'department_name' => "HR & Admin"
            ],
            [
                'department_code' => "  Finance & Accounting",
                'department_name' => "  Finance & Accounting"
            ],

        ];

        Department::insert($departments);
    }
}
