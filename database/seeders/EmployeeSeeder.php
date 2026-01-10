<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'department_id' => 1,
                'branch_id'     => 1,
                'position_id'   => 1,
                'employee_code' => 'EMP001',
                'image'        => null,
                'status'        => 'active',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],  
            [
                'department_id' => 3,
                'branch_id'     => 1,
                'position_id'   => 4,
                'employee_code' => 'EMP002',
                'image'        => null,
                'status'        => 'active',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],   
        ];
        Employee::insert($employees);
    }
}
