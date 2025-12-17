<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // បន្ថែមជាមួយ nullable ជាមុន
        $table->string('employee_login')->nullable()->after('username');
    });

    // បំពេញ value ឲ្យ record ចាស់
    DB::statement("UPDATE users SET employee_login = CONCAT('TEMP', id) WHERE employee_login IS NULL OR employee_login = ''");

    Schema::table('users', function (Blueprint $table) {
        // បន្ទាប់មកទើប unique + not null
        $table->string('employee_login')->unique()->nullable(false)->change();
    });
}

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['employee_login']);
            $table->dropColumn('employee_login');
        });
    }
};