<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'default shift',
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'late_after_min' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'special shift',
                'start_time' => '08:30:00',
                'end_time' => '17:00:00',
                'late_after_min' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        Shift::insert($shifts);
    }
}
