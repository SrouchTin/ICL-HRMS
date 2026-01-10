<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->boolean('come_early')->default(false);
            $table->boolean('come_late')->default(false);
            $table->boolean('leave_early')->default(false);
            $table->boolean('leave_late')->default(false);
            $table->integer('overtime_minutes')->default(0);
            $table->foreignId('leave_id')->nullable()->constrained('leaves')->onDelete('set null');
            $table->foreignId('holiday_id')->nullable()->constrained('holidays')->onDelete('set null');
            $table->boolean('is_working_day');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};