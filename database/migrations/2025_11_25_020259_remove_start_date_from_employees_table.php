<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }

    public function down(): void
    {
        // បើចង់ rollback វិញ (បន្ថែម column មកវិញ)
        Schema::table('employees', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('position_id');
        });
    }
};