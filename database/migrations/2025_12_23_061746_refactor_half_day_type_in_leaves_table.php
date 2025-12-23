<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Step 1: Remove old half_day_type column (if exists)
            if (Schema::hasColumn('leaves', 'half_day_type')) {
                $table->dropColumn('half_day_type');
            }

           
            $table->enum('half_day_type', ['morning', 'afternoon'])->nullable()->after('leave_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            
            $table->dropColumn('half_day_type');

            
            $table->string('half_day_type', 191)->nullable()->after('leave_period');
        });
    }
};