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
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn(['holiday_date', 'name']);

            $table->string('holiday_name');
            $table->date('from_date');
            $table->date('to_date');
            $table->integer('day');
            $table->text('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropColumn([
                'holiday_name',
                'from_date',
                'to_date',
                'day',
                'remark'
            ]);

            $table->date('holiday_date');
            $table->string('name');
        });

    }
};
