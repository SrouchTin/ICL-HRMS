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
        Schema::table('personal_infos', function (Blueprint $table) {
            // Contract Type: UDC or FDC
            $table->enum('contract_type', ['UDC', 'FDC'])
                  ->after('tax_number')
                  ->default('UDC');


            // Employee Type: full_time, part_time, probation, contract
            $table->enum('employee_type', ['full_time', 'part_time', 'probation', 'contract'])
                  ->after('contract_type')
                  ->default('full_time');
                  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_infos', function (Blueprint $table) {
            $table->dropColumn(['contract_type', 'employee_type']);
        });
    }
};