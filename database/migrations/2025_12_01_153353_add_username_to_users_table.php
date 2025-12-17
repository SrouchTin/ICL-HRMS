<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Str;   // <-- ត្រូវ import បែបនេះ

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add column (nullable first)
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });

        // Step 2: Fill unique usernames for existing users
        User::whereNull('username')
            ->orWhere('username', '')
            ->orderBy('id')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    // ប្រើ Str::slug() ដោយគ្មាន backslash ខាងមុខ
                    $base = Str::slug($user->name ?: 'user'); // e.g. "sokha-chan"
                    $username = $base;
                    $i = 1;

                    while (User::where('username', $username)->exists()) {
                        $username = $base . '-' . $i;
                        $i++;
                    }

                    $user->username = strtolower($username);
                    $user->saveQuietly();
                }
            });

        // Step 3: Make it NOT NULL + UNIQUE
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};