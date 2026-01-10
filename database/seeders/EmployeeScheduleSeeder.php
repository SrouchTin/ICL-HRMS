<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeSchedule;

class EmployeeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();

        $data = [];

        foreach ($employees as $employee) {
            $data[] = [
                'employee_id' => $employee->id,
                'schedule_template_id' => 1, // default schedule
                'shift_id' => 1,             // default shift
                'start_date' => now()->toDateString(),
                'end_date' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        EmployeeSchedule::insert($data);
    }
}
