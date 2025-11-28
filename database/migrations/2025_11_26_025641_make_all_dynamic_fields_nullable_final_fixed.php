<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'education_histories' => ['institute', 'degree', 'subject', 'start_date', 'end_date', 'remark'],
            'training_histories'  => ['institute', 'subject', 'start_date', 'end_date', 'remark', 'attachment'],
            'employment_histories'=> ['company_name', 'designation', 'start_date', 'end_date', 'supervisor_name', 'rate', 'reason', 'remark'],
            'achievements'        => ['title', 'year_awarded', 'country', 'program_name', 'organizer_name', 'remark', 'attachment'],
            'emergency_contacts'  => ['contact_person', 'relationship', 'phone_number'],
            'family_members'      => ['name', 'relationship', 'dob', 'gender', 'tax_filling', 'phone_number', 'attachment'], // ← Fixed here
            'attachments'         => ['attachment_name', 'file_path', 'attachment'],
        ];

        foreach ($tables as $table => $columns) {
            // Skip if table doesn't exist
            $tableExists = DB::select("SHOW TABLES LIKE '{$table}'");
            if (empty($tableExists)) {
                continue;
            }

            foreach ($columns as $column) {
                // Check if column exists (no ? placeholder → works on Windows)
                $colExists = DB::select("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");

                if (!empty($colExists)) {
                    try {
                        if (in_array($column, ['start_date', 'end_date', 'dob'])) {
                            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` DATE NULL");
                        } elseif ($column === 'year_awarded') {
                            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` INT NULL");
                        } elseif ($column === 'remark' || $column === 'reason') {
                            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` TEXT NULL");
                        } else {
                            DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` VARCHAR(255) NULL");
                        }
                    } catch (\Exception $e) {
                        // Silently ignore (already nullable, etc.)
                    }
                }
            }
        }
    }

    public function down()
    {
        // No rollback needed
    }
};