<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('emergency_contacts', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->change();
            $table->string('relationship')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('emergency_contacts', function (Blueprint $table) {
            $table->string('contact_person')->nullable(false)->change();
            $table->string('relationship')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
        });
    }
};