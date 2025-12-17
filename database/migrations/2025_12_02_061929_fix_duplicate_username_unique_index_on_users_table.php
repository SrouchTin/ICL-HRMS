<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    // Drop the old duplicate index if it exists
    Schema::table('users', function (Blueprint $table) {
        if (DB::getDriverName() === 'mysql') {
            // Check and drop the index safely
            $table->dropUnique('users_username_unique'); // this drops by index name
        }

        // Now safely add it again (optional, only if you really need to recreate)
        $table->string('username')->change()->unique();
        // or just: $table->unique('username');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropUnique('users_username_unique');
    });
}
};
