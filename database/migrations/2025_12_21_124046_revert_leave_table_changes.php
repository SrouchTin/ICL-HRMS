<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - ត្រឡប់មកដូចចាស់វិញ
     */
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // បើមាន column half_day_session និង half_day_applies_to នៅ → លុបចោល
            if (Schema::hasColumn('leaves', 'half_day_session')) {
                $table->dropColumn('half_day_session');
            }

            if (Schema::hasColumn('leaves', 'half_day_applies_to')) {
                $table->dropColumn('half_day_applies_to');
            }

            // បើធ្លាប់ rename ពី half_day_type → half_day_session
            // ត្រូវ rename ត្រឡប់មកវិញ (បើ column half_day_session នៅមាន)
            // ប៉ុន្តែបើអ្នកបាន drop រួចហើយ គឺគ្មាន half_day_type ទេ
            // បើចង់បង្កើត half_day_type មកវិញ (string nullable)
            $table->string('half_day_type')->nullable()->after('leave_period');
        });
    }

    /**
     * Reverse the migrations - បើ rollback
     */
    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // បើ rollback វិញ → លុប half_day_type ហើយបង្កើត column ថ្មីមកវិញ
            $table->dropColumn('half_day_type');

            $table->enum('half_day_session', ['morning', 'afternoon'])->nullable()->after('leave_period');
            $table->string('half_day_applies_to')->nullable()->after('half_day_session');
        });
    }
};