<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id');

            $table->string('salutation');
            $table->string('full_name_kh');
            $table->string('full_name_en');
            $table->date('dob');
            $table->string('gender');
            $table->string('marital_status');
            $table->string('nationality');
            $table->string('blood_group')->nullable();
            $table->string('religion');
            $table->string('bank_account_number');
            $table->string('tax_number');

            $table->timestamps();

            // Foreign key
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_infos');
    }
};
