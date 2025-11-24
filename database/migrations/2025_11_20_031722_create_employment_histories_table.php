<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');

            $table->string('company_name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('designation')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->text('remark')->nullable();
            $table->string('rate')->nullable();
            $table->text('reason_for_leaving')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_histories');
    }
};
