<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_infos', function (Blueprint $table) {
            // ធ្វើឲ្យ column ទាំង ៣ នេះ nullable
            $table->string('religion')->nullable()->change();
            $table->string('blood_group')->nullable()->change();
            $table->string('bank_account_number')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('personal_infos', function (Blueprint $table) {
            // បើ rollback វិញ → ធ្វើឲ្យ required វិញ (តាមដើម)
            $table->string('religion')->nullable(false)->change();
            $table->string('blood_group')->nullable(false)->change();
            $table->string('bank_account_number')->nullable(false)->change();
        });
    }
};