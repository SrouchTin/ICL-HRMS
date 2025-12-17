<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up()
    {
        // Fix employees: Drop user_id if exists
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });

        // Fix users: Add employee_id (foreign key) if not exists
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->foreignId('employee_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('employees')
                      ->onDelete('cascade');
                $table->index('employee_id');
            }

            // Add employee_login if not exists
            if (!Schema::hasColumn('users', 'employee_login')) {
                $table->string('employee_login')->unique()->nullable()->after('username');
            }
        });

        // Optional: Fill employee_login for existing users (avoid duplicates)
        DB::table('users')->whereNull('employee_login')->update(['employee_login' => DB::raw("CONCAT('EMP', id)")]);
    }

    public function down()
    {
        // Reverse changes (for rollback)
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }
            if (Schema::hasColumn('users', 'employee_login')) {
                $table->dropUnique(['employee_login']);
                $table->dropColumn('employee_login');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users');
        });
    }
};