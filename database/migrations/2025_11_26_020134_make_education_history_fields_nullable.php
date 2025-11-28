<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('education_histories', function (Blueprint $table) {
            $table->string('institute')->nullable()->change();
            $table->string('degree')->nullable()->change();
            $table->string('subject')->nullable()->change();
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->text('remark')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('education_histories', function (Blueprint $table) {
            $table->string('institute')->nullable(false)->change();
            $table->string('degree')->nullable(false)->change();
            
            // ... etc
        });
    }
};
