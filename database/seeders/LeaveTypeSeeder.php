<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Symfony\Component\Clock\now;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                "name" => "ច្បាប់ឈប់សម្រាកដោយជម្ងឺ - Sick Leave",
                "max_days" => 6,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "ច្បាប់ឈប់សម្រាកប្រចាំឆ្នាំ - Annual Leave",
                "max_days" => 18,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "ច្បាប់ឈប់សម្រាកបពិសេស - Special Leave",
                "max_days" => 7,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "ច្បាប់ឈប់សម្រាកមិនមានប្រាក់ឈ្នួល - Unpaid Leave",
                "max_days" => null,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "ច្បាប់ឈប់សម្រាកសង - Compensate Leave",
                "max_days" => null,
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "name" => "ច្បាប់ឈប់សម្រាកលំហែមាតុភាព - Meternity Leave",
                "max_days" => 90,
                "created_at" => now(),
                "updated_at" => now()
            ],
            ];

            LeaveType::insert($leaveTypes);
    }
}
