<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // 1. ប្តូរឈ្មោះ half_day_type → half_day_session (ច្បាស់ជាង)
            $table->renameColumn('half_day_type', 'half_day_session');

            // 2. បន្ថែម column ថ្មី ដើម្បីដឹងថា Half Day នៅថ្ងៃណា (from ឬ to)
            $table->string('half_day_applies_to')->nullable()->after('half_day_session');
            // តម្លៃអាចជា: null (បើ full day), 'from_date', 'to_date'

            // 3. កែ half_day_session ឱ្យជា enum ច្បាស់លាស់
            $table->enum('half_day_session', ['morning', 'afternoon'])->nullable()->change();


        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->renameColumn('half_day_session', 'half_day_type');
            $table->dropColumn('half_day_applies_to');
        });
    }
};