<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('id');
            $table->unsignedBigInteger('person_incharge_id')->nullable()->after('employee_id');
            $table->foreign('person_incharge_id')->references('id')->on('employees');
        });
    }

    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['person_incharge_id']);
            $table->dropColumn(['subject', 'person_incharge_id']);
        });
    }
};
