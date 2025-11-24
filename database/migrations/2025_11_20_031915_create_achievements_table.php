<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');

            $table->string('salutation')->nullable();
            $table->year('year_awarded')->nullable();
            $table->string('country')->nullable();
            $table->string('program_name')->nullable();
            $table->string('organizer_name')->nullable();
            $table->text('remark')->nullable();
            $table->string('attachment')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
