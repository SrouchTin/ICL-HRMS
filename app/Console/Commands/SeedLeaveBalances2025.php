<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeedLeaveBalances2025 extends Command
{
    protected $signature = 'leave:seed-balances-2025';

    protected $description = 'Seed or update leave balances for ALL employees in year 2025 based on leave_types.max_days';

    public function handle()
    {
        $year = 2025;

        $this->info("🌱 Starting to seed/update leave balances for year {$year}...");

        // Get all leave types that have max_days defined
        $leaveTypes = DB::table('leave_types')
            ->whereNotNull('max_days')
            ->where('max_days', '>', 0)
            ->get(['id', 'name', 'max_days']);

        if ($leaveTypes->isEmpty()) {
            $this->error('❌ No leave types with max_days found!');
            return 1;
        }

        $this->info("Found {$leaveTypes->count()} leave types with entitlements.");

        // Get ALL employees (active or inactive – you can filter if needed)
        $employees = DB::table('employees')
            ->select('id')
            ->get();

        if ($employees->isEmpty()) {
            $this->error('❌ No employees found in the database!');
            return 1;
        }

        $this->info("Found {$employees->count()} employees. Processing...");

        $bar = $this->output->createProgressBar($employees->count() * $leaveTypes->count());
        $bar->start();

        $inserted = 0;
        $updated = 0;

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $type) {
                $affected = DB::table('leave_balances')->updateOrInsert(
                    [
                        'employee_id'   => $employee->id,
                        'leave_type_id' => $type->id,
                        'year'          => $year,
                    ],
                    [
                        'total_days'     => $type->max_days,
                        'used_days'      => DB::raw('used_days'), // Keep existing used_days
                        'remaining_days' => DB::raw('total_days - used_days'), // Recalculate
                        'updated_at'     => now(),
                        'created_at'     => now(), // Only used on insert
                    ]
                );

                if ($affected) {
                    // To distinguish insert vs update, check if record existed before
                    $exists = DB::table('leave_balances')
                        ->where('employee_id', $employee->id)
                        ->where('leave_type_id', $type->id)
                        ->where('year', $year)
                        ->where('created_at', '<', Carbon::now()->subMinute()) // rough check
                        ->exists();

                    if ($exists) {
                        $updated++;
                    } else {
                        $inserted++;
                    }
                }

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Completed!");
        $this->info("   • New records inserted: {$inserted}");
        $this->info("   • Existing records updated: {$updated}");
        $this->info("   • Total employees processed: {$employees->count()}");
        $this->info("   • Total leave types: {$leaveTypes->count()}");

        return 0;
    }
}