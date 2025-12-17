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
    Schema::table('addresses', function (Blueprint $table) {
        $table->string('city')->nullable()->change();
        $table->string('province')->nullable()->change();
        $table->string('country')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('addresses', function (Blueprint $table) {
        $table->string('city')->nullable(false)->change();
        $table->string('province')->nullable(false)->change();
        $table->string('country')->nullable(false)->change();
    });
}

};
