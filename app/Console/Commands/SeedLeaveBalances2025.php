<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedLeaveBalances2025 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:seed-balances-2025';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed leave balances for all employees in year 2025 based on leave_types.max_days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = 2025;

        $this->info("Seeding leave balances for year {$year}...");

        $leaveTypes = DB::table('leave_types')
            ->whereNotNull('max_days')
            ->get(['id', 'max_days']);

        if ($leaveTypes->isEmpty()) {
            $this->error('No leave types with max_days found!');
            return;
        }

        $employees = DB::table('employees')->pluck('id');

        if ($employees->isEmpty()) {
            $this->error('No employees found!');
            return;
        }

        $totalInserted = 0;

        foreach ($employees as $employeeId) {
            foreach ($leaveTypes as $type) {
                $affected = DB::table('leave_balances')->updateOrInsert(
                    [
                        'employee_id'   => $employeeId,
                        'leave_type_id' => $type->id,
                        'year'          => $year,
                    ],
                    [
                        'total_days'     => $type->max_days,
                        'used_days'      => 0,
                        'remaining_days' => $type->max_days,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]
                );

                if ($affected) {
                    $totalInserted++;
                }
            }
        }

        $this->info("Successfully seeded {$totalInserted} leave balance records for year 2025!");
    }
}