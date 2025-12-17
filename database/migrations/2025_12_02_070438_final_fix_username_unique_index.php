<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // យក index ទាំងអស់លើ table users
            $indexes = collect(DB::select('SHOW INDEX FROM users WHERE Column_name = "username"'))->pluck('Key_name')->unique();

            // លុប unique index លើ username ទាំងអស់ (ឈ្មោះអ្វីក៏បាន)
            foreach ($indexes as $indexName) {
                if ($indexName !== 'PRIMARY') {
                    // បើជា unique index លើ username → drop វា
                    $table->dropUnique($indexName);
                }
            }

            // ឥឡូវបង្កើត unique index ថ្មីស្រឡាង ឈ្មោះស្តង់ដារ
            $table->unique('username', 'users_username_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
        });
    }
};