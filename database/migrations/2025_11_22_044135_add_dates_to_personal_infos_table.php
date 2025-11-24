<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_infos', function (Blueprint $table) {
            $table->date('joining_date')->nullable()->after('tax_number');
            $table->date('effective_date')->nullable()->after('joining_date');
            $table->date('end_date')->nullable()->after('effective_date');
        });
    }

    public function down(): void
    {
        Schema::table('personal_infos', function (Blueprint $table) {
            $table->dropColumn(['joining_date', 'effective_date', 'end_date']);
        });
    }
};