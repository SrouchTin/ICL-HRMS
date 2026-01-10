<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeedLeaveBalances2026 extends Command
{
    protected $signature = 'leave:seed-balances-2026';

    protected $description = 'Seed or update leave balances for ALL employees in year 2026 based on leave_types.max_days';

    public function handle()
    {
        $year = 2026; // ឥឡូវជាឆ្នាំ 2026 ហើយ!

        $this->info("🌱 Starting to seed/update leave balances for year {$year}...");

        // Get all leave types that have max_days > 0
        $leaveTypes = DB::table('leave_types')
            ->whereNotNull('max_days')
            ->where('max_days', '>', 0)
            ->get(['id', 'name', 'max_days']);

        if ($leaveTypes->isEmpty()) {
            $this->error('❌ No leave types with max_days found in leave_types table!');
            return 1;
        }

        $this->info("Found {$leaveTypes->count()} leave types with entitlements:");

        foreach ($leaveTypes as $type) {
            $this->line("   • {$type->name}: {$type->max_days} days");
        }

        // Get all employees (you can add ->where('status', 'active') if needed)
        $employees = DB::table('employees')->select('id')->get();

        if ($employees->isEmpty()) {
            $this->error('❌ No employees found!');
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
                        'used_days'      => DB::raw('COALESCE(used_days, 0)'), // Keep existing, default 0
                        'remaining_days' => DB::raw('total_days - COALESCE(used_days, 0)'),
                        'updated_at'     => now(),
                    ]
                );

                if ($affected) {
                    // Simple way: if record had used_days > 0 before, count as update
                    $hadUsedDays = DB::table('leave_balances')
                        ->where('employee_id', $employee->id)
                        ->where('leave_type_id', $type->id)
                        ->where('year', $year)
                        ->where('used_days', '>', 0)
                        ->exists();

                    if ($hadUsedDays) {
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

        $this->info("✅ Completed seeding for year 2026!");
        $this->info("   • New balances created: {$inserted}");
        $this->info("   • Existing balances updated: {$updated}");
        $this->info("   • Total records processed: " . ($inserted + $updated));

        $this->newLine();
        $this->info("🎉 All employees now have correct leave balance for 2026!");
        $this->info("   Now when they open Create Leave → Balance will show correctly (no more 0 0 0)");

        return 0;
    }
}