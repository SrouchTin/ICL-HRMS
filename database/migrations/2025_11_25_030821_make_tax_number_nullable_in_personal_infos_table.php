<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_infos', function (Blueprint $table) {
            // ធ្វើឲ្យ tax_number អាចទទេបាន (nullable)
            $table->string('tax_number')->nullable()->change();
            
            // បើចង់សុវត្ថិភាពជាងនេះ បន្ថែម default empty string
            // $table->string('tax_number')->default('')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('personal_infos', function (Blueprint $table) {
            // បើ rollback វិញ → ធ្វើឲ្យ required វិញ (តាមដើម)
            $table->string('tax_number')->nullable(false)->change();
        });
    }
};