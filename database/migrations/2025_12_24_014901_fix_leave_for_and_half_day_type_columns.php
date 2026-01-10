<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // 1. Drop the wrong column
            $table->dropColumn('leave_period');

            // 2. Add correct leave_for column
            $table->enum('leave_for', ['full_day', 'half_day'])
                  ->default('full_day')
                  ->after('leave_type_id');

            // 3. Fix half_day_type to include 'other' and make it properly nullable
            $table->dropColumn('half_day_type');

            $table->enum('half_day_type', ['morning', 'afternoon', 'other'])
                  ->nullable()
                  ->after('leave_for');
        });
    }

    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Reverse changes
            $table->dropColumn(['leave_for', 'half_day_type']);

            $table->enum('leave_period', ['Full Day', 'Half Day'])
                  ->default('Full Day');

            $table->enum('half_day_type', ['morning', 'afternoon'])
                  ->nullable();
        });
    }
};