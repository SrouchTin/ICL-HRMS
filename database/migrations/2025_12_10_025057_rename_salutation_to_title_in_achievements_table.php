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
        Schema::table('achievements', function (Blueprint $table) {
            // ប្តូរឈ្មោះ column ពី salutation → title
            $table->renameColumn('salutation', 'title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achievements', function (Blueprint $table) {
            // បើ rollback វិញ → ប្តូរត្រឡប់ទៅ salutation
            $table->renameColumn('title', 'salutation');
        });
    }
};