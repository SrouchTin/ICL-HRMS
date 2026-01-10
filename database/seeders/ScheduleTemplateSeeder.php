<?php

namespace Database\Seeders;

use App\Models\ScheduleTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $scheduleTemplates = [
            [
                'name' => 'default schedule template',
                'shift_id' => 1,
                'mon'       => 1,
                'tue'       => 1,
                'wed'       => 1,
                'thu'       => 1,
                'fri'       => 1,
                'sat'       => 0,
                'sun'       => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'special schedule template',
                'shift_id'  => 2,
                'mon'    => 1,
                'tue'    => 1,
                'wed'    => 1,
                'thu'    => 1,
                'fri'    => 1,
                'sat'    => 0,
                'sun'    => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        ScheduleTemplate::insert($scheduleTemplates);
    }
}
