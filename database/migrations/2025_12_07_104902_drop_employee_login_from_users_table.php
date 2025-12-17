<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('employee_login');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->integer('employee_login')->default(0); // optional rollback
    });
}

};
