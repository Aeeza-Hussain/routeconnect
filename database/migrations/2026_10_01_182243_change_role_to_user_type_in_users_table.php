<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Replace the string `role` column with an integer `user_type` column.
     *
     * user_type = 0  → Passenger (normal user)
     * user_type = 1  → Admin
     * user_type = 2  → Driver
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the new integer user_type column with default 0 (passenger)
            $table->tinyInteger('user_type')->default(0)->after('bio');
        });

        // Migrate existing data from role (string) → user_type (int)
        DB::statement("UPDATE users SET user_type = 1 WHERE role = 'admin'");
        DB::statement("UPDATE users SET user_type = 2 WHERE role = 'driver'");
        DB::statement("UPDATE users SET user_type = 0 WHERE role = 'passenger' OR role IS NULL");

        Schema::table('users', function (Blueprint $table) {
            // Drop the old string role column
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('passenger')->after('bio');
        });

        DB::statement("UPDATE users SET role = 'admin' WHERE user_type = 1");
        DB::statement("UPDATE users SET role = 'driver' WHERE user_type = 2");
        DB::statement("UPDATE users SET role = 'passenger' WHERE user_type = 0");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });
    }
};
